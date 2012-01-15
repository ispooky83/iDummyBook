{include file="default/include/header.tpl"}
{include file="default/include/pheader.tpl"}
{include file="default/include/subheader.tpl"}

<!-- PAGE INTERNAL START -->
<table width="770" border="0" cellspacing="0" cellpadding="0" align="center">
	<tr>
		<td class="internal" align="center" width="150" valign="top">
			<table border="0" width="150" cellpadding="1" cellspacing="0">
			{$leftMenu}
			</table>
		</td>
		<td class="divi" align="left" valign="top"><img src="{$imgPath}blank.gif" width="10" height="100%"></td>
		<td class="internal" align="center">
			<br><br>
			<form action='step1.php?act=first' method="post">
			<table border="0" align="left">
				<tr>
					<td><div class="main_area">{$areaTitle}</div></td>
					<td align="right">{$rightMenu}</td>
				</tr>
				<tr>
					<td class="internal" colspan="2">
						<b>{$subTitle}</b><br>{$description}
						<br>
						{$option}	
					</td>
				</tr>	
				<tr>
					<td  align="right" class="internal" colspan="2"><input type="submit" value="Crea l'ordine" class="pulsanti"></td>
				</tr>
			</table>
			</form>
		</td>
	</tr>
</table>
<!-- PAGE INTERNAL END -->
{include file="default/include/footer.tpl"}