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
            <div class="documents-list">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th class="text-left">{l s='Title'}</th>
                            <th class="text-center">{l s='Preview'}</th>
                            <th class="text-center">{l s='Upload Date'}</th>
                            <th class="text-center">{l s='Actions'}</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
            <div class="add-new-document-form">
                <div class="text-left add-new-wrap">
                    <a class="btn btn-default btn-add-new-document">
                        <i class="icon icon-cloud-upload"></i>
                        {l s='Upload new document'}
                    </a>
                    <span></span>
                </div>
                <form id="form-add-new-document" class="well" method="post" action="#" style="display: none; margin-top: 5px;">

                    <input type="hidden" name="id_htl_booking" value="0">

                    <div class="form-horizontal">
                        <div class="form-group">
                            <label class="control-label col-sm-2">{l s='Title'}</label>
                            <div class="col-sm-10">
                                <div class="input-group fixed-width-xxl">
                                    <input type="text" name="title" value="" placeholder="{l s='Eg. Passport, Driver\'s license'}" />
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label required col-sm-2">{l s='File'}</label>
                            <div class="col-sm-10">
                                <div class="input-file-wrap"></div>
                                <div class="dummyfile input-group fixed-width-xxl">
                                    <span class="input-group-addon"><i class="icon-file"></i></span>
                                    <input type="text" class="file-name" readonly="">
                                    <span class="input-group-btn">
                                        <button type="button" class="btn btn-default btn-add-file">
                                            <i class="icon-folder-open"></i>
                                            {l s='Add file'}
                                        </button>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="clearfix btn-group-add-new">
                            <button class="btn btn-primary pull-right upload" type="submit" name="submitAddPayment">
                                {l s='Upload'}
                            </button>
                            <button class="btn btn-default pull-left cancel">
                                {l s='Cancel'}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{addJsDefL name=txt_booking_document_upload_success}{l s='Document uploaded successfully.' js=1}{/addJsDefL}
{addJsDefL name=txt_booking_document_delete_success}{l s='Document deleted successfully.' js=1}{/addJsDefL}
