<?php
class CounterCommand extends CConsoleCommand
{

	public function init() 
	{
		ini_set('max_execution_time', 3600*12);
		return parent::init();
	}

	public function actionTire()
	{
		
	}
	
}