<?php

class PMO { 
  
    public $pmo;
	public $db;
    public $serverName = "sql-server2";
	public $connectionOptions = "510";
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
		
		
		$this->prepareSQL();
		
				
	}
		
	function getData($type){	   	
		if($type=='table'){
			return $this->getDataColumns();
		}
	}
	
	function prepareSQL(){
		
		$this->sql_pid = $this->db[0]->prepare("SELECT * FROM theme WHERE pid = ?"); 
	
	}
	
	function prepareInsertSQL($table,$fields,$key){
				
		$sql = "INSERT INTO theme ("; 
				
		$i=0;
		foreach($fields as $k => $v){				
			$sql .= ($i>0?', ':'')."[$k]";
			$i++;
		}
			
		$sql .=  ") VALUES (";
		
		$i=0;
		foreach($fields as $k => $v){				
			$sql .= ($i>0?', ':'').":$k";
			$i++;
		}		
		
		$sql .=  ");";
			
		$this->sql_insert = $this->db[0]->prepare($sql); 		
		
		//$this->bindDefaultValues($table);
				
		foreach($fields as $k => $v){				
			$this->sql_insert->bindValue((':'.$k),$v,PDO::PARAM_STR);		
		}		
	}
	
	function prepareUpdateSQL($table,$fields,$keys){
						
		$sql = "UPDATE $table SET ";
		$i=0;
		foreach($fields as $k => $v){		
			$sql .= ($i>0?', ':'')."[$k] = :$k";
			$i++;
		}
		
		$sql .= " WHERE ";
		
		$i=0;
		
		print_r($k);
				
		foreach($keys as $k => $v){		
			$sql .= ($i>0?' AND ':'')."[$k] = :$k";
			$i++;
		}
		
		echo $sql;
		
				
		$this->sql_update = $this->db[0]->prepare($sql); 	
		
		foreach($fields as $k => $v){				
			$this->sql_update->bindValue((':'.$k),$v);		
		}
		
		foreach($fields as $k => $v){				
			$this->sql_update->bindValue((':'.$k),$v);			
		}		
	}
	
	function getPids(){
		if($this->table){
			$sql = "SELECT entity_id FROM `catalog_category_entity`";
		}
		
		$stmt = $this->pmo->query($sql);	
			
		return $stmt->fetchAll(PDO::FETCH_ASSOC);		
	}
	
	
	function bindDefaultValues ($table){
		
		if($table=='theme'){
			//THEME
			$this->sql_insert->bindValue(':pid', 0, PDO::PARAM_INT);
			$this->sql_insert->bindValue(':name', 'leeg', PDO::PARAM_STR);
			$this->sql_insert->bindValue(':code', 'leeg', PDO::PARAM_STR);
			$this->sql_insert->bindValue(':start', date('Y-m-d'), PDO::PARAM_STR);
			$this->sql_insert->bindValue(':end',   date('Y-m-d'), PDO::PARAM_STR);
		}		
	}
	
	
	function checkPidExits (){
						
		$this->sql_pid->bindValue(1, $this->pid, PDO::PARAM_INT);
		
		$stmt = $this->sql_pid->execute();	
		
		if($this->sql_pid->fetchColumn() > 0 ){
			return true;
		}		
	}
	
	
	function checkEntity_type_idExits ($entity_type_id){
		return true;
	}
	
	
	
	
	function setStoreId ($store){
		$this->store = $store;
	}
	
	function setTable ($table){
		$this->table = $table;
		
		$this->setTableDB();
		
	}
	
	function setPid ($pid){
		$this->pid = $pid;
	}
	
	
	function setEntity_type_id($entity_type_id){
		
		if($this->checkEntity_type_idExits ($entity_type_id)){
			$this->entity_type_id = $entity_type_id;			
		}else{
			return false;
		}
		
		return true;
	}
	
	
	
	
    function getDataColumns(){	
		
		if($this->table){
			
			$sql = "SELECT a.attribute_id,l.value,a.backend_type FROM `eav_attribute` a,`eav_attribute_label` l 
			WHERE a.attribute_id = l.attribute_id and entity_type_id = $this->entity_type_id and store_id = $this->store and l.value LIKE '$this->table'";
		
			$stmt = $this->pmo->query($sql);	
			
		   return $stmt->fetchAll(PDO::FETCH_ASSOC);
	   
		}
		
		return false;
	}
	
	
	function setTableDB(){
		$val = explode ('.',$this->table);		
		$this->tabledb = $val[0];
	
	}
	
	/* function getValue ($column){
		if($column){
			
			if($column['backend_type'] == 'varchar'){
				 return $this->getValueVarchar ($column);
				
			}
			
		}		
	} */
	
	function getTableNameForValue($column){
		
		if($this->entity_type_id == 10){		
			return 'catalog_product_entity_'.$column['backend_type'];		
		}elseif($this->entity_type_id == 9){
			return 'catalog_category_entity_'.$column['backend_type'];	
		}		
	}
	
	function setColumnName($column){		
		$val = explode ('.',$column['value']);			
		$this->columnname = $val[1];
		
	}
	
	
	function getValue ($column){
		if($this->store && $this->table && $this->pid ){
	
			$table = $this->getTableNameForValue($column);
	
			$sql = "SELECT IF(v2.value is null,v.value,v2.value) as value FROM $table v 
			LEFT JOIN $table v2 ON v2.attribute_id = v.attribute_id AND v.entity_id = v2.entity_id AND v.store_id = $this->store 
			WHERE v.`entity_id` = $this->pid and v.`attribute_id` = $column[attribute_id] and v.store_id = 0";
			
			
			$stmt = $this->pmo->query($sql);	
															
		   return $stmt->fetchColumn();
	   
		}else{
			return array($this->store , $this->table , $this->pid );
		}
	}	
} 