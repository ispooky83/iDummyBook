<?php
/**
 * Gestisce la struttura del filesystem del Sistema
 *
 * @author Lorenzo Monaco
 */
class ArcipelagoFS{
	// --- ATTRIBUTES ---
	
	// --- OPERATIONS ---
	
	/**
	* Costruttore
	*/
	public function __construct(){
	}
	/**
	* Creazione del filesystem.
	* @access public
	* @return void
	*/
	public function createFsStructure($context, $dirName){
		switch($context){
			case "1": //creazione directory ricorsive
			foreach($dirName as $key=>$path){
				$path= ArcipelagoFS::formatDirName($context, $path);
				if(mkdir($path)){
					chmod($path, DEF_FILE_PERMS);
					chgrp($path, GID);
					chown($path, UID);
				}else{
					$systemLogger= new SystemLogger("Impossibile creare la directory: ".$path, "System call", "");
					$systemLogger->setSystemLogger();
					$systemLogger->setSystemMsg();
				}
				unset($path);
			}
			break;
			case "2":
			if (!is_dir($dirName)){
				if(mkdir($dirName)){
					chmod($dirName, DEF_FILE_PERMS);
					chgrp($dirName, GID);
					chown($dirName, UID);
				}else{
					$systemLogger= new SystemLogger("Impossibile creare la directory: ".$dirName, "System call", "");
					$systemLogger->setSystemLogger();
					$systemLogger->setSystemMsg();
				}
			}			
			break;
		}
	}
	
	/**
	* Formattazione nome directory.
	* @access public
	* @return void
	*/
	public function formatDirName($context, $dirName){
		$arrayReplace= array('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'w', 'x', 'y', 'z', 
		'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', '1', '2', '3', '4',
		'5', '6', '7', '8', '9', '0', '/', '_', '-', '.');
		$arrayDir= str_split($dirName);
		
		switch($context){
			case 1: //creazione directory ricorsive
			$dirName= strip_tags($dirName);
			$dirName= trim($dirName);
			foreach($arrayDir as $values){
				foreach($arrayReplace as $valueAcc){
					if($values==$valueAcc){
						$valueDir.= $values;
					}
				}
			}
			break;
		}
		return $valueDir;
	}
	/**
	* Upload files con crezione di directory di store.
	* @access public
	* @return void
	*/
	public function uploadFileDirCr($file, $tmpName, $path){
		if(is_dir($path)){
			ArcipelagoFS::uploadFile($file, $tmpName, $path);
		}else{
			if(mkdir($path)){
				chmod($path, DEF_FILE_PERMS);
				chgrp($path, GID);
				chown($path, UID);
				ArcipelagoFS::uploadFile($file, $tmpName, $path);
			}else{
				$systemLogger= new SystemLogger("Impossibile creare la directory: ".$path, "System call", "");
				$systemLogger->setSystemLogger();
				$systemLogger->setSystemMsg();
			}
		}	
	}
	/**
	* Upload files.
	* @access public
	* @return void
	*/
	private function uploadFile($file, $tmpName, $path){
		if(move_uploaded_file($tmpName, $path.$file)){
			chmod($path.$file, DEF_FILE_PERMS);
			$returnValue= true;
		}else{
			$systemLogger= new SystemLogger("Impossibile upload del file: ".$file, "System call", "");
			$systemLogger->setSystemLogger();
			$systemLogger->setSystemMsg();
			$returnValue= false;
		}
		
		return $returnValue;
	}
	/**
	* Crea un generico file nella directory indicata dal parametro $dirPath e lo valorizza con i dati presenti in $strFile.
	* @access public
	* @param $strFile stringa da scrivere nel file
	* @param $dirPath path di destinazione del file incluso il filename
	* @return boolean
	*/
	public function createFile($strFile, $dirPath){
		$fd=fopen($dirPath, "w");
		if(fwrite($fd, $strFile)){
			$returnValue= true;
		}else{
			$systemLogger= new SystemLogger("Impossibile creare il file: ".$file, "System call", "");
			$systemLogger->setSystemLogger();
			$systemLogger->setSystemMsg();
			$returnValue= false;
		}
		return $returnValue;
	}
	
	/**
	* Cicla all'interno di una directory e restituisce il contenuto con full path.
	* @access public
	* @param $dirPath path della directory
	* @param $noList elementi da non listare
	* @return boolean
	*/
	public function getDirContents($dirPath, $nolist){
   		$printVal=0;
   		if (!is_dir($dirPath)){
   			$systemLogger= new SystemLogger("Impossibile leggere la directory: ".$dirPath, "System call", "");
			$systemLogger->setSystemLogger();
			$systemLogger->setSystemMsg();
   		}else{
   			if ($root=opendir($dirPath)){
       				while ($file=readdir($root)){
       					foreach ($nolist as $noValue){
       						if($file==$noValue){
       							$printVal= 1;
       						}else{
       							$printVal= 0;
       						}
       					}
           				if($printVal!=1){
               					$files[]=$dirPath."/".$file;
           				}
           				
           				unset($printVal);
       				}
	   		}
	   		return $files;
   		}
	}
	
	
} /* end of class ArcipelagoFS */
?>