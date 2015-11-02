<?php /* Smarty version Smarty-3.1.19, created on 2015-11-02 19:40:42
         compiled from "/home/sumit/public_html/html/ps-hotel-reservation-system/modules/wktestimonialblock/views/templates/hook/wktestimonialblock.tpl" */ ?>
<?php /*%%SmartyHeaderCode:76667051456376ee2bb4b96-93199776%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'f96a40c48d931e08a1f5f076e1821defd3280387' => 
    array (
      0 => '/home/sumit/public_html/html/ps-hotel-reservation-system/modules/wktestimonialblock/views/templates/hook/wktestimonialblock.tpl',
      1 => 1446460207,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '76667051456376ee2bb4b96-93199776',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'testimonials_data' => 0,
    'val' => 0,
    'data' => 0,
    'module_dir' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_56376ee2c57583_07140230',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_56376ee2c57583_07140230')) {function content_56376ee2c57583_07140230($_smarty_tpl) {?><hr class="gap-line">
<div class="slideview">
    <!-- Slides -->
    <div class="slideview-content">
        <?php $_smarty_tpl->tpl_vars['val'] = new Smarty_variable(1, null, 0);?>
        <?php if (isset($_smarty_tpl->tpl_vars['testimonials_data']->value)&&$_smarty_tpl->tpl_vars['testimonials_data']->value) {?> 
            <?php  $_smarty_tpl->tpl_vars['data'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['data']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['testimonials_data']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['data']->key => $_smarty_tpl->tpl_vars['data']->value) {
$_smarty_tpl->tpl_vars['data']->_loop = true;
?>
                <div class="slide slide-<?php echo $_smarty_tpl->tpl_vars['val']->value;?>
">
                    <article class="container">
                        <div id="testimonial_block" class="row">
                            <div class="hotel_testimonial_heading">
                                <p><?php echo smartyTranslate(array('s'=>'Guest Testimonials','mod'=>'wktestimonialblock'),$_smarty_tpl);?>
</p>
                            </div>
                            <div class="hotel_testimonial_content">
                                <p><?php echo $_smarty_tpl->tpl_vars['data']->value['testimonial_description'];?>
 </p>
                            </div>
                            <div class="testimonial_container">
                                <div class="testimonial_content row">
                                    <div class='col-sm-3'>
                                        <img src="<?php echo $_smarty_tpl->tpl_vars['module_dir']->value;?>
views/img/icon-double-codes.png" class="img-responsive">
                                    </div>
                                    <div class='col-sm-6 testimonial_block_content_container'>
                                        <p class="testi_block_content"><?php echo $_smarty_tpl->tpl_vars['data']->value['testimonial_content'];?>
</p>
                                    </div>
                                    <div class='col-sm-3'></div>
                                </div>
                                <div class="row person_image">
                                    <div class="col-sm-5"></div>
                                    <div class="col-sm-1">
                                        <?php if (isset($_smarty_tpl->tpl_vars['data']->value['testimonial_image'])&&$_smarty_tpl->tpl_vars['data']->value['testimonial_image']) {?>
                                            <img height="85px" width="85px" src="<?php echo $_smarty_tpl->tpl_vars['module_dir']->value;?>
views/img/<?php echo $_smarty_tpl->tpl_vars['data']->value['testimonial_image'];?>
">
                                        <?php } else { ?>
                                            <img height="85px" width="85px" src="<?php echo $_smarty_tpl->tpl_vars['module_dir']->value;?>
views/img/default.jpg">
                                        <?php }?>
                                    </div>
                                    <div class="col-sm-3 text-left">
                                        <p class="testimonial_person_name"><?php echo $_smarty_tpl->tpl_vars['data']->value['name'];?>
</p>
                                        <p class="testimonial_person_desig"><?php echo $_smarty_tpl->tpl_vars['data']->value['designation'];?>
</p>
                                    </div>
                                    <div class="col-sm-3"></div>
                                </div>
                            </div>
                        </div>
                    </article>
                </div>
                <?php $_smarty_tpl->tpl_vars['val'] = new Smarty_variable($_smarty_tpl->tpl_vars['val']->value+1, null, 0);?>
            <?php } ?>
        <?php }?> 
    </div>
    <!-- <a href="http://www.jqueryscript.net/slider/">Slider</a> controls -->
    <a class="slideview-button slideview-prev" aria-label="Previous"></a> 
    <a class="slideview-button slideview-next" aria-label="Next"></a>
    <div class="slideview-pagination"></div>
</div>

<script>
  $(document).ready(function(){
    $(".slideview").slideview({
      nextButton: '.slideview-next',
      prevButton: '.slideview-prev'
    });
  });
</script><?php }} ?>
