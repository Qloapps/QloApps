{if !empty($list)}
	<tr class="conf_body">
		<td bgcolor="#f8f8f8" colspan="4" style="border:1px solid #D6D4D4;color:#333;padding:7px 0">
			<table class="table" style="width:100%;border-collapse:collapse">
				<tr>
					<td width="10" style="color:#333;padding:0">&nbsp;</td>
					<td align="right" style="color:#333;padding:0">
						<font size="2" face="Open-sans, sans-serif" color="#555454">
							<strong>Total Paid</strong>
						</font>
					</td>
					<td width="10" style="color:#333;padding:0">&nbsp;</td>
				</tr>
			</table>
		</td>
		<td bgcolor="#f8f8f8" colspan="4" style="border:1px solid #D6D4D4;color:#333;padding:7px 0">
			<table class="table" style="width:100%;border-collapse:collapse">
				<tr>
					<td width="10" style="color:#333;padding:0">&nbsp;</td>
					<td align="right" style="color:#333;padding:0">
						<font size="4" face="Open-sans, sans-serif" color="#555454">
							{$list['total_paid_amount']}
						</font>
					</td>
					<td width="10" style="color:#333;padding:0">&nbsp;</td>
				</tr>
			</table>
		</td>
	</tr>

	<tr class="conf_body">
		<td bgcolor="#f8f8f8" colspan="4" style="border:1px solid #D6D4D4;color:#333;padding:7px 0">
			<table class="table" style="width:100%;border-collapse:collapse">
				<tr>
					<td width="10" style="color:#333;padding:0">&nbsp;</td>
					<td align="right" style="color:#333;padding:0">
						<font size="2" face="Open-sans, sans-serif" color="#555454">
							<strong>Total Due</strong>
						</font>
					</td>
					<td width="10" style="color:#333;padding:0">&nbsp;</td>
				</tr>
			</table>
		</td>
		<td bgcolor="#f8f8f8" colspan="4" style="border:1px solid #D6D4D4;color:#333;padding:7px 0">
			<table class="table" style="width:100%;border-collapse:collapse">
				<tr>
					<td width="10" style="color:#333;padding:0">&nbsp;</td>
					<td align="right" style="color:#333;padding:0">
						<font size="4" face="Open-sans, sans-serif" color="#555454">
							{$list['total_due_amount']}
						</font>
					</td>
					<td width="10" style="color:#333;padding:0">&nbsp;</td>
				</tr>
			</table>
		</td>
	</tr>
{/if}