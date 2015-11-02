$(document).ready(function() {
	if ($('input[name=PS_MAIL_METHOD]:checked').val() == 2)
		$('#mail_fieldset_smtp').show();
	else
		$('#mail_fieldset_smtp').hide();

	$('input[name=PS_MAIL_METHOD]').on('click', function() {
		if ($(this).val() == 2)
			$('#mail_fieldset_smtp').slideDown();
		else
			$('#mail_fieldset_smtp').slideUp();
	});
});