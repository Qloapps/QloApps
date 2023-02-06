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

<div class="modal-body text-center">
    <div class="row">
        <div class="col-lg-12">
            <div class="alert alert-danger text-left errors-wrap" style="display: none;"></div>
            <form class="defaultForm form-horizontal booking-documents" action="#" method="post" enctype="multipart/form-data" novalidate>
                <input type="hidden" name="id_htl_booking" value="0" id="booking-document-id-htl-booking">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th class="text-left">{l s='ID'}</th>
                            <th class="text-center">{l s='Download/Preview'}</th>
                            <th class="text-center">{l s='Actions'}</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>

                <div class="text-left add-new-wrap">
                    <a class="btn btn-default btn-add-new-document">
                        <i class="icon icon-plus"></i>
                        {l s='Add new'}
                    </a>
                    <span></span>
                </div>
                <div class="input-file-wrap"></div>
            </form>
        </div>
    </div>
</div>

{addJsDefL name=txt_booking_document_upload_success}{l s='Document uploaded successfully.' js=1}{/addJsDefL}
{addJsDefL name=txt_booking_document_delete_success}{l s='Document deleted successfully.' js=1}{/addJsDefL}
