{*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License version 3.0
* that is bundled with this package in the file LICENSE.md
* It is also available through the world-wide-web at this URL:
* https://opensource.org/license/osl-3-0-php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to support@qloapps.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to a newer
* versions in the future. If you wish to customize this module for your needs
* please refer to https://store.webkul.com/customisation-guidelines for more information.
*
* @author Webkul IN
* @copyright Since 2010 Webkul
* @license https://opensource.org/license/osl-3-0-php Open Software License version 3.0
*}
<!DOCTYPE html>
<html lang="{$language_code|escape:'html':'UTF-8'}">
	<head>
		<meta charset="utf-8">
		<title>{$meta_title|escape:'html':'UTF-8'}</title>
		<meta name="robots" content="noindex,nofollow">
		<link rel="shortcut icon" href="{$favicon_url}">
		<style>
			html {
				padding: 30px 10px;
				font-size: 16px;
				line-height: 1.4;
				color: #737373;
				background: #f0f0f0;
			}

			html, input {
				font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
			}

			body {
				max-width: 480px;
				padding: 0 0 50px;
				border: 1px solid #b3b3b3;
				border-radius: 4px;
				margin: 0 auto;
				box-shadow: 0 1px 10px #a7a7a7, inset 0 1px 0 #fff;
				background-color: #e0e0e0;
				text-align: center;
			}

			.logo-band {
				background-color: #e0e0e0;
				padding: 20px;
			}

			img.logo {
				max-height: 60px;
			}

			h2 {
				color: #D35780;
				margin: 1em 0 0.5em;
				font-size: 28px;
			}

			p {
				margin: 1em 20px;
			}
		</style>
	</head>
	<body>
		<div class="logo-band">
			<img class="logo" src="{$logo_url}" alt="logo" />
		</div>
		<h2>{l s='Access denied'}</h2>
		<p>{l s='You do not have permission to access this page.'}</p>
	</body>
</html>
