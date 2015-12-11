<?php
	$start = microtime(true);
	require_once 'config/config.php';
	require_once 'controller/Currency.php';
	require_once 'controller/Logs.php';
	
	$oRoute = explode('/', trim(UrlDecode($_SERVER['REQUEST_URI']),'/'));

	if(count($oRoute) > 0)
	{
		
		try {
			$connection = new MongoClient();
			
			switch($oRoute[0])
			{
				case 'currency':
					$app = new Currency($connection->Currency, $defaultCurrency, $oRoute[1], $oRoute[2]);
					if($app->Valid())
					{
						$result = $app->Calc();
						$finish = microtime(true);
						$app->Logger(array(
								'request_date' => date('Y-m-d H:i:s'),
								'request_time' => $finish - $start,
								'request_price' => $app->cost,
								'request_currency' => $app->fCurrency,
								'request_ip' => $_SERVER['REMOTE_ADDR'],
								'request_ua' => $_SERVER['HTTP_USER_AGENT']
						));
					}
					else
						$result = array('error'=>1,'mesage'=>'Запрос некорректен');
						break;
				case 'logs':
					$app = new Logs($connection->Currency);
					$result = $app->GetPeriod($oRoute[1],$oRoute[2]);
					break;
				default:
					$result = array('error'=>1,'mesage'=>'Запрос некорректен');
			}	
			$connection->close();		
		}
		catch (MongoConnectionException $e) {
			$result = array('error'=>1,'mesage'=>'Сервис временно недоступен');
		}

	}
	else
	{
		$result = array('error'=>1,'mesage'=>'Запрос некорректен');		
	}
	
	
	echo json_encode($result,false);

