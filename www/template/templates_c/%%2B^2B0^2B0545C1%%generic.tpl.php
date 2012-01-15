<?php /* Smarty version 2.6.10, created on 2006-03-01 10:48:14
         compiled from default/generic.tpl */ ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "default/include/header.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
  $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "default/include/pheader.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
  $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "default/include/subheader.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

<!-- PAGE INTERNAL START -->
<table width="770" border="0" cellspacing="0" cellpadding="0" align="center">
	<tr>
		<td class="internal" align="left" valign="top" width="100%">
			<table border="0" cellpadding="0" cellspacing="0" width="98%" align="center">
				<tr>
					<td><div class="main_area"><?php echo $this->_tpl_vars['areaTitle']; ?>
</div></td>
					<td align="right"><?php echo $this->_tpl_vars['rightMenu']; ?>
</td>
				</tr>
				<tr>
					<td class="internal" align="left" valign="top" colspan="2">
						<b><?php echo $this->_tpl_vars['subTitle']; ?>
</b><br><?php echo $this->_tpl_vars['description']; ?>

						<br>
						<?php echo $this->_tpl_vars['body']; ?>

						<?php if ($this->_tpl_vars['bodyImage'] != ""): ?>
							<p align="center"><?php echo $this->_tpl_vars['bodyImage']; ?>
</p>
						<?php endif; ?>
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>
<!-- PAGE INTERNAL END -->
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "default/include/footer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>