$(document).on('click', '#blocknewsletter .newsletter-btn', function (e) {
    e.preventDefault();

    $.ajax({
        url: url_newsletter_subscription,
        type: 'POST',
        dataType: 'JSON',
        cache: false,
        data: $(this).closest('form').serialize(),
        beforeSend: function () {
            let messageBlock = $('#blocknewsletter .message-block');
            $(messageBlock).fadeOut('fast');

            $('#blocknewsletter .loader').show();
        },
        success: function (response) {
            let messageBlock = $('#blocknewsletter .message-block');
            $(messageBlock).html(response.message_html).stop(true, true).fadeIn('fast');
        },
        error: function () {
            if (!onlineFlag) {
                showErrorMessage(no_internet_txt);
            }
        },
        complete: function () {
            $('#blocknewsletter .loader').hide();
        }
    });
});
