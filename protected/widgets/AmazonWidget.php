<?php

class AmazonWidget extends CWidget{


    /**
     * Путь к шаблонам
     */
    const TEMPLATE_PATH = 'application.widgets.amazon';

    /**
     * Действие по-умолчанию
     * @var string
     */
    public $params;
    public $action;
 
	/**
     * Роутер
     */
    public function run()
    {	
		if ($_SERVER['SERVER_NAME'] != 'auto.loc') {
			$this->render(self::TEMPLATE_PATH . '.'.$this->action, array('params'=>$this->params));
		}
    }
}