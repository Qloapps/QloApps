{*
* Since 2010 Webkul.
*
* NOTICE OF LICENSE
*
* All right is reserved,
* Please go through this link for complete license : https://store.webkul.com/license.html
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to newer
* versions in the future. If you wish to customize this module for your
* needs please refer to https://store.webkul.com/customisation-guidelines/ for more information.
*
*  @author    Webkul IN <support@webkul.com>
*  @copyright Since 2010 Webkul IN
*  @license   https://store.webkul.com/license.html
*}

<div class="modal-body">
    <div class="row">
        <div class="col-lg-12">
            <div class="text-left errors-wrap"></div>
            <div class="documents-list form-group">
                <table class="table">
                    <thead>
                        <tr>
                            <th class="text-center">{l s='Preview'}</th>
                            <th class="text-left">{l s='Title'}</th>
                            <th class="text-center">{l s='Upload Date'}</th>
                            <th class="text-center">{l s='Actions'}</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
            <hr>
            <div class="well add-new-document-form" style="margin-top: 10px;">
                <form class="form-horizontal" id="form-add-new-document" method="post" action="#">
                    <input type="hidden" name="id_htl_booking" value="0">
                    <div class="form-group">
                        <div class="col-sm-12">
                            <label class="control-label">
                                <span class="label-tooltip" data-toggle="tooltip" title="" data-original-title="{l s='Write the title for the document. Invalid characters <>;=#{}'}">
                                    {l s='Title'}
                                </span>
                            </label>
                            <input class="form-control" type="text" name="title" value="" placeholder="{l s='eg. passport, driving license'}" />
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-12">
                            <label class="control-label required">
                                <span class="label-tooltip" data-toggle="tooltip" title="" data-original-title="{l s='Choose the document file to be uploaded.'}">
                                    {l s='File'}
                                </span>
                            </label>
                            <div class="input-file-wrap"></div>
                            <div class="input-group">
                                <input class="form-control file-name" type="text" readonly="">
                                <span class="input-group-btn">
                                    <button type="button" class="btn btn-primary btn-add-file">
                                        <i class="icon-folder-open"></i>
                                        {l s='Add file'}
                                    </button>
                                </span>
                            </div>
                            <p class="text-left" style="margin-top: 4px; font-style: italic;">
                                {l s='Upload a PDF or an image file. Allowed image formats: .gif, .jpg, .jpeg and .png'}
                            </p>
                        </div>
                    </div>
                    <button class="btn btn-primary pull-right upload" type="submit" name="uploadDocument" id="uploadDocument" style="display:none;">
                        {l s='Upload'}
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
