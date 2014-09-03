<?php

class PMO { 
  
    public $pmo;
	public $db;
    public $serverName = "sql-server2";
	public $connectionOptions = "910";
	public $store = 0;
	public $table;
    public $pid;
	public $entity_type_id;
	public $sql_insert;

   
    function __construct() {
		
		global $pmo;
		global $db;
		
		
		try{
			$this->pmo = new PDO('mysql:host='.MAGEHOST.';dbname='.MAGEDATABASE.';charset=UTF-8', MAGEUSERNAME, MAGEPASSWORD);
		
		}
		catch(Exception $e){
			die( print_r( $e->getMessage() ) );
		}
		
		
		try{
			$this->db[0] = new PDO("sqlsrv:server=$this->serverName;Database=$this->connectionOptions", "", "");
			$this->db[0]->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
			$this->db[0]->setAttribute(PDO::SQLSRV_ATTR_ENCODING, PDO::SQLSRV_ENCODING_SYSTEM);
		}
		catch(Exception $e){
			die( print_r( $e->getMessage() ) );
		}						
	}	
} 