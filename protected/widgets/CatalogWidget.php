<?php
/**
 * Created by PhpStorm.
 * User: Orfey
 * Date: 09.06.2014
 * Time: 16:42
 */
class CatalogWidget extends CWidget{


    /**
     * Путь к шаблонам
     */
    const TEMPLATE_PATH = 'application.widgets.catalog';

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

    public function actionMakes() 
	{
		$this->render(self::TEMPLATE_PATH . '.makes', array(
			'makes' => AutoMake::getAllFront(),
		));
    }
	
    public function actionMostVisitedModelYear() 
	{
		$items = AutoModelYear::getMostVisited(18);

		$this->render(self::TEMPLATE_PATH . '.most_visited_model_year', array(
			'items' => $items,
		));
    }
}