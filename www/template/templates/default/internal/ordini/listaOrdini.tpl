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
		<td class="internal" align="center">
			<br><br>
			<form action='' method="post">
			<table border="0" align="center">
				<tr>
					<td class="internal">Scegli il prodotto di cui voi visualizzare gli ordini:</td>
					<td class="internal"><select name="prodotto" class="textform">
						{$option}
						</select>
					</td>
				</tr>
				<tr>
					<td colspan="2" align="right"><input type="submit" value="Invia" class="pulsanti"></td>
				</tr>
			</table>
			</form>
		</td>
	</tr>
</table>
<!-- PAGE INTERNAL END -->
{include file="default/include/footer.tpl"}