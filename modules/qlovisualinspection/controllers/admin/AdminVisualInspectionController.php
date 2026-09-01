<?php
/**
 * Admin Controller for Visual Inspection and Housekeeping Quality Evaluation
 *
 * @author    QloApps Engineering
 * @copyright Since 2026 QloApps
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License 3.0 (AFL-3.0)
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

class AdminVisualInspectionController extends ModuleAdminController
{
    const PYTHON_SERVICE_URL = 'http://127.0.0.1:8102/v1/visual-inspections';
    const CURL_TIMEOUT_MS = 800;
    const MAX_FILE_SIZE_BYTES = 5242880; // 5 MB

    public function __construct()
    {
        $this->bootstrap = true;
        parent::__construct();
    }

    /**
     * Main action to render and process the room inspection form
     */
    public function initContent()
    {
        parent::initContent();

        $metricsData = null;
        $errorMessage = null;
        $previewImageBase64 = null;
        $checklistSummary = null;
        $selectedRoomId = null;

        if (Tools::isSubmit('submitInspection')) {
            $selectedRoomId = Tools::getValue('room_id');
            $chkBed = (bool) Tools::getValue('chk_bed');
            $chkBath = (bool) Tools::getValue('chk_bath');
            $chkAmenities = (bool) Tools::getValue('chk_amenities');

            $checklistSummary = [
                'bed' => $chkBed,
                'bath' => $chkBath,
                'amenities' => $chkAmenities,
            ];

            if (!isset($_FILES['inspection_photo']) || $_FILES['inspection_photo']['error'] !== UPLOAD_ERR_OK) {
                $errorMessage = $this->l('Por favor, selecione uma foto válida antes de submeter.');
            } else {
                $tmpFilePath = $_FILES['inspection_photo']['tmp_name'];
                $fileSize = (int) $_FILES['inspection_photo']['size'];

                if ($fileSize > self::MAX_FILE_SIZE_BYTES) {
                    $errorMessage = $this->l('O arquivo excede o limite máximo permitido de 5 MB.');
                } else {
                    $mimeType = function_exists('mime_content_type') ? mime_content_type($tmpFilePath) : $_FILES['inspection_photo']['type'];
                    $allowedMimes = ['image/jpeg', 'image/png', 'image/pjpeg', 'image/x-png'];

                    if (!in_array($mimeType, $allowedMimes)) {
                        $errorMessage = $this->l('Formato inválido. Apenas imagens JPEG e PNG são aceitas.');
                    } else {
                        // Generate Base64 preview for immediate feedback display
                        $fileData = @file_get_contents($tmpFilePath);
                        if ($fileData) {
                            $previewImageBase64 = 'data:' . $mimeType . ';base64,' . base64_encode($fileData);
                        }

                        $inspectionId = 'INSP-' . date('YmdHis') . '-' . preg_replace('/[^a-zA-Z0-9_-]/', '', (string)$selectedRoomId);
                        $correlationId = Tools::passwdGen(16, 'ALPHANUMERIC');

                        $cFile = new CURLFile($tmpFilePath, $mimeType, $_FILES['inspection_photo']['name']);
                        $postData = [
                            'file' => $cFile,
                            'room_id' => (string) $selectedRoomId,
                            'inspection_id' => $inspectionId,
                        ];

                        $ch = curl_init(self::PYTHON_SERVICE_URL);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                        curl_setopt($ch, CURLOPT_POST, true);
                        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
                        curl_setopt($ch, CURLOPT_TIMEOUT_MS, self::CURL_TIMEOUT_MS);
                        curl_setopt($ch, CURLOPT_HTTPHEADER, [
                            'X-Correlation-ID: ' . $correlationId,
                        ]);

                        $response = curl_exec($ch);
                        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                        $curlErr = curl_errno($ch);
                        curl_close($ch);

                        if ($response && $httpCode === 200) {
                            $metricsData = json_decode($response, true);
                        } elseif ($httpCode === 400) {
                            $errJson = json_decode($response, true);
                            $errorMessage = !empty($errJson['detail']) ? $errJson['detail'] : $this->l('Arquivo de imagem rejeitado pelo validador.');
                        } else {
                            // Fallback / Contingency if Python microservice is offline or times out (>800ms)
                            $errorMessage = $this->l('Métricas automáticas indisponíveis (Serviço local offline). Checklist registrado manualmente.');
                        }
                    }
                }
            }
        }

        $this->context->smarty->assign([
            'inspectionResult' => $metricsData,
            'inspectionError'  => $errorMessage,
            'previewImage'     => $previewImageBase64,
            'checklistSummary' => $checklistSummary,
            'selectedRoomId'   => $selectedRoomId,
            'roomsList'        => $this->getHotelRoomsList(),
        ]);

        $this->setTemplate('inspection_form.tpl');
    }

    /**
     * Retrieve active hotel rooms from database or fallback list
     *
     * @return array
     */
    protected function getHotelRoomsList()
    {
        $rooms = [];

        try {
            if (class_exists('Db')) {
                $sql = 'SELECT r.`id` AS id_room, r.`room_num`, r.`id_status`, r.`floor`
                        FROM `' . _DB_PREFIX_ . 'htl_room_information` r
                        ORDER BY r.`room_num` ASC LIMIT 50';
                $dbRooms = Db::getInstance()->executeS($sql);

                if (!empty($dbRooms)) {
                    foreach ($dbRooms as $row) {
                        $rooms[] = [
                            'id'   => 'room-' . (int) $row['id_room'],
                            'name' => 'Quarto ' . $row['room_num'] . (!empty($row['floor']) ? ' (Andar ' . $row['floor'] . ')' : ''),
                        ];
                    }
                }
            }
        } catch (Exception $e) {
            // Log or ignore if table is empty in testing sandbox
        }

        if (empty($rooms)) {
            $rooms = [
                ['id' => 'room-101', 'name' => 'Quarto 101 (Standard)'],
                ['id' => 'room-102', 'name' => 'Quarto 102 (Deluxe)'],
                ['id' => 'room-201', 'name' => 'Quarto 201 (Suíte Presidencial)'],
                ['id' => 'room-202', 'name' => 'Quarto 202 (Executivo)'],
            ];
        }

        return $rooms;
    }
}
