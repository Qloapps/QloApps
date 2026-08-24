Modernizr.load([
	{
		test: window.matchMedia,
		nope: ["themes/default/js/vendor/matchMedia.js", "themes/default/js/vendor/matchMedia.addListener.js"]
	},
	{
		load: ["themes/default/js/vendor/enquire.min.js", "themes/default/js/admin-theme.js"],
		complete: function() {
			$(document).trigger('admin-theme.js.ready');
		}
	}
]);