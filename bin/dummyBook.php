#!/usr/local/bin/php
<?php
/**
 * Estimate Indigo Print Creator
 *
 * @author Luca Temperini
 */
require_once("/usr/local/apache2/htdocs/dummyBook/conf/systemConfig.php");
error_reporting(E_ALL);
require_once("/usr/local/apache2/htdocs/dummyBook/classes/system/class.SystemLogger.php");
require_once("/usr/local/apache2/htdocs/dummyBook/classes/db/class.DBInterface.php");
require_once("/usr/local/apache2/htdocs/dummyBook/classes/util/class.Benchmark.php");
require_once("/usr/local/apache2/htdocs/dummyBook/classes/util/class.Utils.php");
require_once("/usr/local/apache2/htdocs/dummyBook/classes/fs/class.ArcipelagoFS.php");
require_once("/usr/local/apache2/htdocs/dummyBook/classes/dummy/class.dummyBook.php");
require_once("/usr/local/apache2/htdocs/dummyBook/classes/net/class.FtpHandler.php");
	
// Connessione generica al database
$dbInterface= new DBInterface(DB_USER, DB_PASSWORD, DB_HOST, DB_NAME);
$connect= $dbInterface->setConnect();

// Valorizzo le variabili che conterranno i path
$pathBin="/usr/local/apache2/htdocs/dummyBook/bin";
$pathStoreLog="/usr/local/apache2/htdocs/dummyBook/store/log/";
$pathconfTex="/usr/local/apache2/htdocs/dummyBook/library/dummyTexTemplate/";
$pathStoreOut="/usr/local/apache2/htdocs/dummyBook/store/OUT/";
$pathStoreTmp="/usr/local/apache2/htdocs/dummyBook/store/tmp/";

// Local Printer Queue
$printerQueue= "CX250";
$printerQueueBrossura= "CX250brossura";
$printerQueueLibretto= "CX250libretto";

// Ciclo all'interno della directory di input
$dirCicle= new ArcipelagoFS();
$nolist=array(".", '.', "..");

//Questi 2 argomenti sono passati da riga di comando e rispettivamente sono: id segnatura e id elemento commessa 
$id_segnature_commessa= $argv[1];
$id_elemento_commessa= $argv[2];
$internal= $argv[3];
if (($id_segnature_commessa!=0)AND($id_elemento_commessa!=0)){

//Query che restituisce il codice della commessa, la base , l'altezza, gli intervalli di pagina, il tipo e il path delle basse risoluzioni dei PDF
	$query= "SELECT rif_cod_commessa, base, altezza, allestimento
			FROM elementi_commessa 
			WHERE id_elemento_commessa='".$id_elemento_commessa."'";
	$result= $dbInterface->execQuery($connect, $query, "",0);
	$result= $result[0];
	$codComm= $result['rif_cod_commessa'];
	$altezza= $result['altezza'];
	$base= $result['base'];
	$allestimento= $result['allestimento'];

	$query2="SELECT segnature_commessa.pag_da1,segnature_commessa.pag_a1, segnature_commessa.pag_da2, segnature_commessa.pag_a2, segnature_commessa.pieghe, 
			pagine_commessa.rif_ordine_tipo, 
			commesse.Path,
			commesse.rif_cod_cliente, 
			commesse.visualizzatore_id
			FROM segnature_commessa, pagine_commessa, commesse
			WHERE segnature_commessa.id_segnature_commessa=".$id_segnature_commessa."
			AND segnature_commessa.rif_id_elemento_commessa=".$id_elemento_commessa."
			AND segnature_commessa.rif_cod_commessa='".$codComm."'
			AND pagine_commessa.rif_id_segnature_commessa='".$id_segnature_commessa."'
			AND commesse.cod_commessa='".$codComm."'";
	$result2= $dbInterface->execQuery($connect, $query2, "",0);
	$result2= $result2[0];
	$pag_da1= $result2['pag_da1'];
   	$pag_a1= $result2['pag_a1'];
   	$pag_da2= $result2['pag_da2'];
   	$pag_a2= $result2['pag_a2'];
   	$pieghe= $result2['pieghe'];
   	$tipo= $result2['rif_ordine_tipo'];
   	$LowresPdf= $result2['Path']."/LowresPdf_".$codComm;

	print_r($result);
	print_r($result2);


//Creazione della variabile formata dalllo standard namin convenction con la relativa creazione della dir nell cartella di log e store   
    	$nameConv=$codComm."_".$tipo."@".$pag_da1."-".$pag_a1."-".$pag_da2."-".$pag_a2;
     	echo "$nameConv";
   	
    	mkdir($pathStoreOut.$nameConv."", 0755);
    	mkdir($pathStoreLog.$nameConv."", 0755);

//Creo l'array contenente le pagine
	$pageInt=new dummyBook();
	$arr=$pageInt->getPageInt($pag_da1,$pag_a1,$pag_da2,$pag_a2);
	print_r($arr);

  //Valorizzo in dirCont il contenuto della directory pathIN
//$dirCont=$dirCicle->getDirContents($pathIN,$nolist);

//Genero i file PDF in bassa risoluzione
	$includePdf=new dummyBook();
	$lowPdf=$includePdf->getLowPdf($codComm,$tipo,$arr,$pathStoreTmp,$LowresPdf);
	echo $lowPdf;
	echo "$allestimento \n"; 
//Genero il file layout.tex
	$includePdf=new dummyBook();
	$layout=$includePdf->getIncludePdf($altezza, $base, $allestimento, $codComm, $tipo, $arr, $pathconfTex, $pathStoreTmp, $nameConv);

//Eseguo il file layout.tex
	system("pdflatex ".$pathconfTex."layout_".$nameConv.".tex");
	system("mv layout_".$nameConv.".pdf ".$pathStoreLog.$nameConv."/");

//Genero il file dummy.tex
	$getDummy=new dummyBook();
	$dummy=$getDummy->getDummyBook($base,$arr,$nameConv,$pathconfTex,$pathStoreLog, $nameConv,$altezza);

//Eseguo il file dummy.tex
	system("pdflatex ".$pathconfTex."dummy_".$nameConv.".tex");
	system("mv dummy_".$nameConv.".pdf ".$pathStoreLog.$nameConv."/");
	system("mv ".$pathStoreLog.$nameConv."/dummy_".$nameConv.".pdf ".$pathStoreOut.$nameConv."/".$nameConv.".pdf");
	system("rm -rf *.aux *.log");
	system("rm ".$pathStoreTmp."*.*");
	system("rm -rf ".$pathStoreLog.$nameConv);

	
if($internal==1){

	//Stampa Locale
	
	
	if ($base>230){
	
		exec ("lp -d $printerQueue ".$pathStoreOut.$nameConv."/".$nameConv.".pdf");
	
	}else if($allestimento=="brossura" OR $allestimento=="brossurato"){
	
		exec ("lp -d $printerQueueBrossura ".$pathStoreOut.$nameConv."/".$nameConv.".pdf");
	
	}else{
		
		exec ("lp -d $printerQueueLibretto ".$pathStoreOut.$nameConv."/".$nameConv.".pdf");
	
	}

/*//////////////////////////////////////////////////////////////////////////////////////////	
NEL CASO IN CUI LA NOSTRA STAMPANTE NON FUNZIONASSE ABILITARE QUESTA STRINGA CHE IMPOSTA 
L'OUTPUT STORE NELLA CARTELLA CIANOGRAFICHE DELLA COMMESSA

//system ("mv ".$pathStoreOut.$nameConv."/".$nameConv.".pdf  ".$result2['Path']."/Cianografiche/");

/////////////////////////////////////////////////////////////////////////////////////////*/
	
}else{
	// *************************************** Invio file FTP *********************************************
	//recupero parametri ftp
	$selFtpParameters="SELECT *
				FROM committenti
				WHERE committenti.cod_cliente='".$result2['visualizzatore_id']."'";
	//print $selFtpParameters."\n";
	$ftpParameters=$dbInterface->execQuery($connect, $selFtpParameters, "",0);
	foreach ($ftpParameters as $key=>$ftpParameter){
		$ftpHost=$ftpParameter['ftphost'];
		$ftpusername=$ftpParameter['ftpusername'];
		$ftppassword=$ftpParameter['ftppassword'];
		$ftpRemoteOutputPath= $ftpParameter['ftppath'];
	}

	//****************************
	echo "HOSTNAME:".$ftpHost."\n";
	echo "USERNAME:".$ftpusername."\n";
	echo "PASSWORD:".$ftppassword."\n";
	//echo "PATH FILE LOCALE:".$pathFileToUp."\n";
	//echo "PATH FILE REMOTO:".$pathFileRem."\n";
	//*****************************

	//connessione ftp
	$FtpHandler=new FtpHandler();
	$connFtp=$FtpHandler->ftpConnRet($ftpHost, $ftpusername, $ftppassword);

	//path del file locale da uplodare.
	$pathFileToUp=$pathStoreOut."/".$nameConv."/".$nameConv.".pdf";
	//path file remoto uplodato
	$pathFileRem=$ftpRemoteOutputPath."/".$nameConv.".pdf";
	echo $pathFileRem;

	// trasferimento del file al server
	if (ftp_put($connFtp, $pathFileRem, $pathFileToUp, FTP_BINARY)) {
		//echo $pathFileToUp." trasferito correttamente\n";
		//cambio i permessi del file
		ftp_chmod($connFtp, 0777, $pathFileToUp);
	} else {
		//echo "Si e' verificato un problema durante il trasferimento di ".$pathFileToUp."\n";
	}
	//creazione ed invio delfile txt
	$fileTXT=$nameConv.".txt";
	exec('echo "upload=1" > "'.$fileTXT.'"');
	$pathFileRemTXT=$ftpRemoteOutputPath."/".$nameConv.".txt";

	if (ftp_put($connFtp, $pathFileRemTXT, $fileTXT, FTP_BINARY)) {
		echo $pathFileToUp." trasferito correttamente\n";
		//cambio i permessi del file
		@ftp_chmod($connFtp, 0777, $fileTXT);
	} else {
		echo "Si e' verificato un problema durante il trasferimento di ".$pathFileToUp."\n";
	}
	//cancellazione del file txt locale e chiusura della connessione ftp
	unlink($fileTXT);
	ftp_close($connFtp);
}
}else{
	echo "id_segnature_commessa e id_elemento_commessa NON presenti";
}
// Disconnessione generica dal database
$dbInterface->setDisConnect($connect);
?>