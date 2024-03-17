/*
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 *  @author PrestaShop SA <contact@prestashop.com>
 *  @copyright  2007-2017 PrestaShop SA
 *  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

// This variables are defined in the dashboard view.tpl
// dashboard_ajax_url
// adminstats_ajax_url
// no_results_translation
// dashboard_use_push
// read_more

function refreshDashboard(module_name, use_push, extra) {
    var module_list = [];
    this.getWidget = function(module_id) {
        $.ajax({
            url: dashboard_ajax_url,
            data: {
                ajax: true,
                action: 'refreshDashboard',
                module: module_list[module_id],
                dashboard_use_push: Number(use_push),
                extra: String(extra)
            },
            // Ensure to get fresh data
            headers: { "cache-control": "no-cache" },
            cache: false,
            global: false,
            dataType: 'json',
            success: function(widgets) {
                for (var widget_name in widgets) {
                    for (var data_type in widgets[widget_name]) {
                        window[data_type](widget_name, widgets[widget_name][data_type]);
                    }
                }
                if (parseInt(dashboard_use_push) === 1) {
                    refreshDashboard(false, true);
                }
            },
            contentType: 'application/json'
        });
    };
    if (module_name === false) {
        $('.widget').each(function() {
            module_list.push($(this).attr('id'));
            if (!use_push) {
                $(this).addClass('loading');
            }
        });
    } else {
        module_list.push(module_name);
        if (!use_push) {
            $('#' + module_name + ' section').each(function() {
                $(this).addClass('loading');
            });
        }
    }
    for (var module_id in module_list) {
        if (use_push && !$('#' + module_list[module_id]).hasClass('allow_push')) {
            continue;
        }
        this.getWidget(module_id);
    }
}

function setDashboardDateRange(action) {
    $('#datepickerFrom, #datepickerTo').parent('.input-group').removeClass('has-error');
    var data = 'ajax=true&action=setDashboardDateRange&submitDatePicker=true&' + $('#calendar_form').serialize() + '&' + action + '=1';
    $.ajax({
        url: adminstats_ajax_url,
        data: data,
        dataType: 'json',
        type: 'POST',
        success: function(jsonData) {
            if (!jsonData.has_errors) {
                refreshDashboard(false, false);
                $('#datepickerFrom').val(jsonData.date_from);
                $('#datepickerTo').val(jsonData.date_to);
            } else {
                $('#datepickerFrom, #datepickerTo').parent('.input-group').addClass('has-error');
            }
        }
    });
}

function data_value(widget_name, data) {
    for (var data_id in data) {
        $('#' + data_id + ' ').html(data[data_id]);
        $('#' + data_id + ', #' + widget_name).closest('section').removeClass('loading');
    }
}

function data_trends(widget_name, data) {
    for (var data_id in data) {
        this.el = $('#' + data_id);
        this.el.html(data[data_id].value);
        if (data[data_id].way === 'up') {
            this.el.parent().removeClass('dash_trend_down').removeClass('dash_trend_right').addClass('dash_trend_up');
        } else if (data[data_id].way === 'down') {
            this.el.parent().removeClass('dash_trend_up').removeClass('dash_trend_right').addClass('dash_trend_down');
        } else {
            this.el.parent().removeClass('dash_trend_down').removeClass('dash_trend_up').addClass('dash_trend_right');
        }
        this.el.closest('section').removeClass('loading');
    }
}

function data_table(widget_name, data) {
    for (var data_id in data) {
        //fill header
        var tr = '<tr>';
        for (var header in data[data_id].header) {
            var head = data[data_id].header[header];
            var th = '<th ' + (head.class ? ' class="' + head.class + '" ' : '') + ' ' + (head.id ? ' id="' + head.id + '" ' : '') + '>';
            th += (head.wrapper_start ? ' ' + head.wrapper_start + ' ' : '');
            th += head.title;
            th += (head.wrapper_stop ? ' ' + head.wrapper_stop + ' ' : '');
            th += '</th>';
            tr += th;
        }
        tr += '</tr>';
        $('#' + data_id + ' thead').html(tr);

        //fill body
        $('#' + data_id + ' tbody').html('');

        if (typeof data[data_id].body === 'string') {
            $('#' + data_id + ' tbody').html('<tr><td class="text-center" colspan="' + data[data_id].header.length + '"><br/>' + data[data_id].body + '</td></tr>');
        } else if (data[data_id].body.length) {
            for (var body_content_id in data[data_id].body) {
                tr = '<tr>';
                for (var body_content in data[data_id].body[body_content_id]) {
                    var body = data[data_id].body[body_content_id][body_content];
                    var td = '<td ' + (body.class ? ' class="' + body.class + '" ' : '') + ' ' + (body.id ? ' id="' + body.id + '" ' : '') + '>';
                    td += (body.wrapper_start ? ' ' + body.wrapper_start + ' ' : '');
                    td += body.value;
                    td += (body.wrapper_stop ? ' ' + body.wrapper_stop + ' ' : '');
                    td += '</td>';
                    tr += td;
                }
                tr += '</tr>';
                $('#' + data_id + ' tbody').append(tr);
            }
        } else {
            $('#' + data_id + ' tbody').html('<tr><td class="text-center" colspan="' + data[data_id].header.length + '">' + no_results_translation + '</td></tr>');
        }
    }
}

function data_chart(widget_name, charts) {
    for (var chart_id in charts) {
        window[charts[chart_id].chart_type](widget_name, charts[chart_id]);
    }
}

function data_list_small(widget_name, data) {
    for (var data_id in data) {
        $('#' + data_id).html('');
        for (var item in data[data_id]) {
            $('#' + data_id).append('<li><span class="data_label">' + item + '</span><span class="data_value size_s">' + data[data_id][item] + '</span></li>');
        }
        $('#' + data_id + ', #' + widget_name).closest('section').removeClass('loading');
    }
}

function getBlogRss() {
    $.ajax({
        url: dashboard_ajax_url,
        data: {
            ajax: true,
            action: 'getBlogRss'
        },
        dataType: 'json',
        success: function(jsonData) {
            if (typeof jsonData !== 'undefined' && jsonData !== null && !jsonData.has_errors) {
                for (var article in jsonData.rss) {
                    var article_html = '<article><h4><a href="' + jsonData.rss[article].link + '" class="_blank" onclick="return !window.open(this.href);">' + jsonData.rss[article].title + '</a></h4><span class="dash-news-date text-muted">' + jsonData.rss[article].date + '</span><p>' + jsonData.rss[article].short_desc + ' <a href="' + jsonData.rss[article].link + '">' + read_more + '</a><p></article><hr/>';
                    $('.dash_news .dash_news_content').append(article_html);
                }
            } else {
                $('.dash_news').hide();
            }
        }
    });
}

function toggleDashConfig(widget) {
    var func_name = widget + '_toggleDashConfig';
    if ($('#' + widget + ' section.dash_config').hasClass('hide')) {
        $('#' + widget + ' section').not('.dash_config').slideUp(500, function() {
            $('#' + widget + ' section.dash_config').fadeIn(500).removeClass('hide');
            if (window[func_name] != undefined)
                window[func_name]();
        });
    } else {
        $('#' + widget + ' section.dash_config').slideUp(500, function() {
            $('#' + widget + ' section').not('.dash_config').slideDown(500).removeClass('hide');
            $('#' + widget + ' section.dash_config').addClass('hide');
            if (window[func_name] != undefined)
                window[func_name]();
        });
    }
}

function bindSubmitDashConfig() {
    $('.submit_dash_config').on('click', function() {
        saveDashConfig($(this).closest('section.widget').attr('id'));
        return false;
    });
}

function bindCancelDashConfig() {
    $('.cancel_dash_config').on('click', function() {
        toggleDashConfig($(this).closest('section.widget').attr('id'));
        return false;
    });
}

function saveDashConfig(widget_name) {
    $('section#' + widget_name + ' .form-group').removeClass('has-error');
    $('#' + widget_name + '_errors').remove();
    configs = '';

    $('#' + widget_name + ' form input, #' + widget_name + ' form textarea , #' + widget_name + ' form select').each(function() {
        if ($(this).attr('type') === 'radio' && !$(this).attr('checked')) {
            return;
        }
        configs += '&configs[' + $(this).attr('name') + ']=' + $(this).val();
    });

    data = 'ajax=true&action=saveDashConfig&module=' + widget_name + configs + '&hook=' + $('#' + widget_name).closest('[id^=hook]').attr('id');

    $.ajax({
        url: dashboard_ajax_url,
        data: data,
        dataType: 'json',
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            jAlert("TECHNICAL ERROR: \n\nDetails:\nError thrown: " + XMLHttpRequest + "\n" + 'Text status: ' + textStatus);
        },
        success: function(jsonData) {

            if (!jsonData.has_errors) {
                if (in_array(widget_name, ['dashactivity', 'dashproducts'])) {
                    location.reload();
                } else {
                    $('#' + widget_name).find('section').not('.dash_config').remove();
                    $('#' + widget_name).append($(jsonData.widget_html).find('section').not('.dash_config'));
                }
                refreshDashboard(widget_name);
                toggleDashConfig(widget_name);
            } else {
                errors_str = '<div class="alert alert-danger" id="' + widget_name + '_errors">';
                for (error in jsonData.errors) {
                    errors_str += jsonData.errors[error] + '<br/>';
                    $('#' + error).closest('.form-group').addClass('has-error');
                }
                errors_str += '</div>';
                $('section#' + widget_name + '_config header').after(errors_str);
                errors_str += '</div>';
            }
        }
    });
}

// This function is used to generate the tooltip content on the dashboard different sections
function getTooltipContent(title, content = '', color = '') {
    var tooltipContent = '';
    tooltipContent += '<div class="dashboard-chart-tooltip">';
        tooltipContent += '<label>';
        if (color) {
            tooltipContent += '<span class="tooltip-data-color"';
            tooltipContent += ' style="background:' + color + '"';
            tooltipContent += '">' + '</span> ';
        }
        tooltipContent += title + '</label>';
        tooltipContent += '<div class="tooltip-content">';
            tooltipContent += content;
        tooltipContent += '</div>';
    tooltipContent += '</div>';

    return tooltipContent;
}

$(document).ready(function() {
    $('#calendar_form input[type="submit"]').on('click', function(elt) {
        elt.preventDefault();
        setDashboardDateRange(elt.currentTarget.name);
    });

    refreshDashboard(false, false);
    bindSubmitDashConfig();
    bindCancelDashConfig();

    $('#page-header-desc-configuration-switch_demo').tooltip().click(function(e) {
        $.ajax({
            url: dashboard_ajax_url,
            data: {
                ajax: true,
                action: 'setSimulationMode',
                PS_DASHBOARD_SIMULATION: $(this).find('i').hasClass('process-icon-toggle-on') ? 0 : 1
            },
            success: function(result) {
                if ($('#page-header-desc-configuration-switch_demo i').hasClass('process-icon-toggle-on')) {
                    $('#page-header-desc-configuration-switch_demo i').removeClass('process-icon-toggle-on').addClass('process-icon-toggle-off');
                } else {
                    $('#page-header-desc-configuration-switch_demo i').removeClass('process-icon-toggle-off').addClass('process-icon-toggle-on');
                }
                refreshDashboard(false, false);
            }
        });
    });

    $(document).on('change', '.stats-filter-hotel', function() {
        $('#submit-stats-hotel').removeAttr('disabled');
        $(this).closest('form').submit();
    });
});
