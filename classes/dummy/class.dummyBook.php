<?php
/**
 * Permette la gestione del dummyBook
 *
 * @author  Luca Temperini
 */

/* user defined includes */
/* user defined constants */

class dummyBook{
	// --- ATTRIBUTES ---

	// --- OPERATIONS ---

public function __construct(){
}

	/**
	* 
	*
	* @access public
	*
	* @param string $path 
	*
	* @return string
	*/

//Creazione di un array contenente il numero delle sezioni delle pagine, necessario per ricomporre il nome del file PDF da recuperare all'interno del DB 
	public function getPageInt($pag_da1,$pag_a1,$pag_da2,$pag_a2){
		$pag=array($pag_da1,$pag_a1,$pag_da2,$pag_a2);
		for ($i=$pag[0];$i<=$pag[1];$i++){
			if($i<1000){$add= "0";}
			if($i<100){$add= "00";}
			if($i<10){$add= "000";}
			$arr[]=$add.$i;
		}	
		for ($i=$pag[2];$i<=$pag[3];$i++){
			if($i<1000){$add= "0";}
			if($i<100){$add= "00";}
			if($i<10){$add= "000";}
			$arr[]=$add.$i;
		}
		return($arr);
	}

//Generazione del file PDF in bassa risoluzione tramite il GhostScript
	public function getLowPdf($codComm,$tipo,$arr,$pathStoreTmp,$LowresPdf){
		for ($i=1;$i<=count($arr);$i++){
			system ("gs -q -dNOPAUSE -dBATCH -sDEVICE=pdfwrite -sOutputFile=".$pathStoreTmp."/".$codComm."_".$tipo."@".$arr[$i-1].".pdf ".$LowresPdf."/".$codComm."_".$tipo."@".$arr[$i-1].".pdf");
			 echo "".$LowresPdf."/".$codComm."_".$tipo."@".$arr[$i-1].".pdf CREATO \n";
			}
		return($fileLowRes);
	}

//Generazione del file TEX per la creazione del PDF di "n¡ pieghe"pagine, impaginato sul margine dellla pagina A4
	public function getIncludePdf($altezza,$base,$allestimento,$codComm,$tipo,$arr,$pathconfTex,$pathStoreTmp, $nameConv){
		$write="\documentclass{article}\n\usepackage{pdfpages}\n\usepackage[paperheight=".($altezza+20)."mm,paperwidth=".($base+10)."mm,hcentering,vcentering,nohead,nofoot,nomarginpar]{geometry}\n\begin{document}\n";
		for ($i=1;$i<=count($arr);$i++){
			if (($i%2)==0){
				if ($allestimento=="brossura" OR $allestimento=="brossurato"){
					$offset="1.9";
				}else{
					$offset="5";
				}
				$angle="angle=180";
			}else{
				if ($allestimento=="brossura" OR $allestimento=="brossurato"){
					$offset="-1.9";
				}else{
					$offset="-4.9";
				}
				$angle="angle=0";
			}
//questo ciclo IF contiene le stringhe da INSERIRE nel caso in cui il cliente non accetti lo scale dei formati con base maggiore di 215 			
			if ($base<=230 AND $altezza<295){
				$write.="\includepdf[pages={1},noautoscale,offset=".$offset."mm 0mm,link]{".$pathStoreTmp.$codComm."_".$tipo."@".$arr[$i-1].".pdf}\n";
   			}else if ($base<=230 AND $altezza>=295){
				$write.="\includepdf[pages={1},noautoscale,offset=".$offset."mm 3mm,link]{".$pathStoreTmp.$codComm."_".$tipo."@".$arr[$i-1].".pdf}\n";
   			}else if ($base>=231 AND $base<400 AND $altezza<=200){
				$write.="\includepdf[pages={1},noautoscale,offset=0mm 0mm,link]{".$pathStoreTmp.$codComm."_".$tipo."@".$arr[$i-1].".pdf}\n";
   			}else if ($base>=231 AND $base<400){
				$write.="\includepdf[pages={1},noautoscale,".$angle.",offset=0mm 0mm,link]{".$pathStoreTmp.$codComm."_".$tipo."@".$arr[$i-1].".pdf}\n";
			}else if ($base>=400 AND $altezza>=295 AND $altezza<300){
				$write.="\includepdf[pages={1},noautoscale,offset=0mm 3mm,link]{".$pathStoreTmp.$codComm."_".$tipo."@".$arr[$i-1].".pdf}\n";
			}else if ($base>=400){
				$write.="\includepdf[pages={1},noautoscale,offset=0mm 0mm,link]{".$pathStoreTmp.$codComm."_".$tipo."@".$arr[$i-1].".pdf}\n";
			}
		}
		$write.="\end{document}";
		$file = $pathconfTex."layout_".$nameConv.".tex";
		$file = fopen($file, 'w');
		fputs($file,$write);
		fclose($file);
		return($file);
	}			

//Generazione del file TEX per la creazione del definitivo dummyBook
	public function getDummyBook($base,$arr,$nameConv,$pathconfTex,$pathStoreLog, $nameConv, $altezza){
		$write2="\documentclass{article}\n\usepackage{pdfpages}\n\usepackage[paperheight=445mm,paperwidth=320mm,hcentering,vcentering,nohead,nofoot,nomarginpar]{geometry}\n\begin{document}\n\includepdf[pages=";

	if ($base<=230){		
		switch (count($arr)) {
			case 2:
			$write2.="{2,1}";
			break;
			
			case 4:
			$write2.="{4,1,2,3}";
			break;
			
			case 6:
			$write2.="{6,1,2,5,4,3}";
			break;
  		
			case 8:
			$write2.="{8,1,2,7,6,3,4,5}";
			break;

			case 10:
			$write2.="{10,1,9,2,3,8,7,4,5,6}";
			break;
			
			case 12:
			$write2.="{12,1,2,11,10,3,4,9,8,5,6,7}";
			break;
			
			case 16:
			$write2.="{16,1,2,15,14,3,4,13,12,5,6,11,10,7,8,9}";
			break;

			case 20:
			$write2.="{20,1,2,19,18,3,4,17,16,5,6,15,14,7,8,13,12,9,10,11}";
			break;
			
			case 24:
			$write2.="{24,1,2,23,22,3,4,21,20,5,6,19,18,7,8,17,16,9,10,15,14,11,12,13}";
			break;
			
			case 28:
			$write2.="{28,1,2,27,26,3,4,25,24,5,6,23,22,7,8,21,20,9,10,19,18,11,12,17,16,13,14,15}";
			break;
						
			case 32:
			$write2.="{32,1,2,31,30,3,4,29,28,5,6,27,26,7,8,25,24,9,10,23,22,11,12,21,20,13,14,19,18,15,16,17}";
			break;
			
			case 40:
			$write2.="{40,1,2,39,38,3,4,37,36,5,6,35,34,7,8,33,32,9,10,31,30,11,12,29,28,13,14,27,26,15,16,25,24,17,18,23,22,19,20,21}";
			break;
			
			case 48:
			$write2.="{48,1,2,47,46,3,4,45,44,5,6,43,42,7,8,41,40,9,10,39,38,11,12,37,36,13,14,35,34,15,16,33,32,17,18,31,30,19,20,29,28,21,22,27,26,23,24,25}";
			break;
			
			case 64:
			$write2.="{64,1,2,63,62,3,4,61,60,5,6,59,58,7,8,57,56,9,10,55,54,11,12,53,52,13,14,51,50,15,16,49,48,17,18,47,46,19,20,45,44,21,22,43,42,23,24,41,40,25,26,39,38,27,28,37,36,29,30,35,34,31,32,33}";
			break;
			
			case 128:
			$write2.="{128,1,2,127,126,3,4,125,124,5,6,123,122,7,8,121,120,9,10,119,118,11,12,117,116,13,14,115,114,15,16,113,112,17,18,111,110,19,20,109,108,21,22,107,106,23,24,105,104,25,26,103,102,27,28,101,100,29,30,99,98,31,32,97,96,33,34,95,94,35,36,93,92,37,38,91,90,39,40,89,88,41,42,87,86,43,44,85,84,45,46,83,82,47,48,81,80,49,50,79,78,51,52,77,76,53,54,75,74,55,56,73,72,57,58,71,70,59,60,69,68,61,62,67,66,63,64,65}";
			break;
		}
	}else{
		switch (count($arr)) {
			case 2:
			$write2.="{1,2}";
			break;
			
			case 4:
			$write2.="{1,2,3,4}";
			break;

			case 6:
			$write2.="{1,2,3,4,5,6}";
			break;
  		
			case 8:
			$write2.="{1,2,3,4,5,6,7,8}";
			break;
			
			case 10:
			$write2.="{1,2,3,4,5,6,7,8,9,10}";
			break;			
			
			case 12:
			$write2.="{1,2,3,4,5,6,7,8,9,10,11,12}";
			break;
			
			case 16:
			$write2.="{1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16}";
			break;

			case 20:
			$write2.="{1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20}";
			break;
			
			case 24:
			$write2.="{1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24}";
			break;
			
			case 28:
			$write2.="{1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28}";
			break;
						
			case 32:
			$write2.="{1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32}";
			break;
			
			case 40:
			$write2.="{1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40}";
			break;
			
			case 48:
			$write2.="{1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40,41,42,43,44,45,46,47,48}";
			break;
			
			case 64:
			$write2.="{1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40,41,42,43,44,45,46,47,48,49,50,51,52,53,54,55,56,57,58,59,60,61,62,63,64}";
			break;
			
			case 128:
			$write2.="{1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40,41,42,43,44,45,46,47,48,49,50,51,52,53,54,55,56,57,58,59,60,61,62,63,64,65,66,67,68,69,70,71,72,73,74,75,76,77,78,79,80,81,82,83,84,85,86,87,88,89,90,91,92,93,94,95,96,97,98,99,100,101,102,103,104,105,106,107,108,109,110,111,112,113,114,115,116,117,118,119,120,121,122,123,124,125,126,127,128}";
			break;
		}
	}

//Strutture di controllo che dtermina la creazione del tex in base al formato($base), all'allestimento()		
	if ($base<=215 AND (count($arr)!=2)){
		$write2.=",nup=1x2,noautoscale,landscape,offset= 0 0,delta= 0 0,link,linkname=mylink]{".$pathStoreLog.$nameConv."/layout_".$nameConv.".pdf}\n\end{document}";
	}
	
	else if ($base>215 AND $base<=230 AND (count($arr)!=2)){
		$write2.=",nup=1x2,landscape,offset= 0 0,delta= 0 0,link,linkname=mylink]{".$pathStoreLog.$nameConv."/layout_".$nameConv.".pdf}\n\end{document}";
	}
	
	else if ($base<=215 AND (count($arr)==2)){
		$write2.=",noautoscale,landscape,offset= 0 0,delta= 0 0,link,linkname=mylink]{".$pathStoreLog.$nameConv."/layout_".$nameConv.".pdf}\n\end{document}";
	}
	
	else if ($base>215 AND $base<=230 AND (count($arr)==2)){
		$write2.=",noautoscale,landscape,offset= 0 0,delta= 0 0,link,linkname=mylink]{".$pathStoreLog.$nameConv."/layout_".$nameConv.".pdf}\n\end{document}";

//Strutture di controllo per formati che usciranno a pagine singole in quanto hanno la base maggione di 230 mm
	}else if ($base>230 AND $base<=305){
		$write2.=",noautoscale,offset= 0 0mm,delta= 0 0,link,linkname=mylink]{".$pathStoreLog.$nameConv."/layout_".$nameConv.".pdf}\n\end{document}";
	}else if ($base>301 AND $base<=400 AND $altezza<=200){
		$write2.=",offset= 0 0,noautoscale,landscape,delta= 0 0,link,linkname=mylink]{".$pathStoreLog.$nameConv."/layout_".$nameConv.".pdf}\n\end{document}";
	}else if ($base>301 AND $base<=400){
		$write2.=",offset= 0 0,delta= 0 0,link,linkname=mylink]{".$pathStoreLog.$nameConv."/layout_".$nameConv.".pdf}\n\end{document}";
	}else if ($base>400 AND $base<=430){
		$write2.=",noautoscale,landscape,offset= 0 0,delta= 0 0,link,linkname=mylink]{".$pathStoreLog.$nameConv."/layout_".$nameConv.".pdf}\n\end{document}";
	}else if ($base>430){
		$write2.=",landscape,offset= 0 0,delta= 0 0,link,linkname=mylink]{".$pathStoreLog.$nameConv."/layout_".$nameConv.".pdf}\n\end{document}";
	}
	$file2 = $pathconfTex."dummy_".$nameConv.".tex";
	$file2 = fopen($file2, 'w');
	fputs($file2,$write2);
	fclose($file2);
	return($file2);
	}
} 
/* end of class ############ */
?>
