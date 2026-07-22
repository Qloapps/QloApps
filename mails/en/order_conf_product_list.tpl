{foreach $list as $product}
    <tr>
        <td style="border:1px solid #D6D4D4;">
            <table class="table">
                <tr>
                    <td width="10">&nbsp;</td>
                    <td>
                        <span style="font-size:12px; font-family:Open-sans, sans-serif; color:#555454;">
                            {$product['reference']}
                        </span>
                    </td>
                    <td width="10">&nbsp;</td>
                </tr>
            </table>
        </td>
        <td style="border:1px solid #D6D4D4;">
            <table class="table">
                <tr>
                    <td width="10">&nbsp;</td>
                    <td>
                        <span style="font-size:12px; font-family:Open-sans, sans-serif; color:#555454;">
                            <strong>{$product['name']}</strong>
                        </span>
                    </td>
                    <td width="10">&nbsp;</td>
                </tr>
            </table>
        </td>
        <td style="border:1px solid #D6D4D4;">
            <table class="table">
                <tr>
                    <td width="10">&nbsp;</td>
                    <td align="right">
                        <span style="font-size:12px; font-family:Open-sans, sans-serif; color:#555454;">
                            {$product['unit_price']}
                        </span>
                    </td>
                    <td width="10">&nbsp;</td>
                </tr>
            </table>
        </td>
        <td style="border:1px solid #D6D4D4;">
            <table class="table">
                <tr>
                    <td width="10">&nbsp;</td>
                    <td align="right">
                        <span style="font-size:12px; font-family:Open-sans, sans-serif; color:#555454;">
                            {$product['quantity']}
                        </span>
                    </td>
                    <td width="10">&nbsp;</td>
                </tr>
            </table>
        </td>
        <td style="border:1px solid #D6D4D4;">
            <table class="table">
                <tr>
                    <td width="10">&nbsp;</td>
                    <td align="right">
                        <span style="font-size:12px; font-family:Open-sans, sans-serif; color:#555454;">
                            {$product['price']}
                        </span>
                    </td>
                    <td width="10">&nbsp;</td>
                </tr>
            </table>
        </td>
    </tr>
    {foreach $product['customization'] as $customization}
        <tr>
        <td colspan="2" style="border:1px solid #D6D4D4;">
            <table class="table">
                <tr>
                    <td width="10">&nbsp;</td>
                    <td>
                        <span style="font-size:12px; font-family:Open-sans, sans-serif; color:#555454;">
                            <strong>{$product['name']}</strong><br>
                            {$customization['customization_text']}
                        </span>
                    </td>
                    <td width="10">&nbsp;</td>
                </tr>
            </table>
        </td>
        <td style="border:1px solid #D6D4D4;">
            <table class="table">
                <tr>
                    <td width="10">&nbsp;</td>
                    <td align="right">
                        <span style="font-size:12px; font-family:Open-sans, sans-serif; color:#555454;">
                            {$product['unit_price']}
                        </span>
                    </td>
                    <td width="10">&nbsp;</td>
                </tr>
            </table>
        </td>
        <td style="border:1px solid #D6D4D4;">
            <table class="table">
                <tr>
                    <td width="10">&nbsp;</td>
                    <td align="right">
                        <span style="font-size:12px; font-family:Open-sans, sans-serif; color:#555454;">
                            {$customization['customization_quantity']}
                        </span>
                    </td>
                    <td width="10">&nbsp;</td>
                </tr>
            </table>
        </td>
        <td style="border:1px solid #D6D4D4;">
            <table class="table">
                <tr>
                    <td width="10">&nbsp;</td>
                    <td align="right">
                        <span style="font-size:12px; font-family:Open-sans, sans-serif; color:#555454;">
                            {$customization['quantity']}
                        </span>
                    </td>
                    <td width="10">&nbsp;</td>
                </tr>
            </table>
        </td>
    </tr>
    {/foreach}
{/foreach}