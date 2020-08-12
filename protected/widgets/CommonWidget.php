<?php

class CommonWidget extends CWidget{

	public $data;
	
    /**
     * Путь к шаблонам
     */
    const TEMPLATE_PATH = 'application.widgets.common';

    /**
     * Действие по-умолчанию
     * @var string
     */
    public $action;
 
	/**
     * Роутер
     */
    public function run()
    {
        $this->{sprintf('action%s', $this->action)}();
    }

    public function actionSpoiler() 
	{
		$this->render(self::TEMPLATE_PATH . '.spoiler', $this->data);
    }
}