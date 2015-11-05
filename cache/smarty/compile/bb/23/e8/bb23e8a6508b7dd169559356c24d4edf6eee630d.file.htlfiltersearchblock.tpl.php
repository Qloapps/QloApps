<?php /* Smarty version Smarty-3.1.19, created on 2015-11-03 09:00:30
         compiled from "/home/sumit/public_html/html/ps-hotel-reservation-system/modules/wkhotelfiltersearchblock/views/templates/hook/htlfiltersearchblock.tpl" */ ?>
<?php /*%%SmartyHeaderCode:3747999925638bdfe08b507-86810932%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'bb23e8a6508b7dd169559356c24d4edf6eee630d' => 
    array (
      0 => '/home/sumit/public_html/html/ps-hotel-reservation-system/modules/wkhotelfiltersearchblock/views/templates/hook/htlfiltersearchblock.tpl',
      1 => 1446483103,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '3747999925638bdfe08b507-86810932',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'booking_data' => 0,
    'error' => 0,
    'location_enable' => 0,
    'search_data' => 0,
    'hotel_name' => 0,
    'name_val' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_5638bdfe13e195_94129016',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5638bdfe13e195_94129016')) {function content_5638bdfe13e195_94129016($_smarty_tpl) {?><!-- <?php echo $_smarty_tpl->tpl_vars['booking_data']->value['stats']['num_avail'];?>
 -->
<!-- <?php echo $_smarty_tpl->tpl_vars['booking_data']->value['stats']['total_rooms'];?>
 -->
<div class="row margin-lr-0 block" id="filter_search_block">
    <div class="filter_header">
        <div class="col-sm-12">
            <p><?php echo smartyTranslate(array('s'=>'Search Rooms','mod'=>'wkhotelfiltersearchblock'),$_smarty_tpl);?>
</p>
        </div>
    </div>
    <div class="col-sm-12 category_page_search_block clear-both">
        <form method="POST" autocomplete="on" autofill="on">
            
            <?php if (isset($_smarty_tpl->tpl_vars['error']->value)&&($_smarty_tpl->tpl_vars['error']->value==1)) {?>
                <p class="error_msg"><i class="icon-times-circle-o"></i>&nbsp;&nbsp;<?php echo smartyTranslate(array('s'=>'All Fields are mandatory.','mod'=>'wkroomsearchblock'),$_smarty_tpl);?>
</p>
            <?php }?>
            
            <?php if (isset($_smarty_tpl->tpl_vars['location_enable']->value)&&$_smarty_tpl->tpl_vars['location_enable']->value) {?>
                <div class="form-group hotel_location_div">
                    <label class="control-label" for=""><?php echo smartyTranslate(array('s'=>'Hotel Location','mod'=>'wkroomsearchblock'),$_smarty_tpl);?>
</label>
                    <input class="form-control" placeholder="Enter a city, state, country name" type="text" id="hotel_location" name="hotel_location" autocomplete="off" <?php if (isset($_smarty_tpl->tpl_vars['search_data']->value)) {?>value="<?php echo $_smarty_tpl->tpl_vars['search_data']->value['parent_data']['name'];?>
" city_cat_id="<?php echo $_smarty_tpl->tpl_vars['search_data']->value['parent_data']['id_category'];?>
"<?php }?>/>
                    <ul class="location_search_results_ul"></ul>
                </div>
            <?php }?>
            <div class="form-group htl_nm_cont">
                <label class="control-label" for=""><?php echo smartyTranslate(array('s'=>'Hotel Name','mod'=>'wkroomsearchblock'),$_smarty_tpl);?>
</label>
                <div class="dropdown">
                    <button class="btn btn-default hotel_cat_id_btn dropdown-toggle" type="button" data-toggle="dropdown">
                        <?php if (isset($_smarty_tpl->tpl_vars['search_data']->value)) {?>
                            <span id="hotel_cat_name" class="pull-left"><?php echo $_smarty_tpl->tpl_vars['search_data']->value['htl_dtl']['hotel_name'];?>
</span>
                        <?php } else { ?>
                            <span id="hotel_cat_name" class="pull-left"><?php echo smartyTranslate(array('s'=>'Select Hotel'),$_smarty_tpl);?>
</span>
                        <?php }?>
                        <input type="hidden" id="hotel_cat_id" name="hotel_cat_id" <?php if (isset($_smarty_tpl->tpl_vars['search_data']->value)) {?>value="<?php echo $_smarty_tpl->tpl_vars['search_data']->value['htl_dtl']['id_category'];?>
"<?php }?>>
                        <div class="caret_div">
                            <span class="caret"></span>
                        </div>
                    </button>
                    <ul class="dropdown-menu hotel_dropdown_ul">
                        <?php if (isset($_smarty_tpl->tpl_vars['hotel_name']->value)&&$_smarty_tpl->tpl_vars['hotel_name']->value) {?>
                            <?php  $_smarty_tpl->tpl_vars['name_val'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['name_val']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['hotel_name']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['name_val']->key => $_smarty_tpl->tpl_vars['name_val']->value) {
$_smarty_tpl->tpl_vars['name_val']->_loop = true;
?>
                                <li class="hotel_name" data-hotel-cat-id="<?php echo $_smarty_tpl->tpl_vars['name_val']->value['id_category'];?>
">
                                    <?php echo $_smarty_tpl->tpl_vars['name_val']->value['hotel_name'];?>

                                </li>
                            <?php } ?>
                        <?php }?> 
                    </ul>
                </div>
                <p class="error_msg" id="select_htl_error_p"></p>
            </div>
            <div class="form-group">
                <div class="row">
                    <div class="col-xs-12 col-sm-12">
                        <label class="control-label" for="check_in_time"><?php echo smartyTranslate(array('s'=>'Check In Time','mod'=>'wkroomsearchblock'),$_smarty_tpl);?>
</label>
                        <div class="input-group">
                            <input class="form-control" type="text" id="check_in_time" name="check_in_time" <?php if (isset($_smarty_tpl->tpl_vars['search_data']->value)) {?>value="<?php echo $_smarty_tpl->tpl_vars['search_data']->value['date_from'];?>
"<?php }?>/>
                            <label class="input-group-addon" for="check_in_time"><i class="icon-calendar"></i></label>
                        </div>
                        <p class="error_msg" id="check_in_time_error_p"></p>
                    </div>
                    <div class="col-xs-12 col-sm-12 margin-top-10">
                        <label class="control-label" for="check_out_time"><?php echo smartyTranslate(array('s'=>'Check Out Time','mod'=>'wkroomsearchblock'),$_smarty_tpl);?>
</label>
                        <div class="input-group">
                            <input class="form-control" type="text" id="check_out_time" name="check_out_time" <?php if (isset($_smarty_tpl->tpl_vars['search_data']->value)) {?>value="<?php echo $_smarty_tpl->tpl_vars['search_data']->value['date_to'];?>
"<?php }?> />
                            <label class="input-group-addon" for="check_out_time"><i class="icon-calendar"></i></label>
                        </div>
                        <p class="error_msg" id="check_out_time_error_p"></p>
                    </div>
                </div>
            </div>
            <div>
                <button type="submit" name="filter_search_btn" class="btn filter_search_btn col-sm-12" id="filter_search_btn">
                    <span><?php echo smartyTranslate(array('s'=>'Search','mod'=>'wkhotelfiltersearchblock'),$_smarty_tpl);?>
</span>
                </button>
            </div>
        </form>
    </div>
</div>
<?php $_smarty_tpl->smarty->_tag_stack[] = array('addJsDefL', array('name'=>'hotel_name_cond')); $_block_repeat=true; echo $_smarty_tpl->smarty->registered_plugins['block']['addJsDefL'][0][0]->addJsDefL(array('name'=>'hotel_name_cond'), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
<?php echo smartyTranslate(array('s'=>'Please select a hotel name','js'=>1,'mod'=>'wkroomsearchblock'),$_smarty_tpl);?>
<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo $_smarty_tpl->smarty->registered_plugins['block']['addJsDefL'][0][0]->addJsDefL(array('name'=>'hotel_name_cond'), $_block_content, $_smarty_tpl, $_block_repeat); } array_pop($_smarty_tpl->smarty->_tag_stack);?>
<?php $_smarty_tpl->smarty->_tag_stack[] = array('addJsDefL', array('name'=>'check_in_time_cond')); $_block_repeat=true; echo $_smarty_tpl->smarty->registered_plugins['block']['addJsDefL'][0][0]->addJsDefL(array('name'=>'check_in_time_cond'), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
<?php echo smartyTranslate(array('s'=>'Please enter Check In time','js'=>1,'mod'=>'wkroomsearchblock'),$_smarty_tpl);?>
<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo $_smarty_tpl->smarty->registered_plugins['block']['addJsDefL'][0][0]->addJsDefL(array('name'=>'check_in_time_cond'), $_block_content, $_smarty_tpl, $_block_repeat); } array_pop($_smarty_tpl->smarty->_tag_stack);?>
<?php $_smarty_tpl->smarty->_tag_stack[] = array('addJsDefL', array('name'=>'check_out_time_cond')); $_block_repeat=true; echo $_smarty_tpl->smarty->registered_plugins['block']['addJsDefL'][0][0]->addJsDefL(array('name'=>'check_out_time_cond'), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
<?php echo smartyTranslate(array('s'=>'Please enter Check Out time','js'=>1,'mod'=>'wkroomsearchblock'),$_smarty_tpl);?>
<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo $_smarty_tpl->smarty->registered_plugins['block']['addJsDefL'][0][0]->addJsDefL(array('name'=>'check_out_time_cond'), $_block_content, $_smarty_tpl, $_block_repeat); } array_pop($_smarty_tpl->smarty->_tag_stack);?>
<?php }} ?>
