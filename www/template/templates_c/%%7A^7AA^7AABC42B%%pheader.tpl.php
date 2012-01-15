<?php /* Smarty version 2.6.10, created on 2006-03-01 10:58:55
         compiled from default/include/pheader.tpl */ ?>
<!-- BODY START -->
<body bgcolor="White">
<!-- PAGE HEADER START -->
<table width="770" border="0" cellspacing="0" cellpadding="0" align="center">
  <tr>
	<!-- BENCHMARK UNIT -->
	<?php if ($this->_tpl_vars['execTime'] != ""): ?>
	<td class="internal">BenchmarkUnit::ExecutionTime -> <b><?php echo $this->_tpl_vars['execTime']; ?>
</b> sec.</td>
	<?php endif; ?>
	<!-- BENCHMARK UNIT END -->
	<!-- USER AD ROLE -->
  	<!-- <td class="internal" align="left">Utente: <b><?php echo $this->_tpl_vars['user']; ?>
</b>&nbsp;Ruolo: <b><?php echo $this->_tpl_vars['userGroup']; ?>
</b></td> -->
  	<!-- USER AD ROLE END -->
  	<!-- CHANGE DOMAIN -->
	<?php if ($this->_tpl_vars['changeDomain'] == 1): ?>
	<td class="internal" align="right"><b>Dominio: </b><?php echo $this->_tpl_vars['selectDomain']; ?>
</td>
	<?php endif; ?>
	<!-- CHANGE DOMAIN END -->
  </tr>
  <tr>
    <td class="header" colspan="2"><img src="<?php echo $this->_tpl_vars['imgPath']; ?>
arcipelago_logo.jpg" width="242" height="65"></td>
  </tr>
</table>
<!-- PAGE HEADER END -->
 