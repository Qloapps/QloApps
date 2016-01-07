<?php /* Smarty version Smarty-3.1.19, created on 2016-01-07 11:09:28
         compiled from "/home/sumit/public_html/html/hotel-reservation-system/themes/hotel-reservation-theme/modules/productcomments//productcomments.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1494326956568d3031d632c5-06837519%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '79bd600f13efd77ad15e0d4d750e2bf1a4466d96' => 
    array (
      0 => '/home/sumit/public_html/html/hotel-reservation-system/themes/hotel-reservation-theme/modules/productcomments//productcomments.tpl',
      1 => 1452142872,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1494326956568d3031d632c5-06837519',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_568d3031e5a2f0_48955327',
  'variables' => 
  array (
    'comments' => 0,
    'too_early' => 0,
    'is_logged' => 0,
    'allow_guests' => 0,
    'product' => 0,
    'productcomment_cover_image' => 0,
    'mediumSize' => 0,
    'criterions' => 0,
    'criterion' => 0,
    'id_product_comment_form' => 0,
    'comment' => 0,
    'productcomments_controller_url' => 0,
    'moderation_active' => 0,
    'productcomments_url_rewriting_activated' => 0,
    'secure_key' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_568d3031e5a2f0_48955327')) {function content_568d3031e5a2f0_48955327($_smarty_tpl) {?>
<div id="idTab5" class="tab-pane">
	<div id="product_comments_block_tab">
		<?php if ($_smarty_tpl->tpl_vars['comments']->value) {?>
			<?php if ((!$_smarty_tpl->tpl_vars['too_early']->value&&($_smarty_tpl->tpl_vars['is_logged']->value||$_smarty_tpl->tpl_vars['allow_guests']->value))) {?>
				<p class="align_center open-comment-block">
					<a id="new_comment_tab_btn" class="btn btn-default open-comment-form" href="#new_comment_form" style="background-color:#bf9958">
						<span><?php echo smartyTranslate(array('s'=>'Make a review','mod'=>'productcomments'),$_smarty_tpl);?>
</span>
					</a>
				</p>

				<div class="new_comment_form_outer" style="display: none;">
					<div id="new_comment_form">
						<form id="id_new_comment_form" action="#">
							<!-- <h2 class="page-subheading">
								<?php echo smartyTranslate(array('s'=>'Write a review','mod'=>'productcomments'),$_smarty_tpl);?>

							</h2> -->
							<div class="row">
								<!-- <?php if (isset($_smarty_tpl->tpl_vars['product']->value)&&$_smarty_tpl->tpl_vars['product']->value) {?>
									<div class="product clearfix  col-xs-12 col-sm-6">
										<img src="<?php echo $_smarty_tpl->tpl_vars['productcomment_cover_image']->value;?>
" height="<?php echo $_smarty_tpl->tpl_vars['mediumSize']->value['height'];?>
" width="<?php echo $_smarty_tpl->tpl_vars['mediumSize']->value['width'];?>
" alt="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['product']->value->name, ENT_QUOTES, 'UTF-8', true);?>
" />
										<div class="product_desc">
											<p class="product_name">
												<strong><?php echo $_smarty_tpl->tpl_vars['product']->value->name;?>
</strong>
											</p>
											<?php echo $_smarty_tpl->tpl_vars['product']->value->description_short;?>

										</div>
									</div>
								<?php }?> -->
								<div class="new_comment_form_content col-xs-12 col-sm-12">
									<div id="new_comment_form_error" class="error" style="display: none;background-color: #cd5d5d;color: #ffffff;font-size: 13px;padding-left: 10px;font-family: 'PT Serif', serif;font-weight:400;">
										<ul></ul>
									</div>
									<label for="comment_title">
										<?php echo smartyTranslate(array('s'=>'Title:','mod'=>'productcomments'),$_smarty_tpl);?>
 <sup class="required">*</sup>
									</label>
									<input id="comment_title" name="title" type="text" value=""/>
									<?php if (count($_smarty_tpl->tpl_vars['criterions']->value)>0) {?>
										<ul id="criterions_list">
										<?php  $_smarty_tpl->tpl_vars['criterion'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['criterion']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['criterions']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['criterion']->key => $_smarty_tpl->tpl_vars['criterion']->value) {
$_smarty_tpl->tpl_vars['criterion']->_loop = true;
?>
											<li>
												<label><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['criterion']->value['name'], ENT_QUOTES, 'UTF-8', true);?>
:</label>
												<div class="star_content">
													<input class="star" type="radio" name="criterion[<?php echo round($_smarty_tpl->tpl_vars['criterion']->value['id_product_comment_criterion']);?>
]" value="1" />
													<input class="star" type="radio" name="criterion[<?php echo round($_smarty_tpl->tpl_vars['criterion']->value['id_product_comment_criterion']);?>
]" value="2" />
													<input class="star" type="radio" name="criterion[<?php echo round($_smarty_tpl->tpl_vars['criterion']->value['id_product_comment_criterion']);?>
]" value="3" />
													<input class="star" type="radio" name="criterion[<?php echo round($_smarty_tpl->tpl_vars['criterion']->value['id_product_comment_criterion']);?>
]" value="4" checked="checked" />
													<input class="star" type="radio" name="criterion[<?php echo round($_smarty_tpl->tpl_vars['criterion']->value['id_product_comment_criterion']);?>
]" value="5" />
												</div>
												<div class="clearfix"></div>
											</li>
										<?php } ?>
										</ul>
									<?php }?>
									<label for="content">
										<?php echo smartyTranslate(array('s'=>'Description:','mod'=>'productcomments'),$_smarty_tpl);?>
 <sup class="required">*</sup>
									</label>
									<textarea id="content" name="content"></textarea>
									<?php if ($_smarty_tpl->tpl_vars['allow_guests']->value==true&&!$_smarty_tpl->tpl_vars['is_logged']->value) {?>
										<label>
											<?php echo smartyTranslate(array('s'=>'Your name:','mod'=>'productcomments'),$_smarty_tpl);?>
 <sup class="required">*</sup>
										</label>
										<input id="commentCustomerName" name="customer_name" type="text" value=""/>
									<?php }?>
									<div id="new_comment_form_footer">
										<input id="id_product_comment_send" name="id_product" type="hidden" value='<?php echo $_smarty_tpl->tpl_vars['id_product_comment_form']->value;?>
' />
										<!-- <p class="fl required"><sup>*</sup> <?php echo smartyTranslate(array('s'=>'Required fields','mod'=>'productcomments'),$_smarty_tpl);?>
</p> -->
										<p class="fr review_submit_div">
											<button id="submitNewMessage" name="submitMessage" type="submit" class="btn btn-default">
												<span><?php echo smartyTranslate(array('s'=>'Make Review','mod'=>'productcomments'),$_smarty_tpl);?>
</span>
											</button>&nbsp;
											<!-- <?php echo smartyTranslate(array('s'=>'or','mod'=>'productcomments'),$_smarty_tpl);?>
&nbsp; -->
											<button id="cancelreview" name="cancelreview" type="submit" class="btn btn-default">
												<span><?php echo smartyTranslate(array('s'=>'Cancel','mod'=>'productcomments'),$_smarty_tpl);?>
</span>
											</button>
										</p>
										<div class="clearfix"></div>
									</div> <!-- #new_comment_form_footer -->
								</div>
							</div>
						</form><!-- /end new_comment_form_content -->
					</div>
				</div>


			<?php }?>
			<hr>
			<?php  $_smarty_tpl->tpl_vars['comment'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['comment']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['comments']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['comment']->key => $_smarty_tpl->tpl_vars['comment']->value) {
$_smarty_tpl->tpl_vars['comment']->_loop = true;
?>
				<?php if ($_smarty_tpl->tpl_vars['comment']->value['content']) {?>
					<div class="reviews_blogs">
						<div class="name_person">
							<span><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['comment']->value['customer_name'], ENT_QUOTES, 'UTF-8', true);?>
</span>
							<span class="comment_date"><em><?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['dateFormat'][0][0]->dateFormat(array('date'=>htmlspecialchars($_smarty_tpl->tpl_vars['comment']->value['date_add'], ENT_QUOTES, 'UTF-8', true),'full'=>0),$_smarty_tpl);?>
</em></span>
						</div>
						<div class="review_container">
							<div class="review_title">
								<p><?php echo $_smarty_tpl->tpl_vars['comment']->value['title'];?>
</p>
							</div>
							<div class="star_content clearfix"  itemprop="reviewRating" itemscope itemtype="http://schema.org/Rating">
								<?php if (isset($_smarty_tpl->tpl_vars['smarty']->value['section']["i"])) unset($_smarty_tpl->tpl_vars['smarty']->value['section']["i"]);
$_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['name'] = "i";
$_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['start'] = (int) 0;
$_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['loop'] = is_array($_loop=5) ? count($_loop) : max(0, (int) $_loop); unset($_loop);
$_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['step'] = ((int) 1) == 0 ? 1 : (int) 1;
$_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['show'] = true;
$_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['max'] = $_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['loop'];
if ($_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['start'] < 0)
    $_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['start'] = max($_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['step'] > 0 ? 0 : -1, $_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['loop'] + $_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['start']);
else
    $_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['start'] = min($_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['start'], $_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['step'] > 0 ? $_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['loop'] : $_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['loop']-1);
if ($_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['show']) {
    $_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['total'] = min(ceil(($_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['step'] > 0 ? $_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['loop'] - $_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['start'] : $_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['start']+1)/abs($_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['step'])), $_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['max']);
    if ($_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['total'] == 0)
        $_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['show'] = false;
} else
    $_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['total'] = 0;
if ($_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['show']):

            for ($_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['index'] = $_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['start'], $_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['iteration'] = 1;
                 $_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['iteration'] <= $_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['total'];
                 $_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['index'] += $_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['step'], $_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['iteration']++):
$_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['rownum'] = $_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['iteration'];
$_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['index_prev'] = $_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['index'] - $_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['step'];
$_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['index_next'] = $_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['index'] + $_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['step'];
$_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['first']      = ($_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['iteration'] == 1);
$_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['last']       = ($_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['iteration'] == $_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['total']);
?>
									<?php if ($_smarty_tpl->tpl_vars['comment']->value['grade']<=$_smarty_tpl->getVariable('smarty')->value['section']['i']['index']) {?>
										<div class="star"></div>
									<?php } else { ?>
										<div class="star star_on"></div>
									<?php }?>
								<?php endfor; endif; ?>
		        				<meta itemprop="worstRating" content = "0" />
								<meta itemprop="ratingValue" content = "<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['comment']->value['grade'], ENT_QUOTES, 'UTF-8', true);?>
" />
		        				<meta itemprop="bestRating" content = "5" />
							</div>
							<div class="review_content">
								<p itemprop="reviewBody"><?php echo nl2br(htmlspecialchars($_smarty_tpl->tpl_vars['comment']->value['content'], ENT_QUOTES, 'UTF-8', true));?>
</p>
									<ul>
										<?php if ($_smarty_tpl->tpl_vars['comment']->value['total_advice']>0) {?>
											<li>
												<?php echo smartyTranslate(array('s'=>'%1$d out of %2$d people found this review useful.','sprintf'=>array($_smarty_tpl->tpl_vars['comment']->value['total_useful'],$_smarty_tpl->tpl_vars['comment']->value['total_advice']),'mod'=>'productcomments'),$_smarty_tpl);?>

											</li>
										<?php }?>
										<?php if ($_smarty_tpl->tpl_vars['is_logged']->value) {?>
											<?php if (!$_smarty_tpl->tpl_vars['comment']->value['customer_advice']) {?>
											<li>
												<?php echo smartyTranslate(array('s'=>'Was this comment useful to you?','mod'=>'productcomments'),$_smarty_tpl);?>

												<button class="usefulness_btn btn btn-default button button-small" data-is-usefull="1" data-id-product-comment="<?php echo $_smarty_tpl->tpl_vars['comment']->value['id_product_comment'];?>
">
													<span><?php echo smartyTranslate(array('s'=>'Yes','mod'=>'productcomments'),$_smarty_tpl);?>
</span>
												</button>
												<button class="usefulness_btn btn btn-default button button-small" data-is-usefull="0" data-id-product-comment="<?php echo $_smarty_tpl->tpl_vars['comment']->value['id_product_comment'];?>
">
													<span><?php echo smartyTranslate(array('s'=>'No','mod'=>'productcomments'),$_smarty_tpl);?>
</span>
												</button>
											</li>
											<?php }?>
											<?php if (!$_smarty_tpl->tpl_vars['comment']->value['customer_report']) {?>
											<li>
												<span class="report_btn" data-id-product-comment="<?php echo $_smarty_tpl->tpl_vars['comment']->value['id_product_comment'];?>
">
													<?php echo smartyTranslate(array('s'=>'Report abuse','mod'=>'productcomments'),$_smarty_tpl);?>

												</span>
											</li>
											<?php }?>
										<?php }?>
									</ul>
								</p><!-- .comment_details -->
							</div>
						</div>
					</div>
				<?php }?>
			<?php } ?>
		<?php } else { ?>
			<?php if ((!$_smarty_tpl->tpl_vars['too_early']->value&&($_smarty_tpl->tpl_vars['is_logged']->value||$_smarty_tpl->tpl_vars['allow_guests']->value))) {?>
			<p class="align_center">
				<a id="new_comment_tab_btn" class="btn btn-default open-comment-form" href="#new_comment_form">
					<span><?php echo smartyTranslate(array('s'=>'Be the first to write your review!','mod'=>'productcomments'),$_smarty_tpl);?>
</span>
				</a>
			</p>
			<div class="new_comment_form_outer" style="display: none;">
				<div id="new_comment_form">
					<form id="id_new_comment_form" action="#">
						<!-- <h2 class="page-subheading">
							<?php echo smartyTranslate(array('s'=>'Write a review','mod'=>'productcomments'),$_smarty_tpl);?>

						</h2> -->
						<div class="row">
							<!-- <?php if (isset($_smarty_tpl->tpl_vars['product']->value)&&$_smarty_tpl->tpl_vars['product']->value) {?>
								<div class="product clearfix  col-xs-12 col-sm-6">
									<img src="<?php echo $_smarty_tpl->tpl_vars['productcomment_cover_image']->value;?>
" height="<?php echo $_smarty_tpl->tpl_vars['mediumSize']->value['height'];?>
" width="<?php echo $_smarty_tpl->tpl_vars['mediumSize']->value['width'];?>
" alt="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['product']->value->name, ENT_QUOTES, 'UTF-8', true);?>
" />
									<div class="product_desc">
										<p class="product_name">
											<strong><?php echo $_smarty_tpl->tpl_vars['product']->value->name;?>
</strong>
										</p>
										<?php echo $_smarty_tpl->tpl_vars['product']->value->description_short;?>

									</div>
								</div>
							<?php }?> -->
							<div class="new_comment_form_content col-xs-12 col-sm-12">
								<div id="new_comment_form_error" class="error" style="display: none;background-color: #cd5d5d;color: #ffffff;font-size: 13px;padding-left: 10px;font-family: 'PT Serif', serif;font-weight:400;">
									<ul></ul>
								</div>
								<label for="comment_title">
									<?php echo smartyTranslate(array('s'=>'Title:','mod'=>'productcomments'),$_smarty_tpl);?>
 <sup class="required">*</sup>
								</label>
								<input id="comment_title" name="title" type="text" value=""/>
								<?php if (count($_smarty_tpl->tpl_vars['criterions']->value)>0) {?>
									<ul id="criterions_list">
									<?php  $_smarty_tpl->tpl_vars['criterion'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['criterion']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['criterions']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['criterion']->key => $_smarty_tpl->tpl_vars['criterion']->value) {
$_smarty_tpl->tpl_vars['criterion']->_loop = true;
?>
										<li>
											<label><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['criterion']->value['name'], ENT_QUOTES, 'UTF-8', true);?>
:</label>
											<div class="star_content">
												<input class="star" type="radio" name="criterion[<?php echo round($_smarty_tpl->tpl_vars['criterion']->value['id_product_comment_criterion']);?>
]" value="1" />
												<input class="star" type="radio" name="criterion[<?php echo round($_smarty_tpl->tpl_vars['criterion']->value['id_product_comment_criterion']);?>
]" value="2" />
												<input class="star" type="radio" name="criterion[<?php echo round($_smarty_tpl->tpl_vars['criterion']->value['id_product_comment_criterion']);?>
]" value="3" />
												<input class="star" type="radio" name="criterion[<?php echo round($_smarty_tpl->tpl_vars['criterion']->value['id_product_comment_criterion']);?>
]" value="4" checked="checked" />
												<input class="star" type="radio" name="criterion[<?php echo round($_smarty_tpl->tpl_vars['criterion']->value['id_product_comment_criterion']);?>
]" value="5" />
											</div>
											<div class="clearfix"></div>
										</li>
									<?php } ?>
									</ul>
								<?php }?>
								<label for="content">
									<?php echo smartyTranslate(array('s'=>'Description:','mod'=>'productcomments'),$_smarty_tpl);?>
 <sup class="required">*</sup>
								</label>
								<textarea id="content" name="content"></textarea>
								<?php if ($_smarty_tpl->tpl_vars['allow_guests']->value==true&&!$_smarty_tpl->tpl_vars['is_logged']->value) {?>
									<label>
										<?php echo smartyTranslate(array('s'=>'Your name:','mod'=>'productcomments'),$_smarty_tpl);?>
 <sup class="required">*</sup>
									</label>
									<input id="commentCustomerName" name="customer_name" type="text" value=""/>
								<?php }?>
								<div id="new_comment_form_footer">
									<input id="id_product_comment_send" name="id_product" type="hidden" value='<?php echo $_smarty_tpl->tpl_vars['id_product_comment_form']->value;?>
' />
									<!-- <p class="fl required"><sup>*</sup> <?php echo smartyTranslate(array('s'=>'Required fields','mod'=>'productcomments'),$_smarty_tpl);?>
</p> -->
									<p class="fr review_submit_div">
										<button id="submitNewMessage" name="submitMessage" type="submit" class="btn btn-default">
											<span><?php echo smartyTranslate(array('s'=>'Make Review','mod'=>'productcomments'),$_smarty_tpl);?>
</span>
										</button>&nbsp;
										<!-- <?php echo smartyTranslate(array('s'=>'or','mod'=>'productcomments'),$_smarty_tpl);?>
&nbsp; -->
										<button id="cancelreview" name="cancelreview" type="submit" class="btn btn-default">
											<span><?php echo smartyTranslate(array('s'=>'Cancel','mod'=>'productcomments'),$_smarty_tpl);?>
</span>
										</button>
									</p>
									<div class="clearfix"></div>
								</div> <!-- #new_comment_form_footer -->
							</div>
						</div>
					</form><!-- /end new_comment_form_content -->
				</div>
			</div>
			<?php } else { ?>
			<p class="align_center no_reviews_cond_block"><?php echo smartyTranslate(array('s'=>'No customer reviews for the moment.','mod'=>'productcomments'),$_smarty_tpl);?>
</p>
			<?php }?>
		<?php }?>
	</div> <!-- #product_comments_block_tab -->
</div>
<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['addJsDef'][0][0]->addJsDef(array('productcomments_controller_url'=>addcslashes($_smarty_tpl->tpl_vars['productcomments_controller_url']->value,'\'')),$_smarty_tpl);?>
<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['addJsDef'][0][0]->addJsDef(array('moderation_active'=>$_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_MODIFIER]['boolval'][0][0]->boolval($_smarty_tpl->tpl_vars['moderation_active']->value)),$_smarty_tpl);?>
<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['addJsDef'][0][0]->addJsDef(array('productcomments_url_rewrite'=>$_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_MODIFIER]['boolval'][0][0]->boolval($_smarty_tpl->tpl_vars['productcomments_url_rewriting_activated']->value)),$_smarty_tpl);?>
<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['addJsDef'][0][0]->addJsDef(array('secure_key'=>$_smarty_tpl->tpl_vars['secure_key']->value),$_smarty_tpl);?>
<?php $_smarty_tpl->smarty->_tag_stack[] = array('addJsDefL', array('name'=>'confirm_report_message')); $_block_repeat=true; echo $_smarty_tpl->smarty->registered_plugins['block']['addJsDefL'][0][0]->addJsDefL(array('name'=>'confirm_report_message'), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
<?php echo smartyTranslate(array('s'=>'Are you sure that you want to report this comment?','mod'=>'productcomments','js'=>1),$_smarty_tpl);?>
<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo $_smarty_tpl->smarty->registered_plugins['block']['addJsDefL'][0][0]->addJsDefL(array('name'=>'confirm_report_message'), $_block_content, $_smarty_tpl, $_block_repeat); } array_pop($_smarty_tpl->smarty->_tag_stack);?>
<?php $_smarty_tpl->smarty->_tag_stack[] = array('addJsDefL', array('name'=>'productcomment_added')); $_block_repeat=true; echo $_smarty_tpl->smarty->registered_plugins['block']['addJsDefL'][0][0]->addJsDefL(array('name'=>'productcomment_added'), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
<?php echo smartyTranslate(array('s'=>'Your comment has been added!','mod'=>'productcomments','js'=>1),$_smarty_tpl);?>
<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo $_smarty_tpl->smarty->registered_plugins['block']['addJsDefL'][0][0]->addJsDefL(array('name'=>'productcomment_added'), $_block_content, $_smarty_tpl, $_block_repeat); } array_pop($_smarty_tpl->smarty->_tag_stack);?>
<?php $_smarty_tpl->smarty->_tag_stack[] = array('addJsDefL', array('name'=>'productcomment_added_moderation')); $_block_repeat=true; echo $_smarty_tpl->smarty->registered_plugins['block']['addJsDefL'][0][0]->addJsDefL(array('name'=>'productcomment_added_moderation'), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
<?php echo smartyTranslate(array('s'=>'Your comment has been added and will be available once approved by a moderator.','mod'=>'productcomments','js'=>1),$_smarty_tpl);?>
<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo $_smarty_tpl->smarty->registered_plugins['block']['addJsDefL'][0][0]->addJsDefL(array('name'=>'productcomment_added_moderation'), $_block_content, $_smarty_tpl, $_block_repeat); } array_pop($_smarty_tpl->smarty->_tag_stack);?>
<?php $_smarty_tpl->smarty->_tag_stack[] = array('addJsDefL', array('name'=>'productcomment_title')); $_block_repeat=true; echo $_smarty_tpl->smarty->registered_plugins['block']['addJsDefL'][0][0]->addJsDefL(array('name'=>'productcomment_title'), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
<?php echo smartyTranslate(array('s'=>'New comment','mod'=>'productcomments','js'=>1),$_smarty_tpl);?>
<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo $_smarty_tpl->smarty->registered_plugins['block']['addJsDefL'][0][0]->addJsDefL(array('name'=>'productcomment_title'), $_block_content, $_smarty_tpl, $_block_repeat); } array_pop($_smarty_tpl->smarty->_tag_stack);?>
<?php $_smarty_tpl->smarty->_tag_stack[] = array('addJsDefL', array('name'=>'productcomment_ok')); $_block_repeat=true; echo $_smarty_tpl->smarty->registered_plugins['block']['addJsDefL'][0][0]->addJsDefL(array('name'=>'productcomment_ok'), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
<?php echo smartyTranslate(array('s'=>'OK','mod'=>'productcomments','js'=>1),$_smarty_tpl);?>
<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo $_smarty_tpl->smarty->registered_plugins['block']['addJsDefL'][0][0]->addJsDefL(array('name'=>'productcomment_ok'), $_block_content, $_smarty_tpl, $_block_repeat); } array_pop($_smarty_tpl->smarty->_tag_stack);?>

<style type="text/css">
	.open-comment-form
	{
		background-color:#BF9958;
		color:#ffffff!important;
		font-size:18px;
		font-family: 'PT Serif', serif;
		font-weight:400;
		padding-top: 10px;
		padding-right: 20px;
		padding-bottom: 10px;
		padding-left: 20px;
	}
	.no_reviews_cond_block
	{
		olor: #404040;
		font-size:15px;
		font-family: 'PT Serif', serif;
		font-weight:400;
	}
	.name_person
	{
		padding: 10px;
		color: #404040;
		font-size:15px;
		font-family: 'PT Serif', serif;
		font-weight:700;
	}
	.review_title
	{
		color: #404040;
		font-size:15px;
		font-family: 'PT Serif', serif;
		font-weight:700;
	}
	.review_content
	{
		color: #404040;
		font-size:15px;
		font-family: 'PT Serif', serif;
		font-weight:400;
		line-height: 23px;
		padding-top: 10px;
	}
	.review_container
	{
		padding:20px;
		border: 1px solid #cccccc;
	}
	.reviews_blogs
	{
		margin-bottom: 25px;
	}
	.comment_date
	{
		font-size:15px;
		font-family: 'PT Serif', serif;
		font-weight:400;
		color: #9A9A9A;
		margin-left: 10px;
	}
	.review_submit_div
	{
		float: left!important;
	}
	.cancel, .star
	{
		width: 25px;
		font-size: 21px;
		height: 21px;

	}

	.review_container
	{
		border: 1px solid #cccccc;
	    padding: 20px;
	    position: relative;
	}
	.review_container:after, .review_container:before
	{
		border: medium solid transparent;
	    bottom: 100%;
	    content: " ";
	    height: 0;
	    left: 7%;
	    pointer-events: none;
	    position: absolute;
	    width: 0;
	}

	.review_container:after
	{
		border-color: rgba(136, 183, 213, 0) rgba(136, 183, 213, 0) #ffffff;
	    border-width: 9px;
	    margin-left: -22px;
	}
	.review_container:before
	{
		border-color: rgba(245, 233, 171, 0) rgba(245, 233, 171, 0) #cccccc;
	    border-width: 11px;
	    margin-left: -24px;
	}

</style>
<?php }} ?>
