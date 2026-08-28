# RFC-002 — Inspeção Visual e Métricas Objetivas de Imagem

## 1. Identificação e Informações do Projeto

- **Código da Feature:** `QLO-FEAT-002`
- **Nome da Feature:** Inspeção Visual e Avaliação Objetiva de Qualidade de Imagens de Governança
- **Engenharia Responsável:** Engenharia de Visão Computacional & Processamento de Mídia
- **Stack do Desenvolvedor:** Python 3.10+ (processamento de imagem/algoritmos com Pillow)
- **Stack de Apresentação:** Módulo QloApps (`qlovisualinspection`, PHP 8.1+ / Smarty 3.x / HTML / CSS)
- **Porta de Execução Local:** `http://127.0.0.1:8102`
- **Prazo de Execução:** Milestone de 2 Semanas (10 dias úteis de sprint focado)

---

## 2. Contexto de Negócio e Motivação Operacional

Em operações hoteleiras, a equipe de governança (*housekeeping*) é responsável por inspecionar os quartos após a limpeza e antes da liberação para novos check-ins. Para garantir o padrão de qualidade, os supervisores registram fotos dos cômodos (cama arrumada, banheiro higienizado, reposição de amenities).

No entanto, frequentemente as fotos enviadas por funcionários são tiradas com a luz apagada (subexpostas), com a câmera em movimento (desfocadas/tremidas) ou em resolução insuficiente para identificar detalhes. Isso inutiliza a evidência visual para resolução de contestações de hóspedes.

O **Avaliador de Qualidade de Imagem** resolve esse problema executando um pipeline local de processamento determinístico que analisa a imagem no momento do envio, mede a resolução, o nível de luminância média e a nitidez (variância de gradiente/Laplaciano), emitindo um veredito objetivo (`EVIDENCE_VALID` ou `EVIDENCE_REQUIRES_RETAKE`) e orientando o funcionário a refazer a foto imediatamente se a qualidade for inadequada.

---

## 3. Histórias de Usuário e Personas

### Personas

- **Mariana (Supervisora de Governança):** Inspeciona dezenas de quartos por turno e precisa de confirmação imediata de que as fotos tiradas pela equipe atendem aos requisitos de nitidez e iluminação.
- **Fernando (Gerente Geral):** Precisa auditar relatórios de inspeção passados com métricas técnicas comprovadas para respaldar a qualidade dos serviços do hotel.

### Histórias de Usuário

1. **Inspeção de Quarto com Validação Imediata:**
   - *Como* supervisora de governança,
   - *Quero* selecionar o quarto, preencher o checklist de 3 itens e fazer upload da foto do quarto limpo,
   - *Para que* o sistema me informe imediatamente se a foto está clara e nítida o suficiente para ser arquivada como evidência válida.
2. **Alerta de Foto Inadequada:**
   - *Como* supervisora inspecionando um quarto com lâmpada queimada ou pressa,
   - *Quero* receber um aviso explícito de *"Foto muito escura"* ou *"Foto desfocada"*,
   - *Para que* eu possa corrigir o problema e tirar outra foto antes de sair do quarto.
3. **Consulta de Histórico de Auditoria:**
   - *Como* gerente geral,
   - *Quero* abrir o relatório de uma inspeção realizada e ver a foto acompanhada de suas métricas numéricas de luminância e nitidez.

---

## 4. Escopo Operacional e Limites da Entrega

### No Escopo (In-Scope para o MVP)

- Módulo QloApps (`qlovisualinspection`) com aba dedicada no menu de administração do hotel.
- Interface administrativa contendo seleção de quarto, checklist de 3 itens essenciais (cama, banheiro, amenidades) e campo de upload de imagem (JPEG/PNG).
- Serviço local em Python escutando em `127.0.0.1:8102` com endpoint `POST /v1/visual-inspections` aceitando `multipart/form-data`.
- Algoritmo de verificação de resolução mínima (largura $\ge 800$ e altura $\ge 600$).
- Algoritmo de luminância média em escala de cinza ($[0..255]$) com categorização (`UNDEREXPOSED`, `OPTIMAL`, `OVEREXPOSED`).
- Algoritmo de detecção de desfoque via filtro Laplaciano/convolução de gradiente com pontuação de nitidez (`BLURRY`, `SHARP`).
- Renderização visual da foto inspecionada ao lado dos badges de qualidade e do checklist preenchido.
- Tratamento de timeout de 600ms e contingência caso o serviço Python esteja inativo.

### Fora do Escopo (Out-of-Scope / Versão 2)

- Redes neurais pesadas de detecção de objetos (YOLO, Faster-RCNN) ou segmentação semântica.
- Reconhecimento facial ou identificação biométrica de pessoas na imagem.
- Upload de arquivos para provedores de nuvem externa (AWS S3, Google Cloud Storage).
- Câmera ao vivo em streaming WebRTC.

---

## 5. Mapa de Entidades, Ciclo de Vida e Estados

### 5.1. Mapeamento de Tabelas Relacionais do QloApps (MySQL)
A inspeção visual apoia os seguintes fluxos e tabelas de governança:
- `ps_htl_room_information`: Cadastro de quartos físicos (`id_room`, `room_num`, `id_status`), onde o status é atualizado para disponível ou em limpeza.
- `ps_image` e `ps_image_shop`: Armazenamento de metadados das fotos oficiais das acomodações.

### Diagrama de Estados da Inspeção

```text
  [Upload da Imagem]
          │
          ▼
  [Validação de Formato e MIME] ──(Arquivo Inválido)──► [HTTP 400 Bad Request]
          │
          ▼
  [Análise de Métricas Técnicas]
     ├── Resolução < 800x600, Luminância < 40 ou Nitidez < 50
     │        │
     │        ▼
     │   [Veredito: EVIDENCE_REQUIRES_RETAKE (Warnings Emitidos)]
     │
     └── Resolução >= 800x600, Luminância [40..220], Nitidez >= 50
              │
              ▼
         [Veredito: EVIDENCE_VALID (Selo Verde de Aprovação)]
```

---

## 6. Topologia de Comunicação e Fluxo de Dados Ponta a Ponta

```text
[Navegador da Supervisora]
         │
         │ 1. Preenche checklist, seleciona quarto e anexa foto JPEG/PNG
         ▼
[QloApps Back-Office: Módulo qlovisualinspection (PHP/Smarty)]
         │
         │ 2. Valida extensão/tamanho (< 5MB) e encaminha arquivo
         ▼ (HTTP POST multipart/form-data em loopback, timeout 800ms)
[Serviço Local Python: http://127.0.0.1:8102/v1/visual-inspections]
   ├── 3.1. Decodificador de Imagem e Validador de Formato (Pillow)
   ├── 3.2. Verificador de Resolução Mínima (Width x Height)
   ├── 3.3. Calculador de Luminância Média (Grayscale Mean)
   └── 3.4. Detector de Nitidez (Variância Laplaciana / Gradiente)
         │
         │ 3.5. Retorna JSON com Métricas e Veredito (VALID / RETAKE)
         ▼
[QloApps Módulo PHP]
         │
         │ 4. Renderiza foto com badges técnicos e salva registro de inspeção
         ▼
[Navegador da Supervisora]
```

### Estrutura de Diretórios Recomendada

```text
projeto/
├── qlovisualinspection/               # Módulo PHP para QloApps
│   ├── qlovisualinspection.php        # Registro do módulo e menus
│   ├── config.xml                     # Metadados
│   ├── controllers/
│   │   └── admin/
│   │       └── AdminVisualInspectionController.php # Controller administrativo
│   └── views/
│       └── templates/
│           └── admin/
│               └── inspection_form.tpl # View Smarty com formulário e cards
│
└── inspection-service-python/         # Serviço Local Python
    ├── requirements.txt               # fastapi, uvicorn, pillow, pytest
    ├── app/
    │   ├── __init__.py
    │   ├── main.py                    # Servidor FastAPI e rota HTTP
    │   └── analyzer.py                # Lógica pura de luminância e nitidez
    └── tests/
        ├── fixtures/                  # Imagens de teste (sharp, dark, blurry)
        └── test_analyzer.py           # Testes unitários com pytest
```

---

## 7. Regras de Negócio Detalhadas e Tabela de Casos de Borda

| ID | Regra de Negócio | Condição de Entrada | Comportamento Esperado | Caso de Borda / Tratamento |
| --- | --- | --- | --- | --- |
| **RN-001** | **Formato e Tamanho de Imagem** | Arquivo anexado não é JPEG ou PNG, ou tamanho $> 5\text{ MB}$ | Rejeitar no PHP com erro amigável sem enviar ao serviço Python. | Arquivo corrompido com extensão válida deve ser rejeitado no Python com HTTP `400`. |
| **RN-002** | **Resolução Mínima** | Imagem com largura $< 800\text{ px}$ ou altura $< 600\text{ px}$ | Adicionar aviso `LOW_RESOLUTION` e marcar `assessment = "EVIDENCE_REQUIRES_RETAKE"`. | Imagens verticais (600x800) devem ser aceitas normalmente. |
| **RN-003** | **Classificação de Luminância (Muito Escura)** | Brilho médio $< 40$ (escala 0-255) | Classificar como `UNDEREXPOSED`, emitir aviso `UNDEREXPOSED` e sugerir acender as luzes. | Evitar falso positivo em quartos com cabeceira preta analisando a média global. |
| **RN-004** | **Classificação de Luminância (Estourada)** | Brilho médio $> 220$ | Classificar como `OVEREXPOSED`, emitir aviso `OVEREXPOSED` e sugerir fechar cortinas. | Luz solar direta na lente. |
| **RN-005** | **Classificação de Nitidez (Desfocada/Tremida)** | Pontuação de nitidez (variância de bordas) $< 50.0$ | Classificar como `BLURRY`, emitir aviso `BLURRY_IMAGE` e veredito `EVIDENCE_REQUIRES_RETAKE`. | Superfícies lisas e uniformes têm menor nitidez natural; considerar limiar calibrado. |
| **RN-006** | **Aprovação de Evidência Válida** | Imagem atende resolução $\ge 800\text{x}600$, brilho entre 40 e 220, e nitidez $\ge 50.0$ | Retornar `assessment = "EVIDENCE_VALID"` e lista de avisos vazia (`[]`). | Habilitar conclusão da inspeção no QloApps. |

---

## 8. Especificação Completa do Contrato de API (OpenAPI / RFC 7807)

### Endpoint Local

- **URL:** `http://127.0.0.1:8102/v1/visual-inspections`
- **Método:** `POST`
- **Content-Type:** `multipart/form-data`
- **Headers Obrigatórios:** `X-Correlation-ID: <uuid-v4>`

### Parâmetros de Entrada (Form Data)

| Campo | Tipo | Obrigatório | Descrição |
| --- | --- | --- | --- |
| `file` | Binary (File) | Sim | Arquivo binário da foto inspecionada (JPEG ou PNG). |
| `room_id` | String | Sim | Identificador do quarto no QloApps (ex.: `"room-101"`). |
| `inspection_id` | String | Sim | Identificador único da inspeção (ex.: `"INSP-2026-08-27-01"`). |

### Exemplo de Resposta com Imagem Aprovada (Response: 200 OK)

```json
{
  "correlation_id": "f47ac10b-58cc-4372-a567-0e02b2c3d479",
  "inspection_id": "INSP-2026-08-27-01",
  "room_id": "room-101",
  "metrics": {
    "width": 1920,
    "height": 1080,
    "luminance": 118.5,
    "luminance_status": "OPTIMAL",
    "sharpness_score": 145.2,
    "sharpness_status": "SHARP"
  },
  "warnings": [],
  "assessment": "EVIDENCE_VALID"
}
```

### Exemplo de Resposta com Imagem Reprovada (Response: 200 OK com avisos)

```json
{
  "correlation_id": "f47ac10b-58cc-4372-a567-0e02b2c3d479",
  "inspection_id": "INSP-2026-08-27-02",
  "room_id": "room-102",
  "metrics": {
    "width": 640,
    "height": 480,
    "luminance": 24.2,
    "luminance_status": "UNDEREXPOSED",
    "sharpness_score": 18.7,
    "sharpness_status": "BLURRY"
  },
  "warnings": ["LOW_RESOLUTION", "UNDEREXPOSED", "BLURRY_IMAGE"],
  "assessment": "EVIDENCE_REQUIRES_RETAKE"
}
```

### Schema de Erro Estruturado (RFC 7807 — 400 Bad Request)

```json
{
  "type": "https://hotel.local/errors/invalid-image",
  "title": "Arquivo de Imagem Inválido",
  "status": 400,
  "detail": "O arquivo enviado não pôde ser decodificado como uma imagem válida.",
  "instance": "/v1/visual-inspections"
}
```

### Matriz de Códigos HTTP

| Código | Nome | Condição | Ação do QloApps |
| --- | --- | --- | --- |
| `200` | OK | Imagem analisada e métricas calculadas. | Exibe foto e badges coloridos de conformidade. |
| `400` | Bad Request | Arquivo corrompido ou parâmetros ausentes. | Exibe mensagem de erro solicitando novo upload. |
| `503` | Service Unavailable | Serviço Python offline na porta 8102. | Salva o checklist com nota de métricas indisponíveis. |

---

## 9. Massa de Dados de Teste e Cenários de Fixture

```python
# inspection-service-python/tests/generate_fixtures.py
from PIL import Image, ImageDraw, ImageFilter

# 1. Imagem Nítida e Clara (1920x1080)
sharp_img = Image.new("RGB", (1920, 1080), color=(128, 128, 128))
draw = ImageDraw.Draw(sharp_img)
for i in range(0, 1920, 40):
    draw.line([(i, 0), (i, 1080)], fill=(255, 255, 255), width=4)
sharp_img.save("tests/fixtures/room_sharp.jpg", "JPEG", quality=90)

# 2. Imagem Escura (1920x1080)
dark_img = Image.new("RGB", (1920, 1080), color=(15, 15, 20))
dark_img.save("tests/fixtures/room_dark.jpg", "JPEG", quality=90)

# 3. Imagem Desfocada (640x480 com Blur)
blurry_img = sharp_img.resize((640, 480)).filter(ImageFilter.GaussianBlur(radius=15))
blurry_img.save("tests/fixtures/room_blurry.jpg", "JPEG", quality=60)
```

---

## 10. Critérios de Aceitação em BDD (Gherkin: Given-When-Then)

```gherkin
Feature: Análise Objetiva de Qualidade de Imagens de Inspeção

  Scenario: Imagem nítida e bem iluminada é aprovada com sucesso
    Given que o serviço Python está ativo em "http://127.0.0.1:8102"
    When o supervisor envia a foto "room_sharp.jpg" para o quarto "room-101"
    Then o status HTTP da resposta deve ser 200
    And o campo "assessment" deve ser "EVIDENCE_VALID"
    And o campo "metrics.luminance_status" deve ser "OPTIMAL"
    And o campo "metrics.sharpness_status" deve ser "SHARP"
    And a lista de "warnings" deve estar vazia

  Scenario: Imagem escura gera alerta de subexposição
    Given que o serviço Python está ativo
    When o supervisor envia a foto "room_dark.jpg"
    Then o status HTTP deve ser 200
    And o campo "metrics.luminance_status" deve ser "UNDEREXPOSED"
    And o array "warnings" deve conter "UNDEREXPOSED"
    And o campo "assessment" deve ser "EVIDENCE_REQUIRES_RETAKE"

  Scenario: Upload de arquivo não suportado
    Given que o serviço Python está ativo
    When um arquivo com conteúdo de texto simples é enviado como imagem
    Then o status HTTP retornado deve ser 400
    And o corpo da resposta deve conter o erro "INVALID_IMAGE"

  Scenario: Queda do serviço local não interrompe o QloApps
    Given que o serviço Python na porta 8102 está finalizado
    When o supervisor submete a inspeção no back-office
    Then o módulo PHP captura o timeout em no máximo 800ms
    And a tela salva os itens do checklist com o aviso de métricas indisponíveis
```

---

## 11. Guia de Integração com o QloApps (Módulo PHP / Smarty)

### Classe Principal do Módulo (`qlovisualinspection.php`)
```php
<?php
// modules/qlovisualinspection/qlovisualinspection.php

if (!defined('_PS_VERSION_')) {
    exit;
}

class QloVisualInspection extends Module
{
    public function __construct()
    {
        $this->name = 'qlovisualinspection';
        $this->tab = 'hotel_reservation';
        $this->version = '1.0.0';
        $this->author = 'QloApps Engineering';
        $this->need_instance = 0;
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Inspeção Visual de Quartos');
        $this->description = $this->l('Métricas objetivas de luminância e nitidez para governança.');
    }

    public function install()
    {
        return parent::install() && $this->installTab();
    }

    public function uninstall()
    {
        return $this->uninstallTab() && parent::uninstall();
    }

    private function installTab()
    {
        $tab = new Tab();
        $tab->active = 1;
        $tab->class_name = 'AdminVisualInspection';
        $tab->name = array();
        foreach (Language::getLanguages(true) as $lang) {
            $tab->name[$lang['id_lang']] = 'Inspeção de Quartos';
        }
        $tab->id_parent = (int) Tab::getIdFromClassName('AdminParentOrders');
        $tab->module = $this->name;
        return $tab->add();
    }

    private function uninstallTab()
    {
        $idTab = (int) Tab::getIdFromClassName('AdminVisualInspection');
        if ($idTab) {
            $tab = new Tab($idTab);
            return $tab->delete();
        }
        return true;
    }
}
```

### Controller PHP (`AdminVisualInspectionController.php`)

```php
<?php
// modules/qlovisualinspection/controllers/admin/AdminVisualInspectionController.php

class AdminVisualInspectionController extends ModuleAdminController
{
    public function __construct()
    {
        $this->bootstrap = true;
        parent::__construct();
    }

    public function initContent()
    {
        parent::initContent();

        $metricsData = null;
        $errorMessage = null;

        if (Tools::isSubmit('submitInspection')) {
            $roomId       = Tools::getValue('room_id');
            $inspectionId = 'INSP-' . date('YmdHis') . '-' . $roomId;
            $corrId       = Tools::passwdGen(16, 'ALPHANUMERIC');

            if (!isset($_FILES['inspection_photo']) || $_FILES['inspection_photo']['error'] !== UPLOAD_ERR_OK) {
                $errorMessage = 'Por favor, selecione uma foto válida antes de submeter.';
            } else {
                $tmpFilePath = $_FILES['inspection_photo']['tmp_name'];
                $mimeType    = mime_content_type($tmpFilePath);
                $cFile       = new CURLFile($tmpFilePath, $mimeType, $_FILES['inspection_photo']['name']);

                $postData = [
                    'file'          => $cFile,
                    'room_id'       => $roomId,
                    'inspection_id' => $inspectionId
                ];

                $ch = curl_init('http://127.0.0.1:8102/v1/visual-inspections');
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
                curl_setopt($ch, CURLOPT_TIMEOUT_MS, 800);
                curl_setopt($ch, CURLOPT_HTTPHEADER, [
                    'X-Correlation-ID: ' . $corrId
                ]);

                $response = curl_exec($ch);
                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);

                if ($response && $httpCode === 200) {
                    $metricsData = json_decode($response, true);
                } else {
                    $errorMessage = 'Métricas automáticas indisponíveis (Serviço local offline). Checklist registrado manualmente.';
                }
            }
        }

        $this->context->smarty->assign([
            'inspectionResult' => $metricsData,
            'inspectionError'  => $errorMessage,
            'roomsList'        => [
                ['id' => 'room-101', 'name' => 'Quarto 101 (Standard)'],
                ['id' => 'room-102', 'name' => 'Quarto 102 (Deluxe)'],
                ['id' => 'room-201', 'name' => 'Quarto 201 (Suíte)']
            ]
        ]);

        $this->setTemplate('inspection_form.tpl');
    }
}
```

### Template Smarty (`inspection_form.tpl`)

```html
<div class="panel">
    <div class="panel-heading">
        <i class="icon-camera"></i> Inspeção Visual e Checklist de Governança
    </div>

    {if $inspectionError}
        <div class="alert alert-warning">
            <i class="icon-warning-sign"></i> {$inspectionError}
        </div>
    {/if}

    <form method="post" action="" enctype="multipart/form-data" class="form-horizontal">
        <div class="form-group">
            <label class="control-label col-lg-3">Quarto Inspecionado:</label>
            <div class="col-lg-4">
                <select name="room_id" class="form-control">
                    {foreach from=$roomsList item=room}
                        <option value="{$room.id}">{$room.name}</option>
                    {/foreach}
                </select>
            </div>
        </div>

        <div class="form-group">
            <label class="control-label col-lg-3">Itens do Checklist:</label>
            <div class="col-lg-7">
                <div class="checkbox"><label><input type="checkbox" name="chk_bed" checked /> Cama arrumada e enxoval trocado</label></div>
                <div class="checkbox"><label><input type="checkbox" name="chk_bath" checked /> Banheiro higienizado</label></div>
                <div class="checkbox"><label><input type="checkbox" name="chk_amenities" checked /> Amenities repostos</label></div>
            </div>
        </div>

        <div class="form-group">
            <label class="control-label col-lg-3">Foto de Evidência (JPEG/PNG):</label>
            <div class="col-lg-4">
                <input type="file" name="inspection_photo" accept="image/jpeg,image/png" required />
            </div>
            <div class="col-lg-2">
                <button type="submit" name="submitInspection" class="btn btn-primary btn-block">
                    <i class="icon-upload"></i> Avaliar e Salvar
                </button>
            </div>
        </div>
    </form>

    {if $inspectionResult}
        <hr />
        <div class="well">
            <h4><i class="icon-bar-chart"></i> Avaliação da Evidência Fotográfica:</h4>
            <p><strong>Veredito:</strong> 
                {if $inspectionResult.assessment == 'EVIDENCE_VALID'}
                    <span class="label label-success">EVIDÊNCIA VÁLIDA</span>
                {else}
                    <span class="label label-danger">REFAZER FOTO</span>
                {/if}
            </p>
            <p><strong>Resolução:</strong> {$inspectionResult.metrics.width}x{$inspectionResult.metrics.height} px</p>
            <p><strong>Luminância (Brilho):</strong> {$inspectionResult.metrics.luminance|string_format:"%.1f"} ({$inspectionResult.metrics.luminance_status})</p>
            <p><strong>Nitidez (Foco):</strong> {$inspectionResult.metrics.sharpness_score|string_format:"%.1f"} ({$inspectionResult.metrics.sharpness_status})</p>
            
            {if $inspectionResult.warnings|@count > 0}
                <div class="alert alert-danger">
                    <strong>Avisos:</strong>
                    <ul>
                        {foreach from=$inspectionResult.warnings item=warn}
                            <li>{$warn}</li>
                        {/foreach}
                    </ul>
                </div>
            {/if}
        </div>
    {/if}
</div>
```

---

### 11.4. Código Inicial Standalone do Serviço Python (`main.py`)
```python
# inspection-service-python/app/main.py
import io
from fastapi import FastAPI, UploadFile, File, Form, Header, HTTPException
from PIL import Image, ImageStat, ImageFilter
import uvicorn

app = FastAPI(title="Visual Inspection Service")

@app.get("/healthz")
def health_check():
    return {"status": "UP"}

@app.post("/v1/visual-inspections")
async def analyze_inspection(
    file: UploadFile = File(...),
    room_id: str = Form(...),
    inspection_id: str = Form(...),
    x_correlation_id: str = Header(default="corr-local-demo")
):
    try:
        contents = await file.read()
        img = Image.open(io.BytesIO(contents))
        width, height = img.size
        
        gray_img = img.convert("L")
        stat = ImageStat.Stat(gray_img)
        luminance = stat.mean[0]
        
        edges = gray_img.filter(ImageFilter.FIND_EDGES)
        edge_stat = ImageStat.Stat(edges)
        sharpness_score = edge_stat.var[0] if edge_stat.var else 0.0
        
        warnings = []
        if width < 800 or height < 600:
            warnings.append("LOW_RESOLUTION")
        if luminance < 40:
            warnings.append("UNDEREXPOSED")
        elif luminance > 220:
            warnings.append("OVEREXPOSED")
        if sharpness_score < 50.0:
            warnings.append("BLURRY_IMAGE")
            
        assessment = "EVIDENCE_REQUIRES_RETAKE" if warnings else "EVIDENCE_VALID"
        
        return {
            "correlation_id": x_correlation_id,
            "inspection_id": inspection_id,
            "room_id": room_id,
            "metrics": {
                "width": width,
                "height": height,
                "luminance": round(luminance, 2),
                "luminance_status": "UNDEREXPOSED" if luminance < 40 else ("OVEREXPOSED" if luminance > 220 else "OPTIMAL"),
                "sharpness_score": round(sharpness_score, 2),
                "sharpness_status": "BLURRY" if sharpness_score < 50.0 else "SHARP"
            },
            "warnings": warnings,
            "assessment": assessment
        }
    except Exception as e:
        raise HTTPException(status_code=400, detail=f"Erro ao processar imagem: {str(e)}")

if __name__ == "__main__":
    uvicorn.run(app, host="127.0.0.1", port=8102)
```

## 12. Observabilidade, Logs Estruturados e SLAs Operacionais

### Níveis de Serviço (SLAs / SLOs)

- **Latência P95:** $< 150\text{ ms}$ para processamento e cálculo de imagem em Python.
- **Latência Total Ponta a Ponta (Upload + cURL + Python):** $< 350\text{ ms}$.
- **Timeout Máximo do Cliente:** $800\text{ ms}$.

### Formato de Log Estruturado (Stdout do Serviço Python)

```json
{
  "timestamp": "2026-08-27T10:20:15.312Z",
  "level": "INFO",
  "correlation_id": "f47ac10b-58cc-4372-a567-0e02b2c3d479",
  "event": "IMAGE_INSPECTED",
  "room_id": "room-101",
  "width": 1920,
  "height": 1080,
  "luminance": 118.5,
  "sharpness": 145.2,
  "assessment": "EVIDENCE_VALID",
  "duration_ms": 42.1
}
```

---

## 13. Matriz de Riscos, Segurança e Privacidade

| Ameaça Identificada | Impacto | Nível | Controle Técnico Aplicado |
| --- | --- | --- | --- |
| **Bomba de Descompressão de Imagem** | Esgotamento de memória RAM | Crítico | Configuração estrita de `Image.MAX_IMAGE_PIXELS = 10_000_000` no Pillow. |
| **Upload de Executável Disfarçado** | Comprometimento do servidor | Crítico | PHP valida extensão e MIME real com `mime_content_type()`; Python valida com `Image.open().verify()`. |
| **Exposição da Porta na Rede Local** | Acesso não autenticado | Alto | Uvicorn/Flask configurado explicitamente para escutar apenas em `127.0.0.1:8102`. |
| **Persistência Excessiva de Fotos Temporárias** | Esgotamento de disco | Médio | Imagens temporárias de upload excluídas automaticamente após o processamento. |

---

## 14. Guia de Diagnóstico e Resolução de Problemas (Troubleshooting FAQ)

### FAQ Técnico

1. **Erro: `cURL error 7: Failed to connect to 127.0.0.1 port 8102`**
   - *Causa:* O servidor Python FastAPI/Flask não está ativo.
   - *Solução:* Inicie o servidor com `uvicorn app.main:app --host 127.0.0.1 --port 8102`.
2. **Erro: `400 Bad Request` com mensagem `Arquivo corrompido`**
   - *Causa:* O arquivo enviado não é uma imagem JPEG/PNG válida ou foi corrompido durante o upload.
   - *Solução:* Teste o envio usando as imagens de fixture geradas pelo script `generate_fixtures.py`.
3. **Fotos claras classificadas indevidamente como escuras**
   - *Causa:* Imagem com fundo predominantemente escuro (ex.: carpete e móveis escuros).
   - *Solução:* Calibre o limiar de luminância em `analyzer.py` ajustando o valor de corte de 40 para 35.

---

## 15. Plano de Execução Diário (Cronograma de 10 Dias Úteis)

### Semana 1: Núcleo de Processamento de Imagem em Python (10h)

- **Dia 1 (2h):** Configuração do ambiente virtual Python, dependências (`fastapi`, `pillow`, `pytest`) e script de geração de fixtures.
- **Dia 2 (2h):** Implementação da função de cálculo de luminância média em escala de cinza com validações numéricas.
- **Dia 3 (2h):** Implementação do cálculo de variância do Laplaciano/gradiente de nitidez e categorização (`SHARP`/`BLURRY`).
- **Dia 4 (2h):** Criação do servidor FastAPI na porta 8102 com rota `POST /v1/visual-inspections` multipart.
- **Dia 5 (2h):** Bateria de testes unitários com pytest cobrindo as 3 fixtures geradas e casos de erro 400.

### Semana 2: Módulo QloApps e Interface de Inspeção (10h)

- **Dia 6 (2h):** Criação da estrutura de pastas do módulo `qlovisualinspection` e registro do menu administrativo.
- **Dia 7 (2h):** Desenvolvimento da view Smarty com seleção de quartos, checklist de 3 itens e campo de upload.
- **Dia 8 (2h):** Implementação do envio cURL multipart no PHP com cabeçalho de correlação e timeout de 800ms.
- **Dia 9 (2h):** Renderização dos badges de qualidade (Luminância / Nitidez) ao lado da foto inspecionada.
- **Dia 10 (2h):** Validação dos 4 cenários BDD, teste de contingência (serviço offline) e gravação de demonstração.

---

### 15.1. Quickstart de 1 Linha (Execução & Teste cURL)
```bash
# 1. Iniciar o serviço Python:
uvicorn app.main:app --host 127.0.0.1 --port 8102

# 2. Em outro terminal, enviar uma imagem de teste via cURL multipart:
curl -s -X POST http://127.0.0.1:8102/v1/visual-inspections   -H "X-Correlation-ID: test-img-001"   -F "file=@tests/fixtures/room_sharp.jpg"   -F "room_id=101"   -F "inspection_id=INSP-01" | jq .
```

## 16. Definição de Pronto (Definition of Done — DoD Checklist)

- [ ] Serviço Python inicia e responde em `http://127.0.0.1:8102/healthz`.
- [ ] Testes automatizados do pytest cobrem as 3 imagens de fixture com 100% de sucesso.
- [ ] Módulo QloApps instala sem erros e adiciona a aba de inspeção no back-office.
- [ ] Upload de imagem válida exibe os indicadores técnicos calculados pelo Python em menos de 1 segundo.
- [ ] QloApps exibe mensagem de contingência adequada caso o serviço Python esteja desligado.
- [ ] Documentação de compilação e fixtures testadas com sucesso.