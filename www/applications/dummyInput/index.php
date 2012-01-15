<?php
/**
 * DummyBook Input
 *
 * @author Lorenzo Monaco
 */
require_once("../../../conf/systemConfig.php");
error_reporting(ERROR_LEVEL);
require_once("../../../classes/db/class.DBInterface.php");
require_once("../../../classes/system/class.SystemLogger.php");
require_once("../../../classes/ui/class.UserInterface.php");
require_once("../../../classes/ui/class.UIObjects.php");
require_once("../../../classes/util/class.Utils.php");
require_once("../../../classes/fs/class.XMLParser.php");
require_once("../../../classes/db/class.DataProcessors.php");

//Connessione generica al database
$dbInterface= new DBInterface(DB_USER, DB_PASSWORD, DB_HOST, DB_NAME);
$connect= $dbInterface->setConnect();

//Istanzio un nuovo oggetto dataProcessor
$dataProcessor= new DataProcessors();

//Form Manager
if($dataProcessor->actionValidator($_POST['act']) OR $dataProcessor->actionValidator($_GET['act'])){
	switch($_GET['act']){
		//Nuovo Dummy Book
		case "N":
			/*
			* INSERIRE CONTROLLI SULLA PRESENZA NEL DATABASE DI SEGNATURA ED ELEMENTO
			*/
			if(!empty($_POST['segnatura']) AND !empty($_POST['elemento'])){
				$output= "<script>alert('CREAZIONE DUMMYBOOK IN CORSO...');</script>";
				exec("/usr/local/apache2/htdocs/dummyBook/bin/dummyBook.php ".$_POST['segnatura']." ".$_POST['elemento']."", $outValue, $retValue);
				echo "/usr/local/apache2/htdocs/dummyBook/bin/dummyBook.php ".$_POST['segnatura']." ".$_POST['elemento']."<br>";
				print_r($retValue);
			}else{
				$contolVar= "ATTENZIONE: Valorizzare n. segnatura e n. elemento commessa";
			}
		break;
	}
}

//Output
if(isset($contolVar)){
	$output= $contolVar."<br>";
}
$output= "<form method=\"post\" action=\"index.php?act=N\">";
$output.= "<table border=\"0\">";
$output.= "<tr><td class=\"internal\">Segnatura:</td>";
$output.= "<td><input type=\"text\" name=\"segnatura\" class=\"textform\"></td>";
$output.= "</tr>";
$output.= "<tr><td class=\"internal\">Elemento commessa:</td>";
$output.= "<td><input type=\"text\" name=\"elemento\" class=\"textform\"></td>";
$output.= "</tr>";
$output.= "<tr><td></td>";
$output.= "<td><input type=\"submit\" value=\"Invia Dummy Book\" class=\"pulsanti\"></td>";
$output.= "</tr>";
$output.= "</form>";
$output.= "</table>";

/*################################ GUI OUTPUT ######################################*/
$outputVariables['pageTitle']= 'DummyBook Input';
$outputVariables['areaTitle']= 'DummyBook nput';
$outputVariables['subTitle']= "Input data form";
$outputVariables['body']= $output;

$userInterface= new UserInterface(false, TEMPLATE, $outputVariables, 'generic.tpl');
$userInterface->templateHandler();
$userInterface->guiOutput(1, 1, $connect);

//Disconnessione generica dal database
$dbInterface->setDisConnect($connect);
?>
