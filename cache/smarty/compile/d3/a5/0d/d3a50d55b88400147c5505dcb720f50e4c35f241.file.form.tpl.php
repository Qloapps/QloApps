<?php /* Smarty version Smarty-3.1.19, created on 2015-11-02 11:05:16
         compiled from "/home/sumit/public_html/html/ps-hotel-reservation-system/admin527qbaxyx/themes/default/template/controllers/products/helpers/form/form.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1512700154563789bcae5209-84684818%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'd3a50d55b88400147c5505dcb720f50e4c35f241' => 
    array (
      0 => '/home/sumit/public_html/html/ps-hotel-reservation-system/admin527qbaxyx/themes/default/template/controllers/products/helpers/form/form.tpl',
      1 => 1446455074,
      2 => 'file',
    ),
    '560a8d45db1cc2923528abf6fb45860903a94c2c' => 
    array (
      0 => '/home/sumit/public_html/html/ps-hotel-reservation-system/admin527qbaxyx/themes/default/template/helpers/form/form.tpl',
      1 => 1446455061,
      2 => 'file',
    ),
    '6f3ca25bb7592a25a782eb12015702368e5accc6' => 
    array (
      0 => '/home/sumit/public_html/html/ps-hotel-reservation-system/admin527qbaxyx/themes/default/template/helpers/form/form_group.tpl',
      1 => 1446455061,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1512700154563789bcae5209-84684818',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'fields' => 0,
    'tabs' => 0,
    'identifier_bk' => 0,
    'identifier' => 0,
    'table_bk' => 0,
    'table' => 0,
    'name_controller' => 0,
    'current' => 0,
    'token' => 0,
    'style' => 0,
    'form_id' => 0,
    'submit_action' => 0,
    'f' => 0,
    'fieldset' => 0,
    'key' => 0,
    'field' => 0,
    'input' => 0,
    'contains_states' => 0,
    'fields_value' => 0,
    'hint' => 0,
    'languages' => 0,
    'language' => 0,
    'defaultFormLanguage' => 0,
    'value_text' => 0,
    'name' => 0,
    'value' => 0,
    'option' => 0,
    'optiongroup' => 0,
    'field_value' => 0,
    'id_checkbox' => 0,
    'select' => 0,
    'k' => 0,
    'v' => 0,
    'categories_tree' => 0,
    'asso_shop' => 0,
    'p' => 0,
    'hookName' => 0,
    'show_cancel_button' => 0,
    'back_url' => 0,
    'btn' => 0,
    'tinymce' => 0,
    'iso' => 0,
    'ad' => 0,
    'firstCall' => 0,
    'vat_number' => 0,
    'allowEmployeeFormLang' => 0,
    'use_textarea_autosize' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_563789bd4925a4_78080960',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_563789bd4925a4_78080960')) {function content_563789bd4925a4_78080960($_smarty_tpl) {?><?php if (!is_callable('smarty_function_counter')) include '/home/sumit/public_html/html/ps-hotel-reservation-system/tools/smarty/plugins/function.counter.php';
?>
<?php if (isset($_smarty_tpl->tpl_vars['fields']->value['title'])) {?><h3><?php echo $_smarty_tpl->tpl_vars['fields']->value['title'];?>
</h3><?php }?>

<?php if (isset($_smarty_tpl->tpl_vars['tabs']->value)&&count($_smarty_tpl->tpl_vars['tabs']->value)) {?>
<script type="text/javascript">
	var helper_tabs = <?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_MODIFIER]['json_encode'][0][0]->jsonEncode($_smarty_tpl->tpl_vars['tabs']->value);?>
;
	var unique_field_id = '';
</script>
<?php }?>

	<div class="row">
		<div class="productTabs col-lg-2 col-md-3">
			<div class="list-group">
			<?php  $_smarty_tpl->tpl_vars['tab'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['tab']->_loop = false;
 $_smarty_tpl->tpl_vars['numStep'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['product_tabs']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['tab']->key => $_smarty_tpl->tpl_vars['tab']->value) {
$_smarty_tpl->tpl_vars['tab']->_loop = true;
 $_smarty_tpl->tpl_vars['numStep']->value = $_smarty_tpl->tpl_vars['tab']->key;
?>
				<?php if ($_smarty_tpl->tpl_vars['tab']->value['name']!="Pack") {?>
					<a class="list-group-item <?php if ($_smarty_tpl->tpl_vars['tab']->value['selected']) {?>active<?php }?>" id="link-<?php echo $_smarty_tpl->tpl_vars['tab']->value['id'];?>
" href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['tab']->value['href'], ENT_QUOTES, 'UTF-8', true);?>
&amp;updateproduct"><?php echo $_smarty_tpl->tpl_vars['tab']->value['name'];?>
</a>
				<?php }?>
			<?php } ?>
			</div>
		</div>
		<script type="text/javascript">
			var id_product = <?php if (isset($_smarty_tpl->tpl_vars['product']->value->id)) {?><?php echo $_smarty_tpl->tpl_vars['product']->value->id;?>
<?php } else { ?>0<?php }?>;
			var id_lang_default = <?php echo $_smarty_tpl->tpl_vars['id_lang_default']->value;?>
;
			var product_type_pack = <?php echo Product::PTYPE_PACK;?>
;
			var product_type_virtual = <?php echo Product::PTYPE_VIRTUAL;?>
;
			var product_type_simple = <?php echo Product::PTYPE_SIMPLE;?>
;

			var has_combinations = <?php echo $_smarty_tpl->tpl_vars['has_combinations']->value;?>
;

			var toload = new Array();
			var empty_pack_msg = '<?php echo smartyTranslate(array('s'=>'This pack is empty. You will need to add at least one product to the pack before you can save.','js'=>1),$_smarty_tpl);?>
';
			var empty_name_msg = '<?php echo smartyTranslate(array('s'=>'The product name is empty. You will at least need to enter a name for the default language before you can save the product.','js'=>1),$_smarty_tpl);?>
';
			var empty_link_rewrite_msg = '<?php echo smartyTranslate(array('s'=>'The friendly URL is empty. You will at least need to enter a friendly URL for the default language before you can save the product.','slashes'=>1),$_smarty_tpl);?>
';
			var reload_tab_title = '<?php echo smartyTranslate(array('s'=>'Confirmation','slashes'=>1),$_smarty_tpl);?>
';
			var reload_tab_description = '<?php echo smartyTranslate(array('s'=>'A server error occurred while loading the tabs: some tabs could not be loaded.','js'=>1),$_smarty_tpl);?>
'+'\n'+'<?php echo smartyTranslate(array('s'=>'Please try again by refreshing the page.','js'=>1),$_smarty_tpl);?>
'+'\n'+'<?php echo smartyTranslate(array('s'=>'If you are still encountering this problem, please check your server logs or contact your hosting provider for assistance.','js'=>1),$_smarty_tpl);?>
';

			$('#product-tab-content-wait').show();
			var post_data = <?php echo $_smarty_tpl->tpl_vars['post_data']->value;?>
;
			var save_error = <?php if ($_smarty_tpl->tpl_vars['save_error']->value) {?>true<?php } else { ?>false<?php }?>;
			var error_heading_msg = '<?php echo smartyTranslate(array('s'=>'Error','js'=>1),$_smarty_tpl);?>
';
			var error_continue_msg = '<?php echo smartyTranslate(array('s'=>'Continue','js'=>1),$_smarty_tpl);?>
';

			var product_type = <?php echo $_smarty_tpl->tpl_vars['product_type']->value;?>
;
			
			<?php if (isset($_smarty_tpl->tpl_vars['display_multishop_checkboxes']->value)&&$_smarty_tpl->tpl_vars['display_multishop_checkboxes']->value) {?>
				var display_multishop_checkboxes = true;
			<?php } else { ?>
				var display_multishop_checkboxes = false;
			<?php }?>

			var tabs_preloaded = new Array();
			var tabs_to_preload = new Array();
			var mod_evasive = <?php if (isset($_smarty_tpl->tpl_vars['mod_evasive']->value)&&$_smarty_tpl->tpl_vars['mod_evasive']->value) {?>true<?php } else { ?>false<?php }?>;
			var mod_security = <?php if (isset($_smarty_tpl->tpl_vars['mod_security']->value)&&$_smarty_tpl->tpl_vars['mod_security']->value) {?>true<?php } else { ?>false<?php }?>;

			$(document).ready(function()
			{
				if (product_type == product_type_pack)
				{
					$('a[id*="VirtualProduct"]').hide();
					$('a[id*="Combinations"]').hide();
				}
				else if (product_type == product_type_virtual)
				{
					$('a[id*="Pack"]').hide();
					$('a[id*="Shipping"]').hide();
					$('a[id*="Combinations"]').hide();
				}
				else
				{
					$('a[id*="Pack"]').hide();
					$('a[id*="VirtualProduct"]').hide();
				}

				$('#desc-product-newCombination').hide();

				
				$(".productTabs a").click(function(e){
					e.preventDefault();
					// currentId is the current product tab id
					currentId = false;
					if ($(".productTabs a.active").length)
						currentId = $(".productTabs a.active").attr('id').substr(5);
					// id is the wanted producttab id
					id = $(this).attr('id').substr(5);

					// Update submit button value
					var split_position = id.indexOf('-') + 1;
					var btn_name = id.substr(split_position);

					if ((btn_name == 'VirtualProduct' || btn_name == 'Pack') && $('#name_' + id_lang_default).val() == '')
					{
						alert(missing_product_name);
						$('#name_' + id_lang_default).focus();
						return false;
					}

					$('#key_tab').val(btn_name);

					if ($(this).attr("id") != $(".productTabs a.active ").attr('id'))
					{
						$(".productTabs a").removeClass('active');
						$("#product-tab-content-"+currentId).hide();
					}

					// if the tab has not already been loaded, load it now
					tabs_manager.display(id, true);

					tabs_manager.onLoad(id, function(){
						$("#product-tab-content-"+id).show(0, function(){
							$(this).trigger('displayed');
						});
						$("#link-"+id).addClass('active');
					});

					var languages = new Array();
					if (btn_name == "Combinations")
					{
						$('#desc-product-new').hide();
						$('#desc-product-newCombination').show();
						populate_attrs();
					}
					else if (btn_name == "Attachments")
					{
						handleSaveButtons();
					}
					else
					{
						$('#desc-product-newCombination').hide();
						// if pack is enabled, save button are visible only if pack is valid
						if ($("input[name='id_product']").val() != 0 || btn_name != 'Informations')
							handleSaveButtons();
					}

					$('.label-tooltip').tooltip();
				});

				$(".productTabs a.active").click();

				// disable save if Associations tab is not loaded
				if ($('#product-tab-content-Associations').hasClass('not-loaded'))
					disableSave();

				tabs_manager.onLoad('Associations', function(){
					if ($("input[name='id_product']").val() != 0)
						handleSaveButtons();
				});

				$('.confirm_leave').live('click', function(){
					// Double quotes are necessary when the translated string has single quotes
					return confirm("<?php echo smartyTranslate(array('s'=>'You will lose all unsaved modifications. Are you sure that you want to proceed?','js'=>1),$_smarty_tpl);?>
");
				});

				$('#toolbar-footer').appendTo($('#product-tab-content-Informations').children('.product-tab'));

				$('.product-tab-content').on('displayed', function(e) {
					$('#toolbar-footer').appendTo($(this).children('.product-tab'));
				});

			});

			// Listen to the load event that is fired each time an ajax call to load a tab has completed
			$(window).bind("load", function() {
				
				var tabs_to_preload = new Array();
				<?php  $_smarty_tpl->tpl_vars['value'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['value']->_loop = false;
 $_smarty_tpl->tpl_vars['tab_name'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['tabs_preloaded']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['value']->key => $_smarty_tpl->tpl_vars['value']->value) {
$_smarty_tpl->tpl_vars['value']->_loop = true;
 $_smarty_tpl->tpl_vars['tab_name']->value = $_smarty_tpl->tpl_vars['value']->key;
?>
					
					<?php if ((is_numeric($_smarty_tpl->tpl_vars['value']->value))) {?>
						if ($("#product-tab-content-"+'<?php echo $_smarty_tpl->tpl_vars['tab_name']->value;?>
').hasClass('not-loaded'))
							tabs_to_preload.push('<?php echo $_smarty_tpl->tpl_vars['tab_name']->value;?>
');
					<?php }?>
				<?php } ?>

				// Recursively load tabs starting with the first element of stack
				tabs_manager.displayBulk(tabs_to_preload);
				$('.productTabs').show();
				$('#product-tab-content-wait').hide();

				function checkIfProductTypeIsPack() {
					var typeIsPack = $('#pack_product').is(':checked');
					if (typeIsPack && $('#inputPackItems').val()=='' ) {
						$('.pack-empty-warning').removeClass('alert-warning').addClass('alert-danger');
						$('#curPackItemName').select2('open');
					}
					return typeIsPack;
				}
				$("#product_form").validate({
					ignore: '.updateCurrentText',
					rules: {
						inputPackItems: {
							required: {
								depends: checkIfProductTypeIsPack
							},
						}
					},
					messages: {
						inputPackItems: {
							required: ""
						}
					},
					submitHandler: function(form) {
						form.submit();
					},
					// override jquery validate plugin defaults for bootstrap 3
					highlight: function(element) {
						$(element).closest('.form-group').addClass('has-error');
					},
					unhighlight: function(element) {
						$(element).closest('.form-group').removeClass('has-error');
					},
					errorElement: 'span',
					errorClass: 'help-block',
					errorPlacement: function(error, element) {
						if(element.parent('.input-group').length) {
							error.insertAfter(element.parent());
						} else {
							error.insertAfter(element);
						}
					}
				});
			});
		</script>

		<script type="text/javascript">
			// <![CDATA[
				ThickboxI18nImage = "<?php echo smartyTranslate(array('s'=>'Image'),$_smarty_tpl);?>
";
				ThickboxI18nOf = "<?php echo smartyTranslate(array('s'=>'of'),$_smarty_tpl);?>
";
				ThickboxI18nClose = "<?php echo smartyTranslate(array('s'=>'Close'),$_smarty_tpl);?>
";
				ThickboxI18nOrEscKey = "<?php echo smartyTranslate(array('s'=>'(or "Esc")'),$_smarty_tpl);?>
";
				ThickboxI18nNext = "<?php echo smartyTranslate(array('s'=>'Next >'),$_smarty_tpl);?>
";
				ThickboxI18nPrev = "<?php echo smartyTranslate(array('s'=>'< Previous'),$_smarty_tpl);?>
";
				tb_pathToImage = "../img/loadingAnimation.gif";
			//]]>
		</script>

		<div id="product-tab-content-wait" style="display:none">
			<div id="loading"><i class="icon-refresh icon-spin"></i>&nbsp;<?php echo smartyTranslate(array('s'=>'Loading...'),$_smarty_tpl);?>
</div>
		</div>

		<form id="product_form" class="form-horizontal col-lg-10 col-md-9" action="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['form_action']->value, ENT_QUOTES, 'UTF-8', true);?>
" method="post" enctype="multipart/form-data" name="product" novalidate>
			<input type="hidden" name="id_product" value="<?php echo $_smarty_tpl->tpl_vars['id_product']->value;?>
" />
			<input type="hidden" id="is_virtual" name="is_virtual" value="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['product']->value->is_virtual, ENT_QUOTES, 'UTF-8', true);?>
" />
			<?php if (isset($_REQUEST['page'])&&$_REQUEST['page']>1) {?>
				<input type="hidden" id="page" name="page" value="<?php echo intval($_REQUEST['page']);?>
" />
			<?php }?>
			<?php if (!$_smarty_tpl->tpl_vars['product']->value->active&&$_smarty_tpl->tpl_vars['product']->value->isAssociatedToShop()) {?>
			<div class="alert alert-info draft" >
				<?php echo smartyTranslate(array('s'=>'Your product will be saved as a draft.'),$_smarty_tpl);?>

				<a href="#" class="btn btn-default" onclick="submitAddProductAndPreview()" ><i class="icon-eye-open"></i>&nbsp;<?php echo smartyTranslate(array('s'=>'Save and preview'),$_smarty_tpl);?>
</a>
				<input type="hidden" name="fakeSubmitAddProductAndPreview" id="fakeSubmitAddProductAndPreview" />
			</div>
			<?php }?>
			
			<?php  $_smarty_tpl->tpl_vars['tab'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['tab']->_loop = false;
 $_smarty_tpl->tpl_vars['numStep'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['product_tabs']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['tab']->key => $_smarty_tpl->tpl_vars['tab']->value) {
$_smarty_tpl->tpl_vars['tab']->_loop = true;
 $_smarty_tpl->tpl_vars['numStep']->value = $_smarty_tpl->tpl_vars['tab']->key;
?>
				<?php if ($_smarty_tpl->tpl_vars['tab']->value['id']!="Pack") {?>
				<div id="product-tab-content-<?php echo $_smarty_tpl->tpl_vars['tab']->value['id'];?>
" class="<?php if (!$_smarty_tpl->tpl_vars['tab']->value['selected']) {?>not-loaded<?php }?> product-tab-content" <?php if (!$_smarty_tpl->tpl_vars['tab']->value['selected']) {?>style="display:none"<?php }?>>
					<?php if ($_smarty_tpl->tpl_vars['tab']->value['selected']) {?>
						<?php echo $_smarty_tpl->tpl_vars['custom_form']->value;?>

					<?php }?>
				</div>
				<?php }?>
			<?php } ?>
			<input type="hidden" name="id_product_attribute" id="id_product_attribute" value="0" />
			<input type="hidden" name="key_tab" id="key_tab" value="Informations" />
		</form>
	</div>




<?php if (isset($_smarty_tpl->tpl_vars['tinymce']->value)&&$_smarty_tpl->tpl_vars['tinymce']->value) {?>
<script type="text/javascript">
	var iso = '<?php echo addslashes($_smarty_tpl->tpl_vars['iso']->value);?>
';
	var pathCSS = '<?php echo addslashes(@constant('_THEME_CSS_DIR_'));?>
';
	var ad = '<?php echo addslashes($_smarty_tpl->tpl_vars['ad']->value);?>
';

	$(document).ready(function(){
		
	// Execute when tab Informations has finished loading
	tabs_manager.onLoad('Informations', function(){
		tinySetup({
			editor_selector :"autoload_rte",
			setup : function(ed) {
				ed.on('init', function(ed)
				{
					if (typeof ProductMultishop.load_tinymce[ed.target.id] != 'undefined')
					{
						if (typeof ProductMultishop.load_tinymce[ed.target.id])
							tinyMCE.get(ed.target.id).hide();
						else
							tinyMCE.get(ed.target.id).show();
					}
				});

				ed.on('keydown', function(ed, e) {
					tinyMCE.triggerSave();
					textarea = $('#'+tinymce.activeEditor.id);
					var max = textarea.parent('div').find('span.counter').data('max');
					if (max != 'none')
					{
						count = tinyMCE.activeEditor.getBody().textContent.length;
						rest = max - count;
						if (rest < 0)
							textarea.parent('div').find('span.counter').html('<span style="color:red;"><?php echo smartyTranslate(array('s'=>'Maximum'),$_smarty_tpl);?>
 '+ max +' <?php echo smartyTranslate(array('s'=>'characters'),$_smarty_tpl);?>
 : '+rest+'</span>');
						else
							textarea.parent('div').find('span.counter').html(' ');
					}
				});
			}
		});
	});

	});
</script>
<?php }?>
<?php if ($_smarty_tpl->tpl_vars['firstCall']->value) {?>
	<script type="text/javascript">
		var module_dir = '<?php echo @constant('_MODULE_DIR_');?>
';
		var id_language = <?php echo intval($_smarty_tpl->tpl_vars['defaultFormLanguage']->value);?>
;
		var languages = new Array();
		var vat_number = <?php if ($_smarty_tpl->tpl_vars['vat_number']->value) {?>1<?php } else { ?>0<?php }?>;
		// Multilang field setup must happen before document is ready so that calls to displayFlags() to avoid
		// precedence conflicts with other document.ready() blocks
		<?php  $_smarty_tpl->tpl_vars['language'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['language']->_loop = false;
 $_smarty_tpl->tpl_vars['k'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['languages']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['language']->key => $_smarty_tpl->tpl_vars['language']->value) {
$_smarty_tpl->tpl_vars['language']->_loop = true;
 $_smarty_tpl->tpl_vars['k']->value = $_smarty_tpl->tpl_vars['language']->key;
?>
			languages[<?php echo $_smarty_tpl->tpl_vars['k']->value;?>
] = {
				id_lang: <?php echo $_smarty_tpl->tpl_vars['language']->value['id_lang'];?>
,
				iso_code: '<?php echo $_smarty_tpl->tpl_vars['language']->value['iso_code'];?>
',
				name: '<?php echo $_smarty_tpl->tpl_vars['language']->value['name'];?>
',
				is_default: '<?php echo $_smarty_tpl->tpl_vars['language']->value['is_default'];?>
'
			};
		<?php } ?>
		// we need allowEmployeeFormLang var in ajax request
		allowEmployeeFormLang = <?php echo intval($_smarty_tpl->tpl_vars['allowEmployeeFormLang']->value);?>
;
		displayFlags(languages, id_language, allowEmployeeFormLang);

		$(document).ready(function() {

			$(".show_checkbox").click(function () {
				$(this).addClass('hidden')
				$(this).siblings('.checkbox').removeClass('hidden');
				$(this).siblings('.hide_checkbox').removeClass('hidden');
				return false;
			});
			$(".hide_checkbox").click(function () {
				$(this).addClass('hidden')
				$(this).siblings('.checkbox').addClass('hidden');
				$(this).siblings('.show_checkbox').removeClass('hidden');
				return false;
			});

			<?php if (isset($_smarty_tpl->tpl_vars['fields_value']->value['id_state'])) {?>
				if ($('#id_country') && $('#id_state'))
				{
					ajaxStates(<?php echo $_smarty_tpl->tpl_vars['fields_value']->value['id_state'];?>
);
					$('#id_country').change(function() {
						ajaxStates();
					});
				}
			<?php }?>

			if ($(".datepicker").length > 0)
				$(".datepicker").datepicker({
					prevText: '',
					nextText: '',
					dateFormat: 'yy-mm-dd'
				});

			if ($(".datetimepicker").length > 0)
			$('.datetimepicker').datetimepicker({
				prevText: '',
				nextText: '',
				dateFormat: 'yy-mm-dd',
				// Define a custom regional settings in order to use PrestaShop translation tools
				currentText: '<?php echo smartyTranslate(array('s'=>'Now','js'=>1),$_smarty_tpl);?>
',
				closeText: '<?php echo smartyTranslate(array('s'=>'Done','js'=>1),$_smarty_tpl);?>
',
				ampm: false,
				amNames: ['AM', 'A'],
				pmNames: ['PM', 'P'],
				timeFormat: 'hh:mm:ss tt',
				timeSuffix: '',
				timeOnlyTitle: '<?php echo smartyTranslate(array('s'=>'Choose Time','js'=>1),$_smarty_tpl);?>
',
				timeText: '<?php echo smartyTranslate(array('s'=>'Time','js'=>1),$_smarty_tpl);?>
',
				hourText: '<?php echo smartyTranslate(array('s'=>'Hour','js'=>1),$_smarty_tpl);?>
',
				minuteText: '<?php echo smartyTranslate(array('s'=>'Minute','js'=>1),$_smarty_tpl);?>
',
			});
			<?php if (isset($_smarty_tpl->tpl_vars['use_textarea_autosize']->value)) {?>
			$(".textarea-autosize").autosize();
			<?php }?>
		});
	state_token = '<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['getAdminToken'][0][0]->getAdminTokenLiteSmarty(array('tab'=>'AdminStates'),$_smarty_tpl);?>
';
	
	</script>
<?php }?>
<?php }} ?>
<?php /* Smarty version Smarty-3.1.19, created on 2015-11-02 11:05:17
         compiled from "/home/sumit/public_html/html/ps-hotel-reservation-system/admin527qbaxyx/themes/default/template/helpers/form/form_group.tpl" */ ?>
<?php if ($_valid && !is_callable('content_563789bd1bf847_68229010')) {function content_563789bd1bf847_68229010($_smarty_tpl) {?>

<?php if (count($_smarty_tpl->tpl_vars['groups']->value)&&isset($_smarty_tpl->tpl_vars['groups']->value)) {?>
<div class="row">
	<div class="col-lg-6">
		<table class="table table-bordered">
			<thead>
				<tr>
					<th class="fixed-width-xs">
						<span class="title_box">
							<input type="checkbox" name="checkme" id="checkme" onclick="checkDelBoxes(this.form, 'groupBox[]', this.checked)" />
						</span>
					</th>
					<th class="fixed-width-xs"><span class="title_box"><?php echo smartyTranslate(array('s'=>'ID'),$_smarty_tpl);?>
</span></th>
					<th>
						<span class="title_box">
							<?php echo smartyTranslate(array('s'=>'Group name'),$_smarty_tpl);?>

						</span>
					</th>
				</tr>
			</thead>
			<tbody>
			<?php  $_smarty_tpl->tpl_vars['group'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['group']->_loop = false;
 $_smarty_tpl->tpl_vars['key'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['groups']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['group']->key => $_smarty_tpl->tpl_vars['group']->value) {
$_smarty_tpl->tpl_vars['group']->_loop = true;
 $_smarty_tpl->tpl_vars['key']->value = $_smarty_tpl->tpl_vars['group']->key;
?>
				<tr>
					<td>
						<?php $_smarty_tpl->tpl_vars['id_checkbox'] = new Smarty_variable((('groupBox').('_')).($_smarty_tpl->tpl_vars['group']->value['id_group']), null, 0);?>
						<input type="checkbox" name="groupBox[]" class="groupBox" id="<?php echo $_smarty_tpl->tpl_vars['id_checkbox']->value;?>
" value="<?php echo $_smarty_tpl->tpl_vars['group']->value['id_group'];?>
" <?php if ($_smarty_tpl->tpl_vars['fields_value']->value[$_smarty_tpl->tpl_vars['id_checkbox']->value]) {?>checked="checked"<?php }?> />
					</td>
					<td><?php echo $_smarty_tpl->tpl_vars['group']->value['id_group'];?>
</td>
					<td>
						<label for="<?php echo $_smarty_tpl->tpl_vars['id_checkbox']->value;?>
"><?php echo $_smarty_tpl->tpl_vars['group']->value['name'];?>
</label>
					</td>
				</tr>
			<?php } ?>
			</tbody>
		</table>
	</div>
</div>
<?php } else { ?>
<p>
	<?php echo smartyTranslate(array('s'=>'No group created'),$_smarty_tpl);?>

</p>
<?php }?><?php }} ?>
