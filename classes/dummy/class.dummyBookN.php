<?php
/**
 * Permette la gestione del dummyBook
 *
 * @author  Luca Temperini
 * ver 0.2
 */

/* user defined includes */
/* user defined constants */

class dummyBook{
	// --- ATTRIBUTES ---

	// --- OPERATIONS ---

public function __construct(){
}	/**
	* 
	*
	* @access public
	*
	* @param string $path 
	*
	* @return string
	*/

//1-------------------------------------------------------------------COPIA I PDF O LI CONVERTE IN LOWRES---------------------------------------------------------------
//Riempo la cartella "store/tmp" con i file PDF che verranno trattati, se si tratta di una bassa risoluzione i file verranno alleggeriti dal GhostScript 
	public function getLowPdf($codComm,$tipo,$pathStoreTmp,$path,$pathAssoluto,$pagina,$result,$res){
//Crazione dell'array contenente gli array che a loro volta conterranno pagina e presenza di pagina_base necessaria per la costruzione dell'imposizione di Telepiu
		foreach($result as $key=>$r){
			$contrPagina_base=array();
			$contrPagina_base[0]=$r['pagina'];
			$contrPagina_base[1]=$r['pagina_base'];
			$pagine[]=$contrPagina_base;
		}
		$zPos=strpos($codComm,'Z');
		if($zPos===false){
//		$pathGs=$pathStoreTmp.$codComm."_".$tipo."@".$pagina.".pdf ".$path."/".$codComm."_".$tipo."@".$pagina.".pdf";
//		echo $pathGs."\n";
		
//Se si tratta di una BASSA risoluzione crea elabora i files con il GhostScript
			foreach ($pagine as $key=>$value){
				if (is_numeric($value[0])){
					if($value[0]<1000){$add= "0";}
					if($value[0]<100){$add= "00";}
					if($value[0]<10){$add= "000";}
					$pagina=$add.$value[0];
					$pathGs="\"".$pathStoreTmp.$codComm."_".$tipo."@".$pagina.".pdf\" \"".$path."/".$codComm."_".$tipo."@".$pagina.".pdf\"";
					exec ("gs -q -dNOPAUSE -dBATCH -dPDFSETTINGS=/default -sDEVICE=pdfwrite -sOutputFile=".$pathGs);
					echo "\n".$pathGs."\n";
					echo "".$path."/".$codComm."_".$tipo."@".$pagina.".pdf CREATO \n";
				}else{
					$pagina=$value[0];
					$pathGs="\"".$pathStoreTmp.$codComm."_".$tipo."@".$pagina.".pdf\" \"".$path."/".$codComm."_".$tipo."@".$pagina.".pdf\"";
					exec ("gs -q -dNOPAUSE -dBATCH -dPDFSETTINGS=/default -sDEVICE=pdfwrite -sOutputFile=".$pathGs);
//					echo $pathGs."\n";	
					echo "".$path."/".$codComm."_".$tipo."@".$pagina.".pdf CREATO \n";
				}
			}
		}else{
//__________________________________________Blocco per la creazione del path per TELEPIU__________________________________
		print_r($pagine);
		$pieces= explode("Z", $codComm);

			foreach ($pagine as $key=>$value){
//Nela caso in cui $value[1] di Pagine è = 1 , significa che la pagina corrente fa parte della BASE e quindi caricherà il path corrispondente 			

				if ($value[1]==1){
					$codComm2=$pieces[0];
					$path2=str_replace($codComm, $codComm2, $path);
					if (is_numeric($value[0])){
						if($value[0]<1000){$add= "0";}
						if($value[0]<100){$add= "00";}
						if($value[0]<10){$add= "000";}
						$pagina=$add.$value[0];
						$pathGs="\"".$pathStoreTmp.$codComm."_".$tipo."@".$pagina.".pdf\" \"".$path2."/".$codComm2."_".$tipo."@".$pagina.".pdf\"";
						echo "gs -q -dNOPAUSE -dBATCH -dPDFSETTINGS=/default -sDEVICE=pdfwrite -sOutputFile=".$pathGs." \n \n";
						exec ("gs -q -dNOPAUSE -dBATCH -dPDFSETTINGS=/default -sDEVICE=pdfwrite -sOutputFile=".$pathGs);
						echo "".$path."/".$codComm2."_".$tipo."@".$pagina.".pdf CREATO \n \n \n \n \n";
					}else{
						$pathGs="\"".$pathStoreTmp.$codComm."_".$tipo."@".$pagina.".pdf\" \"".$path2."/".$codComm2."_".$tipo."@".$pagina.".pdf\"";
						exec ("gs -q -dNOPAUSE -dBATCH -dPDFSETTINGS=/default -sDEVICE=pdfwrite -sOutputFile=".$pathGs);
						echo "".$path."/".$codComm2."_".$tipo."@".$pagina.".pdf CREATO \n";
					}
				}

//Nela caso in cui $value[1] di Pagine è = 0 , significa che la pagina corrente fa parte della ZONA e quindi caricherà un path diverso rispetto a quello della base				
				else if ($value[1]==0){
					$codComm2=$pieces[1];
					if (is_numeric($value[0])){
						if($value[0]<1000){$add= "0";}
						if($value[0]<100){$add= "00";}
						if($value[0]<10){$add= "000";}
						$pagina=$add.$value[0];
						$pathGs="\"".$pathStoreTmp.$codComm."_".$tipo."@".$pagina.".pdf\" \"".$path."/".$codComm."_".$tipo."@".$pagina.".pdf\"";
						echo "gs -q -dNOPAUSE -dBATCH -dPDFSETTINGS=/default -sDEVICE=pdfwrite -sOutputFile=".$pathGs." \n \n";
						exec ("gs -q -dNOPAUSE -dBATCH -dPDFSETTINGS=/default -sDEVICE=pdfwrite -sOutputFile=".$pathGs);
						echo "".$path."/".$codComm."_".$tipo."@".$pagina.".pdf CREATO \n";
					}else{
						$pathGs="\"".$pathStoreTmp.$codComm."_".$tipo."@".$pagina.".pdf\" \"".$path."/".$codComm."_".$tipo."@".$pagina.".pdf\"";
						exec ("gs -q -dNOPAUSE -dBATCH -dPDFSETTINGS=/default -sDEVICE=pdfwrite -sOutputFile=".$pathGs);
	 					echo "".$path."/".$codComm."_".$tipo."@".$pagina.".pdf CREATO \n";
					}
				}
			}			
			
		}
		
		
//Se si tratta di una ALTA risoluzione copia direttamente i PDF nella cartella "tmp"
	}
//2-------------------------------------------------------------------GENERAZIONE DI PRELAYOUT.TEX--------------------------------------------------------------------------
	public function getPreLayout($altezza,$base,$codComm,$tipo,$pathconfTex,$pathStoreTmp, $pagPos, $nameConv){
		$write="\documentclass{article}\n\usepackage{pdfpages}\n\usepackage[paperheight=".$altezza."mm,paperwidth=".$base."mm,hcentering,vcentering,nohead,nofoot,nomarginpar,left=0mm,right=0mm,pdftex]{geometry}\n";
		$write.="\usepackage[cam,info,height=".($altezza+20)."truemm,width=".($base+20)."truemm,center,pdftex]{crop}\n\begin{document}\n";
		foreach($pagPos as $key=>$value){
			$pagina=$value[0];
			$posizione=$value[1];
		
			if (is_numeric($pagina)){
				if($pagina<1000){$add= "0";}
				if($pagina<100){$add= "00";}
				if($pagina<10){$add= "000";}
				$pagina=$add.$pagina;
				}	
				$pathLayout=$pathStoreTmp.$codComm."_".$tipo."_H_".$pagina.".pdf";
				$write.="\includepdf[pages={1},noautoscale,offset=0mm 0mm,link]{".$pathLayout."}\n";
		}
		$write.="\end{document}";
		$file = $pathconfTex."preLayout_".$nameConv.".tex";
		$file = fopen($file,'w');
		fputs($file,$write);
		fclose($file);
		return($file);
	}
	
//2-------------------------------------------------------------------GENERAZIONE DI LAYOUT.TEX--------------------------------------------------------------------------
	public function getIncludePdf($altezza,$base,$allestimento,$codComm,$tipo,$pathconfTex,$pathStoreTmp, $pagPos, $nameConv,$lh,$crop,$res){
		$write="\documentclass{article}\n\usepackage{pdfpages}\n\usepackage[paperheight=".($altezza+20)."mm,paperwidth=".($base+10)."mm,hcentering,vcentering,nohead,nofoot,nomarginpar]{geometry}%% Le misure originali sono: ALTEZZA ".$altezza." - BASE ".$base."%%\n\begin{document}\n";
		$posizione=0;
		foreach($pagPos as $key=>$value){
			$pagina=$value[0];
			if ($res=="EH"){
					$posizione=$posizione+1;
			}else{
				$posizione=$value[1];
			}
			if (is_numeric($pagina)){
				if($pagina<1000){$add= "0";}
				if($pagina<100){$add= "00";}
				if($pagina<10){$add= "000";}
				$pagina=$add.$pagina;
				}
			if ($crop=="crop"){
				$pathLayout="/htdocs/dummyBook/store/tmp/".$posizione.".pdf";
			}else{
				$pathLayout=$pathStoreTmp.$codComm."_".$tipo.$lh.$pagina.".pdf";
			}
			if (($posizione%2)==0){
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
				$write.="\includepdf[pages={1},noautoscale,offset=".$offset."mm 0mm,link]{".$pathLayout."}\n";
   			}else if ($base<=230 AND $altezza>=295 AND $crop=="crop"){
				$write.="\includepdf[pages={1},noautoscale,offset=".$offset."mm 3mm,link]{".$pathLayout."}\n";
   			}else if ($base<=230 AND $altezza>=295){
				$write.="\includepdf[pages={1},noautoscale,offset=".$offset."mm 3mm,link]{".$pathLayout."}\n";
   			}else if ($base<=230 AND $altezza>=295 AND $crop=="crop"){
				$write.="\includepdf[pages={1},noautoscale,offset=".$offset."mm 3mm,link]{".$pathLayout."}\n";
   			}else if ($base>=231 AND $base<400){
				$write.="\includepdf[pages={1},noautoscale,".$angle.",offset=0mm 0mm,link]{".$pathLayout."}\n";
			}else if ($base>=400 AND $altezza>=295 AND $altezza<300){
				$write.="\includepdf[pages={1},noautoscale,offset=0mm 3mm,link]{".$pathLayout."}\n";
			}else if ($base>=400){
				$write.="\includepdf[pages={1},noautoscale,offset=0mm 0mm,link]{".$pathLayout."}\n";
			}	
		}
		$write.="\end{document}";
		$file = $pathconfTex."layout_".$nameConv.".tex";
		$file = fopen($file, 'w');
		fputs($file,$write);
		fclose($file);
		return($file);
	}
	
//3-------------------------------------------------------------------GENERAZIONE DI DUMMY.TEX--------------------------------------------------------------------------
	public function getDummyBookL($base,$pieghe,$nameConv,$pathconfTex,$pathStoreLog,$aut){
		$pathDummy=$pathStoreLog.$nameConv."/layout_".$nameConv.".pdf";
		$write2="\documentclass{article}\n\usepackage{pdfpages}\n\usepackage[paperheight=450mm,paperwidth=320mm,hcentering,vcentering,nohead,nofoot,nomarginpar]{geometry}\n\begin{document}\n\includepdf[pages=";
//Strutture di controllo che detrmina la creazione del tex in base al formato($base), all'allestimento()		
		if ($base<=215 AND $pieghe!=2){
		$write2.=$aut.",nup=1x2,noautoscale,landscape,offset= 0 0,delta= 0 0,link,linkname=mylink]{".$pathDummy."}\n\end{document}";
		$write2.="\n%%CARATTERISTICHE: Base minore o uguale a 215mm e non ha 2 pieghe%%";
	}	
	else if ($base>215 AND $base<=230 AND $pieghe!=2){
		$write2.=$aut.",nup=1x2,landscape,offset= 0 0,delta= 0 0,link,linkname=mylink]{".$pathDummy."}\n\end{document}";
		$write2.="\n%%CARATTERISTICHE: Base compresa tra 216 e 230 e non ha 2 pieghe%%";
	}	
	else if ($base<=215 AND $pieghe==2){
		$write2.=$aut.",noautoscale,landscape,offset= 0 0,delta= 0 0,link,linkname=mylink]{".$pathDummy."}\n\end{document}";
		$write2.="\n%%CARATTERISTICHE: Base minore o uguale a 215mm e ha 2 pieghe%%";
	}	
	else if ($base>215 AND $base<=230 AND $pieghe==2){
		$write2.=$aut.",noautoscale,landscape,offset= 0 0,delta= 0 0,link,linkname=mylink]{".$pathDummy."}\n\end{document}";
		$write2.="\n%%CARATTERISTICHE: Base compresa fra 216mm e 230 e ha 2 pieghe%%";
		
//Strutture di controllo per formati che usciranno a pagine singole in quanto hanno la base maggione di 230 mm
	}else if ($base>230 AND $base<=305){
		$write2.=$aut.",noautoscale,offset= 0 0mm,delta= 0 0,link,linkname=mylink]{".$pathDummy."}\n\end{document}";
		$write2.="\n%%CARATTERISTICHE: Base compresa tra 231 e 305%%";
	}else if ($base>301 AND $base<=400){
		$write2.=$aut.",offset= 0 0mm,delta= 0 0,link,linkname=mylink]{".$pathDummy."}\n\end{document}";
		$write2.="\n%%CARATTERISTICHE: Base compresa tra 302 e 400%%";
	}else if ($base>400 AND $base<=430){
		$write2.=$aut.",noautoscale,landscape,offset= 0 0,delta= 0 0,link,linkname=mylink]{".$pathDummy."}\n\end{document}";
		$write2.="\n%%CARATTERISTICHE: Base compresa tra i 401 e 430%%";
	}else if ($base>430){
		$write2.=$aut.",landscape,offset= 0 0,delta= 0 0,link,linkname=mylink]{".$pathDummy."}\n\end{document}";
		$write2.="\n%%CARATTERISTICHE: Base maggiore di 430%%";
	}
	$file2 = $pathconfTex."dummy_".$nameConv.".tex";
	$file2 = fopen($file2, 'w');
	fputs($file2,$write2);
	fclose($file2);
	return($file2);
	}
	
//3-------------------------------------------------------------------GENERAZIONE DI DUMMY.TEX PER LE ALTE RISOLUZIONI---------------------------------------------------
	public function getDummyBookH($pieghe,$nameConv,$pathconfTex,$pathStoreLog,$aut,$height,$width,$scale){
		$pathDummy=$pathStoreLog.$nameConv."/layout_".$nameConv.".pdf";
		$write2="\documentclass{article}\n\usepackage{pdfpages}\n\usepackage[paperheight=".$width."mm,paperwidth=".$height."mm,hcentering,vcentering,nohead,nofoot,nomarginpar]{geometry}\n\begin{document}\n\includepdf[pages=";
		$write2.=$aut.",nup=1x2,".$scale."landscape,offset= 0 0,delta= 0 0,link,linkname=mylink]{".$pathDummy."}\n\end{document}";
		$file2 = $pathconfTex."dummy_".$nameConv.".tex";
		$file2 = fopen($file2, 'w');
		fputs($file2,$write2);
		fclose($file2);
		return($file2);
	}
} 
/* end of class ############ */
?>