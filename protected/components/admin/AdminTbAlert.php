<?php 

Yii::import('bootstrap.widgets.TbAlert');

class adminTbAlert extends TbAlert 
{
	public $userComponentId = 'admin';

	public function init()
	{
		return parent::init();
	}
}