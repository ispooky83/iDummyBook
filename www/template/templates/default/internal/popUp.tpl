{include file="default/include/header.tpl"}

<body leftmargin="10" topmargin="10">
<!-- PAGE INTERNAL START -->
<table width="90%" border="0" cellspacing="0" cellpadding="0" align="center">
	<tr>
		<td><div class="main_area">{$areaTitle}</div></td>
		<td align="right"></td>
	</tr>
	<tr>
		<td class="internal" align="left" valign="top" colspan="2">
		<b>{$subTitle}</b><br>{$description}
		<br>
		{$body}
		</td>
	</tr>
	{if $buttClose!=""}
	<tr>
		<td colspan="2" align="right" valign="top" class="internal"><hr noshade size="1">{$buttClose}</td>
	</tr>
	{/if}
</table>
<!-- PAGE INTERNAL END -->
</body>
</html>