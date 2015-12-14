<?php
class Currency {
	public $oDb;	
	public $oCourses;
	public $sCurrency;
	public $fCurrency;
	public $cost;
	

	public function Currency($oDb, $sCurrency, $fCurrency, $cost)
	{
		$this->oDb = $oDb;
		$this->oDb->Rates->createIndex(array('date' => 1), array('unique' => true));
		$this->sCurrency = strtoupper($sCurrency);
		$this->fCurrency = strtoupper($fCurrency);
		$this->cost = str_replace(',', '.', $cost);
		
	}
	
	public function Valid()
	{
	
		if (!preg_match("/^([a-z]{3})$/i", $this->fCurrency))
			return false;

		if (!preg_match("/^([\d]+)$/i", $this->cost))
				return false;

		$curCurrency = array();
		
		if(!$curCurrency = $this->oDb->Rates->findOne(array('date' => date('d/m/Y'))))
		{
			
			if($xml = simplexml_load_file(CBR.date('d/m/Y')))
			{
				$curCurrency = array('date' => date('d/m/Y'));
				foreach($xml->Valute as $v)
				{
					$curCurrency[(string)$v->CharCode] = (float)(str_replace(',', '.', $v->Value));
				}
				try
				{
					$this->oDb->Rates->insert($curCurrency);
				}
				catch (MongoDuplicateKeyException $e) 
				{
					$curCurrency = $this->oDb->Rates->findOne(array('date' => date('d/m/Y')));
				}
			}
			else
			{
				return false;
			}			
		}
		$this->oCourses = $curCurrency;

		if(!isset($this->oCourses[$this->sCurrency]) || !isset($this->oCourses[$this->fCurrency]))
			return false;
				
		
		return true;
	}
	
	public function Logger($oLog)
	{
		$insertOpts = array("w" => 1);
		$this->oDb->Logs->insert($oLog,$insertOpts);
	}
	
	public function Calc()
	{
		return array(
			'cost' => round(($this->cost * $this->oCourses[$this->sCurrency])/$this->oCourses[$this->fCurrency] , 2), 
			'currency' => $this->fCurrency
		);
	}
	
	
	
}