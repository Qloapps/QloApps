/**
 * plugin.js
 *
 * Copyright, Alberto Peripolli
 * Released under Creative Commons Attribution-NonCommercial 3.0 Unported License.
 *
 * Contributing: https://github.com/trippo/ResponsiveFilemanager
 */
tinymce.PluginManager.add("filemanager",function(e){function t(t,n,r,i){urltype=2;if(r=="image"){urltype=1}if(r=="media"){urltype=3}var s="RESPONSIVE FileManager";if(typeof e.settings.filemanager_title!=="undefined"&&e.settings.filemanager_title)s=e.settings.filemanager_title;var o="";var u="false";if(typeof e.settings.filemanager_sort_by!=="undefined"&&e.settings.filemanager_sort_by)o=e.settings.filemanager_sort_by;if(typeof e.settings.filemanager_descending!=="undefined"&&e.settings.filemanager_descending)u=e.settings.filemanager_descending;tinymce.activeEditor.windowManager.open({title:s,file:e.settings.external_filemanager_path.replace(/\/\/filemanager/, '\/filemanager')+"dialog.php?type="+urltype+"&descending="+u+"&sort_by="+o+"&lang="+e.settings.language,width:860,height:570,resizable:true,maximizable:true,inline:1},{setUrl:function(n){var r=i.document.getElementById(t);r.value=e.convertURL(n);if("fireEvent"in r){r.fireEvent("onchange")}else{var s=document.createEvent("HTMLEvents");s.initEvent("change",false,true);r.dispatchEvent(s)}}})}tinymce.activeEditor.settings.file_browser_callback=t;return false})
