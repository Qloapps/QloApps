<?php /* Smarty version Smarty-3.1.19, created on 2016-01-29 15:44:56
         compiled from "/home/sumit/public_html/html/hotelcommerce-master/admin/themes/default/template/invalid_token.tpl" */ ?>
<?php /*%%SmartyHeaderCode:154042369256ab3ba0a63847-54516532%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '5063848a50a053f8491adda0359bf5a5c31d3232' => 
    array (
      0 => '/home/sumit/public_html/html/hotelcommerce-master/admin/themes/default/template/invalid_token.tpl',
      1 => 1454061940,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '154042369256ab3ba0a63847-54516532',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'url' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_56ab3ba0a70b57_27917914',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_56ab3ba0a70b57_27917914')) {function content_56ab3ba0a70b57_27917914($_smarty_tpl) {?>

<!doctype html>
<html>
	<head>
		<meta charset="UTF-8">
		<title><?php echo smartyTranslate(array('s'=>'Invalid security token'),$_smarty_tpl);?>
</title>
		<style>
			body{
				background-color: white;
				color: #000000;
				font: 400 13px/15px "arial" ;
			}
			.container{
				width: 600px;
				margin: 0 auto;
			}
			.error-msg {
				display: block;
				background-color: #D9534F;
				border-radius: 3px;
				padding: 10px;
				margin: 0 0 10px 0;
				color: white;
				font-size: 1.2em;
				text-transform: uppercase;
			}
			.error-msg img{
				vertical-align: text-bottom;
			}
			.action-container {
				display: block;
			}
			.btn{
				background: #E3E3E3;
				border-radius: 3px;
				padding: 8px 10px;
				color: #000000;
				text-decoration: none;
			}
			.btn-continue{
				float: right;
				background-color: #F0AD4E;
			}
			.btn-continue:hover{
				background-color: #FECE5B;
			}
			.btn-cancel{
				float: right;
				margin-right: 10px;
				background-color: #46A74E;
			}
			.btn-cancel:hover{
				background-color: #64B848;
			}
		</style>
	</head>
	<body>
		<div class="container">
			<div class="error-msg">
				<img width="32px" alt="warning" src="data:image/png;base64,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" />
				<?php echo smartyTranslate(array('s'=>'Invalid security token'),$_smarty_tpl);?>

			</div>
			<div class="action-container">
				<a class="btn btn-continue" href="<?php echo $_smarty_tpl->tpl_vars['url']->value;?>
">
					<?php echo smartyTranslate(array('s'=>'I understand the risks and I really want to display this page'),$_smarty_tpl);?>

				</a>
				<a class="btn btn-cancel" href="index.php">
					<?php echo smartyTranslate(array('s'=>'Take me out of here!'),$_smarty_tpl);?>

				</a>
			</div>
		</div>
	</body>
</html>
<?php }} ?>
