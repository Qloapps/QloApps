{foreach $list as $cart_rule}
	<tr class="conf_body">
		<td bgcolor="#f8f8f8" colspan="4" style="border:1px solid #D6D4D4;color:#333;padding:7px 0">
			<table class="table" style="width:100%;border-collapse:collapse">
				<tr>
					<td width="10" style="color:#333;padding:0"></td>
					<td align="right" style="color:#333;padding:0">
						<span style="font-size:12px; font-family:Open-sans, sans-serif; color:#555454;">
							<strong>{$cart_rule['voucher_name']}</strong>
						</span>
					</td>
					<td width="10" style="color:#333;padding:0"></td>
				</tr>
			</table>
		</td>
		<td bgcolor="#f8f8f8" colspan="5" style="border:1px solid #D6D4D4;color:#333;padding:7px 0">
			<table class="table" style="width:100%;border-collapse:collapse">
				<tr>
					<td width="10" style="color:#333;padding:0"></td>
					<td align="right" style="color:#333;padding:0">
						<span style="font-size:12px; font-family:Open-sans, sans-serif; color:#555454;">
							{$cart_rule['voucher_reduction']}
						</span>
					</td>
					<td width="10" style="color:#333;padding:0"></td>
				</tr>
			</table>
		</td>
	</tr>
{/foreach}
