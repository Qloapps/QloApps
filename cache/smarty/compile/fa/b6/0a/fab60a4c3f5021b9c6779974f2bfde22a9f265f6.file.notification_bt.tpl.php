<?php /* Smarty version Smarty-3.1.19, created on 2016-01-29 13:14:57
         compiled from "/home/sumit/public_html/html/hotelcommerce-master/modules/gamification/views/templates/hook/notification_bt.tpl" */ ?>
<?php /*%%SmartyHeaderCode:39032377656ab3d94c90105-15496285%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'fab60a4c3f5021b9c6779974f2bfde22a9f265f6' => 
    array (
      0 => '/home/sumit/public_html/html/hotelcommerce-master/modules/gamification/views/templates/hook/notification_bt.tpl',
      1 => 1454083789,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '39032377656ab3d94c90105-15496285',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_56ab3d94d51606_91840372',
  'variables' => 
  array (
    'current_id_tab' => 0,
    'current_level_percent' => 0,
    'current_level' => 0,
    'advice_hide_url' => 0,
    'link' => 0,
    'notification' => 0,
    'badges_to_display' => 0,
    'unlock_badges' => 0,
    'badge' => 0,
    'i' => 0,
    'next_badges' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_56ab3d94d51606_91840372')) {function content_56ab3d94d51606_91840372($_smarty_tpl) {?><script>
	var current_id_tab = <?php echo intval($_smarty_tpl->tpl_vars['current_id_tab']->value);?>
;
	var current_level_percent = <?php echo intval($_smarty_tpl->tpl_vars['current_level_percent']->value);?>
;
	var current_level = <?php echo intval($_smarty_tpl->tpl_vars['current_level']->value);?>
;
	var gamification_level = '<?php echo smartyTranslate(array('s'=>'Level','mod'=>'gamification','js'=>1),$_smarty_tpl);?>
';
	var advice_hide_url = '<?php echo $_smarty_tpl->tpl_vars['advice_hide_url']->value;?>
';
	var hide_advice = '<?php echo smartyTranslate(array('s'=>'Do you really want to hide this advice?','mod'=>'gamification','js'=>1),$_smarty_tpl);?>
';

	$('#dropdown_gamification .notifs_panel_header, #dropdown_gamification .tab-content').click(function () {
		return false;
	});

	$('#dropdown_gamification .panel-footer').click(function (elt) {
		window.location.href = '<?php echo $_smarty_tpl->tpl_vars['link']->value->getAdminLink('AdminGamification');?>
';
	});

	$('#gamification_tab li').click(function () {
		gamificationDisplayTab($(this).children('a'));
		return false;
	});

	function gamificationDisplayTab(elt)
	{
		$('#gamification_tab li, .gamification-tab-pane').removeClass('active');
		$(elt).parent('li').addClass('active');
		$('#'+$(elt).data('target')).addClass('active');
	}

</script>
<li id="gamification_notif" style="background:none" class="dropdown">
	<a href="javascript:void(0);" class="dropdown-toggle gamification_notif" data-toggle="dropdown">
		<i class="icon-trophy"></i>
		<span id="gamification_notif_number_wrapper" class="notifs_badge">
			<span id="gamification_notif_value"><?php echo intval($_smarty_tpl->tpl_vars['notification']->value);?>
</span>
		</span>
	</a>
	<div class="dropdown-menu notifs_dropdown" id="dropdown_gamification">
		<section id="gamification_notif_wrapper" class="notifs_panel" style="width:325px">
			<header class="notifs_panel_header">
				<h3><?php echo smartyTranslate(array('s'=>'Your Merchant Expertise','mod'=>'gamification'),$_smarty_tpl);?>

					<span class="label label-default" style="float:right"><?php echo smartyTranslate(array('s'=>'Level','mod'=>'gamification'),$_smarty_tpl);?>
 <?php echo intval($_smarty_tpl->tpl_vars['current_level']->value);?>
 : <?php echo intval($_smarty_tpl->tpl_vars['current_level_percent']->value);?>
 %</span>
				</h3>
			</header>
			<div class="progress" style="margin: 10px">
				<div class="progress-bar progress-bar-info" role="progressbar" aria-valuenow="<?php echo intval($_smarty_tpl->tpl_vars['current_level_percent']->value);?>
" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo intval($_smarty_tpl->tpl_vars['current_level_percent']->value);?>
%;">
				<span style="color:#FFF"><?php echo smartyTranslate(array('s'=>'Level','mod'=>'gamification'),$_smarty_tpl);?>
 <?php echo intval($_smarty_tpl->tpl_vars['current_level']->value);?>
 : <?php echo intval($_smarty_tpl->tpl_vars['current_level_percent']->value);?>
 %</span>
				</div>
			</div>
			<!-- Nav tabs -->
			<ul class="nav nav-tabs" id="gamification_tab" style="margin-left:10px">
				<li class="active">
					<a href="#home" data-toggle="tab" data-target="gamification_1" onclick="gamificationDisplayTab(this); return false;"><?php echo smartyTranslate(array('s'=>'Last badge :','mod'=>'gamification'),$_smarty_tpl);?>
</a>
				</li>
				<li>
					<a href="#profile" data-toggle="tab" data-target="gamification_2" onclick="gamificationDisplayTab(this); return false;"><?php echo smartyTranslate(array('s'=>'Next badge :','mod'=>'gamification'),$_smarty_tpl);?>
</a>
				</li>
			</ul>

			<!-- Tab panes -->
			<div class="tab-content">
				<div class="tab-pane gamification-tab-pane active" id="gamification_1">
					<ul id="gamification_badges_list" style="<?php if (count($_smarty_tpl->tpl_vars['badges_to_display']->value)<=2) {?> height:170px;<?php }?> padding-left:0">
					<?php  $_smarty_tpl->tpl_vars['badge'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['badge']->_loop = false;
 $_smarty_tpl->tpl_vars["i"] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['unlock_badges']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['badge']->key => $_smarty_tpl->tpl_vars['badge']->value) {
$_smarty_tpl->tpl_vars['badge']->_loop = true;
 $_smarty_tpl->tpl_vars["i"]->value = $_smarty_tpl->tpl_vars['badge']->key;
?>
						<?php if ($_smarty_tpl->tpl_vars['badge']->value->id) {?>
							<li class="<?php if ($_smarty_tpl->tpl_vars['badge']->value->validated) {?> unlocked <?php } else { ?> locked <?php }?>" style="float:left;">
								<span class="<?php if ($_smarty_tpl->tpl_vars['badge']->value->validated) {?> unlocked_img <?php } else { ?> locked_img <?php }?>" <?php if ($_smarty_tpl->tpl_vars['badge']->value->validated) {?>style="left: 12px;"<?php }?>></span>
								<div class="gamification_badges_title"><span><?php if ($_smarty_tpl->tpl_vars['badge']->value->validated) {?> <?php echo smartyTranslate(array('s'=>'Last badge :','mod'=>'gamification'),$_smarty_tpl);?>
 <?php } else { ?> <?php echo smartyTranslate(array('s'=>'Next badge :','mod'=>'gamification'),$_smarty_tpl);?>
 <?php }?></span></div>
								<div class="gamification_badges_img" data-placement="<?php if ($_smarty_tpl->tpl_vars['i']->value<=1) {?>bottom<?php } else { ?>top<?php }?>" data-original-title="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['badge']->value->description, ENT_QUOTES, 'UTF-8', true);?>
"><img src="<?php echo $_smarty_tpl->tpl_vars['badge']->value->getBadgeImgUrl();?>
"></div>
								<div class="gamification_badges_name"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['badge']->value->name, ENT_QUOTES, 'UTF-8', true);?>
</div>
							</li>
						<?php }?>
					<?php } ?>
					</ul>
				</div>
				<div class="tab-pane gamification-tab-pane" id="gamification_2">
					<ul id="gamification_badges_list" style="<?php if (count($_smarty_tpl->tpl_vars['badges_to_display']->value)<=2) {?> height:170px;<?php }?> padding-left:0">
					<?php  $_smarty_tpl->tpl_vars['badge'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['badge']->_loop = false;
 $_smarty_tpl->tpl_vars["i"] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['next_badges']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['badge']->key => $_smarty_tpl->tpl_vars['badge']->value) {
$_smarty_tpl->tpl_vars['badge']->_loop = true;
 $_smarty_tpl->tpl_vars["i"]->value = $_smarty_tpl->tpl_vars['badge']->key;
?>
						<?php if ($_smarty_tpl->tpl_vars['badge']->value->id&&!$_smarty_tpl->tpl_vars['badge']->value->awb) {?>
							<li class="<?php if ($_smarty_tpl->tpl_vars['badge']->value->validated) {?> unlocked <?php } else { ?> locked <?php }?>" style="float:left;">
								<span class="<?php if ($_smarty_tpl->tpl_vars['badge']->value->validated) {?> unlocked_img <?php } else { ?> locked_img <?php }?>" <?php if ($_smarty_tpl->tpl_vars['badge']->value->validated) {?>style="left: 12px;"<?php }?>></span>
								<div class="gamification_badges_title"><span><?php if ($_smarty_tpl->tpl_vars['badge']->value->validated) {?> <?php echo smartyTranslate(array('s'=>'Last badge :','mod'=>'gamification'),$_smarty_tpl);?>
 <?php } else { ?> <?php echo smartyTranslate(array('s'=>'Next badge :','mod'=>'gamification'),$_smarty_tpl);?>
 <?php }?></span></div>
								<div class="gamification_badges_img" data-placement="<?php if ($_smarty_tpl->tpl_vars['i']->value<=1) {?>bottom<?php } else { ?>top<?php }?>"data-toggle="tooltip" data-original-title="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['badge']->value->description, ENT_QUOTES, 'UTF-8', true);?>
"><img src="<?php echo $_smarty_tpl->tpl_vars['badge']->value->getBadgeImgUrl();?>
"></div>
								<div class="gamification_badges_name"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['badge']->value->name, ENT_QUOTES, 'UTF-8', true);?>
</div>
							</li>
						<?php }?>
					<?php } ?>
					</ul>
				</div>
			</div>

			<footer class="panel-footer">
				<a href="<?php echo $_smarty_tpl->tpl_vars['link']->value->getAdminLink('AdminGamification');?>
"><?php echo smartyTranslate(array('s'=>'View my complete profile','mod'=>'gamification'),$_smarty_tpl);?>
</a>
			</footer>
		</section>
	</div>
</li>
<?php }} ?>
