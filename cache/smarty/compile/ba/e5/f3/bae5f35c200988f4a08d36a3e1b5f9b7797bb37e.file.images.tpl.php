<?php /* Smarty version Smarty-3.1.19, created on 2015-11-05 03:29:09
         compiled from "/home/sumit/public_html/html/ps-hotel-reservation-system/admin584hec64n/themes/default/template/controllers/products/images.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1902164452563b13550b1917-81275188%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'bae5f35c200988f4a08d36a3e1b5f9b7797bb37e' => 
    array (
      0 => '/home/sumit/public_html/html/ps-hotel-reservation-system/admin584hec64n/themes/default/template/controllers/products/images.tpl',
      1 => 1446483944,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1902164452563b13550b1917-81275188',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'id_product' => 0,
    'product' => 0,
    'countImages' => 0,
    'max_image_size' => 0,
    'id_image' => 0,
    'image_uploader' => 0,
    'languages' => 0,
    'language' => 0,
    'input_class' => 0,
    'images' => 0,
    'image' => 0,
    'shops' => 0,
    'shop' => 0,
    'iso_lang' => 0,
    'imageType' => 0,
    'link' => 0,
    'table' => 0,
    'current_shop_id' => 0,
    'default_language' => 0,
    'id_category_default' => 0,
    'token' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_563b13551b41b1_47029861',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_563b13551b41b1_47029861')) {function content_563b13551b41b1_47029861($_smarty_tpl) {?>

<?php if (isset($_smarty_tpl->tpl_vars['id_product']->value)&&isset($_smarty_tpl->tpl_vars['product']->value)) {?>
<div id="product-images" class="panel product-tab">
	<input type="hidden" name="submitted_tabs[]" value="Images" />
	<div class="panel-heading tab" >
		<?php echo smartyTranslate(array('s'=>'Images'),$_smarty_tpl);?>

		<span class="badge" id="countImage"><?php echo $_smarty_tpl->tpl_vars['countImages']->value;?>
</span>
	</div>
	<div class="row">
		<div class="form-group">
			<label class="control-label col-lg-3 file_upload_label">
				<span class="label-tooltip" data-toggle="tooltip"
					title="<?php echo smartyTranslate(array('s'=>'Format:'),$_smarty_tpl);?>
 JPG, GIF, PNG. <?php echo smartyTranslate(array('s'=>'Filesize:'),$_smarty_tpl);?>
 <?php echo sprintf("%.2f",$_smarty_tpl->tpl_vars['max_image_size']->value);?>
 <?php echo smartyTranslate(array('s'=>'MB max.'),$_smarty_tpl);?>
">
					<?php if (isset($_smarty_tpl->tpl_vars['id_image']->value)) {?><?php echo smartyTranslate(array('s'=>'Edit this product\'s image:'),$_smarty_tpl);?>
<?php } else { ?><?php echo smartyTranslate(array('s'=>'Add a new image to this product'),$_smarty_tpl);?>
<?php }?>
				</span>
			</label>
			<div class="col-lg-9">
				<?php echo $_smarty_tpl->tpl_vars['image_uploader']->value;?>

			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-lg-3">
				<span class="label-tooltip" data-toggle="tooltip"
					title="<?php echo smartyTranslate(array('s'=>'Update all captions at once, or select the position of the image whose caption you wish to edit. Invalid characters: %s','sprintf'=>array('<>;=#{}')),$_smarty_tpl);?>
">
					<?php echo smartyTranslate(array('s'=>'Caption'),$_smarty_tpl);?>

				</span>
			</label>
			<div class="col-lg-4">
			<?php  $_smarty_tpl->tpl_vars['language'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['language']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['languages']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['language']->key => $_smarty_tpl->tpl_vars['language']->value) {
$_smarty_tpl->tpl_vars['language']->_loop = true;
?>
				<?php if (count($_smarty_tpl->tpl_vars['languages']->value)>1) {?>
				<div class="translatable-field row lang-<?php echo $_smarty_tpl->tpl_vars['language']->value['id_lang'];?>
">
					<div class="col-lg-8">
				<?php }?>
						<input type="text" id="legend_<?php echo $_smarty_tpl->tpl_vars['language']->value['id_lang'];?>
"<?php if (isset($_smarty_tpl->tpl_vars['input_class']->value)) {?> class="<?php echo $_smarty_tpl->tpl_vars['input_class']->value;?>
"<?php }?> name="legend_<?php echo $_smarty_tpl->tpl_vars['language']->value['id_lang'];?>
" value="<?php if (count($_smarty_tpl->tpl_vars['images']->value)) {?><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['images']->value[0]->legend[$_smarty_tpl->tpl_vars['language']->value['id_lang']], ENT_QUOTES, 'UTF-8', true);?>
<?php } else { ?><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['product']->value->name[$_smarty_tpl->tpl_vars['language']->value['id_lang']], ENT_QUOTES, 'UTF-8', true);?>
<?php }?>"<?php if (!$_smarty_tpl->tpl_vars['product']->value->id) {?> disabled="disabled"<?php }?>/>
				<?php if (count($_smarty_tpl->tpl_vars['languages']->value)>1) {?>
					</div>
					<div class="col-lg-2">
						<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" tabindex="-1">
							<?php echo $_smarty_tpl->tpl_vars['language']->value['iso_code'];?>

							<span class="caret"></span>
						</button>
						<ul class="dropdown-menu">
							<?php  $_smarty_tpl->tpl_vars['language'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['language']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['languages']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['language']->key => $_smarty_tpl->tpl_vars['language']->value) {
$_smarty_tpl->tpl_vars['language']->_loop = true;
?>
							<li>
								<a href="javascript:hideOtherLanguage(<?php echo $_smarty_tpl->tpl_vars['language']->value['id_lang'];?>
);"><?php echo $_smarty_tpl->tpl_vars['language']->value['name'];?>
</a>
							</li>
							<?php } ?>
						</ul>
					</div>
				</div>
				<?php }?>
			<?php } ?>
			</div>
			<div class="col-lg-2<?php if (count($_smarty_tpl->tpl_vars['images']->value)<=1) {?> hidden<?php }?>" id="caption_selection">
				<select name="id_caption">
					<option value="0"><?php echo smartyTranslate(array('s'=>'All captions'),$_smarty_tpl);?>
</option>
					<?php  $_smarty_tpl->tpl_vars['image'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['image']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['images']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['image']->key => $_smarty_tpl->tpl_vars['image']->value) {
$_smarty_tpl->tpl_vars['image']->_loop = true;
?>
					<option value="<?php echo intval($_smarty_tpl->tpl_vars['image']->value->id_image);?>
">
						<?php echo smartyTranslate(array('s'=>'Position %d','sprintf'=>intval($_smarty_tpl->tpl_vars['image']->value->position)),$_smarty_tpl);?>

					</option>
					<?php } ?>
				</select>
			</div>
			<div class="col-lg-2">
				<button type="submit" class="btn btn-default" name="submitAddproductAndStay" value="update_legends"><i class="icon-random"></i> <?php echo smartyTranslate(array('s'=>'Update'),$_smarty_tpl);?>
</button>
			</div>
		</div>
	</div>
	<table class="table tableDnD" id="imageTable">
		<thead>
			<tr class="nodrag nodrop">
				<th class="fixed-width-lg"><span class="title_box"><?php echo smartyTranslate(array('s'=>'Image'),$_smarty_tpl);?>
</span></th>
				<th class="fixed-width-lg"><span class="title_box"><?php echo smartyTranslate(array('s'=>'Caption'),$_smarty_tpl);?>
</span></th>
				<th class="fixed-width-xs"><span class="title_box"><?php echo smartyTranslate(array('s'=>'Position'),$_smarty_tpl);?>
</span></th>
				<?php if ($_smarty_tpl->tpl_vars['shops']->value) {?>
					<?php  $_smarty_tpl->tpl_vars['shop'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['shop']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['shops']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['shop']->key => $_smarty_tpl->tpl_vars['shop']->value) {
$_smarty_tpl->tpl_vars['shop']->_loop = true;
?>
						<th class="fixed-width-xs"><span class="title_box"><?php echo $_smarty_tpl->tpl_vars['shop']->value['name'];?>
</span></th>
					<?php } ?>
				<?php }?>
				<th class="fixed-width-xs"><span class="title_box"><?php echo smartyTranslate(array('s'=>'Cover'),$_smarty_tpl);?>
</span></th>
				<th></th> <!-- action -->
			</tr>
		</thead>
		<tbody id="imageList">
		</tbody>
	</table>
	<table id="lineType" style="display:none;">
		<tr id="image_id">
			<td>
				<a href="<?php echo @constant('_THEME_PROD_DIR_');?>
image_path.jpg" class="fancybox">
					<img
						src="<?php echo @constant('_THEME_PROD_DIR_');?>
<?php echo $_smarty_tpl->tpl_vars['iso_lang']->value;?>
-default-<?php echo $_smarty_tpl->tpl_vars['imageType']->value;?>
.jpg"
						alt="legend"
						title="legend"
						class="img-thumbnail" />
				</a>
			</td>
			<td>legend</td>
			<td id="td_image_id" class="pointer dragHandle center positionImage">
				<div class="dragGroup">
					<div class="positions">
						image_position
                                        </div>
                                </div>
			</td>
			<?php if ($_smarty_tpl->tpl_vars['shops']->value) {?>
				<?php  $_smarty_tpl->tpl_vars['shop'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['shop']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['shops']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['shop']->key => $_smarty_tpl->tpl_vars['shop']->value) {
$_smarty_tpl->tpl_vars['shop']->_loop = true;
?>
				<td>
					<input
						type="checkbox"
						class="image_shop"
						name="id_image"
						id="<?php echo $_smarty_tpl->tpl_vars['shop']->value['id_shop'];?>
image_id"
						value="<?php echo $_smarty_tpl->tpl_vars['shop']->value['id_shop'];?>
" />
				</td>
				<?php } ?>
			<?php }?>
			<td class="cover">
				<a href="#">
					<i class="icon-check-empty icon-2x covered"></i>
				</a>
			</td>
			<td>
				<a href="#" class="delete_product_image pull-right btn btn-default" >
					<i class="icon-trash"></i> <?php echo smartyTranslate(array('s'=>'Delete this image'),$_smarty_tpl);?>

				</a>
			</td>
		</tr>
	</table>
	<div class="panel-footer">
		<a href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['link']->value->getAdminLink('AdminProducts'), ENT_QUOTES, 'UTF-8', true);?>
<?php if (isset($_REQUEST['page'])&&$_REQUEST['page']>1) {?>&amp;submitFilterproduct=<?php echo intval($_REQUEST['page']);?>
<?php }?>" class="btn btn-default"><i class="process-icon-cancel"></i> <?php echo smartyTranslate(array('s'=>'Cancel'),$_smarty_tpl);?>
</a>
		<button type="submit" name="submitAddproduct" class="btn btn-default pull-right" disabled="disabled"><i class="process-icon-loading"></i> <?php echo smartyTranslate(array('s'=>'Save'),$_smarty_tpl);?>
</button>
		<button type="submit" name="submitAddproductAndStay" class="btn btn-default pull-right" disabled="disabled"><i class="process-icon-loading"></i> <?php echo smartyTranslate(array('s'=>'Save and stay'),$_smarty_tpl);?>
</button>
	</div>
	<script type="text/javascript">
		var upbutton = '<?php echo smartyTranslate(array('s'=>'Upload an image'),$_smarty_tpl);?>
';
		var come_from = '<?php echo $_smarty_tpl->tpl_vars['table']->value;?>
';
		var success_add =  '<?php echo smartyTranslate(array('s'=>'The image has been successfully added.'),$_smarty_tpl);?>
';
		var id_tmp = 0;
		var current_shop_id = <?php echo intval($_smarty_tpl->tpl_vars['current_shop_id']->value);?>
;
		
		//Ready Function

		function imageLine(id, path, position, cover, shops, legend)
		{
			line = $("#lineType").html();
			line = line.replace(/image_id/g, id);
			line = line.replace(/(\/)?[a-z]{0,2}-default/g, function($0, $1){
				return $1 ? $1 + path : $0;
			});
			line = line.replace(/image_path/g, path);
			line = line.replace(/image_position/g, position);
			line = line.replace(/legend/g, legend);
			line = line.replace(/icon-check-empty/g, cover);
			line = line.replace(/<tbody>/gi, "");
			line = line.replace(/<\/tbody>/gi, "");
			if (shops != false)
			{
				$.each(shops, function(key, value){
					if (value == 1)
						line = line.replace('id="' + key + '' + id + '"','id="' + key + '' + id + '" checked=checked');
				});
			}
			$("#imageList").append(line);
		}

		$(document).ready(function(){
			
			<?php  $_smarty_tpl->tpl_vars['image'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['image']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['images']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['image']->key => $_smarty_tpl->tpl_vars['image']->value) {
$_smarty_tpl->tpl_vars['image']->_loop = true;
?>
				assoc = "{";
				<?php if ($_smarty_tpl->tpl_vars['shops']->value) {?>
					<?php  $_smarty_tpl->tpl_vars['shop'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['shop']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['shops']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['shop']->key => $_smarty_tpl->tpl_vars['shop']->value) {
$_smarty_tpl->tpl_vars['shop']->_loop = true;
?>
						assoc += '"<?php echo $_smarty_tpl->tpl_vars['shop']->value['id_shop'];?>
" : <?php if ($_smarty_tpl->tpl_vars['image']->value->isAssociatedToShop($_smarty_tpl->tpl_vars['shop']->value['id_shop'])) {?>1<?php } else { ?>0<?php }?>,';
					<?php } ?>
				<?php }?>
				if (assoc != "{")
				{
					assoc = assoc.slice(0, -1);
					assoc += "}";
					assoc = jQuery.parseJSON(assoc);
				}
				else
					assoc = false;
				imageLine(<?php echo $_smarty_tpl->tpl_vars['image']->value->id;?>
, "<?php echo $_smarty_tpl->tpl_vars['image']->value->getExistingImgPath();?>
", <?php echo $_smarty_tpl->tpl_vars['image']->value->position;?>
, "<?php if ($_smarty_tpl->tpl_vars['image']->value->cover) {?>icon-check-sign<?php } else { ?>icon-check-empty<?php }?>", assoc, "<?php echo addcslashes($_smarty_tpl->tpl_vars['image']->value->legend[$_smarty_tpl->tpl_vars['default_language']->value],'\"');?>
");
			<?php } ?>
			
			var originalOrder = false;

			$("#imageTable").tableDnD(
			{	dragHandle: 'dragHandle',
                                onDragClass: 'myDragClass',
                                onDragStart: function(table, row) {
                                        originalOrder = $.tableDnD.serialize();
                                        reOrder = ':even';
                                        if (table.tBodies[0].rows[1] && $('#' + table.tBodies[0].rows[1].id).hasClass('alt_row'))
                                                reOrder = ':odd';
                                        $(table).find('#' + row.id).parent('tr').addClass('myDragClass');
                                },
				onDrop: function(table, row) {
					if (originalOrder != $.tableDnD.serialize()) {
						current = $(row).attr("id");
						stop = false;
						image_up = "{";
						$("#imageList").find("tr").each(function(i) {
							$("#td_" +  $(this).attr("id")).html('<div class="dragGroup"><div class="positions">'+(i + 1)+'</div></div>');
							if (!stop || (i + 1) == 2)
								image_up += '"' + $(this).attr("id") + '" : ' + (i + 1) + ',';
						});
						image_up = image_up.slice(0, -1);
						image_up += "}";
						updateImagePosition(image_up);
					}
				}
			});
			/**
			 * on success function
			 */
			function afterDeleteProductImage(data)
			{
				data = $.parseJSON(data);
				if (data)
				{
					cover = 0;
					id = data.content.id;
					if (data.status == 'ok')
					{
						if ($("#" + id + ' .covered').hasClass('icon-check-sign'))
							cover = 1;
						$("#" + id).remove();
					}
					if (cover)
						$("#imageTable tr").eq(1).find(".covered").addClass('icon-check-sign');
					$("#countImage").html(parseInt($("#countImage").html()) - 1);
					refreshImagePositions($("#imageTable"));
					showSuccessMessage(data.confirmations);

					if (parseInt($("#countImage").html()) <= 1)
						$('#caption_selection').addClass('hidden');
				}
			}

			$('.delete_product_image').die().live('click', function(e)
			{
				e.preventDefault();
				id = $(this).parent().parent().attr('id');
				if (confirm("<?php echo smartyTranslate(array('s'=>'Are you sure?','js'=>1),$_smarty_tpl);?>
"))
				doAdminAjax({
						"action":"deleteProductImage",
						"id_image":id,
						"id_product" : <?php echo $_smarty_tpl->tpl_vars['id_product']->value;?>
,
						"id_category" : <?php echo $_smarty_tpl->tpl_vars['id_category_default']->value;?>
,
						"token" : "<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['token']->value, ENT_QUOTES, 'UTF-8', true);?>
",
						"tab" : "AdminProducts",
						"ajax" : 1 }, afterDeleteProductImage
				);
			});

			$('.covered').die().live('click', function(e)
			{
				e.preventDefault();
				id = $(this).parent().parent().parent().attr('id');
				$("#imageList .cover i").each( function(i){
					$(this).removeClass('icon-check-sign').addClass('icon-check-empty');
				});
				$(this).removeClass('icon-check-empty').addClass('icon-check-sign');

				if (current_shop_id != 0)
					$('#' + current_shop_id + id).attr('check', true);
				else
					$(this).parent().parent().parent().children('td input').attr('check', true);
				doAdminAjax({
					"action":"UpdateCover",
					"id_image":id,
					"id_product" : <?php echo $_smarty_tpl->tpl_vars['id_product']->value;?>
,
					"token" : "<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['token']->value, ENT_QUOTES, 'UTF-8', true);?>
",
					"controller" : "AdminProducts",
					"ajax" : 1 }
				);
			});

			$('.image_shop').die().live('click', function()
			{
				active = false;
				if ($(this).attr("checked"))
					active = true;
				id = $(this).parent().parent().attr('id');
				id_shop = $(this).attr("id").replace(id, "");
				doAdminAjax(
				{
					"action":"UpdateProductImageShopAsso",
					"id_image":id,
					"id_product":id_product,
					"id_shop": id_shop,
					"active":active,
					"token" : "<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['token']->value, ENT_QUOTES, 'UTF-8', true);?>
",
					"tab" : "AdminProducts",
					"ajax" : 1
				});
			});

			function updateImagePosition(json)
			{
				doAdminAjax(
				{
					"action":"updateImagePosition",
					"json":json,
					"token" : "<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['token']->value, ENT_QUOTES, 'UTF-8', true);?>
",
					"tab" : "AdminProducts",
					"ajax" : 1
				});
			}

			function delQueue(id)
			{
				$("#img" + id).fadeOut("slow");
				$("#img" + id).remove();
			}


			$('.fancybox').fancybox();
		});
		if (tabs_manager.allow_hide_other_languages)
			hideOtherLanguage(default_language);
		
	</script>
</div>
<?php }?>
<?php }} ?>
