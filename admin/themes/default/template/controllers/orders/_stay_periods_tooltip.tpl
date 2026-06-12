{*
 * Hidden template used by orders_stay_periods.js to build the Stay Periods
 * jQuery UI tooltip. The JS clones .tooltip_cont, populates .tip-body
 * with the period rows, and passes the result as the tooltip content.
 *}
<style>
    .stay-period-tooltip {
        border: none;
        box-shadow: 0 0 5px #aaa;
        -webkit-box-shadow: 0 0 5px #aaa;
        padding-bottom: 10px;
        min-width: 260px;
    }
    .stay-period-tooltip .tip_date {
        font-weight: bold;
    }
    .stay-period-tooltip .tip-body {
        display: table;
        width: 100%;
        margin-top: 6px;
    }
    .stay-period-tooltip .stay_period {
        display: table-row;
    }
    .stay-period-tooltip .tip_element_head,
    .stay-period-tooltip .tip_element_value {
        display: table-cell;
        padding: 3px 6px;
    }
    .stay-period-tooltip .tip_element_value {
        text-align: right;
    }
    .stay-period-tooltip .stay_period_header .tip_element_head,
    .stay-period-tooltip .stay_period_header .tip_element_value {
        border-bottom: 1px solid #ddd;
        padding-bottom: 5px;
    }
</style>
<div id="stay-period-tpl"
     style="display:none;"
     data-label-dates="{l s='Dates'}"
     data-label-rooms="{l s='Rooms'}">
    <div class="tooltip_cont">
        <div class="tip_header">
            <div class="tip_date">{l s='Stay Periods'}</div>
        </div>
        <div class="tip-body"></div>
    </div>
</div>
