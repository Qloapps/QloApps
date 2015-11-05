<div class="panel">
	<h3 class="tab"> <i class="icon-info"></i> {l s='Configuration' mod='wktestimonialblock'}</h3>
	<div class="panel-body">
		<form method="post" action="" enctype="multipart/form-data">
			<div class="row">	
				{if isset($testimonials_data)} 
					<div class="form_testimonial_div">	
					{foreach $testimonials_data as $data}
						<div class="testimonial_form_elements">
							<div class="col-sm-12">
								<button data-id_row="{$data.id}" type="submit" class="delete_htl_testimonial btn btn-primary pull-right">
									{l s='Delete' mod='wktestimonialblock'}
								</button>
							</div>
							<div class="form-group">
								<label for="name" class="control-label">
									<span title="" data-toggle="tooltip" class="label-tooltip">{l s='Name' mod='wktestimonialblock'}</span>
								</label>
								<div class="">
									<input type="text" name="name[]" class="form-control" {if isset($data.name)}value="{$data.name}"{/if}>
									<input type="hidden" name="testimonial_id[]" class="form-control" {if isset($data.id)}value="{$data.id}"{/if}>
								</div>
							</div>
							<div class="form-group">
								<label for="testimonial_description" class="control-label required">
									<span title="" data-toggle="tooltip" class="label-tooltip">{l s='Testimonial Description' mod='wktestimonialblock'}</span>
								</label>
								<div class="">
									<input type="text" name="testimonial_description[]" class="form-control" {if isset($data.testimonial_description)}value="{$data.testimonial_description}"{/if}>
								</div>
							</div>
							<div class="form-group">
								<label for="testimonial_content" class="control-label required">
									<span title="" data-toggle="tooltip" class="label-tooltip">{l s='Testimonial Content' mod='wktestimonialblock'}</span>
								</label>
								<div class="form-group">
									<textarea name="testimonial_content[]" class="testimonial_content wk_tinymce">{$data.testimonial_content}</textarea>
								</div>
							</div>
							<div class="form-group">
								<label for="testimonial_image" class="control-label col-sm-2">
									<span title="" data-toggle="tooltip" class="label-tooltip">{l s='Person Image' mod='wktestimonialblock'}</span>
								</label>
								<div class="col-sm-4">
									<input type="file" name="testimonial_image[]">
								</div>
								<div class="col-sm-6">
								{if isset($data.id)}
									{if isset($data.testimonial_image)}
										<img height="50px" width="50px" src="{$module_dir}wktestimonialblock/views/img/{$data.testimonial_image}">
									{else}
										<img height="50px" width="50px" src="{$module_dir}wktestimonialblock/views/img/default.png">
									{/if}
								{/if}
								</div>
							</div>
							<br><br><hr>
						</div>
					{/foreach}
					</div>	
					<div class="form-group">
						<div class="col-lg-12">
							<a class="btn btn-default htl-testimonial-btn-more-testimonials">
								<i class="icon-image"></i>
								<span>{l s='Add More Testimonials' mod='wktestimonialblock'}</span>
							</a>
							<div id="wk_testimonials_other_images"></div>
						</div>
					</div>
				{/if}
				<div class="">
					<button id="testimonial_submit" name="save_testimonial_data" type="submit" class="btn btn-primary col-sm-1 pull-right">
						{l s='Save' mod='wktestimonialblock'}
					</button>
				</div>
			</div>
		</form>
	</div>
</div>
{strip}
	{addJsDef module_dir = $module_dir}
	{addJsDefL name='some_error_occur_cond'}{l s='Some error occured, Please try again.' js=1 mod='wktestimonialblock'}{/addJsDefL}
	{addJsDefL name=remove_success_var}{l s='Remove successful' js=1 mod='wktestimonialblock'}{/addJsDefL}
	{addJsDefL name=name_var}{l s='Name' js=1 mod='wktestimonialblock'}{/addJsDefL}
	{addJsDefL name=testimonial_description_var}{l s='Testimonial Description' js=1 mod='wktestimonialblock'}{/addJsDefL}
	{addJsDefL name=testimonial_content_var}{l s='Testimonial Content' js=1 mod='wktestimonialblock'}{/addJsDefL}
	{addJsDefL name=person_image_var}{l s='Person Image' js=1 mod='wktestimonialblock'}{/addJsDefL}
	{addJsDefL name=remove_var}{l s='Remove' js=1 mod='wktestimonialblock'}{/addJsDefL}
{/strip}




<script>
// for tiny mce setup
  var iso = "{$iso|escape:'htmlall':'UTF-8'}";
  var pathCSS = "{$smarty.const._THEME_CSS_DIR_|escape:'htmlall':'UTF-8'}";
  var ad = "{$ad|escape:'htmlall':'UTF-8'}";

  $(document).ready(function(){
    {block name="autoload_tinyMCE"}
      tinySetup({
        editor_selector :"wk_tinymce",
      });
    {/block}
  });
</script>