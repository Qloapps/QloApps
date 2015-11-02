<?php /* Smarty version Smarty-3.1.19, created on 2015-11-02 12:11:16
         compiled from "/home/sumit/public_html/html/ps-hotel-reservation-system/modules/wkroomsearchblock/views/templates/hook/roomseachblock.tpl" */ ?>
<?php /*%%SmartyHeaderCode:192427286156376ee3211a15-44619513%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '681462b88d24e2fb258a6459f3a99e654fd84fc4' => 
    array (
      0 => '/home/sumit/public_html/html/ps-hotel-reservation-system/modules/wkroomsearchblock/views/templates/hook/roomseachblock.tpl',
      1 => 1446483103,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '192427286156376ee3211a15-44619513',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_56376ee32cba75_82681361',
  'variables' => 
  array (
    'is_index_page' => 0,
    'header_block_title' => 0,
    'header_block_content' => 0,
    'location_enable' => 0,
    'error' => 0,
    'hotel_name' => 0,
    'name_val' => 0,
    'link' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_56376ee32cba75_82681361')) {function content_56376ee32cba75_82681361($_smarty_tpl) {?><?php if (isset($_smarty_tpl->tpl_vars['is_index_page']->value)&&$_smarty_tpl->tpl_vars['is_index_page']->value) {?>
<div class="row search_block_container">
  <div class="hidden-xs col-sm-7">
    <div class="outer_div">
      <div class="inner_div">
        <div class="block_heading">
          <?php echo $_smarty_tpl->tpl_vars['header_block_title']->value;?>

        </div>
        <div class="block_description">
          <?php echo $_smarty_tpl->tpl_vars['header_block_content']->value;?>

        </div>
      </div>
    </div>
  </div>
  <div class="col-sm-5">
    <div class="search_block">
    <div class="row search-header">
      <div class="search-heading">
        <i class='icon-search'></i>&nbsp&nbsp<?php echo smartyTranslate(array('s'=>'Search Rooms','mod'=>'wkroomsearchblock'),$_smarty_tpl);?>

      </div>
    </div>
    <hr style="border:1px solid #bf9958;margin-top:10px;">
      <form method="POST" id="search_hotel_block_form">
        <?php if (isset($_smarty_tpl->tpl_vars['location_enable']->value)&&$_smarty_tpl->tpl_vars['location_enable']->value) {?>
          <div class="form-group hotel_location_div">
            <label class="control-label" for=""><?php echo smartyTranslate(array('s'=>'Hotel Location','mod'=>'wkroomsearchblock'),$_smarty_tpl);?>
</label>
            <input class="form-control" placeholder="Enter a city, state, country name" type="text" id="hotel_location" name="hotel_location" autocomplete="off"/>
            <ul class="location_search_results_ul">
            </ul>
          </div>
        <?php }?>
        <div class="form-group">
          <label class="control-label" for=""><?php echo smartyTranslate(array('s'=>'Hotel Name','mod'=>'wkroomsearchblock'),$_smarty_tpl);?>
</label>
          <div class="dropdown">
            <button class="btn btn-default dropdown-toggle hotel_cat_id_btn <?php if (isset($_smarty_tpl->tpl_vars['error']->value)&&$_smarty_tpl->tpl_vars['error']->value==1) {?>error_border<?php }?>" type="button" data-toggle="dropdown">
              <span id="hotel_cat_name" class="pull-left"><?php echo smartyTranslate(array('s'=>'Select Hotel'),$_smarty_tpl);?>
</span>
              <input type="hidden" id="hotel_cat_id" name="hotel_cat_id">
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
"><?php echo $_smarty_tpl->tpl_vars['name_val']->value['hotel_name'];?>
</li>
                <?php } ?>
              <?php }?> 
            </ul>
          </div>
          <p class="error_msg" id="select_htl_error_p"><?php if (isset($_smarty_tpl->tpl_vars['error']->value)&&$_smarty_tpl->tpl_vars['error']->value==1) {?><?php echo smartyTranslate(array('s'=>'Please select a hotel.','mod'=>'wkroomsearchblock'),$_smarty_tpl);?>
<?php }?></p>
        </div>

        <div class="form-group">
          <div class="row">
            <div class="col-xs-6 col-md-6 col-sm-12">
              <label class="control-label" for="check_in_time"><?php echo smartyTranslate(array('s'=>'Check In Time','mod'=>'wkroomsearchblock'),$_smarty_tpl);?>
</label>
              <input type="hidden" name="is_hotel_rooms_search" value="1">
              <div class="input-group">
                <input class="form-control <?php if (isset($_smarty_tpl->tpl_vars['error']->value)&&$_smarty_tpl->tpl_vars['error']->value==2) {?>error_border<?php }?>" type="text" id="check_in_time" name="check_in_time" autocomplete="off"/>
                <label class="input-group-addon calender-icon-cont" for="check_in_time"><i class="icon-calendar"></i></label>
              </div>
              <p class="error_msg" id="check_in_time_error_p"><?php if (isset($_smarty_tpl->tpl_vars['error']->value)&&$_smarty_tpl->tpl_vars['error']->value==2) {?><?php echo smartyTranslate(array('s'=>'Check In date is required.','mod'=>'wkroomsearchblock'),$_smarty_tpl);?>
<?php }?></p>
            </div>
            <div class="col-xs-6 col-md-6 col-sm-12">
              <label class="control-label" for="check_out_time"><?php echo smartyTranslate(array('s'=>'Check Out Time','mod'=>'wkroomsearchblock'),$_smarty_tpl);?>
</label>
              <div class="input-group">
                <input class="form-control <?php if (isset($_smarty_tpl->tpl_vars['error']->value)&&($_smarty_tpl->tpl_vars['error']->value==3||$_smarty_tpl->tpl_vars['error']->value==4)) {?>error_border<?php }?>" type="text" id="check_out_time" name="check_out_time" autocomplete="off"/>
                <label class="input-group-addon calender-icon-cont" for="check_out_time"><i class="icon-calendar"></i></label>
              </div>
              <p class="error_msg" id="check_out_time_error_p">
                <?php if (isset($_smarty_tpl->tpl_vars['error']->value)) {?>
                  <?php if (($_smarty_tpl->tpl_vars['error']->value==3)) {?>
                    <?php echo smartyTranslate(array('s'=>'Check Our Date is required.','mod'=>'wkroomsearchblock'),$_smarty_tpl);?>

                  <?php } elseif (($_smarty_tpl->tpl_vars['error']->value==4)) {?>
                    <?php echo smartyTranslate(array('s'=>'check Out date must be greater then check In date.','mod'=>'wkroomsearchblock'),$_smarty_tpl);?>

                  <?php }?>
                <?php }?>
              </p>
            </div>
          </div>
        </div>

        <div>
          <button type="submit" class="btn btn-default button button-medium" name="search_room_submit" id="search_room_submit">
            <span><?php echo smartyTranslate(array('s'=>'Search Now','mod'=>'wkroomsearchblock'),$_smarty_tpl);?>
</span>
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
<?php }?>
<?php $_smarty_tpl->smarty->_tag_stack[] = array('addJsDefL', array('name'=>'no_results_found_cond')); $_block_repeat=true; echo $_smarty_tpl->smarty->registered_plugins['block']['addJsDefL'][0][0]->addJsDefL(array('name'=>'no_results_found_cond'), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
<?php echo smartyTranslate(array('s'=>'No results found for this search','js'=>1,'mod'=>'wkroomsearchblock'),$_smarty_tpl);?>
<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo $_smarty_tpl->smarty->registered_plugins['block']['addJsDefL'][0][0]->addJsDefL(array('name'=>'no_results_found_cond'), $_block_content, $_smarty_tpl, $_block_repeat); } array_pop($_smarty_tpl->smarty->_tag_stack);?>
<?php $_smarty_tpl->smarty->_tag_stack[] = array('addJsDefL', array('name'=>'hotel_loc_cond')); $_block_repeat=true; echo $_smarty_tpl->smarty->registered_plugins['block']['addJsDefL'][0][0]->addJsDefL(array('name'=>'hotel_loc_cond'), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
<?php echo smartyTranslate(array('s'=>'Please enter a hotel location','js'=>1,'mod'=>'wkroomsearchblock'),$_smarty_tpl);?>
<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo $_smarty_tpl->smarty->registered_plugins['block']['addJsDefL'][0][0]->addJsDefL(array('name'=>'hotel_loc_cond'), $_block_content, $_smarty_tpl, $_block_repeat); } array_pop($_smarty_tpl->smarty->_tag_stack);?>
<?php $_smarty_tpl->smarty->_tag_stack[] = array('addJsDefL', array('name'=>'hotel_name_cond')); $_block_repeat=true; echo $_smarty_tpl->smarty->registered_plugins['block']['addJsDefL'][0][0]->addJsDefL(array('name'=>'hotel_name_cond'), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
<?php echo smartyTranslate(array('s'=>'Please select a hotel name','js'=>1,'mod'=>'wkroomsearchblock'),$_smarty_tpl);?>
<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo $_smarty_tpl->smarty->registered_plugins['block']['addJsDefL'][0][0]->addJsDefL(array('name'=>'hotel_name_cond'), $_block_content, $_smarty_tpl, $_block_repeat); } array_pop($_smarty_tpl->smarty->_tag_stack);?>
<?php $_smarty_tpl->smarty->_tag_stack[] = array('addJsDefL', array('name'=>'check_in_time_cond')); $_block_repeat=true; echo $_smarty_tpl->smarty->registered_plugins['block']['addJsDefL'][0][0]->addJsDefL(array('name'=>'check_in_time_cond'), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
<?php echo smartyTranslate(array('s'=>'Please enter Check In time','js'=>1,'mod'=>'wkroomsearchblock'),$_smarty_tpl);?>
<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo $_smarty_tpl->smarty->registered_plugins['block']['addJsDefL'][0][0]->addJsDefL(array('name'=>'check_in_time_cond'), $_block_content, $_smarty_tpl, $_block_repeat); } array_pop($_smarty_tpl->smarty->_tag_stack);?>
<?php $_smarty_tpl->smarty->_tag_stack[] = array('addJsDefL', array('name'=>'check_out_time_cond')); $_block_repeat=true; echo $_smarty_tpl->smarty->registered_plugins['block']['addJsDefL'][0][0]->addJsDefL(array('name'=>'check_out_time_cond'), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
<?php echo smartyTranslate(array('s'=>'Please enter Check Out time','js'=>1,'mod'=>'wkroomsearchblock'),$_smarty_tpl);?>
<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo $_smarty_tpl->smarty->registered_plugins['block']['addJsDefL'][0][0]->addJsDefL(array('name'=>'check_out_time_cond'), $_block_content, $_smarty_tpl, $_block_repeat); } array_pop($_smarty_tpl->smarty->_tag_stack);?>
<?php $_smarty_tpl->smarty->_tag_stack[] = array('addJsDefL', array('name'=>'num_adults_cond')); $_block_repeat=true; echo $_smarty_tpl->smarty->registered_plugins['block']['addJsDefL'][0][0]->addJsDefL(array('name'=>'num_adults_cond'), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
<?php echo smartyTranslate(array('s'=>'Please enter number of adults.','js'=>1,'mod'=>'wkroomsearchblock'),$_smarty_tpl);?>
<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo $_smarty_tpl->smarty->registered_plugins['block']['addJsDefL'][0][0]->addJsDefL(array('name'=>'num_adults_cond'), $_block_content, $_smarty_tpl, $_block_repeat); } array_pop($_smarty_tpl->smarty->_tag_stack);?>
<?php $_smarty_tpl->smarty->_tag_stack[] = array('addJsDefL', array('name'=>'num_children_cond')); $_block_repeat=true; echo $_smarty_tpl->smarty->registered_plugins['block']['addJsDefL'][0][0]->addJsDefL(array('name'=>'num_children_cond'), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
<?php echo smartyTranslate(array('s'=>'Please enter number of children.','js'=>1,'mod'=>'wkroomsearchblock'),$_smarty_tpl);?>
<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo $_smarty_tpl->smarty->registered_plugins['block']['addJsDefL'][0][0]->addJsDefL(array('name'=>'num_children_cond'), $_block_content, $_smarty_tpl, $_block_repeat); } array_pop($_smarty_tpl->smarty->_tag_stack);?>
<?php $_smarty_tpl->smarty->_tag_stack[] = array('addJsDefL', array('name'=>'some_error_occur_cond')); $_block_repeat=true; echo $_smarty_tpl->smarty->registered_plugins['block']['addJsDefL'][0][0]->addJsDefL(array('name'=>'some_error_occur_cond'), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
<?php echo smartyTranslate(array('s'=>'Some error occured. Please try again.','js'=>1,'mod'=>'wkroomsearchblock'),$_smarty_tpl);?>
<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo $_smarty_tpl->smarty->registered_plugins['block']['addJsDefL'][0][0]->addJsDefL(array('name'=>'some_error_occur_cond'), $_block_content, $_smarty_tpl, $_block_repeat); } array_pop($_smarty_tpl->smarty->_tag_stack);?>
<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['addJsDef'][0][0]->addJsDef(array('autocomplete_search_url'=>$_smarty_tpl->tpl_vars['link']->value->getModuleLink('wkroomsearchblock','autocompletesearch')),$_smarty_tpl);?>
<?php }} ?>
