<?php /* Smarty version 2.6.10, created on 2006-01-18 13:55:06
         compiled from default/internal/popUp.tpl */ ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "default/include/header.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

<body leftmargin="10" topmargin="10">
<!-- PAGE INTERNAL START -->
<table width="90%" border="0" cellspacing="0" cellpadding="0" align="center">
	<tr>
		<td><div class="main_area"><?php echo $this->_tpl_vars['areaTitle']; ?>
</div></td>
		<td align="right"></td>
	</tr>
	<tr>
		<td class="internal" align="left" valign="top" colspan="2">
		<b><?php echo $this->_tpl_vars['subTitle']; ?>
</b><br><?php echo $this->_tpl_vars['description']; ?>

		<br>
		<?php echo $this->_tpl_vars['body']; ?>

		</td>
	</tr>
	<?php if ($this->_tpl_vars['buttClose'] != ""): ?>
	<tr>
		<td colspan="2" align="right" valign="top" class="internal"><hr noshade size="1"><?php echo $this->_tpl_vars['buttClose']; ?>
</td>
	</tr>
	<?php endif; ?>
</table>
<!-- PAGE INTERNAL END -->
</body>
</html>