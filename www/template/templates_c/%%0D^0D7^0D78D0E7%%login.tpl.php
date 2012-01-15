<?php /* Smarty version 2.6.10, created on 2005-11-29 17:55:53
         compiled from default/login.tpl */ ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "default/include/header.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
  $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "default/include/pHeaderLogin.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
  $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "default/include/subheader.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

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
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "default/include/footer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>