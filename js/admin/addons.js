function sendSearchQuery() {
	pattern = $('#addons-search-box').val();
	url = 'https://addons.prestashop.com/en/search.php?search_query='+pattern+'&amp;utm_source=back-office&amp;utm_medium=recherche-theme&amp;utm_campaign=back-office-EN';
	window.open(url, '_blank');
}

function goToProduct(url) {
	window.open(url+'&amp;utm_source=back-office&amp;utm_medium=themes-push&amp;utm_campaign=back-office-EN', '_blank');
}

$(document).ready(function() {

	var onSearch = false;
	var ajaxSearch = false;
	var timeouts = [];

	$('#addons-search-box')[0].selectionStart = $('#addons-search-box')[0].selectionEnd = $('#addons-search-box').val().length;


	$('#addons-search-btn').click(function(e)
    {
		if ($("#addons-search-box").val() && !onSearch) {
			sendSearchQuery();
	        e.preventDefault();
		}
	});

	$("#addons-search-box").live('blur', function() {
		setTimeout(function(){
			$("#addons-search-results").remove()
		}, 200);
	});

	$(".addons-style-view-product").live('click', function() {
		goToProduct($(this).find('p').html())
	});

	$("#addons-search-form").submit( function() {
		if ($("#addons-search-box").val() && !onSearch)
			return true;
		return false;
	});

    $("#addons-search-box").keypress(function(e)
    {
        code = (e.keyCode ? e.keyCode : e.which);
        if (code === 13 && $("#addons-search-box").val() && !onSearch) {
			sendSearchQuery();
            e.preventDefault();
		}
	});

	$("#addons-search-box").click(function() {onSearch=false;});

	$("#addons-search-box").keyup( function(event) {

		if (event.which === 40 || event.which === 38)
			return false;

		if ($(this).val().length < 3) {
			$("#addons-search-results").remove();
			return false;
		}

		$("#query").css('background', 'transparent url("https://medias2.prestastore.com/img/loader.gif") no-repeat right center');
		$("#addons-search-results").remove();

		if (ajaxSearch)
			ajaxSearch.abort();

		for (i=0; i<timeouts.length; i++) {
			window.clearTimeout(timeouts[i]);
		}

		//queue new request
		timeout_ref = setTimeout(function(obj) {
			$("#addons-search-results").remove();
			ajaxSearch = $.ajax({
				type: 'POST',
				url: 'http://addons.prestashop.com/search.php',
				crossDomain: true,
				dataType:'jsonp',
				data: {
					q: $("#addons-search-box").val(),
					ajaxSearch: 1,
					id_lang: 1
				},
				success: function(json) {

					if (json)
					{
						html = '<ul id="addons-search-results" class="dropdown-menu">';
						$(json).each( function (index, value) {
						if (value.count)
						{
							html += '<li class="addons-style-view-product search-option">'
								+ '<div class="media">'
								+ '<div class="media-body">'
								+ '<strong>' + value.cname + '</strong>'
								+ '<br />(' + value.count + ' results)'
								+ '<p style="display:none;">' + value.link_rewrite + '?search_query=' + $("#addons-search-box").val() + '</p>'
								+ '</div></div></li>';
						}
						else
						{
								html += '<li class="addons-style-view-product search-option">'
								+ '<div class="media">'
								+ '<img class="media-object pull-left" width="28" src="https://medias2.prestastore.com/img/pico/' + value.id_product + '-mini.jpg" />'
								+ '<div class="media-body">'
								+ '<strong>' + value.name + '</strong>'
								+ '<br />' + value.cname
								+ '<p style="display:none;">' + value.product_link + '</p>'
								+ '</div></div></li>';
						}

							if (index != (json.length -1))
								html += '<li class="divider"></li>';
						});
						html += '</ul>';
					}
					if (json.length > 0)
						$("#addons-search-box").after(html);

					$("#addons-search-results").show();
				}
			});
		}, 500);

		timeouts.push(timeout_ref);
	});
});
