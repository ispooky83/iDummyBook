{include file="default/include/header.tpl"}
{include file="default/include/pHeaderLogin.tpl"}
{include file="default/include/subheader.tpl"}

<!-- PAGE INTERNAL START -->
<form action="index.php" method="POST">
<table border="0" align="center" width="" cellpadding="2" cellspacing="2">
	<tr>
		<td class="internal">USERNAME:</td>
		<td><input type="text" name="username" class="textform"></td>
	</tr>
	<tr>
		<td class="internal">PASSWORD:</td>
		<td><input type="password" name="password" class="textform"></td>
	</tr>
	<tr>
		<td colspan="2" align="right">
			<input type="hidden" name="authValue" value="1" class="pulsanti">
			<input type="submit" value="INVIA" class="pulsanti">
		</td>
	</tr>
</table>
</form>
<!-- PAGE INTERNAL END -->
{include file="default/include/footer.tpl"}