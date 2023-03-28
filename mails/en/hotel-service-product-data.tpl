{if isset($list) && $list}
    <tr>
        <td style="padding:7px 0">
            <font size="2" face="Open-sans, sans-serif" color="#555454">
                <table class="table table-recap" bgcolor="#ffffff" style="width:100%;border-collapse:collapse"><!-- Title -->
                    <tr>
                        <th bgcolor="#f8f8f8" style="border:1px solid #D6D4D4;background-color: #fbfbfb;color: #333;font-family: Arial;font-size: 13px;padding: 10px;">Image</th>
                        <th bgcolor="#f8f8f8" style="border:1px solid #D6D4D4;background-color: #fbfbfb;color: #333;font-family: Arial;font-size: 13px;padding: 10px;">Name</th>
                        <th bgcolor="#f8f8f8" style="border:1px solid #D6D4D4;background-color: #fbfbfb;color: #333;font-family: Arial;font-size: 13px;padding: 10px;">Unit Price</th>
                        <th bgcolor="#f8f8f8" style="border:1px solid #D6D4D4;background-color: #fbfbfb;color: #333;font-family: Arial;font-size: 13px;padding: 10px;" width="17%">Qty</th>
                        <th bgcolor="#f8f8f8" style="border:1px solid #D6D4D4;background-color: #fbfbfb;color: #333;font-family: Arial;font-size: 13px;padding: 10px;" width="17%">Total</th>
                    </tr>
                    <tbody>
                        <tr>
                            <td colspan="5" style="border:1px solid #D6D4D4;text-align:center;color:#777;padding:7px 0">
                                {foreach from=$list key=key item=product}
                                    <tr>
                                        <td style="border:1px solid #D6D4D4;">
                                            <table class="table">
                                                <tr>
                                                    <td width="10">&nbsp;</td>
                                                    <td class="text-center">
                                                        <font size="2" face="Open-sans, sans-serif" color="#555454">
                                                            <img src="{$product['cover_img']}" class="img-responsive" />
                                                        </font>
                                                    </td>
                                                    <td width="10">&nbsp;</td>
                                                </tr>
                                            </table>
                                        </td>
                                        <td style="border:1px solid #D6D4D4;">
                                            <table class="table">
                                                <tr>
                                                    <td width="10">&nbsp;</td>
                                                    <td  class="text-center">
                                                        <font size="2" face="Open-sans, sans-serif" color="#555454">
                                                            {$product['name']}
                                                        </font>
                                                    </td>
                                                    <td width="10">&nbsp;</td>
                                                </tr>
                                            </table>
                                        </td>
                                        <td style="border:1px solid #D6D4D4;">
                                            <table class="table">
                                                <tr>
                                                    <td width="10">&nbsp;</td>
                                                    <td align="right"  class="text-center">
                                                        <font size="2" face="Open-sans, sans-serif" color="#555454">
                                                            {convertPrice price=$product['unit_price']}
                                                        </font>
                                                    </td>
                                                    <td width="10">&nbsp;</td>
                                                </tr>
                                            </table>
                                        </td>
                                        <td style="border:1px solid #D6D4D4;">
                                            <table class="table">
                                                <tr>
                                                    <td width="10">&nbsp;</td>
                                                    <td align="right"  class="text-center">
                                                        <font size="2" face="Open-sans, sans-serif" color="#555454">
                                                            {$product['quantity']}
                                                        </font>
                                                    </td>
                                                    <td width="10">&nbsp;</td>
                                                </tr>
                                            </table>
                                        </td>
                                        <td style="border:1px solid #D6D4D4;">
                                            <table class="table">
                                                <tr>
                                                    <td width="10">&nbsp;</td>
                                                    <td align="right"  class="text-center">
                                                        <font size="2" face="Open-sans, sans-serif" color="#555454">
                                                            {convertPrice price=$product['price']}
                                                        </font>
                                                    </td>
                                                    <td width="10">&nbsp;</td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                {/foreach}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </font>
        </td>
    </tr>
<style>
    .pull-right {
        float: right;
    }
</style>
{/if}
