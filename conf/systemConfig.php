<?php
/**
* Arcipelago 1.0 :: systemConfig.php
* @description File di configurazione globale
* @author Lorenzo Monaco
*/
 
/**
* Php.ini settings
*/
 
// Costanti globali application server
/**
* Identifica il dominio di appartenenza  
* Valori possibili: arcipelago, arcipelago_unstable, arcipelago_testing
*/
define ('DOMAIN', 'dummyBook');

/**
 * Identifica la document root del sistema
*/
define ('DOCUMENT_ROOT', "/usr/local/apache2/htdocs"."/".DOMAIN."/");
/**
 * Identifica la document root del filesystem di Arcipelago
*/
define ('ROOT_FILESYSTEM', "/mnt/arcipelago");
/**
 * Identifica il valore della variabile di sessione 'username'
*/
define ('SESSIONUSER', $_SESSION['_authsession']['username']);
/**
 * Identifica il nome della SESSION
*/
define ('CSESSION_NAME', 'ARCIPELAGO_TESTING_SESSNAME');
/**
 * Identifica il livello di errore da utilizzare
*/
define ('ERROR_LEVEL', NONE);
/**
 * Default file permission
*/
define ('DEF_FILE_PERMS', 0777);
/**
 * Default file uid
*/
define ('UID', "twistadm");
/**
 * Default file gid
*/
define ('GID', "dalim");
/**
 * Identifica l'indirizzo dell'host remoto
*/
define ('REMOTE_ADDRESS', $_SERVER['REMOTE_ADDR']);

//Cache server [memcache] information
/**
 * Identifica l'host del server memcached
*/
define ('MEMCACHE_HOST', "localhost");
/**
 * Identifica la porta del server memcached
*/
define ('MEMCACHE_PORT', 11211);
/**
 * Identifica il valore di default dell'expiry time del server mamcached
*/
define ('MEMCACHE_EXPIRYT', 0);
/**
 * Identifica il valore di default dello sleep time dei daemon
*/
define ('DAEMON_SLEEP_TIME', 10);

//Database information
/**
 * Identifica il tipo di connessione - mysql o mysqli
*/
define('MYSQL_CONN_TYPE', 'mysql');
/**
 * Identifica il nome host o l'indirizzo ip dell'RDBMS
*/
define ('DB_HOST', "localhost");
/**
 * Identifica la username per l'accesso all'RDBMS
*/
define ('DB_USER', "root");
/**
 * Identifica la password per l'accesso all'RDBMS
*/
define ('DB_PASSWORD', "arci1704");
/**
 * Identifica il nome del database da utilizzare
*/
define ('DB_NAME', "arcipelago");
/**
 * dblocker control (1 -> on/ 0 -> off)
*/
define ('DBLOCKER_CONTROL', 0);
/**
 * Identifica sys_user
*/
define ('TABLE_UTENTI', "sys_user");
/**
 * Identifica il nome del campo username di sys_user
*/
define ('FIELD_UTENTI_USERNAME', "sys_user_username");
/**
 * Identifica il nome del campo sys_passowrd  di sys_user
*/
define ('FIELD_UTENTI_PASSWORD', "sys_user_password");

// Path information

/** 
 * Identifica il path della url dell'applicazione
*/
define ('URL_PATH', "http://".$_SERVER['HTTP_HOST']."/".DOMAIN."/");
/**
 * Identifica la directory di storage dei file esterni
*/
define ('STORE_DIR', DOCUMENT_ROOT."store/");
/**
 * Identifica il path assoluto per il file di logging
*/
define ('LOG_PATH', STORE_DIR."log/");
/**
 * Identifica il path assoluto per il file di logging
*/
define ('WORKFLOWS_ENGINE_DIR', STORE_DIR."workflows/");
/**
 * Identifica il path assoluto per il file di workflows
*/
define ('WIN_ENGINE_DIR', WORKFLOWS_ENGINE_DIR."IN");
/**
 * Identifica il path assoluto per il file di workflows
*/
define ('WINRIP_ENGINE_DIR', WORKFLOWS_ENGINE_DIR."INRIP");
/**
 * Identifica il path assoluto per il file di workflows
*/
define ('WOUTRIP_ENGINE_DIR', WORKFLOWS_ENGINE_DIR."OUTRIP");
/**
 * Identifica il path assoluto per il file workflows
*/
define ('WLOG_ENGINE_DIR', WORKFLOWS_ENGINE_DIR."LOG");
/**
 * Identifica la directory contenente le librerie di sistema 
*/
define ('LIB_DIR', DOCUMENT_ROOT."library/");
/**
 * Identifica la directory contenente le librerie di sistema 
*/
define ('WWW_DIR', "www/");
/**
 * Identifica il nome del file di log
*/
define ('LOG_FILE_NAME', "dummyBook.log");
/**
 * flag per la visualizzazione o meno del messaggio di log (1->si, 0->no).
*/
define ('VIEW_LOG_ERROR', 0);

/**
 * Identifica la directory contenente le directory dello Smarty template engine
*/
define ('TEMPLATE_DIR', WWW_DIR."template/");
/**
 * Identifica la directory, interna allo Smarty template engine,
*/
define ('TEMPLATES_DIR', "templates/");
/**
 * Identifica la directory del template in uso
*/
define ('TEMPLATE', "default/");
/**
 * Identifica la directory di cache del sistema.
*/
define ('CACHE_DATA', DOCUMENT_ROOT."tmp/");
/**
 * Identifica la directory di Smarty.
*/
define ('SMARTY_DIR', LIB_DIR."Smarty-2.6.10/");
/**
 * Identifica il path assoluto per la directory di file storing interno.
*/
define ('GENERAL_FILE_STORE', STORE_DIR."fileStore/");
/**
 * Identifica il path assoluto per la directory di file storing interno.
*/
define ('GENERAL_FILE_STORE_URL', URL_PATH."store/fileStore/");

/**
* Mail data
*/
define ('MAILING_SERVER_HOST', '192.168.100.166');
define ('MAILING_SERVER_PORT', '25');
define ('MAILING_SERVER_AUTH', false);
define ('MAILING_METHOD', 'smtp');
?>
