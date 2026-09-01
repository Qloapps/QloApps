<div class="panel">
    <div class="panel-heading">
        <i class="icon-camera"></i> {l s='Inspeção Visual e Checklist de Governança' mod='qlovisualinspection'}
    </div>

    {if isset($inspectionError) && $inspectionError}
        <div class="alert alert-warning">
            <i class="icon-warning-sign"></i> {$inspectionError|escape:'html':'UTF-8'}
        </div>
    {/if}

    <form method="post" action="" enctype="multipart/form-data" class="form-horizontal">
        <div class="form-group">
            <label class="control-label col-lg-3 required">
                {l s='Quarto Inspecionado:' mod='qlovisualinspection'}
            </label>
            <div class="col-lg-4">
                <select name="room_id" class="form-control" required>
                    {foreach from=$roomsList item=room}
                        <option value="{$room.id|escape:'html':'UTF-8'}" {if isset($selectedRoomId) && $selectedRoomId == $room.id}selected="selected"{/if}>
                            {$room.name|escape:'html':'UTF-8'}
                        </option>
                    {/foreach}
                </select>
            </div>
        </div>

        <div class="form-group">
            <label class="control-label col-lg-3">
                {l s='Itens do Checklist:' mod='qlovisualinspection'}
            </label>
            <div class="col-lg-7">
                <div class="checkbox">
                    <label>
                        <input type="checkbox" name="chk_bed" value="1" {if !isset($checklistSummary) || $checklistSummary.bed}checked="checked"{/if} />
                        <i class="icon-check"></i> {l s='Cama arrumada e enxoval trocado' mod='qlovisualinspection'}
                    </label>
                </div>
                <div class="checkbox">
                    <label>
                        <input type="checkbox" name="chk_bath" value="1" {if !isset($checklistSummary) || $checklistSummary.bath}checked="checked"{/if} />
                        <i class="icon-check"></i> {l s='Banheiro higienizado' mod='qlovisualinspection'}
                    </label>
                </div>
                <div class="checkbox">
                    <label>
                        <input type="checkbox" name="chk_amenities" value="1" {if !isset($checklistSummary) || $checklistSummary.amenities}checked="checked"{/if} />
                        <i class="icon-check"></i> {l s='Amenities repostos' mod='qlovisualinspection'}
                    </label>
                </div>
            </div>
        </div>

        <div class="form-group">
            <label class="control-label col-lg-3 required">
                {l s='Foto de Evidência (JPEG/PNG):' mod='qlovisualinspection'}
            </label>
            <div class="col-lg-4">
                <input type="file" name="inspection_photo" accept="image/jpeg,image/png" class="form-control" required />
                <p class="help-block">{l s='Resolução mínima recomendada: 800x600 px. Tamanho máximo: 5 MB.' mod='qlovisualinspection'}</p>
            </div>
            <div class="col-lg-2">
                <button type="submit" name="submitInspection" class="btn btn-primary btn-block">
                    <i class="icon-upload"></i> {l s='Avaliar e Salvar' mod='qlovisualinspection'}
                </button>
            </div>
        </div>
    </form>

    {if isset($inspectionResult) && $inspectionResult}
        <hr />
        <div class="row">
            {if isset($previewImage) && $previewImage}
                <div class="col-lg-4 text-center">
                    <div class="thumbnail" style="padding: 10px; background: #fff;">
                        <img src="{$previewImage}" alt="Foto de Inspeção" style="max-width: 100%; height: auto; max-height: 280px; border-radius: 4px;" />
                        <p class="text-muted" style="margin-top: 5px;">
                            <small><i class="icon-picture"></i> {l s='Foto Enviada' mod='qlovisualinspection'}</small>
                        </p>
                    </div>
                </div>
            {/if}

            <div class="{if isset($previewImage) && $previewImage}col-lg-8{else}col-lg-12{/if}">
                <div class="well">
                    <h4><i class="icon-bar-chart"></i> {l s='Avaliação da Evidência Fotográfica' mod='qlovisualinspection'}</h4>
                    
                    <p style="font-size: 1.2em; margin-bottom: 15px;">
                        <strong>{l s='Veredito:' mod='qlovisualinspection'}</strong> 
                        {if $inspectionResult.assessment == 'EVIDENCE_VALID'}
                            <span class="label label-success" style="font-size: 1em; padding: 4px 10px;">
                                <i class="icon-check"></i> {l s='EVIDÊNCIA VÁLIDA' mod='qlovisualinspection'}
                            </span>
                        {else}
                            <span class="label label-danger" style="font-size: 1em; padding: 4px 10px;">
                                <i class="icon-remove"></i> {l s='REFAZER FOTO' mod='qlovisualinspection'}
                            </span>
                        {/if}
                    </p>

                    <div class="row">
                        <div class="col-sm-4">
                            <div class="panel panel-default text-center">
                                <div class="panel-body">
                                    <div class="text-muted"><small>{l s='Resolução' mod='qlovisualinspection'}</small></div>
                                    <strong>{$inspectionResult.metrics.width|intval} &times; {$inspectionResult.metrics.height|intval} px</strong>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-4">
                            <div class="panel panel-default text-center">
                                <div class="panel-body">
                                    <div class="text-muted"><small>{l s='Luminância (Brilho)' mod='qlovisualinspection'}</small></div>
                                    <strong>{$inspectionResult.metrics.luminance|string_format:"%.1f"}</strong>
                                    <div>
                                        {if $inspectionResult.metrics.luminance_status == 'OPTIMAL'}
                                            <span class="badge badge-success">{l s='Ideal' mod='qlovisualinspection'}</span>
                                        {elseif $inspectionResult.metrics.luminance_status == 'UNDEREXPOSED'}
                                            <span class="badge badge-danger">{l s='Muito Escura' mod='qlovisualinspection'}</span>
                                        {else}
                                            <span class="badge badge-warning">{l s='Estourada' mod='qlovisualinspection'}</span>
                                        {/if}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-4">
                            <div class="panel panel-default text-center">
                                <div class="panel-body">
                                    <div class="text-muted"><small>{l s='Nitidez (Foco)' mod='qlovisualinspection'}</small></div>
                                    <strong>{$inspectionResult.metrics.sharpness_score|string_format:"%.1f"}</strong>
                                    <div>
                                        {if $inspectionResult.metrics.sharpness_status == 'SHARP'}
                                            <span class="badge badge-success">{l s='Nítida' mod='qlovisualinspection'}</span>
                                        {else}
                                            <span class="badge badge-danger">{l s='Desfocada' mod='qlovisualinspection'}</span>
                                        {/if}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {if isset($inspectionResult.warnings) && $inspectionResult.warnings|@count > 0}
                        <div class="alert alert-danger" style="margin-top: 10px;">
                            <strong><i class="icon-warning-sign"></i> {l s='Avisos e Recomendações de Retake:' mod='qlovisualinspection'}</strong>
                            <ul style="margin-top: 5px; margin-bottom: 0;">
                                {foreach from=$inspectionResult.warnings item=warn}
                                    <li>
                                        {if $warn == 'LOW_RESOLUTION'}
                                            {l s='Resolução insuficiente. Tire a foto mais de perto ou ajuste a câmera para alta resolução.' mod='qlovisualinspection'}
                                        {elseif $warn == 'UNDEREXPOSED'}
                                            {l s='Foto muito escura. Acenda as luzes do quarto e abra as cortinas antes de fotografar.' mod='qlovisualinspection'}
                                        {elseif $warn == 'OVEREXPOSED'}
                                            {l s='Foto com excesso de luz. Evite apontar diretamente para lâmpadas ou reflexos de sol.' mod='qlovisualinspection'}
                                        {elseif $warn == 'BLURRY_IMAGE'}
                                            {l s='Foto desfocada ou tremida. Mantenha as mãos firmes ao disparar.' mod='qlovisualinspection'}
                                        {else}
                                            {$warn|escape:'html':'UTF-8'}
                                        {/if}
                                    </li>
                                {/foreach}
                            </ul>
                        </div>
                    {/if}
                </div>
            </div>
        </div>
    {/if}
</div>
