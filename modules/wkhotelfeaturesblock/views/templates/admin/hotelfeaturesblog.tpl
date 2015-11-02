<div class="panel">
	<h3 class="tab"> <i class="icon-info"></i> {l s='Configuration' mod='wkhotelfeaturesblock'}</h3>
	<div class="panel-body">
		<form method="post" action="">
			<div class="row">	
				<div class="form-group">
					<label for="feature_blog_title" class="control-label required">
						<span title="" data-toggle="tooltip" class="label-tooltip">{l s='Feature Blog Title' mod='wkhotelfeaturesblock'}</span>
					</label>
					<div class="">
						<input type="text" name="feature_blog_title" class="form-control" id="feature_blog_title" {if isset($main_blog_data.blog_heading)}value="{$main_blog_data.blog_heading}"{/if}>
						<input type="hidden" name="feature_id" class="form-control" {if isset($main_blog_data.id)}value="{$main_blog_data.id}"{/if}>
					</div>
				</div>
				<div class="form-group">
					<label for="feature_blog_description" class="control-label required">
						<span title="" data-toggle="tooltip" class="label-tooltip">{l s='Feature Blog Description' mod='wkhotelfeaturesblock'}</span>
					</label>
					<div class="">
						<input type="text" name="feature_blog_description" class="form-control" id="feature_blog_description" {if isset($main_blog_data.blog_description)}value="{$main_blog_data.blog_description}"{/if}>
					</div>
				</div>
				<div class="">
					<button id="search_hotel_list" name="save_feature_blog" type="submit" class="btn btn-primary col-sm-1 pull-right">
						{l s='Save' mod='wkhotelfeaturesblock'}
					</button>
				</div>
			</div>
		</form>
	</div>
</div>
<div class="panel">
	<h3 class="tab"> <i class="icon-info"></i> {l s='Configuration' mod='wkhotelfeaturesblock'}</h3>
	<div class="panel-body">
		<form method="post" action="" enctype="multipart/form-data">
			<div class="row">	
				{if isset($features_data)} 
					<div class="form_feature_div">	
					{foreach $features_data as $data}
						<div class="feature_form_elements">
							<div class="col-sm-12">
								<button data-id_row="{$data.id}" type="submit" class="delete_htl_ftr btn btn-primary pull-right">
									{l s='Delete' mod='wkhotelfeaturesblock'}
								</button>
							</div>
							<div class="form-group">
								<label for="feature_title" class="control-label required">
									<span title="" data-toggle="tooltip" class="label-tooltip">{l s='Feature Title' mod='wkhotelfeaturesblock'}</span>
								</label>
								<div class="">
									<input type="text" name="feature_title[]" class="form-control" {if isset($data.feature_title)}value="{$data.feature_title}"{/if}>
									<input type="hidden" name="features_id[]" class="form-control" {if isset($data.id)}value="{$data.id}"{/if}>
								</div>
							</div>
							<div class="form-group">
								<label for="feature_description" class="control-label required">
									<span title="" data-toggle="tooltip" class="label-tooltip">{l s='Feature Description' mod='wkhotelfeaturesblock'}</span>
								</label>
								<div class="">
									<input type="text" name="feature_description[]" class="form-control" {if isset($data.feature_description)}value="{$data.feature_description}"{/if}>
								</div>
							</div>
							<div class="form-group">
								<label for="feature_image" class="control-label col-sm-2">
									<span title="" data-toggle="tooltip" class="label-tooltip">{l s='Feature Image' mod='wkhotelfeaturesblock'}</span>
								</label>
								<div class="col-sm-4">
									<input type="file" name="feature_image[]" value="">
								</div>
								<div class="col-sm-6">
								{if isset($data.id)}
									{if isset($data.feature_image) && $data.feature_image}
										<img height="50px" width="50px" src="{$module_dir}wkhotelfeaturesblock/views/img/{$data.feature_image}">
									{else}
										<img height="50px" width="50px" src="{$module_dir}wkhotelfeaturesblock/views/img/default.jpg">
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
							<a class="btn btn-default htl-features-btn-more-ftr">
								<i class="icon-image"></i>
								<span>{l s='Add More Features' mod='wkhotelfeaturesblock'}</span>
							</a>
							<div id="wk_prod_other_images"></div>
						</div>
					</div>
				{/if}
				<div class="">
					<button id="search_hotel_list" name="save_feature_data" type="submit" class="btn btn-primary col-sm-1 pull-right">
						{l s='Save' mod='wkhotelfeaturesblock'}
					</button>
				</div>
			</div>
		</form>
	</div>
</div>
{strip}
	{addJsDef module_dir = $module_dir}
	{addJsDefL name='some_error_cond'}{l s='Some error occured, Please try again.' js=1 				mod='wkhotelfeaturesblock'}{/addJsDefL}
	{addJsDefL name=remove_success_var}{l s='Remove successful' js=1 mod='wkhotelfeaturesblock'}{/addJsDefL}
	{addJsDefL name=feature_title_var}{l s='Feature Title' js=1 mod='wkhotelfeaturesblock'}{/addJsDefL}
	{addJsDefL name=feature_description_var}{l s='Feature Description' js=1 mod='wkhotelfeaturesblock'}{/addJsDefL}
	{addJsDefL name=feature_image_var}{l s='Feature Image' js=1 mod='wkhotelfeaturesblock'}{/addJsDefL}
	{addJsDefL name=remove_var}{l s='Remove' js=1 mod='wkhotelfeaturesblock'}{/addJsDefL}
{/strip}



