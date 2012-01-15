<!-- BODY START -->
<body bgcolor="White">
<!-- PAGE HEADER START -->
<table width="770" border="0" cellspacing="0" cellpadding="0" align="center">
  <tr>
	<!-- BENCHMARK UNIT -->
	{if $execTime != ""}
	<td class="internal">BenchmarkUnit::ExecutionTime -> <b>{$execTime}</b> sec.</td>
	{/if}
	<!-- BENCHMARK UNIT END -->
	<!-- USER AD ROLE -->
  	<!-- <td class="internal" align="left">Utente: <b>{$user}</b>&nbsp;Ruolo: <b>{$userGroup}</b></td> -->
  	<!-- USER AD ROLE END -->
  	<!-- CHANGE DOMAIN -->
	{if $changeDomain == 1}
	<td class="internal" align="right"><b>Dominio: </b>{$selectDomain}</td>
	{/if}
	<!-- CHANGE DOMAIN END -->
  </tr>
  <tr>
    <td class="header" colspan="2"><img src="{$imgPath}arcipelago_logo.jpg" width="242" height="65"></td>
  </tr>
</table>
<!-- PAGE HEADER END -->
 