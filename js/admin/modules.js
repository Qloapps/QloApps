/**
* 2010-2022 Webkul.
*
* NOTICE OF LICENSE
*
* All right is reserved,
* Please go through LICENSE.txt file inside our module
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to newer
* versions in the future. If you wish to customize this module for your
* needs please refer to CustomizationPolicy.txt file inside our module for more information.
*
* @author Webkul IN
* @copyright 2010-2022 Webkul IN
* @license LICENSE.txt
*/

$('document').ready( function() {

	$('[data-toggle="tab"]').click(function() {
		if ($(this).parent().hasClass('active')) {
			$($(this).attr("href")).toggleClass('active');
			setTimeout(() => {
				$(this).parent().removeClass('active');
			}, 0);
		}
	});

	$('#module_install_form').on('submit', function(e) {
		e.preventDefault();
		let module;
		initializeUploadPanel();
		uploadModule(this).then((response) => {
			module = response.data.module;
			if (!module.installed) {
				checkModuleTrusted(module).then((response) => {
					if (!response.data.trusted) {
						return displayWariningIfUntrusted(module);
					} else {
						return Promise.resolve(true);
					}
				}).then((install) => {
					if (install) {
						return installModule(module);
					} else {
						if (confirm(remove_uploaded_module_txt)) {
							return rollBackChanges(module, 'delete');
						} else {
							window.location.href = admin_modules_link + '&conf=18';
						}
					}
				}).catch((response) => {
					handleError(response, module);
				});
			} else {
				updateModule(module).catch((response) => {
					handleError(response, module);
				});
			}
		}).catch((response) => {
			displayErrors(response, module);
		}).finally(() => {
			$('#module_install_form').find('button[type="submit"]').attr("disabled", false);
		});
	});

	$('#proceed-install-anyway').on('click', function(e) {
		e.preventDefault();
	})

	function displayErrors(response, module)
	{
		let error_list = $('<ul/>').appendTo('#module_install_status .install_errors .list');
		$.each(response.errors, function(i, val) {
			$('<li/>').text(val).appendTo(error_list);
		});
		$('#module_install_status div.install_errors').show();
	}

	function handleError(response, module)
	{
		displayErrors(response, module);
		if (response.data.callback.process) {
			setTimeout(() => {
				if (confirm(response.data.callback.msg ? response.data.callback.msg : module_install_error_txt+' '+remove_uploaded_module_txt)) {
					rollBackChanges(module, response.data.callback.process);
				}
			}, 1000);
		}
	}

	function displayWariningIfUntrusted(data)
	{
		return new Promise((resolve) => {
			var moduleDisplayName = data.displayName;
			var moduleImage = data.image;
			var authorName = data.author;

			$('.modal #untrusted-module-logo').attr('src', moduleImage);
			$('.modal .module-display-name-placeholder').text(moduleDisplayName);
			$('.modal .author-name-placeholder').text(authorName);

			$('#moduleNotTrusted').modal('show');

			$('#proceed-install-anyway').click(() => {
				$('#moduleNotTrusted').modal('hide');
				resolve(true);
			});
			$('#moduleNotTrusted').on('hidden.bs.modal', (e) => {
				resolve(false);
			});
		});
	}

	function initializeUploadPanel()
	{
		$('#module_install_form').find('button[type="submit"]').attr("disabled","disabled");
		$('#module_install_status').show().find('ul li').hide().find('i').removeClass('icon-check icon-times text-danger text-success').addClass('icon-refresh icon-spin');
		$('#module_install_status div.install_errors').hide();
		return
	}

	function uploadModule(uploadModule)
	{
		return new Promise((resolve, reject) => {
			let formData = new FormData(uploadModule);
			formData.append('ajax', true);
			formData.append('action', 'uploadModule');
			$.ajax({
				type: 'POST',
				url: admin_modules_link,
				data: formData,
				dataType: 'json',
				contentType: false,
				cache: false,
				processData:false,
				beforeSend: function(){
					$('#module_install_status').find('.mod_status_upload').show();
				},
				success: function(json) {
					if (json.success) {
						$('#module_install_status').find('.mod_status_upload i').addClass('icon-check text-success').removeClass('icon-refresh icon-spin');
						resolve(json);
					}
					else {
						$('#module_install_status').find('.mod_status_upload i').addClass('icon-times text-danger').removeClass('icon-refresh icon-spin');
						reject(json);
					}
				}
			});
		});
	}

	function checkModuleTrusted(data)
	{
		return new Promise((resolve, reject) => {
			$.ajax({
				type: 'POST',
				url: admin_modules_link,
				data: {
					ajax: true,
					action: 'checkModuleTrusted',
					module_name: data.module_name
				},
				dataType: 'JSON',
				cache: false,
				beforeSend: function(){
					$('#module_install_status').find('.mod_status_check').show();
				},
				success: function(json) {
					if (json.success){
						$('#module_install_status').find('.mod_status_check i').addClass('icon-check text-success').removeClass('icon-refresh icon-spin');
						if (json.msg){
							$('#module_install_status').find('.mod_status_check').append(' <b>'+json.msg+'</b>');
							if (json.data.trusted) {
								$('#module_install_status').find('.mod_status_check b').addClass('text-success');
							} else {
								$('#module_install_status').find('.mod_status_check b').addClass('text-danger');
							}
						}
						resolve(json);
					} else {
						$('#module_install_status').find('.mod_status_check i').addClass('icon-times text-danger').removeClass('icon-refresh icon-spin');
						reject(json);
					}
				}
			});
		});
	}

	function installModule(data)
	{
		return new Promise((resolve, reject) => {
			$.ajax({
				type: 'POST',
				url: admin_modules_link,
				data: {
					ajax: true,
					action: 'installModule',
					module_name: data.module_name
				},
				dataType: 'JSON',
				cache: false,
				beforeSend: function(){
					$('#module_install_status').find('.mod_status_install').show();
				},
				success: function(json) {
					if (json.success){
						$('#module_install_status').find('.mod_status_install i').addClass('icon-check text-success').removeClass('icon-refresh icon-spin');
						resolve(json);

						if (json.data.redirect) {
							window.location.href = json.data.redirect;
						}
					}
					else{
						$('#module_install_status').find('.mod_status_install i').addClass('icon-times text-danger').removeClass('icon-refresh icon-spin');
						reject(json);
					}
				}
			});


		});
	}

	function rollBackChanges(data, process)
	{
		if (!process) {
			process = 'delete';
		}
		return new Promise((resolve, reject) => {
			$.ajax({
				type:"POST",
				url : admin_modules_link,
				data : {
					ajax : true,
					token : token,
					action : "rollbackModuleUpload",
					process : process,
					module_name: data.module_name
				},
				dataType: 'JSON',
				cache: false,
				beforeSend: function(){
					$('#module_install_status').find('.mod_status_rollback').show();
				},
				success: function(json){
					if (json.success) {
						$('#module_install_status').find('.mod_status_rollback i').addClass('icon-check text-success').removeClass('icon-refresh icon-spin');
						if (json.data.redirect) {
							window.location.href = json.data.redirect;
						}
					} else {
						$('#module_install_status').find('.mod_status_install i').addClass('icon-times text-danger').removeClass('icon-refresh icon-spin');
						reject(json);
					}
				}
			});
		});
	}

	function updateModule(data)
	{
		return new Promise((resolve, reject) => {
			$.ajax({
				type:"POST",
				url : admin_modules_link,
				data : {
					ajax : true,
					token : token,
					action : "updateModule",
					module_name: data.module_name
				},
				dataType: 'JSON',
				cache: false,
				beforeSend: function(){
					$('#module_install_status').find('.mod_status_update').show();
				},
				success: function(json){
					if (json.success) {
						$('#module_install_status').find('.mod_status_update i').addClass('icon-check text-success').removeClass('icon-refresh icon-spin');
						$('#module_install_status div.install_msg').append(json.msg);
						if (json.data.redirect) {
							window.location.href = json.data.redirect;
						}
						resolve(json);
					} else {
						$('#module_install_status').find('.mod_status_update i').addClass('icon-times text-danger').removeClass('icon-refresh icon-spin');
						reject(json);
					}
				}
			});
		});
	}

});
