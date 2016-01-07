<?php /* Smarty version Smarty-3.1.19, created on 2016-01-07 10:45:27
         compiled from "/home/sumit/public_html/html/hotel-reservation-system/themes/hotel-reservation-theme/category.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1648083583568df46fc9bc78-03596770%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '4d6fccd0db0c5f60a8a58685a4dc8cc3142bd7a7' => 
    array (
      0 => '/home/sumit/public_html/html/hotel-reservation-system/themes/hotel-reservation-theme/category.tpl',
      1 => 1452142844,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1648083583568df46fc9bc78-03596770',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'booking_data' => 0,
    'room_v' => 0,
    'foo' => 0,
    'ratting_img' => 0,
    'warning_num' => 0,
    'feat_img_dir' => 0,
    'feat_v' => 0,
    'booking_date_from' => 0,
    'booking_date_to' => 0,
    'num_days' => 0,
    'link' => 0,
    'currency' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_568df46fd37f15_74538529',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_568df46fd37f15_74538529')) {function content_568df46fd37f15_74538529($_smarty_tpl) {?><div class="row cat_cont">
    <div class="col-sm-12">
        <div class="row margin-lr-0 top_filter_cont">
            <div class="col-sm-2 sort_by">
                <p><?php echo smartyTranslate(array('s'=>'Sort By:'),$_smarty_tpl);?>
</p>
            </div>
            <div class="col-sm-3">
                <div class="filter_dw_cont">
                    <button class="btn btn-default dropdown-toggle" type="button" id="gst_rating" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <span class="pull-left sort_btn_span" data-sort-by="0" data-sort-value="0" data-sort-for="<?php echo smartyTranslate(array('s'=>'Rating'),$_smarty_tpl);?>
"><?php echo smartyTranslate(array('s'=>'Rating'),$_smarty_tpl);?>
</span>
                        <span class="caret pull-right margin-top-7"></span>
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="gst_rating">
                        <li><a href="#" class="sort_result" data-sort-by="1" data-value="1"><?php echo smartyTranslate(array('s'=>'Rating Ascending'),$_smarty_tpl);?>
</a></li>
                        <li><a href="#" class="sort_result" data-sort-by="1" data-value="2"><?php echo smartyTranslate(array('s'=>'Rating Descending'),$_smarty_tpl);?>
</a></li>
                    </ul>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="filter_dw_cont">
                    <button class="btn btn-default dropdown-toggle" type="button" id="price_ftr" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <span class="pull-left sort_btn_span" data-sort-by="0" data-sort-value="0" data-sort-for="<?php echo smartyTranslate(array('s'=>'Price'),$_smarty_tpl);?>
"><?php echo smartyTranslate(array('s'=>'Price'),$_smarty_tpl);?>
</span>
                        <span class="caret pull-right margin-top-7"></span>
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="price_ftr">
                        <li><a href="#" class="sort_result" data-sort-by="2" data-value="1"><?php echo smartyTranslate(array('s'=>'Price : Lowest First'),$_smarty_tpl);?>
</a></li>
                        <li><a href="#" class="sort_result" data-sort-by="2" data-value="2"><?php echo smartyTranslate(array('s'=>'Price : Highest first '),$_smarty_tpl);?>
</a></li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="row margin-lr-0" id="category_data_cont">
            <?php  $_smarty_tpl->tpl_vars['room_v'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['room_v']->_loop = false;
 $_smarty_tpl->tpl_vars['room_k'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['booking_data']->value['rm_data']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['room_v']->key => $_smarty_tpl->tpl_vars['room_v']->value) {
$_smarty_tpl->tpl_vars['room_v']->_loop = true;
 $_smarty_tpl->tpl_vars['room_k']->value = $_smarty_tpl->tpl_vars['room_v']->key;
?>
                <div class="col-sm-12 room_cont">
                    <div class="row">
                        <div class="col-sm-4">
                            <a href="<?php echo $_smarty_tpl->tpl_vars['room_v']->value['product_link'];?>
">
                            <img src="<?php echo $_smarty_tpl->tpl_vars['room_v']->value['image'];?>
" class="img-responsive">
                            </a>
                        </div>
                        <div class="col-sm-8">
                            <p class="rm_heading"><?php echo $_smarty_tpl->tpl_vars['room_v']->value['name'];?>
</p>
                            <div class="rm_desc"><?php echo $_smarty_tpl->tpl_vars['room_v']->value['description'];?>
&nbsp;<a href="<?php echo $_smarty_tpl->tpl_vars['room_v']->value['product_link'];?>
"><?php echo smartyTranslate(array('s'=>'View More'),$_smarty_tpl);?>
....</a></div>

                            <p><span class="capa_txt"><?php echo smartyTranslate(array('s'=>'Max Capacity:'),$_smarty_tpl);?>
</span><span class="capa_data"> <?php echo $_smarty_tpl->tpl_vars['room_v']->value['adult'];?>
 <?php echo smartyTranslate(array('s'=>'Adults'),$_smarty_tpl);?>
, <?php echo $_smarty_tpl->tpl_vars['room_v']->value['children'];?>
 <?php echo smartyTranslate(array('s'=>'child'),$_smarty_tpl);?>
</span></p>

                            <div class="rm_review_cont pull-left">
                                <?php $_smarty_tpl->tpl_vars['foo'] = new Smarty_Variable;$_smarty_tpl->tpl_vars['foo']->step = 1;$_smarty_tpl->tpl_vars['foo']->total = (int) ceil(($_smarty_tpl->tpl_vars['foo']->step > 0 ? 5+1 - (1) : 1-(5)+1)/abs($_smarty_tpl->tpl_vars['foo']->step));
if ($_smarty_tpl->tpl_vars['foo']->total > 0) {
for ($_smarty_tpl->tpl_vars['foo']->value = 1, $_smarty_tpl->tpl_vars['foo']->iteration = 1;$_smarty_tpl->tpl_vars['foo']->iteration <= $_smarty_tpl->tpl_vars['foo']->total;$_smarty_tpl->tpl_vars['foo']->value += $_smarty_tpl->tpl_vars['foo']->step, $_smarty_tpl->tpl_vars['foo']->iteration++) {
$_smarty_tpl->tpl_vars['foo']->first = $_smarty_tpl->tpl_vars['foo']->iteration == 1;$_smarty_tpl->tpl_vars['foo']->last = $_smarty_tpl->tpl_vars['foo']->iteration == $_smarty_tpl->tpl_vars['foo']->total;?>
                                    <?php if ($_smarty_tpl->tpl_vars['foo']->value<=$_smarty_tpl->tpl_vars['room_v']->value['ratting']) {?>
                                        <div class="rm_ratting_yes" style="background-image:url(<?php echo $_smarty_tpl->tpl_vars['ratting_img']->value;?>
);"></div>
                                    <?php } else { ?>
                                        <div class="rm_ratting_no" style="background-image:url(<?php echo $_smarty_tpl->tpl_vars['ratting_img']->value;?>
);"></div>
                                    <?php }?>
                                <?php }} ?>
                                <span class="rm_review"><?php echo $_smarty_tpl->tpl_vars['room_v']->value['num_review'];?>
 <?php echo smartyTranslate(array('s'=>'Reviews'),$_smarty_tpl);?>
</span>
                            </div>

                            <span class="rm_left pull-right" <?php if ($_smarty_tpl->tpl_vars['room_v']->value['room_left']>$_smarty_tpl->tpl_vars['warning_num']->value) {?>  style="display:none"<?php }?>><?php echo smartyTranslate(array('s'=>'Hurry!'),$_smarty_tpl);?>
 <span class="cat_remain_rm_qty_<?php echo $_smarty_tpl->tpl_vars['room_v']->value['id_product'];?>
"><?php echo $_smarty_tpl->tpl_vars['room_v']->value['room_left'];?>
</span> <?php echo smartyTranslate(array('s'=>'rooms left'),$_smarty_tpl);?>
</span>

                            <?php if (!empty($_smarty_tpl->tpl_vars['room_v']->value['feature'])) {?>
                                <div class="rm_amenities_cont">
                                    <?php  $_smarty_tpl->tpl_vars['feat_v'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['feat_v']->_loop = false;
 $_smarty_tpl->tpl_vars['feat_k'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['room_v']->value['feature']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['feat_v']->key => $_smarty_tpl->tpl_vars['feat_v']->value) {
$_smarty_tpl->tpl_vars['feat_v']->_loop = true;
 $_smarty_tpl->tpl_vars['feat_k']->value = $_smarty_tpl->tpl_vars['feat_v']->key;
?>
                                        <img src="<?php echo $_smarty_tpl->tpl_vars['feat_img_dir']->value;?>
<?php echo $_smarty_tpl->tpl_vars['feat_v']->value['value'];?>
" class="rm_amen">
                                    <?php } ?>
                                </div>
                            <?php }?>
                            <div class="row margin-lr-0 pull-left rm_price_cont">
                                <span class="pull-left rm_price_val"><?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['displayPrice'][0][0]->displayPriceSmarty(array('price'=>floatVal($_smarty_tpl->tpl_vars['room_v']->value['price'])),$_smarty_tpl);?>
</span><span class="pull-left rm_price_txt">/<?php echo smartyTranslate(array('s'=>'Per Night'),$_smarty_tpl);?>
</span>
                            </div>

                            <a cat_rm_check_in="<?php echo $_smarty_tpl->tpl_vars['booking_date_from']->value;?>
" cat_rm_check_out="<?php echo $_smarty_tpl->tpl_vars['booking_date_to']->value;?>
" href="" rm_product_id="<?php echo $_smarty_tpl->tpl_vars['room_v']->value['id_product'];?>
" cat_rm_book_nm_days="<?php echo $_smarty_tpl->tpl_vars['num_days']->value;?>
" data-id-product-attribute="0" data-id-product="<?php echo intval($_smarty_tpl->tpl_vars['room_v']->value['id_product']);?>
" class="btn rm_book_btn ajax_add_to_cart_button pull-right"><?php echo smartyTranslate(array('s'=>'Book Now'),$_smarty_tpl);?>
</a>

                            <!-- ################################################ -->

                            <div class="rm_qty_cont pull-right clearfix" id="cat_rm_quantity_wanted_<?php echo $_smarty_tpl->tpl_vars['room_v']->value['id_product'];?>
">
                                <span class="qty_txt"><?php echo smartyTranslate(array('s'=>'Qty.'),$_smarty_tpl);?>
:</span>
                                <div class="qty_sec_cont row">
                                    <div class="qty_input_cont row margin-lr-0">
                                        <input autocomplete="off" type="text" min="1" name="qty_<?php echo $_smarty_tpl->tpl_vars['room_v']->value['id_product'];?>
" id="cat_quantity_wanted_<?php echo $_smarty_tpl->tpl_vars['room_v']->value['id_product'];?>
" class="text-center form-control cat_quantity_wanted" value="1" id_room_product="<?php echo $_smarty_tpl->tpl_vars['room_v']->value['id_product'];?>
">
                                    </div>
                                    <div class="qty_direction">
                                        <a href="#" data-room_id_product="<?php echo $_smarty_tpl->tpl_vars['room_v']->value['id_product'];?>
" data-field-qty="qty_<?php echo $_smarty_tpl->tpl_vars['room_v']->value['id_product'];?>
" class="btn btn-default cat_rm_quantity_up">
                                            <span>
                                                <i class="icon-plus"></i>
                                            </span>
                                        </a>
                                        <a href="#" data-field-qty="qty_<?php echo $_smarty_tpl->tpl_vars['room_v']->value['id_product'];?>
" class="btn btn-default cat_rm_quantity_down">
                                            <span>
                                                <i class="icon-minus"></i>
                                            </span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            

                            <!-- <div id="cat_rm_quantity_wanted_<?php echo $_smarty_tpl->tpl_vars['room_v']->value['id_product'];?>
">
                                <a href="#" data-field-qty="qty_<?php echo $_smarty_tpl->tpl_vars['room_v']->value['id_product'];?>
" class="btn btn-default button-minus cat_rm_quantity_down">
                                    <span>
                                        <i class="icon-minus"></i>
                                    </span>
                                </a>
                                <input autocomplete="off" type="text" min="1" name="qty_<?php echo $_smarty_tpl->tpl_vars['room_v']->value['id_product'];?>
" id="cat_quantity_wanted_<?php echo $_smarty_tpl->tpl_vars['room_v']->value['id_product'];?>
" class="text" value="1" />
                                
                                <a href="#" data-room_id_product="<?php echo $_smarty_tpl->tpl_vars['room_v']->value['id_product'];?>
" data-field-qty="qty_<?php echo $_smarty_tpl->tpl_vars['room_v']->value['id_product'];?>
" class="btn btn-default button-plus cat_rm_quantity_up">
                                    <span><i class="icon-plus"></i></span>
                                </a>
                            </div> -->
                            

                            <!-- ################################################ -->
                        </div>
                    </div>
                </div>
            <?php }
if (!$_smarty_tpl->tpl_vars['room_v']->_loop) {
?>
                <div class="noRoomsAvailAlert">
                  <span><?php echo smartyTranslate(array('s'=>'No Rooms Available For this Room Type In this Hotel!'),$_smarty_tpl);?>
</span>
                </div>
            <?php } ?>
        </div>
    </div>
</div>
<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['addJsDef'][0][0]->addJsDef(array('product_controller_url'=>$_smarty_tpl->tpl_vars['link']->value->getPageLink('product')),$_smarty_tpl);?>
<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['addJsDef'][0][0]->addJsDef(array('feat_img_dir'=>$_smarty_tpl->tpl_vars['feat_img_dir']->value),$_smarty_tpl);?>
<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['addJsDef'][0][0]->addJsDef(array('ratting_img'=>$_smarty_tpl->tpl_vars['ratting_img']->value),$_smarty_tpl);?>
<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['addJsDef'][0][0]->addJsDef(array('currency_prefix'=>$_smarty_tpl->tpl_vars['currency']->value->prefix),$_smarty_tpl);?>
<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['addJsDef'][0][0]->addJsDef(array('currency_suffix'=>$_smarty_tpl->tpl_vars['currency']->value->suffix),$_smarty_tpl);?>
<?php }} ?>
