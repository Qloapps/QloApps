<?php /* Smarty version Smarty-3.1.19, created on 2016-01-07 10:53:48
         compiled from "/home/sumit/public_html/html/hotel-reservation-system/mails/en/advanced-book-data.txt" */ ?>
<?php /*%%SmartyHeaderCode:326945322568d305fbf8f17-20513850%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'cd1c746077e17ff7da70afdba3657c8d658ebbde' => 
    array (
      0 => '/home/sumit/public_html/html/hotel-reservation-system/mails/en/advanced-book-data.txt',
      1 => 1452142834,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '326945322568d305fbf8f17-20513850',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_568d305fc008a8_93837393',
  'variables' => 
  array (
    'list' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_568d305fc008a8_93837393')) {function content_568d305fc008a8_93837393($_smarty_tpl) {?><?php if (!empty($_smarty_tpl->tpl_vars['list']->value)) {?>

TOTAL PAID 

<?php echo $_smarty_tpl->tpl_vars['list']->value['total_paid_amount'];?>


TOTAL DUE

<?php echo $_smarty_tpl->tpl_vars['list']->value['total_due_amount'];?>


<?php }?><?php }} ?>
