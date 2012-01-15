<?php
/**
 * Interfaccia per le classi mcache
 *
 * @access public
 * @author Lorenzo Monaco
 * @package Utils
 */

/**
 * Gestisce e amministra la system cache.
 *
 * @access public
 * @author Lorenzo Monaco
 */
class CacheManager extends Memcache{
	// --- ATTRIBUTES ---
	private $compressionEnabler= false;
	private $localMemcacheObj= "";
	
	// --- OPERATIONS ---
	
	/**
	* @return void
	* @param string # ######
	* @param string # ######
	* @param string # ######
	*/
	
	public function __construct($compression, $expiry){
		$this->compressionEnabler= $compression;
		$this->expiryT= $expiry;
		$this->hostLoc= MEMCACHE_HOST;
		$this->cPort= MEMCACHE_PORT;
		$this->localMemcacheObj= new Memcache();
		
		try{
			if(!$retObj= $this->localMemcacheObj->connect($this->hostLoc, $this->cPort)){
				throw new Exception('');
			}
		}catch(Exception $e){
    			$systemLogger= new SystemLogger("Errore di connessione al server memcache: ".$e->getMessage(), "System call", "");
    			$systemLogger->setSystemLogger();
    			$systemLogger->setSystemMsg();
    		}
	}
	
	/**
	* Scrive nella cache l'oggetto desiderato
	*
	* @access public
	* @return boolean
	*/
	public function writeCache($key, $object){
		try{
			if(!$this->localMemcacheObj->set($key, $object, $this->compressionEnabler, $this->expiryT)){
				throw new Exception('');	
			} 	
		}catch(Exception $e){
    			$systemLogger= new SystemLogger("Impossibile scrivere i dati in cache: ".$e->getMessage(), "System call", "");
    			$systemLogger->setSystemLogger();
    			$systemLogger->setSystemMsg();
    		}
    		
	}
	
	/**
	* Elimina i dati dalla cache
	*
	* @access public
	* @return boolean
	*/
	public function eraseCache($key){
		try{
			if(!$this->localMemcacheObj->delete($key)){
				throw new Exception('');	
			} 	
		}catch(Exception $e){
    			$systemLogger= new SystemLogger("Impossibile scrivere i dati in cache: ".$e->getMessage(), "System call", "");
    			$systemLogger->setSystemLogger();
    			$systemLogger->setSystemMsg();
    		}
    		
	}
	
	/**
	* Sostituisce nella cache l'oggetto desiderato
	*
	* @access public
	* @return boolean
	*/
	public function replaceCache($key, $object){
		try{
			if(!$this->localMemcacheObj->replace($key, $object, $this->compressionEnabler, $this->expiryT)){
				throw new Exception('');
			}
			
		}catch(Exception $e){
    			$systemLogger= new SystemLogger("Impossibile aggiornare i dati in cache: ".$e->getMessage(), "System call", "");
    			$systemLogger->setSystemLogger();
    			$systemLogger->setSystemMsg();
    		}
	}
	
	/**
	* Legge dalla cache l'oggetto desiderato
	*
	* @access public
	* @return boolean
	*/
	public function readCache($key, $writeConLog){
		try{
			if(!$retObj= $this->localMemcacheObj->get($key)){
				throw new Exception('');
			}
			
		}catch(Exception $e){
				if($writeConLog=="on"){
    				$systemLogger= new SystemLogger("Impossibile leggere i dati in cache: ".$e->getMessage(), "System call", "");
    				$systemLogger->setSystemLogger();
    				$systemLogger->setSystemMsg();
				}
    		}
    		
    		return $retObj;
	}
	
	
/**
	* Legge dalla cache l'oggetto desiderato
	*
	* @access public
	* @return boolean
	*/
	public function readCacheTest($key){
		if($retObj= $this->localMemcacheObj->get($key)){
    		return $retObj;
		}else{
			return array();
		}
	}	
	
	/**
	* @return void
	*/
	public function __destruct(){
		try{
			if(!$this->localMemcacheObj->close()){
				throw new Exception('');
			}
		}catch (Exception $e){
			$systemLogger= new SystemLogger("Impossibile chiudere la connessione con il server memcached: ".$e->getMessage(), "System call", "");
    			$systemLogger->setSystemLogger();
    			$systemLogger->setSystemMsg();
		}
	}

} /* end of class CacheManager */
?>