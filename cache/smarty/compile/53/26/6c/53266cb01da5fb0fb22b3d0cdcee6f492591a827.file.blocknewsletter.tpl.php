<?php /* Smarty version Smarty-3.1.19, created on 2015-11-02 12:11:16
         compiled from "/home/sumit/public_html/html/ps-hotel-reservation-system/themes/hotel-reservation-theme/modules/blocknewsletter/blocknewsletter.tpl" */ ?>
<?php /*%%SmartyHeaderCode:111920841056376ee2cfdba2-72720596%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '53266cb01da5fb0fb22b3d0cdcee6f492591a827' => 
    array (
      0 => '/home/sumit/public_html/html/ps-hotel-reservation-system/themes/hotel-reservation-theme/modules/blocknewsletter/blocknewsletter.tpl',
      1 => 1446483396,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '111920841056376ee2cfdba2-72720596',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_56376ee2db7882_03251923',
  'variables' => 
  array (
    'hotel_global_contact_num' => 0,
    'hotel_global_email' => 0,
    'link' => 0,
    'msg' => 0,
    'nw_error' => 0,
    'value' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_56376ee2db7882_03251923')) {function content_56376ee2db7882_03251923($_smarty_tpl) {?>

<!-- Block Newsletter module-->
<div id="newsletter_block_left" class="col-sm-12 col-md-12 col-lg-10">
    <div class="row">
        <div class="col-sm-6 col-md-3 hotel_contact text-center"><?php echo smartyTranslate(array('s'=>'Call Us : ','mod'=>'blocknewsletter'),$_smarty_tpl);?>
 <?php if (isset($_smarty_tpl->tpl_vars['hotel_global_contact_num']->value)&&'hotel_global_contact_num') {?><?php echo $_smarty_tpl->tpl_vars['hotel_global_contact_num']->value;?>
<?php }?></div>
        <div class="col-sm-6 col-md-4 hotel_email text-center">
        <?php echo smartyTranslate(array('s'=>'Email : ','mod'=>'blocknewsletter'),$_smarty_tpl);?>
<?php if (isset($_smarty_tpl->tpl_vars['hotel_global_email']->value)&&'hotel_global_email') {?><?php echo $_smarty_tpl->tpl_vars['hotel_global_email']->value;?>
<?php }?>
        </div>
        <div class="col-md-5 hidden-sm">
            <div class="block_content">
                <form action="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['link']->value->getPageLink('index',null,null,null,false,null,true), ENT_QUOTES, 'UTF-8', true);?>
" method="post">
                    <div class="form-group<?php if (isset($_smarty_tpl->tpl_vars['msg']->value)&&$_smarty_tpl->tpl_vars['msg']->value) {?> <?php if ($_smarty_tpl->tpl_vars['nw_error']->value) {?>form-error<?php } else { ?>form-ok<?php }?><?php }?>" >
                        <input placeholder="<?php echo smartyTranslate(array('s'=>'Enter E-mail for NewsLetter','mod'=>'blocknewsletter'),$_smarty_tpl);?>
" class="inputNew form-control grey newsletter-input" id="newsletter-input" type="text" name="email" size="18" value="<?php if (isset($_smarty_tpl->tpl_vars['msg']->value)&&$_smarty_tpl->tpl_vars['msg']->value) {?><?php echo $_smarty_tpl->tpl_vars['msg']->value;?>
<?php } elseif (isset($_smarty_tpl->tpl_vars['value']->value)&&$_smarty_tpl->tpl_vars['value']->value) {?><?php echo $_smarty_tpl->tpl_vars['value']->value;?>
<?php }?>" />
                        <button type="submit" name="submitNewsletter" id="submitNewsletter" class="btn btn-default submitNewsletter">
                            <span><?php echo smartyTranslate(array('s'=>'Send','mod'=>'blocknewsletter'),$_smarty_tpl);?>
</span>
                        </button>
                        <input type="hidden" name="action" value="0" />
                    </div>
                </form>
            </div>
        </div>
        <div class="row margin-lr-0 visible-sm" style="clear:both;">
            <div class="col-sm-12">
                <div class="block_content">
                    <form action="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['link']->value->getPageLink('index',null,null,null,false,null,true), ENT_QUOTES, 'UTF-8', true);?>
" method="post">
                        <div class="form-group<?php if (isset($_smarty_tpl->tpl_vars['msg']->value)&&$_smarty_tpl->tpl_vars['msg']->value) {?> <?php if ($_smarty_tpl->tpl_vars['nw_error']->value) {?>form-error<?php } else { ?>form-ok<?php }?><?php }?>" >
                            <input placeholder="<?php echo smartyTranslate(array('s'=>'Enter E-mail for NewsLetter','mod'=>'blocknewsletter'),$_smarty_tpl);?>
" class="inputNew form-control grey newsletter-input" id="newsletter-input" type="text" name="email" size="18" value="<?php if (isset($_smarty_tpl->tpl_vars['msg']->value)&&$_smarty_tpl->tpl_vars['msg']->value) {?><?php echo $_smarty_tpl->tpl_vars['msg']->value;?>
<?php } elseif (isset($_smarty_tpl->tpl_vars['value']->value)&&$_smarty_tpl->tpl_vars['value']->value) {?><?php echo $_smarty_tpl->tpl_vars['value']->value;?>
<?php }?>" />
                            <button type="submit" name="submitNewsletter" id="submitNewsletter" class="btn btn-default submitNewsletter">
                                <span><?php echo smartyTranslate(array('s'=>'Send','mod'=>'blocknewsletter'),$_smarty_tpl);?>
</span>
                            </button>
                            <input type="hidden" name="action" value="0" />
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['hook'][0][0]->smartyHook(array('h'=>"displayBlockNewsletterBottom",'from'=>'blocknewsletter'),$_smarty_tpl);?>

</div>
<!-- /Block Newsletter module-->
<?php if (isset($_smarty_tpl->tpl_vars['msg']->value)&&$_smarty_tpl->tpl_vars['msg']->value) {?><?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['addJsDef'][0][0]->addJsDef(array('msg_newsl'=>addcslashes($_smarty_tpl->tpl_vars['msg']->value,'\'')),$_smarty_tpl);?>
<?php }?><?php if (isset($_smarty_tpl->tpl_vars['nw_error']->value)) {?><?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['addJsDef'][0][0]->addJsDef(array('nw_error'=>$_smarty_tpl->tpl_vars['nw_error']->value),$_smarty_tpl);?>
<?php }?><?php $_smarty_tpl->smarty->_tag_stack[] = array('addJsDefL', array('name'=>'placeholder_blocknewsletter')); $_block_repeat=true; echo $_smarty_tpl->smarty->registered_plugins['block']['addJsDefL'][0][0]->addJsDefL(array('name'=>'placeholder_blocknewsletter'), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
<?php echo smartyTranslate(array('s'=>'Enter your e-mail','mod'=>'blocknewsletter','js'=>1),$_smarty_tpl);?>
<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo $_smarty_tpl->smarty->registered_plugins['block']['addJsDefL'][0][0]->addJsDefL(array('name'=>'placeholder_blocknewsletter'), $_block_content, $_smarty_tpl, $_block_repeat); } array_pop($_smarty_tpl->smarty->_tag_stack);?>
<?php $_smarty_tpl->smarty->_tag_stack[] = array('addJsDefL', array('name'=>'email_js_error')); $_block_repeat=true; echo $_smarty_tpl->smarty->registered_plugins['block']['addJsDefL'][0][0]->addJsDefL(array('name'=>'email_js_error'), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
<?php echo smartyTranslate(array('s'=>'Please Enter Valid E-mail','mod'=>'blocknewsletter','js'=>1),$_smarty_tpl);?>
<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo $_smarty_tpl->smarty->registered_plugins['block']['addJsDefL'][0][0]->addJsDefL(array('name'=>'email_js_error'), $_block_content, $_smarty_tpl, $_block_repeat); } array_pop($_smarty_tpl->smarty->_tag_stack);?>
<?php if (isset($_smarty_tpl->tpl_vars['msg']->value)&&$_smarty_tpl->tpl_vars['msg']->value) {?><?php $_smarty_tpl->smarty->_tag_stack[] = array('addJsDefL', array('name'=>'alert_blocknewsletter')); $_block_repeat=true; echo $_smarty_tpl->smarty->registered_plugins['block']['addJsDefL'][0][0]->addJsDefL(array('name'=>'alert_blocknewsletter'), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
<?php echo smartyTranslate(array('s'=>'Newsletter : %1$s','sprintf'=>$_smarty_tpl->tpl_vars['msg']->value,'js'=>1,'mod'=>"blocknewsletter"),$_smarty_tpl);?>
<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo $_smarty_tpl->smarty->registered_plugins['block']['addJsDefL'][0][0]->addJsDefL(array('name'=>'alert_blocknewsletter'), $_block_content, $_smarty_tpl, $_block_repeat); } array_pop($_smarty_tpl->smarty->_tag_stack);?>
<?php }?>

<style type="text/css">
    #footer .clearfix
    {
        background-color: #bf9958;
    }
    .hotel_email
    {
        font-size:18px;
        font-family: 'PT Serif', serif;
        font-weight:400;
        color: #ffffff;
        padding-top: 10px;
    }   
    .hotel_contact
    {
        font-size:18px;
        font-family: 'PT Serif', serif;
        font-weight:400;
        color: #ffffff;
        padding-top: 13px;
    }
    .submitNewsletter
    {
        background-color: #bf9958;
        padding-right: 25px;
        padding-left: 25px;
        color: #ffffff;
        font-size:16px;
        font-family: 'PT Serif', serif;
        font-weight:400;
        border: 2px solid #ffffff;
        margin-left: 30px;
    }
    #newsletter-input
    {
        background-color: #bf9958!important;
        border: none!important;
        border-bottom: 2px solid #ffffff!important;
        color: #ffffff!important;
        font-size:16px!important;
        font-family: 'PT Serif', serif!important;
        font-weight:400!important;
        padding-right: 0px!important;
    }
</style><?php }} ?>
