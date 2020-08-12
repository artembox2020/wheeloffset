<?php 

class GridHelper
{
	public static $statuses = array();

	public static function dropDownListYandexCategory($data, $enabled)
	{
		return CHtml::dropDownList(
			"Yandex_category_id_".$data->id, 
			$data->category_id, 
			ServiceCategory::getTree(), 
			array(
				"disabled"=>!$enabled,
				"empty"=>"-",
				"rel"=>$data->id,
				"class"=>'js-yandex-category',
			)
		);
	}
	
	public static function orderGiftCertBtnStatuses($data)
	{
		if (!isset(self::$statuses[$data->status_id])) {
			$items = $data->status->statuses;
			foreach ($items as $item) {
				self::$statuses[$data->status_id][$item->status->id] = $item->status->title;
			}
		}
		
		$html = '';
		if (isset(self::$statuses[$data->status_id]) && is_array(self::$statuses[$data->status_id]))
			foreach (self::$statuses[$data->status_id] as $status_id => $status_title) {
				$html.= '<a href="#" class="js-change-status" status_id="'.$status_id.'" id="'.$data->id.'">'.$status_title.'</a> ';
			}
		
		return $html;
	}
	
	public static function userListSalon($data)
	{
		$criteria = new CDbCriteria();
		$criteria->compare('t.user_id', $data->id);
		$criteria->with = array('salon');
		$items = SalonUser::model()->findAll($criteria);
		$salons = array();
		foreach ($items as $item) {
			$salons[] = CHtml::link($item->salon->name, Yii::app()->createUrl("cs/salon/view", array("id"=>$item->salon_id)), array('target'=>'_blank','style'=>'text-decoration:underline;font-weight:bold;'));
		}
		
		return implode(" <span style='color: #069;font-weight: bold;'>|</span> ", $salons);
	}
	
	public static function contractorContact($data)
	{
		Yii::app()->controller->widget('bootstrap.widgets.TbExtendedGridView', array(
			'dataProvider' => $data->getDataProviderContacts(),
			'type' => ' ',
			'emptyText' => '',
			'template' => '{items}',
			'enableSorting'=>false,		
			'columns' => array(
				array(
					'name' => 'phone',
					'value' => '$data->phone',
					'headerHtmlOptions'=>array('style'=>'display:none'), 
					//'htmlOptions'=>array('style'=>'display:none'),						
				),			
				array(
					'name' => 'name',
					'value' => '$data->name',
					'headerHtmlOptions'=>array('style'=>'display:none'), 
					//'htmlOptions'=>array('style'=>'display:none'),						
				),			
				array(
					'name' => 'note',
					'value' => '$data->note',
					'headerHtmlOptions'=>array('style'=>'display:none'), 
					//'htmlOptions'=>array('style'=>'display:none'),						
				),			
			),
		));	

		echo '<button id="btn-add-contact-'.$data->id.'" class="btn btn-success btn-mini js-btn-add-contact" type="button" rel="'.$data->id.'">Добавить</button>';
	}
	
	
	
}