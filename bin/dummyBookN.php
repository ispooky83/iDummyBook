#!/usr/local/bin/php
<?php
/**
 * DummyBook Creator
 *
 * @author Luca Temperini
 * ver 0.2
 */
require_once("/usr/local/apache2/htdocs/dummyBook/conf/systemConfig.php");
error_reporting(E_ALL);
require_once("/usr/local/apache2/htdocs/dummyBook/classes/system/class.SystemLogger.php");
require_once("/usr/local/apache2/htdocs/dummyBook/classes/db/class.DBInterface.php");
require_once("/usr/local/apache2/htdocs/dummyBook/classes/util/class.Utils.php");
require_once("/usr/local/apache2/htdocs/dummyBook/classes/fs/class.ArcipelagoFS.php");
require_once("/usr/local/apache2/htdocs/dummyBook/classes/dummy/class.dummyBookN.php");
require_once("/usr/local/apache2/htdocs/dummyBook/classes/net/class.FtpHandler.php");
//require_once("/usr/local/apache2/htdocs/dummyBook/classes/");

// Connessione generica al database
$dbInterface= new DBInterface(DB_USER, DB_PASSWORD, DB_HOST, DB_NAME);
$connect= $dbInterface->setConnect();

// Valorizzo le variabili che conterranno i path
$pathBin="/usr/local/apache2/htdocs/dummyBook/bin";
$pathStoreLog="/usr/local/apache2/htdocs/dummyBook/store/log/";
$pathconfTex="/usr/local/apache2/htdocs/dummyBook/library/dummyTexTemplate/";
$pathStoreOut="/usr/local/apache2/htdocs/dummyBook/store/OUT/";
$pathStoreTmp="/usr/local/apache2/htdocs/dummyBook/store/tmp/";
$pathStoreCiano="/usr/local/apache2/htdocs/dummyBook/store/tmp/";
 
// Local Printer Queue
$printerQueue= "CX250";
$printerQueueBrossura= "CX250brossura";
$printerQueueLibretto= "CX250libretto";

// Ciclo all'interno della directory di input
$dirCicle= new ArcipelagoFS();
$nolist=array(".", '.', "..");

//------------------------------------------------------------------------PASSAGGIO DEGLI ARGOMENTI DA RIGA DI COMANDO--------------------------------------------
//Questi 2 argomenti sono passati da riga di comando e rispettivamente sono: id segnatura e scelta della risoluzine finale(H=High e L=Low)
	if ($argv[2]=="L"){
		$id_segnature_commessa= $argv[1];
		$res=$argv[2];
		$internal=$argv[3];
		$indigo=$argv[4];
		$crop="";
	}else if ($argv[2]=="SH"){
		$id_segnature_commessa= $argv[1];
		$res=$argv[2];
		$width=$argv[3];
		$height=$argv[4];
		$crop=$argv[5];
		$scale=$argv[6].",";
	}else if ($argv[2]=="EH"){
		$id_elemento_commessa= $argv[1];	
		$res=$argv[2];
		$a=$argv[3];
		$b=$argv[4];
		$width=$argv[5];
		$height=$argv[6];
		$crop=$argv[7];
		$scale=$argv[8].",";
		
//-----------------------------------------------------------------------------------QUERY-------------------------------------------------------------------------------
		$query="SELECT ordine_tipo
					FROM  `elementi_commessa`
					WHERE `id_elemento_commessa`='".$id_elemento_commessa."'";
		$query= $dbInterface->execQuery($connect, $query, "",0);
		$query=$query[0];
		$nOrd=$query['ordine_tipo'];
		$query="SELECT tipo
					FROM  `ordine_tipo`
					WHERE `id_ordine_tipo`='".$nOrd."'";
		$query= $dbInterface->execQuery($connect, $query, "",0);
		$query=$query[0];
		$ordine_tipo=$query['tipo'];	
		
		
		$queryB="SELECT id_segnature_commessa, rif_cod_commessa
					FROM  `segnature_commessa`
					WHERE `rif_id_elemento_commessa`='".$id_elemento_commessa."'";
		$queryB= $dbInterface->execQuery($connect, $queryB, "",0);
		$queryB=$queryB[0];
		$id_segnature_commessa=$queryB['id_segnature_commessa'];
		$codCom=$queryB['rif_cod_commessa'];
				
		$queryI="SELECT rif_cod_commessa,pagina,pos_assoluta
			FROM `pagine_commessa`
			WHERE `rif_cod_commessa`='".$codCom."' 
			AND `pagina`='".$a."'
			AND `rif_ordine_tipo`='".$ordine_tipo."'";
		$queryI= $dbInterface->execQuery($connect, $queryI, "",0);
		$queryI=$queryI[0];
		$i=$queryI['pos_assoluta'];
		
		$queryF="SELECT rif_cod_commessa,pagina,pos_assoluta
					FROM `pagine_commessa`
					WHERE `rif_cod_commessa`='".$codCom."' 
					AND `pagina`='".$b."'
					AND `rif_ordine_tipo`='".$ordine_tipo."'";
		$queryF= $dbInterface->execQuery($connect, $queryF, "",0);
		$queryF=$queryF[0];
		$f=$queryF['pos_assoluta'];
		
		$queryC="SELECT pagina,pos_assoluta,ck_pdf_h, pagina_base
					FROM `pagine_commessa`
					WHERE `rif_cod_commessa`='".$codCom."'
					AND `pos_assoluta`>='".$i."'
					AND `pos_assoluta`<='".$f."'
					AND `rif_ordine_tipo`='".$ordine_tipo."'
					ORDER BY pagine_commessa.pos_assoluta ASC";
		$queryC= $dbInterface->execQuery($connect, $queryC, "",0);
		
//Creo un array contenente le pagine e le posizioni relative
	$pagPos=array();
		foreach($queryC as $key=>$r){
			$arrayInt=array();
			$arrayInt[0]=$r['pagina'];
			$arrayInt[1]=$r['pos_assoluta'];
			$arrayInt[2]=$r['ck_pdf_h'];
			$pagPos[]=$arrayInt;
		}
	}
//Query sul DB per raccogliere tutte le info necessarie
	$query2="SELECT segnature_commessa.pieghe,
			segnature_commessa.segnatura,
			pagine_commessa.rif_ordine_tipo, 
			pagine_commessa.pagina,
			pagine_commessa.posizione,
			pagine_commessa.ck_pdf_h,
			pagine_commessa.pagina_base,
			commesse.Path, 
			commesse.rif_cod_cliente,
			commesse.visualizzatore_id, 
			elementi_commessa.allestimento,
			elementi_commessa.rif_cod_commessa,
			elementi_commessa.base,
			elementi_commessa.altezza
			FROM segnature_commessa, pagine_commessa, commesse, elementi_commessa
			WHERE commesse.cod_commessa = elementi_commessa.rif_cod_commessa 
			AND segnature_commessa.rif_id_elemento_commessa = elementi_commessa.id_elemento_commessa 
			AND segnature_commessa.id_segnature_commessa = pagine_commessa.rif_id_segnature_commessa 
			AND segnature_commessa.id_segnature_commessa =".$id_segnature_commessa."
			ORDER BY pagine_commessa.posizione ASC";
	$result= $dbInterface->execQuery($connect, $query2, "",0);
	$result2= $result[0];
	$base= $result2['base'];
	$altezza= $result2['altezza'];
	$codComm= $result2['rif_cod_commessa'];
	if ($res=="L" OR $res=="SH"){
   		$pieghe= $result2['pieghe'];
   	}else if ($res=="EH"){
   		$pieghe=count($pagPos);
   	}
   	$segnatura= $result2['segnatura'];
   	$pagina= $result2['pagina'];
   	$ck_pdf_h=$result2['ck_pdf_h'];
   	$tipo= $result2['rif_ordine_tipo'];
   	$allestimento= $result2['allestimento'];  	
//Necessario per la creazione del path di Telepiu(utilizzato nella creazione dei lowRes pdf)
   	$pathAssoluto= $result2['Path']."/";
//	print_r($result2);
	if($res=="EH"){
		$pMax=$b;//CAMBIARE NEI VALORI PASSATI POST
		$pMin=$a;//CAMBIARE NEI VALORI PASSATI POST
	}
//----------------------------------------------------------BLOCCO PER LA CREAZIONE DELLA SEQUENZA DELLE PAGINE DA IMPOSIZIONARE---------------------------------------------------------------
//Riceve il numero di pieghe e lo inserisce in una nuova variabile 
	$nPag= $pieghe;
	$pag=array();	
	$arr=array();	
	$aut="{";
	$a=0;
	for ($i=1;$i<=$nPag;$i++){
		$pag[]=$i;
	}
//Se la base  MINORE di 230mm le pagine sarano imposizionate-----SOGLIA IMPORTANTE PER L'INDIGO
	if ($base<=230){
//Creo l'array contenente le pagine imposizionate
		for ($x=0;$x<=($nPag/4);$x++){
			$arr[]=max($pag);
			$arr[]=min($pag);
			unset($pag[max($pag)-1]);
			unset($pag[min($pag)-1]);	
			$arr[]=min($pag);
			$arr[]=max($pag);
			unset($pag[max($pag)-1]);
			unset($pag[min($pag)-1]);
			}
//Ciclo che scrive la sequenza racchiuse tra parentesi e separate dalla vigola
//Il contatore e la struttura di controllo sono necessari per prendere gli elementi che sono necessari all'imposizione
		foreach ($arr as $value){
			$a=$a+1;
			if ($a<=$nPag){
				$aut.=$value.",";
			}
		}
//Se la base  MAGGIORE di 230mm le pagine saranno in sequenza
	}else{
		foreach ($pag as $value){
			$a=$a+1;
			if ($a<=$nPag){
				$aut.=$value.",";
			}
		}
	}
//Fine della scrittura della stringa delle pagine
	$aut= substr($aut, 0, -1);
	$aut.="}";
	echo $aut;
//-------------------------------------------------------------------------------------------------------------------------------------------------------

//Se la risoluzione richiesta  Low i file verrano trattati dal GhostScript e posizionati in "Path"
	if ($res=="L"){
   		$path= $result2['Path']."/LowresPdf_".$codComm."";
   		$lh="@";
   	}else if ($res=="SH" OR $res=="EH"){
   		$pathStoreTmp= $result2['Path']."/HiresPdf_".$codComm."/".$codComm."_".$tipo."/";
   		 $lh="_H_";
   	}
	
//Creo un array contenente le pagine e le posizioni relative
	if ($res=="L" OR $res=="SH"){
		$pagPos=array();
		foreach($result as $key=>$r){
			$arrayInt=array();
			$arrayInt[0]=$r['pagina'];
			$arrayInt[1]=$r['posizione'];
			$arrayInt[2]=$r['pagina_base'];
			$pagPos[]=$arrayInt;
		}
	}else if ($res=="EH"){
		$pagPos=array();
		foreach($queryC as $key=>$r){
			$arrayInt=array();
			$arrayInt[0]=$r['pagina'];
			$arrayInt[1]=$r['pos_assoluta'];
			$arrayInt[2]=$r['ck_pdf_h'];
	//		if($arrayInt[2]==0){								Questi 3 commenti saranno aperti per fare il controllo sulla presenza dei file delle alte risulzioni
	//			echo "NON SONO PRESENTI LE ALTE RISOLUZIONI";
	//		}else{ 
				$pagPos[]=$arrayInt;
	//		}
		}
	}
// 	print_r($pagPos);
	
//Creazione della variabile formata dallo standard namin convenction con la relativa creazione della dir nell cartella di log e store   
    	if ($res=="L" OR $res=="SH"){
	    	$nameConv=$codComm."_".$tipo."@".$segnatura."sig";
	    }else if ($res=="EH"){
	    	$nameConv=$codComm."_".$tipo."@".$pMin."-".$pMax;	    
	    }   	
    	mkdir($pathStoreOut.$nameConv, 0755);
    	mkdir($pathStoreLog.$nameConv, 0755);

//1-------------------------------------------------------------------CONVERTE I PDF IN LOWRES---------------------------------------------------------------
//Se la risuluzione richiesta  "L" allora riempo la cartella "storeTmp" con i file PDF in bassa risoluzione trattati dal GhostScript 
	if ($res=="L"){
		$includePdf=new dummyBook();
		$includePdf->getLowPdf($codComm,$tipo,$pathStoreTmp,$path,$pathAssoluto,$pagina,$result,$res);
	}

//2-------------------------------------------------------------------GENERAZIONE DI PRELAYOUT.TEX-------------------------------------------------------------------------
	if ($crop=="crop" AND $res!="L"){
		$includePdf=new dummyBook();
		$includePdf->getPreLayout($altezza,$base,$codComm,$tipo,$pathconfTex,$pathStoreTmp, $pagPos, $nameConv);
	

//Eseguo il file preLayout.tex
		system("pdflatex ".$pathconfTex."preLayout_".$nameConv.".tex");
		system("mv preLayout_".$nameConv.".pdf /htdocs/dummyBook/store/tmp/");
		system("pdftk /htdocs/dummyBook/store/tmp/preLayout_".$nameConv.".pdf burst output /htdocs/dummyBook/store/tmp/%d.pdf");	
	}
//3-------------------------------------------------------------------GENERAZIONE DI LAYOUT.TEX-------------------------------------------------------------------------

	$includePdf=new dummyBook();
	$includePdf->getIncludePdf($altezza,$base,$allestimento,$codComm,$tipo,$pathconfTex,$pathStoreTmp, $pagPos, $nameConv,$lh,$crop,$res);

//Eseguo il file layout.tex
	system("pdflatex ".$pathconfTex."layout_".$nameConv.".tex");
	system("mv layout_".$nameConv.".pdf ".$pathStoreLog.$nameConv."/");
	
//4-------------------------------------------------------------------GENERAZIONE DI DUMMY.TEX--------------------------------------------------------------------------
	if ($res=="L"){
		$getDummy=new dummyBook();
		$dummy=$getDummy->getDummyBookL($base,$pieghe,$nameConv,$pathconfTex,$pathStoreLog, $aut,$height,$width);
	}else{
		$getDummy=new dummyBook();
		$dummy=$getDummy->getDummyBookH($pieghe,$nameConv,$pathconfTex,$pathStoreLog, $aut,$height,$width,$scale);
	
	}
//Eseguo il file dummy.tex e pulisco tutte le cartelle associate
	if ($res=="L"){
//Se si vuole che il file venga posizionato il STORE/OUT abilitare questo controllo ed eliminare il successivo
//		if (file_exists($pathStoreOut.$nameConv."/".$nameConv."_DL.pdf")){
//			unlink ($pathStoreOut.$nameConv."/".$nameConv."_DL.pdf");
//		}
		if (file_exists($result2['Path']."/Cianografiche/".$nameConv."_DL.pdf")){
			unlink ($result2['Path']."/Cianografiche/".$nameConv."_DL.pdf");
		}
		system("pdflatex ".$pathconfTex."dummy_".$nameConv.".tex");
		system("mv dummy_".$nameConv.".pdf ".$result2['Path']."/Cianografiche/".$nameConv."_DL.pdf");
		system("cp ".$result2['Path']."/Cianografiche/".$nameConv."_DL.pdf ".$pathStoreOut.$nameConv."/".$nameConv."_DL.pdf");

//Path per posizionare il file finale in STORE/OUT-->system("mv dummy_".$nameConv.".pdf ".$pathStoreOut.$nameConv."/".$nameConv."_DL.pdf");
		echo "IL FILE SI TROVA IN:\n".$result2['Path']."/Cianografiche/".$nameConv."_DL.pdf e in ".$pathStoreOut.$nameConv."/".$nameConv."_DL.pdf\n ";
		
//		system("rm ".$pathStoreTmp."*.*");

//4---------------------------------------------------BLOCCO PER LA GESTIONE E L'INVIO DELLA STAMPA-----------------------------------------------------------------------
		if($internal==1){
			if(isset($indigo) AND $indigo=="b"){
				//Stampa Locale
				if ($base>230){
					exec ("lp -d $printerQueue ".$pathStoreOut.$nameConv."/".$nameConv."_DL.pdf");
					echo "lp -d $printerQueue ".$pathStoreOut.$nameConv."/".$nameConv."_DL.pdf\n";
				}else if($allestimento=="brossura" OR $allestimento=="brossurato"){
					exec ("lp -d $printerQueueBrossura ".$pathStoreOut.$nameConv."/".$nameConv."_DL.pdf");
					echo "lp -d $printerQueueBrossura  ".$pathStoreOut.$nameConv."/".$nameConv."_DL.pdf\n";			
				}else{
					exec ("lp -d $printerQueueLibretto ".$pathStoreOut.$nameConv."/".$nameConv."_DL.pdf");
					echo "lp -d $printerQueueLibretto  ".$pathStoreOut.$nameConv."/".$nameConv."_DL.pdf\n";			
				}
				echo "Inviato in stampa";
			}
		}else if($internal==0){
					
		}else{
			// *************************************** INVIO FILE FTP *********************************************
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
			//echo "HOSTNAME:".$ftpHost."\n";
			//echo "USERNAME:".$ftpusername."\n";
			//echo "PASSWORD:".$ftppassword."\n";
			//echo "PATH FILE LOCALE:".$pathFileToUp."\n";
			//echo "PATH FILE REMOTO:".$pathFileRem."\n";
			//*****************************
		
			//connessione ftp
			$FtpHandler=new FtpHandler();
			$connFtp=$FtpHandler->ftpConnRet($ftpHost, $ftpusername, $ftppassword);
		
			//path del file locale da uplodare.
			$pathFileToUp=$pathStoreOut."/".$nameConv."/".$nameConv."_DL.pdf";
			
			//path file remoto uplodato
			$pathFileRem=$ftpRemoteOutputPath."/".$nameConv."_DL.pdf";
			
			// trasferimento del file al server
			if (ftp_put($connFtp, $pathFileRem, $pathFileToUp, FTP_BINARY)) {
				echo $pathFileToUp." trasferito correttamente\n";
				//cambio i permessi del file
				ftp_chmod($connFtp, 0777, $pathFileToUp);
			} else {
				echo "Si e' verificato un problema durante il trasferimento di ".$pathFileToUp."\n";
			}
			//creazione ed invio del file txt
			$fileTXT=$nameConv."_DL.txt";
			exec('echo "upload=1" > "'.$fileTXT.'"');
			$pathFileRemTXT=$ftpRemoteOutputPath."/".$nameConv."_DL.txt";
			
			if (ftp_put($connFtp, $pathFileRemTXT, $fileTXT, FTP_BINARY)) {
				//echo $pathFileToUp." trasferito correttamente\n";
				//cambio i permessi del file
				ftp_chmod($connFtp, 0777, $fileTXT);
			} else {
				//echo "Si e' verificato un problema durante il trasferimento di ".$pathFileToUp."\n";
			}
			//cancellazione del file txt locale e chiusura della connessione ftp
			unlink($fileTXT);
			ftp_close($connFtp);
		}
	}else if ($res=="SH" OR $res=="EH"){
		if (file_exists($result2['Path']."/Cianografiche/".$nameConv."_DH.pdf")){
			unlink ($result2['Path']."/Cianografiche/".$nameConv."_DH.pdf");
		}
		system("pdflatex ".$pathconfTex."dummy_".$nameConv.".tex");
		system("mv dummy_".$nameConv.".pdf ".$result2['Path']."/Cianografiche/".$nameConv."_DH.pdf");
		echo "IL FILE SI TROVA IN:\n".$result2['Path']."/Cianografiche/".$nameConv."_DH.pdf\n";
	}
	system("rm -Rf ".$pathStoreLog.$nameConv);
//	system("rm -R ".$pathStoreOut.$nameConv);
	system("rm -rf *.aux *.log");
	system("rm -rf /usr/local/apache2/htdocs/dummyBook/store/tmp/*");

// Disconnessione generica dal database*/
$dbInterface->setDisConnect($connect);
?>