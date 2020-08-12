<?php

class AjaxController extends Controller
{
	public function actionGetMakesByYear()
	{
		$year = Yii::app()->request->getParam('year');
		$data = AutoMake::getMakesByYear($year);
		$response['items'] = $data;
		echo json_encode($response);
	}
	
	public function actionGetModelsByMake()
	{
		$alias = Yii::app()->request->getParam('alias');
		$year = (int)Yii::app()->request->getParam('year');
		$make = AutoMake::getMakeByAlias($alias);
		
		$data = AutoModel::getModelsByMake($make['id'], $year);
		$response['items'] = $data;
		echo json_encode($response);
	}
	
	public function actionGetModelsMake()
	{
		$alias = Yii::app()->request->getParam('alias');
		$make = AutoMake::getMakeByAlias($alias);
		
		$data = AutoModel::getModelsMake($make['id']);
		$response['items'] = $data;
		echo json_encode($response);
	}
	
	public function actionGetTireSectionWidthByAttributes()
	{
		$items = TireSectionWidth::getListFront(array(
			'vehicle_class_id' => (int) Yii::app()->request->getParam('vehicle_class_id'),
		));
		
		$response['items'] = array();
		foreach ($items as $value=>$label) {
			$response['items'][] = array(
				'value' => $value,
				'label' => $label,
			);
		}
		
		echo json_encode($response);
	}
	
	public function actionGetTireAspectRatioByAttributes()
	{	
		$items = TireAspectRatio::getListFront(array(
			'vehicle_class_id' => (int) Yii::app()->request->getParam('vehicle_class_id'),
			'section_width_id' => (int) Yii::app()->request->getParam('section_width_id'),
		));
		
		$response['items'] = array();
		foreach ($items as $value=>$label) {
			$response['items'][] = array(
				'value' => $value,
				'label' => $label,
			);
		}
		echo json_encode($response);
	}	
	
	public function actionGetTireRimDiameterByAttributes()
	{
		$items = TireRimDiameter::getListFront(array(
			'vehicle_class_id' => (int) Yii::app()->request->getParam('vehicle_class_id'),
			'section_width_id' => (int) Yii::app()->request->getParam('section_width_id'),
			'aspect_ratio_id' => (int) Yii::app()->request->getParam('aspect_ratio_id'),
		));
		
		$response['items'] = array();
		foreach ($items as $value=>$label) {
			$response['items'][] = array(
				'value' => $value,
				'label' => $label,
			);
		}

		echo json_encode($response);
	}
	
	public function actionGetCountTireSize()
	{
		$section_width_id = (int)Yii::app()->request->getParam('section_width_id');
		$rim_diameter_id = (int)Yii::app()->request->getParam('rim_diameter_id');
		$aspect_ratio_id = (int)Yii::app()->request->getParam('aspect_ratio_id');

		$count = Tire::getCount(array(
			'section_width_id' => $section_width_id,
			'rim_diameter_id' => $rim_diameter_id,
			'aspect_ratio_id' => $aspect_ratio_id,
		));
		
		$response['count'] = $count;
		$response['text'] = $count . ' results';
		
		echo json_encode($response);
	}

	public function actionGetBulbsMakesByYear()
	{
		$year = Yii::app()->request->getParam('year');
		$data = ProductBulb::getMakesByYear($year);
		$response['items'] = $data;
		echo json_encode($response);
	}
	
	public function actionGetBulbsModelsByMake()
	{
		$alias = Yii::app()->request->getParam('alias');
		$year = (int)Yii::app()->request->getParam('year');
		$make = AutoMake::getMakeByAlias($alias);
		
		$data = ProductBulb::getModelsByMake($make['id'], $year);
		$response['items'] = $data;
		echo json_encode($response);
	}
		
}