<script src="{$facebook_js_url}"></script>
<link href="{$facebook_css_url}" rel="stylesheet">
{if $facebookurl != ''}
<div class="bootstrap panel">
	<div class="panel-heading">
		<i class="icon-picture-o"></i> {l s='Preview' mod='blockfacebook'}
	</div>
	<div id="fb-root"></div>
	<div id="facebook_block">
		<h4 >{l s='Follow us on Facebook' mod='blockfacebook'}</h4>
		<div class="facebook-fanbox">
			<div class="fb-like-box" data-href="{$facebookurl|escape:'html':'UTF-8'}" data-colorscheme="light" data-show-faces="true" data-header="false" data-stream="false" data-show-border="false">
			</div>
		</div>
	</div>
</div>
{/if}
