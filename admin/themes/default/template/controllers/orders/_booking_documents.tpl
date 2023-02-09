{**
 * 2010-2023 Webkul.
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
 * @copyright 2010-2023 Webkul IN
 * @license LICENSE.txt
 *}

{if is_array($booking_documents) && count($booking_documents)}
    {foreach from=$booking_documents item=booking_document}
        <tr>
            <td class="text-left">{$booking_document.title}</td>
            <td class="text-center">
                <a href="{$link->getAdminLink('AdminBookingDocument')}&action=getDocument&id_document={$booking_document.id_htl_booking_document}&is_preview=1" target="_blank">
                    {if $booking_document.file_type == HotelBookingDocument::FILE_TYPE_IMAGE}
                        <img class="img img-responsive img-thumbnail" src="{$link->getAdminLink('AdminBookingDocument')}&action=getDocument&id_document={$booking_document.id_htl_booking_document}&is_preview=1">
                    {elseif $booking_document.file_type == HotelBookingDocument::FILE_TYPE_PDF}
                        <img class="img img-responsive img-thumbnail" src="{$pdf_icon_link}">
                    {/if}
                </a>
            </td>
            <td class="text-center">{dateFormat date=$booking_document.date_add full=1}</td>
            <td class="text-center">
                <a class="btn btn-info" href="{$link->getAdminLink('AdminBookingDocument')}&action=getDocument&id_document={$booking_document.id_htl_booking_document}">
                    <i class="icon icon-cloud-download"></i>
                </a>
                <a class="btn btn-danger btn-delete-document" data-id-htl-booking-document="{$booking_document.id_htl_booking_document}">
                    <i class="icon icon-trash"></i>
                </a>
            </td>
        </tr>
    {/foreach}
{else}
    <tr>
        <td colspan="4" class="text-center">{l s='No documents.'}</td>
    </tr>
{/if}
