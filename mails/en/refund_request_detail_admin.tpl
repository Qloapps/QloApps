{if isset($list)}
	{foreach from=$list key=data_k item=data_v}
		<tr>
			<td style="border:1px solid #D6D4D4;">
				<table class="table">
					<tr>
						<td width="10">&nbsp;</td>
						<td  class="text-center">
							<font size="2" face="Open-sans, sans-serif" color="#555454">
								{$data_v['room_num']}
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
								{$data_v['room_type_name']}
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
								{$data_v['hotel_name']}
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
								{$data_v['date_from']|date_format:"%d-%b-%G"} To {$data_v['date_to']|date_format:"%d-%b-%G"}
							</font>
						</td>
						<td width="10">&nbsp;</td>
					</tr>
				</table>
			</td>
		</tr>
	{/foreach}
{/if}
<style>
	.pull-right {
		float: right;
	}
</style>
