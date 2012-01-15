<?php
/**
 * Permette la gestione dei file postscript
 *
 * @author  Luca Temperini
 */

/* user defined includes */
/* user defined constants */

class PsManager{
	// --- ATTRIBUTES ---

	// --- OPERATIONS ---

	/**
	* 
	*
	* @access public
	*
	* @param string $path 
	*
	* @return string
	*/
	public function searchPageSectionPs($path){
		$contfile=file_get_contents($path);	
		$stringsearch="%%QRKPageBegin:";
		
		$pos=strpos($contfile, $stringsearch);
			
		$endLine="\r";
		$pos1=strpos($contfile, $endLine, $pos);
			
		//echo $pos."\n";
		//echo $pos1."\n";
			
		$start=$pos+16;
		$length=$pos1-$start;
		$result=substr($contfile, $start, $length);
			
		//echo strlen($result)."\n";
		return trim($result)."\n";
	}
	
	
	public function getDscContent($pathIN){
		$command="egrep '^%%' ".$pathIN." > text.txt";
		$completefile=exec($command);
		return $completefile;
	}
} /* end of class ############ */
?>