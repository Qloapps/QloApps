{*
* 2007-2015 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2015 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

{extends file="helpers/view/view.tpl"}

{block name="override_tpl"}
	{if !$host_mode}
	<script type="text/javascript">
		$(document).ready(function()
		{
			$.ajax({
				type: 'GET',
				url: '{$link->getAdminLink('AdminInformation')|addslashes}',
				data: {
					'action': 'checkFiles',
					'ajax': 1
				},
				dataType: 'json',
				success: function(json)
				{
					var tab = {
						'missing': '{l s='Missing files'}',
						'updated': '{l s='Updated files'}'
					};

					if (json.missing.length || json.updated.length)
						$('#changedFiles').html('<div class="alert alert-warning">{l s='Changed/missing files have been detected.'}</div>');
					else
						$('#changedFiles').html('<div class="alert alert-success">{l s='No change has been detected in your files.'}</div>');

					$.each(tab, function(key, lang)
					{
						if (json[key].length)
						{
							var html = $('<ul>').attr('id', key+'_files');
							$(json[key]).each(function(key, file)
							{
								html.append($('<li>').html(file))
							});
							$('#changedFiles')
								.append($('<h4>').html(lang+' ('+json[key].length+')'))
								.append(html);
						}
					});
				}
			});
		});
	</script>
	{/if}
	<div class="row">
		<div class="col-lg-6">
			<div class="panel">
				<h3>
					<i class="icon-info"></i>
					{l s='Configuration information'}
				</h3>
				<p>{l s='This information must be provided when you report an issue on our bug tracker or forum.'}</p>
			</div>
			{if !$host_mode}
			<div class="panel">
				<h3>
					<i class="icon-info"></i>
					{l s='Server information'}
				</h3>
				{if count($uname)}
				<p>
					<strong>{l s='Server information:'}</strong> {$uname|escape:'html':'UTF-8'}
				</p>
				{/if}
				<p>
					<strong>{l s='Server software version:'}</strong> {$version.server|escape:'html':'UTF-8'}
				</p>
				<p>
					<strong>{l s='PHP version:'}</strong> {$version.php|escape:'html':'UTF-8'}
				</p>
				<p>
					<strong>{l s='Memory limit:'}</strong> {$version.memory_limit|escape:'html':'UTF-8'}
				</p>
				<p>
					<strong>{l s='Max execution time:'}</strong> {$version.max_execution_time|escape:'html':'UTF-8'}
				</p>
				{if $apache_instaweb}
					<p>{l s='PageSpeed module for Apache installed (mod_instaweb)'}</p>
				{/if}
			</div>
			<div class="panel">
				<h3>
					<i class="icon-info"></i>
					{l s='Database information'}
				</h3>
				<p>
					<strong>{l s='MySQL version:'}</strong> {$database.version|escape:'html':'UTF-8'}
				</p>
				<p>
					<strong>{l s='MySQL server:'}</strong> {$database.server|escape:'html':'UTF-8'}
				</p>
				<p>
					<strong>{l s='MySQL name:'}</strong> {$database.name|escape:'html':'UTF-8'}
				</p>
				<p>
					<strong>{l s='MySQL user:'}</strong> {$database.user|escape:'html':'UTF-8'}
				</p>
				<p>
					<strong>{l s='Tables prefix:'}</strong> {$database.prefix|escape:'html':'UTF-8'}
				</p>
				<p>
					<strong>{l s='MySQL engine:'}</strong> {$database.engine|escape:'html':'UTF-8'}
				</p>
			</div>
		</div>
		{/if}
		<div class="col-lg-6">
			<div class="panel">
				<h3>
					<i class="icon-info"></i>
					{l s='Store information'}
				</h3>
				<p>
					<strong>{l s='Qloapps version:'}</strong> {$shop.qloappsV|escape:'html':'UTF-8'}
				</p>
				<p>
					<strong>{l s='Shop URL:'}</strong> {$shop.url|escape:'html':'UTF-8'}
				</p>
				<p>
					<strong>{l s='Current theme in use:'}</strong> {$shop.theme|escape:'html':'UTF-8'}
				</p>
			</div>
			<div class="panel">
				<h3>
					<i class="icon-info"></i>
					{l s='Mail configuration'}
				</h3>
				<p>
					<strong>{l s='Mail method:'}</strong>

			{if $mail}
				{l s='You are using the PHP mail() function.'}</p>
			{else}
				{l s='You are using your own SMTP parameters.'}</p>
				<p>
					<strong>{l s='SMTP server'}:</strong> {$smtp.server|escape:'html':'UTF-8'}
				</p>
				<p>
					<strong>{l s='SMTP username'}:</strong>
					{if $smtp.user neq ''}
						{l s='Defined'}
					{else}
						<span style="color:red;">{l s='Not defined'}</span>
					{/if}
				</p>
				<p>
					<strong>{l s='SMTP password'}:</strong>
					{if $smtp.password neq ''}
						{l s='Defined'}
					{else}
						<span style="color:red;">{l s='Not defined'}</span>
					{/if}
				</p>
				<p>
					<strong>{l s='Encryption:'}</strong> {$smtp.encryption|escape:'html':'UTF-8'}
				</p>
				<p>
					<strong>{l s='SMTP port:'}</strong> {$smtp.port|escape:'html':'UTF-8'}
				</p>
			{/if}
			</div>
			<div class="panel">
				<h3>
					<i class="icon-info"></i>
					{l s='Your information'}
				</h3>
				<p>
					<strong>{l s='Your web browser:'}</strong> {$user_agent|escape:'html':'UTF-8'}
				</p>
			</div>

			<div class="panel" id="checkConfiguration">
				<h3>
					<i class="icon-info"></i>
					{l s='Check your configuration'}
				</h3>
				<p>
					<strong>{l s='Required parameters:'}</strong>
				{if !$failRequired}
					<span class="text-success">{l s='OK'}</span>
				</p>
				{else}
					<span class="text-danger">{l s='Please fix the following error(s)'}</span>
				</p>
				<ul>
					{foreach from=$testsRequired item='value' key='key'}
						{if $value eq 'fail' && isset($testsErrors[$key])}
							<li>{$testsErrors[$key]}</li>
						{/if}
					{/foreach}
				</ul>
				{/if}
				{if isset($failOptional)}
					<p>
						<strong>{l s='Optional parameters:'}</strong>
					{if !$failOptional}
						<span class="text-success">{l s='OK'}</span>
					</p>
					{else}
						<span class="text-danger">{l s='Please fix the following error(s)'}</span>
					</p>
					<ul>
						{foreach from=$testsOptional item='value' key='key'}
							{if $value eq 'fail' && isset($testsErrors[$key])}
								<li>{$testsErrors[$key]}</li>
							{/if}
						{/foreach}
					</ul>
					{/if}
				{/if}
			</div>
		</div>
	</div>
	{if !$host_mode}
	<div class="panel">
		<h3>
			<i class="icon-info"></i>
			{l s='List of changed files'}
		</h3>
		<div id="changedFiles"><i class="icon-spin icon-refresh"></i> {l s='Checking files...'}</div>
	</div>
	{/if}
{/block}
