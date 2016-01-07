<?php /* Smarty version Smarty-3.1.19, created on 2016-01-07 11:10:36
         compiled from "/home/sumit/public_html/html/hotel-reservation-system/modules/wktestimonialblock/views/templates/hook/wktestimonialblock.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1819281308568d302b229c93-45908921%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '2c88ebe4dac71024b9b74bd32af87050c2d87e48' => 
    array (
      0 => '/home/sumit/public_html/html/hotel-reservation-system/modules/wktestimonialblock/views/templates/hook/wktestimonialblock.tpl',
      1 => 1452142877,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1819281308568d302b229c93-45908921',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_568d302b259a08_76233646',
  'variables' => 
  array (
    'parent_testimonial_data' => 0,
    'testimonials_data' => 0,
    'module_dir' => 0,
    'data' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_568d302b259a08_76233646')) {function content_568d302b259a08_76233646($_smarty_tpl) {?><hr class="gap-line">

<div class="row" id="testimonial_block">
    <div class="col-xs-12 col-sm-12">
        <div class="hotel_testimonial_heading">
            <p><?php echo $_smarty_tpl->tpl_vars['parent_testimonial_data']->value['testimonial_heading'];?>
 </p>
        </div>
        <div class="hotel_testimonial_content">
            <p><?php echo $_smarty_tpl->tpl_vars['parent_testimonial_data']->value['testimonial_description'];?>
 </p>
        </div>
    </div>
    <div class="col-xs-12 col-sm-12">
        <div class="span12">
            <div id="owl-demo" class="owl-carousel">
                <?php if (isset($_smarty_tpl->tpl_vars['testimonials_data']->value)&&$_smarty_tpl->tpl_vars['testimonials_data']->value) {?>
                    <?php  $_smarty_tpl->tpl_vars['data'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['data']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['testimonials_data']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['data']->key => $_smarty_tpl->tpl_vars['data']->value) {
$_smarty_tpl->tpl_vars['data']->_loop = true;
?>
                        <div>
                            <div class="row margin-lr-0 testimonial_container">
                                
                                <!-- Hide for xs screen -->
                                <div class="row testimonial_content hidden-xs">
                                    <div class='col-sm-offset-1 col-sm-2'>
                                        <img src="<?php echo $_smarty_tpl->tpl_vars['module_dir']->value;?>
views/img/icon-double-codes.png" class="img-responsive">
                                    </div>
                                    <div class='col-sm-7 col-md-6 margin-top-70'>
                                        <p class="testi_block_content"><?php echo $_smarty_tpl->tpl_vars['data']->value['testimonial_content'];?>
</p>
                                    </div>
                                </div>

                                <!-- Visible for xs screen -->
                                <div class="row margin-lr-0 testimonial_content visible-xs">
                                    <div class="row">
                                        <div class='col-xs-3'>
                                            <img src="<?php echo $_smarty_tpl->tpl_vars['module_dir']->value;?>
views/img/icon-double-codes.png" class="img-responsive">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class='col-xs-12'>
                                            <p class="testi_block_content"><?php echo $_smarty_tpl->tpl_vars['data']->value['testimonial_content'];?>
</p>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-xs-12 col-sm-12">
                                        <?php if (isset($_smarty_tpl->tpl_vars['data']->value['testimonial_image'])&&$_smarty_tpl->tpl_vars['data']->value['testimonial_image']) {?>
                                            <img src="<?php echo $_smarty_tpl->tpl_vars['module_dir']->value;?>
views/img/<?php echo $_smarty_tpl->tpl_vars['data']->value['testimonial_image'];?>
" class="testimonial_person_img">
                                        <?php }?>
                                        <p class="testimonial_person_name"><?php echo $_smarty_tpl->tpl_vars['data']->value['name'];?>
</p>
                                        <p class="testimonial_person_desig"><?php echo $_smarty_tpl->tpl_vars['data']->value['designation'];?>
</p>
                                    </div>
                                    <!-- <div class="col-sm-offset-5 col-sm-1">
                                        <?php if (isset($_smarty_tpl->tpl_vars['data']->value['testimonial_image'])&&$_smarty_tpl->tpl_vars['data']->value['testimonial_image']) {?>
                                            <img height="85px" width="85px" src="<?php echo $_smarty_tpl->tpl_vars['module_dir']->value;?>
views/img/<?php echo $_smarty_tpl->tpl_vars['data']->value['testimonial_image'];?>
">
                                        <?php } else { ?>
                                            <img height="85px" width="85px" src="<?php echo $_smarty_tpl->tpl_vars['module_dir']->value;?>
views/img/default.png">
                                        <?php }?>
                                    </div>
                                    <div class="col-sm-3 text-left">
                                        <p class="testimonial_person_name"><?php echo $_smarty_tpl->tpl_vars['data']->value['name'];?>
</p>
                                        <p class="testimonial_person_desig"><?php echo $_smarty_tpl->tpl_vars['data']->value['designation'];?>
</p>
                                    </div> -->
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                <?php }?>
            </div>
        </div>
    </div>
</div>

<style>
    #owl-demo .owl-item div
    {
        padding:5px;
    }
</style>

<script>
    $(document).ready(function()
    {
        $("#owl-demo").owlCarousel({
            autoPlay : 5000,
            stopOnHover : true,
            paginationSpeed : 1000,
            goToFirstSpeed : 2000,
            singleItem : true,
            autoHeight : true,
            pagination :true,
            navigation:false,
            // transitionStyle:"fade"
        });
    });
</script><?php }} ?>
