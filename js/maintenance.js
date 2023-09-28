$(document).ready(function(){

    if ($("#PS_SHOP_ENABLE_on").is(':checked')) {
        $('#conf_id_PS_ALLOW_EMP').hide();
    }
    if ($("#PS_SHOP_ENABLE_off").is(':checked')) {
        $("#conf_id_PS_ALLOW_EMP").show();
    }

    $(".clicker.blue").on('click', function(){
        $('.hiddendiv').show('slow');
    });

    $("#cancelLogin").on('click', function(){
        $('.hiddendiv').hide('slow');
    });

    $("input[type='radio'][name='PS_SHOP_ENABLE']").change(function() {
        $("#conf_id_PS_ALLOW_EMP").toggle('slow');
    });

    // manage dropdown
    $(document).on('click', 'button.dropdown-toggle', function () {
        $(this).closest('.dropdown').toggleClass('open');
    });

    $(document).on('click', function (e) {
        if ($('.language-selector-wrap .dropdown').hasClass('open')
            && !$(e.target).closest('.dropdown-toggle').length
        ) {
            $('.language-selector-wrap .dropdown').removeClass('open');
        }
    });
});