{**
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License version 3.0
* that is bundled with this package in the file LICENSE.md
* It is also available through the world-wide-web at this URL:
* https://opensource.org/license/osl-3-0-php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to support@qloapps.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to a newer
* versions in the future. If you wish to customize this module for your needs
* please refer to https://store.webkul.com/customisation-guidelines for more information.
*
* @author Webkul IN
* @copyright Since 2010 Webkul
* @license https://opensource.org/license/osl-3-0-php Open Software License version 3.0
*}

{if is_array($booking_documents) && count($booking_documents)}
    {foreach from=$booking_documents item=booking_document}
        <tr>
            <td class="text-center">
                <a href="{$link->getAdminLink('AdminBookingDocument')}&action=getDocument&id_document={$booking_document.id_htl_booking_document}&is_preview=1" target="_blank">
                    {if $booking_document.file_type == HotelBookingDocument::FILE_TYPE_IMAGE}
                        <img class="img img-responsive img-thumbnail" src="{$link->getAdminLink('AdminBookingDocument')}&action=getDocument&id_document={$booking_document.id_htl_booking_document}&is_preview=1">
                    {elseif $booking_document.file_type == HotelBookingDocument::FILE_TYPE_PDF}
                        <img class="img img-responsive img-thumbnail" src="{$pdf_icon_link}">
                    {/if}
                </a>
            </td>
            <td class="text-left">{$booking_document.title}</td>
            <td class="text-center">{dateFormat date=$booking_document.date_add full=1}</td>
            <td class="text-center">
                <a class="btn btn-info" href="{$link->getAdminLink('AdminBookingDocument')}&action=getDocument&id_document={$booking_document.id_htl_booking_document}">
                    <i class="icon icon-cloud-download"></i>
                </a>
                {if $can_edit}
                    <a class="btn btn-danger btn-delete-document" data-id-htl-booking-document="{$booking_document.id_htl_booking_document}">
                        <i class="icon icon-trash"></i>
                    </a>
                {/if}
            </td>
        </tr>
    {/foreach}
{else}
    <tr>
        <td class="list-empty" colspan="4">
            <div class="list-empty-msg">
                <i class="icon-warning-sign list-empty-icon"></i>
                {l s='No documents uploaded yet.'}
            </div>
        </td>
    </tr>
{/if}
