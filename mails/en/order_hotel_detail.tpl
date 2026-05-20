{if isset($list['hotel_name']) && $list['hotel_name']}
    <font size="2" face="Open-sans, sans-serif" color="#555454">
        <p data-html-only="1" style="margin:3px 0 0px;font-weight:500;font-size:18px;padding-bottom: 10px;">
            {l s='Property details'}:
        </p>
        <p style="margin:3px 0 0px;">
            <span style="color:#333"><strong>{l s='Property Name'}:</strong></span> {$list['hotel_name']}<br />
            <span style="color:#333"><strong>{l s='Property Phone'}:</strong></span> {$list['hotel_phone']}<br />
            <span style="color:#333"><strong>{l s='Property Email'}:</strong></span> {$list['hotel_email']}<br />
            <span style="color:#333"><strong>{l s='Total Stays'}:</strong></span> {$list['num_rooms']}<br />
        </p>
    </font>
{/if}