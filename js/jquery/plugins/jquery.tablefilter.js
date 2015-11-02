/*
 * Copyright (c) 2008 Greg Weber greg at gregweber.info
 * Dual licensed under the MIT and GPLv2 licenses just as jQuery is:
 * http://jquery.org/license
 *
 * documentation at http://gregweber.info/projects/uitablefilter
 *
 * allows table rows to be filtered (made invisible)
 * <code>
 * t = $('table')
 * $.uiTableFilter( t, phrase )
 * </code>
 * arguments:
 *   jQuery object containing table rows
 *   phrase to search for
 *   optional arguments:
 *     column to limit search too (the column title in the table header)
 *     ifHidden - callback to execute if one or more elements was hidden
 */
(function($){$.uiTableFilter=function(jq,phrase,column,ifHidden){var new_hidden=false;if(this.last_phrase===phrase)return false;var phrase_length=phrase.length;var words=phrase.toLowerCase().split(" ");var matches=function(elem){elem.show()};var noMatch=function(elem){elem.hide();new_hidden=true};var getText=function(elem){return elem.text()};if(column){var index=null;jq.find("thead > tr:last > th").each(function(i){if($.trim($(this).text())==column){index=i;return false}});if(index==null)throw"given column: "+
column+" not found";getText=function(elem){return $(elem.find("td:eq("+index+")")).text()}}if(words.size>1&&phrase.substr(0,phrase_length-1)===this.last_phrase){if(phrase[-1]===" "){this.last_phrase=phrase;return false}var words=words[-1];matches=function(elem){};var elems=jq.find("tbody:first > tr:visible")}else{new_hidden=true;var elems=jq.find("tbody:first > tr")}elems.each(function(){var elem=$(this);$.uiTableFilter.has_words(getText(elem),words,false)?matches(elem):noMatch(elem)});last_phrase=
phrase;if(ifHidden&&new_hidden)ifHidden();return jq};$.uiTableFilter.last_phrase="";$.uiTableFilter.has_words=function(str,words,caseSensitive){var text=caseSensitive?str:str.toLowerCase();for(var i=0;i<words.length;i++)if(text.indexOf(words[i])===-1)return false;return true}})(jQuery);
