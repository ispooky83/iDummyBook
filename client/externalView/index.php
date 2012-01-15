<?php
/**
 * Dummy Viewer
 *
 * @author Lorenzo Monaco
 */
$inputDir= "/home/loren/testTattilo";
$printerName= "Phaser";

//Elimino file inutili
exec("rm ".$inputDir."/._*");

//Apro la directory per il polling
$dh  = opendir($inputDir);
while (false !== ($filename = readdir($dh))) {
   $files[] = $filename;
}

foreach($files as $k=>$v){
	if(strpos($v, ".txt")===false){
		
	}else{
		$output.= "<tr>";
		$output.= "<td class=\"style4\">".substr($v, 0, -4).".pdf [".ceil(filesize($inputDir."/".substr($v, 0, -4).".pdf")/1024)." KB]</td>";
		$output.= "<td class=\"style4\">".date("F d Y H:i:s.", filemtime($inputDir."/".substr($v, 0, -4).".pdf"))."</td>";
		$output.= "<td align=\"center\">
		<form action=\"index.php?act=1\" method=\"post\">
			<input type=\"hidden\" name=\"lpFile\" value=\"".substr($v, 0, -4)."\">
			<input type=\"Submit\" value=\"stampa\" class=\"style4\">
		</form>";
		$output.= "</tr>";
	}

//libero le risorse in ram
	//clearstatcache();
}

//chiudo la directory
closedir($dh);

//esecuzione stampa
if(isset($_GET['act']) AND $_GET['act']==1){
	exec("lp -d ".$printerName." ".$inputDir."/\"".$_POST['lpFile'].".pdf\"", $out, $return);
	if($return==0){
		$alert.= "<script>alert('File in coda per la stampa')</script>";
	}else{
		$alert.= "<script>alert('Impossibile inviare il file alla stampante')</script>";
	}
	//Cancello il file txt ed il file pdf
	chmod($inputDir."/".$_POST['lpFile'].".pdf", 0777);
	chmod($inputDir."/".$_POST['lpFile'].".txt", 0777);
	unlink($inputDir."/".$_POST['lpFile'].".pdf");
	unlink($inputDir."/".$_POST['lpFile'].".txt");
	//Formatto l'output per il log
	foreach($out as $key=>$value){
		$retVal.= date("d.m.Y H:i")."= ".$_POST['lpFile'].".pdf -> ".$value."";
	}
	exec('echo "'.$retVal.'" >> printLog.log');
	
	$redir= "<script>window.location='index.php'</script>";
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title> DUMMYBOOK VIEWER</title>
<?
if(isset($alert)){
	echo $alert;
}

if(isset($redir)){
	echo $redir;
}
?>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<style type="text/css">
<!--
.style1 {
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-weight: bold;
	color: #333333;
}
.style2 {
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: 10px;
	font-weight: bold;
	color: #999999;
}
.style3 {
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: 12px;
	font-weight: bold;
	color: #FFFFFF;
}
.style4 {
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: 12px;
	font-weight: bold;
	color: #000000;
}
.style5 {
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: 9px;
	font-weight: bold;
	color: Red;
}
-->
</style>
</head>

<body>
<p><span class="style1"> DUMMYBOOK VIEWER</span>
  <br>
    <span class="style2">Visualizzazione elementi per la stampa</span></p>
<table width="700" border="0" cellspacing="2" cellpadding="2">
  <tr>
    <td colspan="3" align="right"><a href="printLog.log" target="_blank" class="style5">visualizza il logfile</a></td>
  </tr>
  <tr bgcolor="#003366">
    <td width=""><span class="style3">NOME FILE </span></td>
    <td width="" class="style3">DATA</td>
    <td width="" class="style3">STAMPA</td>
  </tr>
 <?
 echo $output;
 ?>
</table>
</body>
</html>