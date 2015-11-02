{if isset($confirmation)}
	<p class="alert alert-success">{l s='Your message has been successfully sent to our team.'}</p>
	<ul class="footer_links clearfix">
		<li>
			<a class="btn btn-default button button-small" href="{$base_dir}">
				<span>
					<i class="icon-chevron-left"></i>{l s='Home'}
				</span>
			</a>
		</li>
	</ul>
{elseif isset($alreadySent)}
	<p class="alert alert-warning">{l s='Your message has already been sent.'}</p>
	<ul class="footer_links clearfix">
		<li>
			<a class="btn btn-default button button-small" href="{$base_dir}">
				<span>
					<i class="icon-chevron-left"></i>{l s='Home'}
				</span>
			</a>
		</li>
	</ul>
{else}
	{include file="$tpl_dir./errors.tpl"}
	<div class="row margin-top-50">
		<div class="col-sm-6">
			<p class="contact-header">{l s='Get in touch with us'}</p>
			<p class="contact-desc">{l s='Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry`s standard dummy text.'}</p>
			<div class="col-sm-12 contact-subdiv">
				<p>
					<i class="icon-map-marker cont_icon_map"></i>
					<span> {l s='Contrary to popular belief, Lorem Ipsum is not simply random text.'}</span>
				</p>
				<p>
					<i class="icon-mobile-phone cont_icon_phone"></i>
					<span> +91-9999999999, +91-9876543210</span>
				</p>
				<p>
					<i class="icon-envelope cont_icon_enve"></i>
					<span> noreply@webkul.com</span>
				</p>
			</div>
		</div>
		<div class="col-sm-6">
			<form method="POST" action="#">
				<input type="text" placeholder="Name" class="form-control contact_input">
				<input type="email" placeholder="Email" class="form-control contact_input">
				<textarea placeholder="Message/Query..." class="form-control contact_textarea"></textarea>
				<button class="btn contact_btn">{l s='Send'}</button>
			</form>
		</div>
	</div>
{/if}
{addJsDefL name='contact_fileDefaultHtml'}{l s='No file selected' js=1}{/addJsDefL}
{addJsDefL name='contact_fileButtonHtml'}{l s='Choose File' js=1}{/addJsDefL}
