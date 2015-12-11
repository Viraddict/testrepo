<?php
class Logs {
	public $oDb;

	public function Logs($db)
	{
		$this->oDb=$db;
	}
	
	public function GetPeriod($sDate,$eDate)
	{
		
		$query = array(
			'request_date' => array('$gte' => $sDate, '$lte' => $eDate)	
		);
		$res = $this->oDb->Logs->find($query);
		
		return iterator_to_array($res,true);
	}	
}