<?php
/**
* 2010-2022 Webkul.
*
* NOTICE OF LICENSE
*
* All right is reserved,
* Please go through LICENSE.txt file inside our module
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to newer
* versions in the future. If you wish to customize this module for your
* needs please refer to CustomizationPolicy.txt file inside our module for more information.
*
* @author Webkul IN
* @copyright 2010-2022 Webkul IN
* @license LICENSE.txt
*/

class QloHotelReviewDefaultModuleFrontController extends ModuleFrontController
{
    public function __construct()
    {
        parent::__construct();
        $this->content_only = true;
    }

    public function checkAccess()
    {
        return strcmp(Tools::getValue('token', ''), $this->module->secure_key) === 0;
    }

    public function displayAjaxAddReview()
    {
        $idHotel = Tools::getValue('id_hotel');
        $idOrder = Tools::getValue('id_order');
        $ratingOverall = Tools::getValue('rating_overall');
        $ratingCategories = Tools::getValue('rating_categories');
        $subject = Tools::getValue('subject');
        $description = Tools::getValue('description');

        $maxImages = (int) Configuration::get('QHR_MAX_IMAGES_PER_REVIEW');

        $status = 'ko';
        $errors = array('by_key' => array(), 'general' => array());
        $objModule = new QloHotelReview();
        $objHotel = new HotelBranchInformation($idHotel);
        $objOrder = new Order($idOrder);

        // Validations
        if (!Validate::isLoadedObject($objHotel)) {
            $errors['general'][] = $objModule->l('Invalid hotel ID.', 'default');
        }

        if (!Validate::isLoadedObject($objOrder)) {
            $errors['general'][] = $objModule->l('Invalid order ID.', 'default');
        }

        if (!$subject) {
            $errors['by_key']['subject'] = $objModule->l('This field can not be empty.', 'default');
        } elseif(!Validate::isGenericName($subject)) {
            $errors['by_key']['subject'] = $objModule->l('This field is invalid.', 'default');
        }

        if (!$description) {
            $errors['by_key']['description'] = $objModule->l('Review description can not be empty.', 'default');
        } elseif(!Validate::isMessage($description)) {
            $errors['by_key']['description'] = $objModule->l('Review description is invalid.', 'default');
        }

        if (is_array($_FILES) && array_key_exists('images', $_FILES)) {
            if (count($_FILES['images']['name']) > $maxImages) {
                $errors['general'][] = sprintf(
                    $objModule->l('Please upload a maximum of %d images.', 'default'),
                    $maxImages
                );
            }
        }

        if (!count($errors['by_key']) && !count($errors['general'])) {
            $customerReview = QhrHotelReview::getByCustomer(
                $objOrder->id_customer,
                $idHotel,
                $idOrder
            );
            if (!$customerReview) {
                $objHotelReview = new QhrHotelReview();
                $objHotelReview->id_hotel = (int) $idHotel;
                $objHotelReview->id_order = (int) $idOrder;
                $objHotelReview->description = strip_tags($description);
                $objHotelReview->subject = $subject;
                $objHotelReview->rating = $ratingOverall;
                $objHotelReview->status_abusive = QhrHotelReview::QHR_STATUS_ABUSIVE_NOT_ABUSIVE;
                $objHotelReview->status = QhrHotelReview::QHR_STATUS_PENDING;
                if ($objHotelReview->save()) {
                    $objHotelReview->addCategoryRatings($ratingCategories);
                    if ($maxImages) {
                        $objHotelReview->saveReviewImages();
                    }
                    $status = 'ok';
                }
            } else {
                $status = 'ko';
                $errors['general'][] = $objModule->l('Something went wrong.', 'default');
            }
        } else {
            $status = 'ko';
        }

        if (is_array($errors['general']) && count($errors['general'])) {
            $html = '<ol>';
            foreach ($errors['general'] as $error) {
                $html .= '<li>'.$error.'</li>';
            }
            $html .= '</ol>';
            $errors['general'] = $html;
        }
        die(json_encode(array('status' => $status, 'errors' => $errors)));
    }

    public function displayAjaxMarkReviewHelpful()
    {
        $response = array('status' => 'ko');
        $idHotelReview = (int) Tools::getValue('id_hotel_review');
        if (!$idHotelReview) {
            die(json_encode($response));
        }

        if (QhrHotelReview::isAlreadyMarkedHelpful($idHotelReview, $this->context->cookie->id_customer)) {
            die(json_encode($response));
        }

        if (QhrHotelReview::markHelpful($idHotelReview, $this->context->cookie->id_customer)) {
            $response['status'] = 'ok';
            die(json_encode($response));
        }
    }

    public function displayAjaxReportAbuse()
    {
        $response = array('status' => 'ko');
        $idHotelReview = (int) Tools::getValue('id_hotel_review');
        if (!$idHotelReview) {
            die(json_encode($response));
        }

        if (QhrHotelReview::isAlreadyReportedAbuse($idHotelReview, $this->context->cookie->id_customer)) {
            die(json_encode($response));
        }

        if (QhrHotelReview::reportAbuse($idHotelReview, $this->context->cookie->id_customer)) {
            $response['status'] = 'ok';
            die(json_encode($response));
        }
    }

    public function displayAjaxGetReviews()
    {
        $response = array('status' => 'ko');
        $idHotel = (int) Tools::getValue('id_hotel');
        $sortBy = (int) Tools::getValue('sort_by');
        $page = (int)Tools::getValue('page');
        $reviewsPerPage = (int) Configuration::get('QHR_REVIEWS_PER_PAGE');

        $reviews = QhrHotelReview::getByHotel(
            $idHotel,
            $page,
            $reviewsPerPage,
            $sortBy,
            $this->context->cookie->id_customer
        );

        $hasNextPage = QhrHotelReview::hasNextPage($idHotel, $page, $reviewsPerPage);

        if (is_array($reviews) && count($reviews)) {
            $response['html'] = $this->renderReviews($reviews);
            $response['status'] = 'ok';
            $response['message'] = 'HTML_OK';
            $response['has_next_page'] = (bool) $hasNextPage;
        }

        die(json_encode($response));
    }

    public function displayAjaxSortBy()
    {
        $response = array('status' => 'ko');
        $idHotel = (int) Tools::getValue('id_hotel');
        $sortBy = (int) Tools::getValue('sort_by');
        $reviewsPerPage = (int) Configuration::get('QHR_REVIEWS_PER_PAGE');

        $reviews = QhrHotelReview::getByHotel(
            $idHotel,
            1,
            $reviewsPerPage,
            $sortBy,
            $this->context->cookie->id_customer
        );

        $hasNextPage = QhrHotelReview::hasNextPage($idHotel, 1, $reviewsPerPage);

        if (is_array($reviews) && count($reviews)) {
            $response['html'] = $this->renderReviews($reviews);
            $response['status'] = 'ok';
            $response['message'] = 'HTML_OK';
            $response['has_next_page'] = (bool) $hasNextPage;
        }

        die(json_encode($response));
    }

    public function renderReviews($reviews)
    {
        if (is_array($reviews) && count($reviews)) {
            foreach ($reviews as &$review) {
                $review['images'] = QhrHotelReview::getImagesById($review['id_hotel_review']);
            }

            $html = '';
            foreach ($reviews as &$review) {
                $this->context->smarty->assign(array('review' => $review));
                $html .= $this->context->smarty->fetch(
                    $this->module->getTemplatePath('_partials/review.tpl')
                );
            }
            return $html;
        }
        return false;
    }
}
