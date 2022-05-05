$(document).ready(function() {
	// Initialize events
	$("#login_form").validate({
		rules: {
			"email":{
				"email": true,
				"required": true
			},
			"passwd": {
				"required": true
			}
		},
		submitHandler: function(form) {
			doAjaxLogin($('#redirect').val());
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

	$("#forgot_password_form").validate({
		rules: {
			"email_forgot": {
				"email": true,
				"required": true
			}
		},
		submitHandler: function(form) {
		  doAjaxForgot();
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

	$('.show-forgot-password').on('click',function(e) {
		e.preventDefault();
		displayForgotPassword();
	});

	$('.show-login-form').on('click',function(e) {
		e.preventDefault();
		displayLogin();
	});

	$('#email').focus();

	//Tab-index loop
	$('form').each(function(){
		var list  = $(this).find('*[tabindex]').sort(function(a,b){ return a.tabIndex < b.tabIndex ? -1 : 1; }),
			first = list.first();
		list.last().on('keydown', function(e){
			if( e.keyCode === 9 ) {
				first.focus();
				return false;
			}
		});
	});

	//Preload images
	$('<img/>')[0].src = img_dir+'qloapps-login@2x.png';
	$('<img/>')[0].src = img_dir+'qloapps-login-wink@2x.png';

	$('button[name="submitLogin"]').on('mouseover', function() {
		$('#shop-img img').attr('src', img_dir+'qloapps-login-wink@2x.png');
	});

	$('button[name="submitLogin"]').on('mouseout', function() {
		$('#shop-img img').attr('src', img_dir+'qloapps-login@2x.png');
	});
});

//todo: ladda init
var l = new Object();
function feedbackSubmit() {
	l = Ladda.create( document.querySelector( 'button[type=submit]' ) );
}

function displayForgotPassword() {
	$('#error').hide();
	$("#login").find('.flip-container').toggleClass("flip");
	setTimeout(function(){$('.front').hide()},200);
	setTimeout(function(){$('.back').show()},200);
	$('#email_forgot').select();
}

function displayLogin() {
	$('#error').hide();
	$("#login").find('.flip-container').toggleClass("flip");
	setTimeout(function(){$('.back').hide()},200);
	setTimeout(function(){$('.front').show()},200);
	$('#email').select();
	return false;
}

/**
 * Check user credentials
 *
 * @param string redirect name of the controller to redirect to after login (or null)
 */
function doAjaxLogin(redirect) {
	$('#error').hide();
	$('#login_form').fadeIn('slow', function() {
		$.ajax({
			type: "POST",
			headers: { "cache-control": "no-cache" },
			url: "ajax-tab.php" + '?rand=' + new Date().getTime(),
			async: true,
			dataType: "json",
			data: {
				ajax: "1",
				token: "",
				controller: "AdminLogin",
				submitLogin: "1",
				passwd: $('#passwd').val(),
				email: $('#email').val(),
				redirect: redirect,
				stay_logged_in: $('#stay_logged_in:checked').val()
			},
			beforeSend: function() {
				feedbackSubmit();
				l.start();
			},
			success: function(jsonData) {
				if (jsonData.hasErrors) {
					displayErrors(jsonData.errors);
					l.stop();
				} else {
					window.location.assign(jsonData.redirect);
				}
			},
			error: function(XMLHttpRequest, textStatus, errorThrown) {
				l.stop();
				$('#error').html('<h3>TECHNICAL ERROR:</h3><p>Details: Error thrown: ' + XMLHttpRequest + '</p><p>Text status: ' + textStatus + '</p>').removeClass('hide');
				$('#login_form').fadeOut('slow');
			}
		});
	});
}

function doAjaxForgot() {
	$('#error').hide();
	$('#forgot_password_form').fadeIn('slow', function() {
		$.ajax({
			type: 'POST',
			headers: {'cache-control': 'no-cache'},
			url: 'ajax-tab.php' + '?rand=' + new Date().getTime(),
			async: true,
			dataType: 'json',
			data: {
				ajax: 1,
				controller: 'AdminLogin',
				submitForgot: 1,
				email_forgot: $('#email_forgot').val()
			},
			success: function(jsonData) {
				if (jsonData.hasErrors) {
					displayErrors(jsonData.errors);
				} else {
					alert(jsonData.confirm);
					$('#forgot_password_form').hide();
					$('.show-forgot-password').hide();
					displayLogin();
				}
			},
			error: function(XMLHttpRequest, textStatus, errorThrown) {
				$('#error').html(XMLHttpRequest.responseText).removeClass('hide').fadeIn('slow');
			}
		});
	});
}

function displayErrors(errors) {
	str_errors = '<p><strong>' + (errors.length > 1 ? more_errors : one_error) + '</strong></p><ol>';
	for (var error in errors) //IE6 bug fix
		if (error != 'indexOf') str_errors += '<li>' + errors[error] + '</li>';
	$('#error').html(str_errors + '</ol>').removeClass('hide').fadeIn('slow');
}
