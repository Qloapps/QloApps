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
					<span> {if isset($global_phone_num) && $global_phone_num }{$global_phone_num}{else}{l s='No contact available'}{/if}</span>
				</p>
				<p>
					<i class="icon-envelope cont_icon_enve"></i>
					<span> {if isset($global_email) && $global_email }{$global_email}{else}{l s='No email available'}{/if}</span>
				</p>
			</div>
		</div>
		
		<div class="col-sm-6">
			<form action="{$request_uri}" method="post" class="contact-form-box" enctype="multipart/form-data">
				{if isset($customerThread.id_contact) && $customerThread.id_contact && $contacts|count}
					{assign var=flag value=true}
					{foreach from=$contacts item=contact}
						{if $contact.id_contact == $customerThread.id_contact}
							<input type="text" class="form-control" id="contact_name" name="contact_name" value="{$contact.name|escape:'html':'UTF-8'}" readonly="readonly" />
							<input type="hidden" name="id_contact" value="{$contact.id_contact|intval}" />
							{$flag=false}
						{/if}
					{/foreach}
					{if $flag && isset($contacts.0.id_contact)}
							<input type="text" class="form-control" id="contact_name" name="contact_name" value="{$contacts.0.name|escape:'html':'UTF-8'}" readonly="readonly" />
							<input type="hidden" name="id_contact" value="{$contacts.0.id_contact|intval}" />
					{/if}
				{else}
					{*<select id="id_contact" class="form-control contact_input" name="id_contact">
						<option value="0">{l s='-- Choose --'}</option>
						{foreach from=$contacts item=contact}
							<option value="{$contact.id_contact|intval}"{if isset($smarty.request.id_contact) && $smarty.request.id_contact == $contact.id_contact} selected="selected"{/if}>{$contact.name|escape:'html':'UTF-8'}</option>
						{/foreach}
					</select>
					<p id="desc_contact0" class="desc_contact{if isset($smarty.request.id_contact)} unvisible{/if}">&nbsp;</p>
					{foreach from=$contacts item=contact}
						<p id="desc_contact{$contact.id_contact|intval}" class="desc_contact contact-title{if !isset($smarty.request.id_contact) || $smarty.request.id_contact|intval != $contact.id_contact|intval} unvisible{/if}">
							<i class="icon-comment-alt"></i>{$contact.description|escape:'html':'UTF-8'}
						</p>
					{/foreach}
					*}
					<!-- By webkul For changing design of input fields -->
					<div class="form-group">
                        <div class="dropdown">
                            <button class="form-control contact_type_input" type="button" data-toggle="dropdown">
								<span id="contact_type" class="pull-left">{l s='Choose'}</span>
								<input type="hidden" id="id_contact" name="id_contact" value="0">
								<span class="arrow_span">
                                    <i class="icon icon-angle-down"></i>
                                </span>
                            </button>
                            <ul class="dropdown-menu contact_type_ul">
								{foreach from=$contacts item=contact}
									<li  value="{$contact.id_contact|intval}"{if isset($smarty.request.id_contact) && $smarty.request.id_contact == $contact.id_contact} selected="selected"{/if}>{$contact.name|escape:'html':'UTF-8'}
									</li>
								{/foreach}
                            
                                {if isset($all_hotels_info) && $all_hotels_info}
									{foreach from=$all_hotels_info key=htl_k item=htl_v}
									{/foreach}
								{/if} 
                            </ul>
                        </div>
                    </div>

					<!-- End -->
					
				{/if}
				{*<input type="text" placeholder="Name" class="form-control contact_input">*}
				{if isset($customerThread.email)}
					<input class="form-control contact_input" placeholder="Email" type="email" id="email" name="from" value="{$customerThread.email|escape:'html':'UTF-8'}" readonly="readonly" />
				{else}
					<input class="form-control contact_input validate" placeholder="Email" type="email" id="email" name="from" data-validate="isEmail" value="{$email|escape:'html':'UTF-8'}" />
				{/if}
				<textarea placeholder="Message/Query..." class="form-control contact_textarea" id="message" name="message">{if isset($message)}{$message|escape:'html':'UTF-8'|stripslashes}{/if}</textarea>
				{if $fileupload == 1}
					<p class="form-group">
						{*<label for="fileUpload">{l s='Attach File'}</label>*}
						<input type="hidden" name="MAX_FILE_SIZE" value="{if isset($max_upload_size) && $max_upload_size}{$max_upload_size|intval}{else}2000000{/if}" />
						<input type="file" name="fileUpload" id="fileUpload" class="form-control" />
					</p>
				{/if}
				<div class="form-group">
					<button class="btn button button-medium contact_btn" type="submit" name="submitMessage" id="submitMessage" ><span>{l s='Send'}</span></button>
				</div>
			</form>
		</div>
		{if isset($hotelLocationArray)}
			<div class="col-xs-12 col-sm-12 margin-top-50" id="googleMapWrapper">
				<div id="map"></div>
			</div>
		{/if}
		<div style="clear:both;"></div>
	</div>
{/if}
{addJsDefL name='contact_fileDefaultHtml'}{l s='No file selected' js=1}{/addJsDefL}
{addJsDefL name='contact_fileButtonHtml'}{l s='Choose File' js=1}{/addJsDefL}
{if isset($hotelLocationArray)}
	{addJsDef hotelLocationArray = $hotelLocationArray}
{else}
	{addJsDef hotelLocationArray = 0}
{/if}