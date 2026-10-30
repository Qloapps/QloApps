<div class="panel">
    <div class="panel-heading">
        <i class="icon-clock-o"></i>
        {l s='Cron job setting' mod='qlocmconnector'}
    </div>

    {if $curl_missing}
        <div class="alert alert-danger">
            {l s='CURL extension is not installed or enabled on your server. Cron execution will not work until CURL is available.' mod='qlocmconnector'}
        </div>
    {/if}

    <div class="alert alert-info">
        <strong>
            {l s='For seamless syncing of availability from the PMS to the Channel Manager automatically, you need to set the following cron job to run every minute' mod='qlocmconnector'}
        </strong>
        <br><br>
        <strong>
            <code>* * * * * curl {$cron_url}</code>
        </strong>
    </div>
</div>
