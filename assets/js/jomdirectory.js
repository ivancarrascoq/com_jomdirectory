window.addEventListener('load', function () {
	var url = '/components/com_jomcomdev/assets/js/mdb.min.js';
	var script = document.createElement('script');
	script.type = "text/javascript";
	script.src = url;
	var path = window.location.href;
	//console.log(path);
	if (path.indexOf('view=admin_listings') == -1) {
		document.head.appendChild(script);
	}
});
var $ = jQuery;
//console.log('ready');
(function ($) {
	$(document).ready(function () {
		var bootstrapLoaded = (typeof $().carousel == 'function');
		var mootoolsLoaded = (typeof MooTools != 'undefined');
		if (bootstrapLoaded && mootoolsLoaded) {
			Element.implement({
				hide: function () {
					return this;
				},
				show: function (v) {
					return this;
				},
				slide: function (v) {
					return this;
				}
			});
		}
	});
})(jQuery);