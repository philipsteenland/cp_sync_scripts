<?php

include_once('C:\\xampp\\htdocs\\shop\\jobscripts\\config.php');

$Mag=new Mag;
$Mag_Mssql=new Mag_Mssql;

//Connect to mssql
$Mag_Mssql->Mag_Mssql_connect();


//MYSQL
$DBTransip = NewADOConnection('mysql');
$DBTransip->Connect(MAGEHOST, MAGEUSERNAME, MAGEPASSWORD, MAGEDATABASE);



$update = true;

	
$collections = array('G1'=>'2012-07-07 00:00:00.000');
		
		
foreach($collections as $code => $date){	
	
	$unix_eta_date = dttm1unixtime($date);
	
	$Mag_Mssql->Mssql->StartTrans();
		
	$rs = $Mag_Mssql->Mssql->Execute("SELECT o.ID as kID,
		o.ordernr,
		o2.ID,
		o2.sysguid,
		o2.artcode,
		o2.esr_aantal,
		o.afldat AS kafldat,
		o2.afldat        
	FROM   orkrg o(NOLOCK)
	   INNER JOIN orsrg o2 (NOLOCK)
			ON  o2.ordernr = o.ordernr
	WHERE  (o.afldat <> '".date('Y-d-m',$unix_eta_date)."' or o2.afldat <> '".date('Y-d-m',$unix_eta_date)."')
	   AND o.ex_artcode = '".$code."'
	   AND o.ord_soort = 'V'
	AND o.ordernr = ' 4705680'");	
		
	if($rs && $rs->_numOfRows > 0){	
		//checken of regel al niet is toegevoegd		
		while (!$rs->EOF){	
			
			$Mag_Mssql->Mssql->Execute("SELECT ID FROM orkrg WHERE ordernr=?",$rs->fields['ordernr']);
	
			$r1 = $Mag_Mssql->Mssql->Execute("SELECT ID FROM gbkmut WHERE bud_vers='MRP' AND transtype='B' AND transsubtype IN ('B','H','K','C','A','J')  
	AND freefield1 IN ('V','Q','B','K','A','D') AND bkstnr_sub=? AND EntryGuid=?",array($rs->fields['ordernr'],$rs->fields['sysguid']));
			
			$r2 = $Mag_Mssql->Mssql->GetRow("SELECT DISTINCT bkjrcode,per_fin FROM perdat WHERE ? BETWEEN bgdatum AND eddatum",date('Y-m-d',$unix_eta_date));
			
			$r3 = $Mag_Mssql->Mssql->Execute("SELECT ID FROM orsrg WHERE ID=?",$rs->fields['ID']);
			
						
			$fromdate = dttm1unixtime($rs->fields['afldat']);	
			
			echo $unix_eta_date.'/';
			echo $fromdate;
					
			
					
			if($r1 && $r2 && $r3 && $update === true){
				
			
				if($unix_eta_date <> $fromdate){			
					if($rs->_currentRow == 0){
						$Mag_Mssql->Mssql->Execute("UPDATE orkrg SET afldat=? FROM orkrg WHERE ordernr=?",array($date,$rs->fields['ordernr']));						
					}
					
					$Mag_Mssql->Mssql->Execute("UPDATE orsrg SET afldat =?,PlannedDate=?,afl_week =? WHERE ID =? AND esr_aantal > 0",array(date('Y-m-d',$unix_eta_date),date('Y-m-d',$unix_eta_date),date('W',$unix_eta_date),$rs->fields['ID']));
					
					if($r1 && $r1->_numOfRows > 0){	
						//checken of regel al niet is toegevoegd		
						while (!$r1->EOF){	
						
							$Mag_Mssql->Mssql->Execute("UPDATE gbkmut SET bkjrcode=?, periode=?, datum=?, afldat=? WHERE bud_vers='MRP' AND ID=?",array($r2['bkjrcode'],$r2['per_fin'],date('Y-m-d',$unix_eta_date),date('Y-m-d',$unix_eta_date),$r1->fields['ID']));
										
							$r1->MoveNext();	
						
						}		
					}										
				}		
			}else{
			
				if(!$r1){
			echo 'cannot open r1:'.$rs->fields['sysguid']."\n";
				
				}
				if(!$r2){
			echo 'cannot open r2:'.$rs->fields['sysguid']."\n";
				
				}
				if(!$r3){
			echo 'cannot open r3:'.$rs->fields['sysguid']."\n";
				
				}			
			}			
			$rs->MoveNext();
		}	
		
		
		$Mag_Mssql->Mssql->CompleteTrans();	
		
		
		
		
		echo '<br>Update ETA complete';
	}else{
		echo 'Nothing todo';
		
		echo $Mag_Mssql->Mssql->ErrorMsg();
		
	}	
	
}



function dttm1unixtime($dttm2timestamp_in)
{
	if($dttm2timestamp_in)
	{
		//    returns unixtime stamp for a given date time string that comes from DB
		$date_time = explode(" ", $dttm2timestamp_in);
		$date = explode("-",$date_time[0]);    
		$time = explode(":",$date_time[1]);    
		unset($date_time);
		list($year, $month, $day)=$date;
		list($hour,$minute,$second)=$time;
		return mktime(intval($hour), intval($minute), intval($second), intval($month), intval($day), intval($year));
	}
}

function max_execution_time($seconds)
{	
	if (ini_get('safe_mode')){
		$safe_mode = TRUE;
	} 
	else{
		$safe_mode = FALSE;
		@ini_set('max_execution_time', $seconds);
	}
	return $safe_mode;
}




	

?>