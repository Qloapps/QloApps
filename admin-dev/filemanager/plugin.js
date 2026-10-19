/**
 * plugin.js
 *
 * Copyright, Alberto Peripolli
 * Released under Creative Commons Attribution-NonCommercial 3.0 Unported License.
 *
 * Contributing: https://github.com/trippo/ResponsiveFilemanager
 */

tinymce.PluginManager.add('filemanager', function(editor) {

	tinymce.activeEditor.settings.file_browser_callback = filemanager;
	
	function filemanager (id, value, type, win) {
		// DEFAULT AS FILE
		urltype=2;
		if (type=='image') { urltype=1; }
		if (type=='media') { urltype=3; }
                var title="RESPONSIVE FileManager";
                if (typeof editor.settings.filemanager_title !== "undefined" && editor.settings.filemanager_title) 
                    title=editor.settings.filemanager_title;
                var sort_by="";
                var descending="false";
		if (typeof editor.settings.filemanager_sort_by !== "undefined" && editor.settings.filemanager_sort_by) 
                    sort_by=editor.settings.filemanager_sort_by;
		if (typeof editor.settings.filemanager_descending !== "undefined" && editor.settings.filemanager_descending) 
                    descending=editor.settings.filemanager_descending;
		tinymce.activeEditor.windowManager.open({
			title: title,
			file: editor.settings.external_filemanager_path+'dialog.php?type='+urltype+'&descending='+descending+'&sort_by='+sort_by+'&lang='+editor.settings.language,
			width: 860,  
			height: 570,
			resizable: true,
			maximizable: true,
			inline: 1
			}, {
			setUrl: function (url) {
				var fieldElm = win.document.getElementById(id);
				fieldElm.value = editor.convertURL(url);
				if ("fireEvent" in fieldElm) {
					fieldElm.fireEvent("onchange")
				} else {
					var evt = document.createEvent("HTMLEvents");
					evt.initEvent("change", false, true);
					fieldElm.dispatchEvent(evt);
				}
			}
		});
	};
	return false;
});
