{include file="default/include/header.tpl"}
{include file="default/include/pheader.tpl"}
{include file="default/include/subheader.tpl"}

<!-- PAGE INTERNAL START -->
<table width="770" border="0" cellspacing="0" cellpadding="0" align="center">
	<tr>
		<td class="internal" align="left" valign="top" width="100%">
			<table border="0" cellpadding="0" cellspacing="0" width="98%" align="center">
				<tr>
					<td><div class="main_area">{$areaTitle}</div></td>
					<td align="right">{$rightMenu}</td>
				</tr>
				<tr>
					<td class="internal" align="left" valign="top" colspan="2">
						<b>{$subTitle}</b><br>{$description}
						<br>
						{$body}
						{if $bodyImage!=""}
							<p align="center">{$bodyImage}</p>
						{/if}
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>
<!-- PAGE INTERNAL END -->
{include file="default/include/footer.tpl"}