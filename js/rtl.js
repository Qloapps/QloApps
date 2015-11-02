/*
 * dmartl.js
 * DM Auto RTL - Auto RTL all inline style in page using jquery
 * Autor: Danoosh Miralayi
 * Website: presta-shop.ir
 * License: MIT
 * Find it here: https://github.com/Danoosh/DM-Auto-RTL
*/
$(document).ready(function () {
    $('[style]').each(function (index) {
        var styles_old = $(this).attr('style');
        styles_old = styles_old.split(';');
        var styles = {};
        var s = '';
        var i = '';
        var v = '';
        for (var x = 0, l = styles_old.length; x < l; x++) {
            s = styles_old[x].split(':');
            i = $.trim(s[0]);
            styles[makeGeneralRTL(i)] = makeValueRTL(i, $.trim(s[1]));
        }
        $(this).removeAttr("style");
        $(this).css(styles);
    });
});

function makeGeneralRTL(index) {
    var res = index.replace(/right/g, "rtemp");
    res = res.replace(/left/g, "right");
    res = res.replace(/rtemp/g, "left");
    return res;
}
function makeValueRTL(property, value) {
    if (property.match(/text-align|float/)) {
        return makeGeneralRTL(value);
    }
    if (property.match(/^background(-position)?$/))
        return value.replace(/^((?!#\((.*)\)).)*$/, ' ') + makeGeneralRTL(value.replace(/(\s)?url\((.*)\)(\s)?/, ''));
    if (property.match(/margin|padding/) && value.match(/(\S*) (\S*) (\S*) (\S*)/))
        return value.replace(/(\S*) (\S*) (\S*) (\S*)/, "$1 $4 $3 $2");
    return value;
}
/* end of file dmartl.js */
