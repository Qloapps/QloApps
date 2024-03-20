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

    // manage dropdowns
    $(document).on('click', function (e) {
        const closestDropdown = $(e.target).closest('.dropdown');

        if (closestDropdown.length) {
            if ($(e.target).closest('.dropdown-toggle').length) {
                $('.dropdown').not(closestDropdown).removeClass('open');
                closestDropdown.toggleClass('open');
            }
        } else {
            $('.dropdown').removeClass('open');
        }
    });
});
