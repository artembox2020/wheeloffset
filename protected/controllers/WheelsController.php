<?php

class WheelsController extends Controller
{
	public function actionIndex()
	{
		$this->pageTitle = SiteConfig::getInstance()->getValue('seo_wheels_title');
		$this->meta_keywords = SiteConfig::getInstance()->getValue('seo_wheels_meta_keywords');
		$this->meta_description = SiteConfig::getInstance()->getValue('seo_wheels_meta_description');		
		
		$this->breadcrumbs = array(
			'/' => 'Home',
			'#' => 'Wheels',
		);		
		
		$this->render('index', array(
			
		));
	}
	
	public function actionBoltPattern()
	{
		if (Yii::app()->request->isAjaxRequest && Yii::app()->request->isPostRequest) {
			$make = Yii::app()->request->getParam('make');
			$model = Yii::app()->request->getParam('model');
			$year = (int)Yii::app()->request->getParam('year');
		
			$criteria=new CDbCriteria;
			$criteria->compare('t.year', $year);
			$criteria->compare('Model.alias', $model);
			$criteria->compare('Make.alias', $make);
			$criteria->with = array('Model', 'Model.Make', 'BoltPattern');
			$modelYear = AutoModelYear::model()->find($criteria);
			
			if (!empty($modelYear)) {
				if (!empty($modelYear->BoltPattern)) {
					echo '<br><h2>It`s <a href="/wheels/bolt-pattern/'.$modelYear->BoltPattern->value.'/">'.$modelYear->BoltPattern->value.'</h2></a>';
				} else {
					echo '<br><h2>Bolt Pattern not selected this model</h2></a>';
				}
			}
			
			Yii::app()->end();
		}
		
		
		$this->pageTitle = SiteConfig::getInstance()->getValue('seo_wheels_bolt_pattern_title');
		$this->meta_keywords = SiteConfig::getInstance()->getValue('seo_wheels_bolt_pattern_meta_keywords');
		$this->meta_description = SiteConfig::getInstance()->getValue('seo_wheels_bolt_pattern_meta_description');		
		
		$this->breadcrumbs = array(
			'/' => 'Home',
			'wheels.html' => 'Wheels',
			'#' => 'Bolt Pattern',
		);		
		
		$this->render('bolt_pattern', array(
			'list' => RimBoltPattern::getListOfBoltPatterns(),
		));
	}
	
	public function actionMake($alias)
	{
		$make = AutoMake::getMakeByAlias($alias);
		
		if (empty($make)) {
			 throw new CHttpException(404,'Page cannot be found.');
		}
		
		$this->pageTitle = str_replace('[make]', $make['title'], SiteConfig::getInstance()->getValue('seo_wheels_make_title'));
		$this->meta_keywords = str_replace('[make]', $make['title'], SiteConfig::getInstance()->getValue('seo_wheels_make_meta_keywords'));
		$this->meta_description = str_replace('[make]', $make['title'], SiteConfig::getInstance()->getValue('seo_wheels_make_meta_description'));		
		$header_text_block = str_replace('[make]', $make['title'], SiteConfig::getInstance()->getValue('wheels_make_header_text_block'));		
		
		$this->breadcrumbs = array(
			'/' => 'Home',
			'/wheels.html' => 'Wheels',
			'#' => $make['title'],
		);
		
		$dataModels = AutoMake::getModels($make['id']);

		$this->render('make', array(
			'header_text_block' => $header_text_block,
			'make' => $make,
			'dataModels' => $dataModels,
			'dataModelsWheels' => AutoMake::getWheelsData($make['id']),
		));
	}
	
	
	public function actionModel($makeAlias, $modelAlias)
	{
		$make = AutoMake::getMakeByAlias($makeAlias);
		if (empty($make)) {
			 throw new CHttpException(404,'Page cannot be found.');
		}	
	
		$model = AutoModel::getModelByMakeAndAlias($make['id'], $modelAlias);

		if (empty($model)) {
			 throw new CHttpException(404,'Page cannot be found.');
		}
		
		$this->pageTitle = str_replace(array('[make]', '[model]'), array($make['title'], $model['title']), SiteConfig::getInstance()->getValue('seo_wheels_model_title'));
		$this->meta_keywords = str_replace(array('[make]', '[model]'), array($make['title'], $model['title']), SiteConfig::getInstance()->getValue('seo_wheels_model_meta_keywords'));
		$this->meta_description = str_replace(array('[make]', '[model]'), array($make['title'], $model['title']), SiteConfig::getInstance()->getValue('seo_wheels_model_meta_description'));		
		$header_text_block = str_replace(array('[make]', '[model]'), array($make['title'], $model['title']), SiteConfig::getInstance()->getValue('wheels_model_header_text_block'));		
			
		$this->breadcrumbs = array(
			'/' => 'Home',
			'/wheels.html' => 'Wheels',
			'/wheels'.$make['url'] => $make['title'] . ' wheels',
			'#' => $model['title'],
		);
			
		$modelByYears = AutoModel::getYears($model['id']);
		$lastModelYear = AutoModel::getLastYear($model['id']);
				
		$wheelsDataItems = AutoModel::getWheelsDataFull($model['id']);
		foreach ($wheelsDataItems as $k => $v) {
			$wheelsDataItems[$k]['custom_rim_sizes_range'] = Project::getCustomRimSizesRangeByModelYears($v['ids']);
			$wheelsDataItems[$k]['tires_range_from'] = AutoModelYear::getTireRangeByModelYears($v['ids'], 'ASC');
			$wheelsDataItems[$k]['tires_range_to'] = AutoModelYear::getTireRangeByModelYears($v['ids'], 'DESC');
			$wheelsDataItems[$k]['custom_rim_sizes'] = Project::getCustomRimSizes($v['ids']);
		}
		
		$this->render('model', array(
			'lastModelYear' => $lastModelYear,
			'make' => $make,
			'model' => $model,
			'modelByYears' => $modelByYears,
			'header_text_block' => $header_text_block,
			'wheelsDataItems' => $wheelsDataItems,
		));
	}
	
	public function actionDiametrWidth($diametr, $width)
	{	
		$listDiameter 	= TireRimDiameter::getList();
		$listWidth 		= RimWidth::getAll();
		$allRims 		= Project::getAllRims();
		
		$diametrList =  array_flip ($listDiameter);
		$diametr_id = null;
		if (isset($diametrList[$diametr])) {
			$diametr_id = $diametrList[$diametr];
		}
	
		$widthList =  array_flip(RimWidth::getAll());
		$width_id = null;
		if (isset($widthList[$width])) {
			$width_id = $widthList[$width];
		}
		
		$rim = "{$diametr}x{$width}";
		
		if (empty($diametr_id) || empty($width_id) || !in_array($rim, $allRims)) {
			throw new CHttpException(404,'Page cannot be found.');
		}		
			
		if (Yii::app()->request->isAjaxRequest) {
			$projects = Project::getModifiedCarsByRim($diametr_id, $width_id, Yii::app()->request->getParam('offset'));
			$this->renderPartial('_projects', array(
				'rim' => $rim,
				'diametr' => $diametr,
				'projects' => $projects,
			));
			Yii::app()->end();
		}
			
		$this->pageTitle = str_replace(array('[diametr]', '[width]'), array($diametr, $width, ), SiteConfig::getInstance()->getValue('seo_wheels_diametr_width_title'));
		$this->meta_keywords = str_replace(array('[diametr]', '[width]'), array($diametr, $width), SiteConfig::getInstance()->getValue('seo_wheels_diametr_width_meta_keywords'));
		$this->meta_description = str_replace(array('[diametr]', '[width]'), array($diametr, $width), SiteConfig::getInstance()->getValue('seo_wheels_diametr_width_meta_description'));		
		$header_text_block = str_replace(array('[diametr]', '[width]'), array($diametr, $width), SiteConfig::getInstance()->getValue('wheels_wheels_diametr_width_header_text_block'));		
			
		$this->breadcrumbs = array(
			'/' => 'Home',
			'/wheels.html' => 'Wheels',
			'#' => $rim,
		);
		
		$tireRangeData = TireRimWidthRange::getData();
		$recommendedTireSizesItems = array();
		foreach (array(1=>'P', 2=>'LT') as $k=>$v) {
			$recommendedTireSizesItems[$k]['title'] = $v;	
			$recommendedTireSizesItems[$k]['items'] = Project::getRecommendedTireSizes($diametr_id, $width, $k);	
		}
		
		$possibleTireSizes = Project::getPossibleTireSizesByRim($diametr_id, $width_id);
		$projects = Project::getModifiedCarsByRim($diametr_id, $width_id, 0);
		
		//rims navifations
		$dataDiametr = array();
		foreach ($listDiameter as $k=>$v) {
			$dataDiametr[] = $v;
		}

		$dataWidth = array();
		foreach ($listWidth as $k=>$v) {
			$dataWidth[] = $v;
		}
		
		$key_d = array_search($diametr, $dataDiametr);
		$key_w = array_search($width, $dataWidth);
		
		$rimsNavigation = array();		
		//-1 position width
		if (isset($dataWidth[$key_w-1])) {
			$rimItem = $diametr . 'x'. $dataWidth[$key_w-1];
			if (in_array($rimItem, $allRims)) {
				$rimsNavigation[$rimItem] = 'Narrower rim';
			}
		}
		//+1 position width
		if (isset($dataWidth[$key_w+1])) {
			$rimItem = $diametr . 'x'. $dataWidth[$key_w+1];
			if (in_array($rimItem, $allRims)) {
				$rimsNavigation[$rimItem] = 'Wider rim';
			}
		}
		//-1 position diametr
		if (isset($dataDiametr[$key_d-1])) {
			$rimItem = $dataDiametr[$key_d-1] . 'x'. $width;
			if (in_array($rimItem, $allRims)) {
				$rimsNavigation[$rimItem] = 'Smaller rim';
			}
		}
		//+1 position diametr
		if (isset($dataDiametr[$key_d+1])) {
			$rimItem = $dataDiametr[$key_d+1] . 'x'. $width;
			if (in_array($rimItem, $allRims)) {
				$rimsNavigation[$rimItem] = 'Larger rim';
			}
		}	

		//d($tireRangeData);
		
		$this->render('diametr_width', array(
			'header_text_block' => $header_text_block,
			'rim' => $rim,
			'diametr' => $diametr,
			'width' => $width,
			'possibleTireSizes' => $possibleTireSizes,
			'recommendedTireSizesItems' => $recommendedTireSizesItems,
			'rimsNavigation' => $rimsNavigation,
			'projects' => $projects,
			'tireRangeData' => $tireRangeData,
			'count' => Project::getCountModifiedCarsByRim($diametr_id, $width_id),
		));
	}
	
	public function actionDiametrWidthTire($diametr, $width, $vehicle_class, $section_width, $aspect_ratio, $rim_diameter)
	{	
		if ($diametr != $rim_diameter) {
			throw new CHttpException(404,'Page cannot be found.');
		}
		
		$listDiameter 		= TireRimDiameter::getList();
		$listWidth 			= RimWidth::getAll();
		$listSectionWidth 	= TireSectionWidth::getList();
		$listVehicleClass 	= TireVehicleClass::getList();
		$listAspectRatio 	= TireAspectRatio::getList();
		$allRims 			= Project::getAllRims();
		
		$diametrList =  array_flip ($listDiameter);
		$diametr_id = null;
		if (isset($diametrList[$diametr])) {
			$diametr_id = $diametrList[$diametr];
		}
	
		$widthList =  array_flip(RimWidth::getAll());
		$width_id = null;
		if (isset($widthList[$width])) {
			$width_id = $widthList[$width];
		}
		
		$widthList =  array_flip(RimWidth::getAll());
		$width_id = null;
		if (isset($widthList[$width])) {
			$width_id = $widthList[$width];
		}
		
		$listSectionWidth =  array_flip($listSectionWidth);
		$section_width_id = null;
		if (isset($listSectionWidth[$section_width])) {
			$section_width_id = $listSectionWidth[$section_width];
		}
		
		$listVehicleClass =  array_flip($listVehicleClass);
		$vehicle_class_id = null;
		if (isset($listVehicleClass[$vehicle_class])) {
			$vehicle_class_id = $listVehicleClass[$vehicle_class];
		}
		
		$listAspectRatio =  array_flip($listAspectRatio);
		$aspect_ratio_id = null;
		if (isset($listAspectRatio[$aspect_ratio])) {
			$aspect_ratio_id = $listAspectRatio[$aspect_ratio];
		}
		
		$rim = "{$diametr}x{$width}";
		
		if (empty($vehicle_class_id) || empty($section_width_id) || empty($aspect_ratio_id) || empty($diametr_id) || empty($width_id) || !in_array($rim, $allRims)) {
			//throw new CHttpException(404,'Page cannot be found.');
		}	

		$attributes = array(
			'vc.code' => $vehicle_class,
			'sw.value' => $section_width,
			'ar.value' => $aspect_ratio,
			'rd.value' => $rim_diameter,
		);

		$tires = Tire::getItemsByAttributes($attributes);
			
		if (empty($tires)) {
			throw new CHttpException(404,'Page cannot be found.');
		}	
		$tire = $tires[0];		
		$tireTitle = Tire::format(array(
			'vehicle_class' => $vehicle_class,
			'section_width' => $section_width,
			'aspect_ratio' => $aspect_ratio,
			'rim_diameter' => $rim_diameter,
		));			
		
		$range = TireRimWidthRange::getRangeTire($tire['id']);
		//d($range);
			
		$replaceFrom = array(
			'[diametr]', 
			'[width]', 
			'[vehicle_class]',
			'[section_width]',
			'[aspect_ratio]',
		);	
			
		$replaceTo = array(
			$diametr, 
			$width,
			$vehicle_class,
			$section_width,
			$aspect_ratio,		
		);	
			
		$this->pageTitle = str_replace($replaceFrom, $replaceTo, SiteConfig::getInstance()->getValue('seo_wheels_diametr_width_tire_title'));
		$this->meta_keywords = str_replace($replaceFrom, $replaceTo, SiteConfig::getInstance()->getValue('seo_wheels_diametr_width_tire_meta_keywords'));
		$this->meta_description = str_replace($replaceFrom, $replaceTo, SiteConfig::getInstance()->getValue('seo_wheels_diametr_width_tire_meta_description'));		
		$header_text_block = str_replace($replaceFrom, $replaceTo, SiteConfig::getInstance()->getValue('wheels_wheels_diametr_width_tire_header_text_block'));		
			
		$this->breadcrumbs = array(
			'/' => 'Home',
			'/wheels.html' => 'Wheels',
			'/wheels/'.$rim . '/' => $rim,
			'#' => $tireTitle,
		);
	
		$countProject = Project::getCountByRimTire($diametr_id, $width_id, $vehicle_class_id, $section_width_id, $aspect_ratio_id);
		$answer = '';
		if ($countProject && !empty($range['front']) && $range['front']['from']<=$width && $range['front']['to']>=$width) {
			$answer = 'Yes, it will fit.';
		} else if ($countProject && !empty($range['front']) && $range['front']['to']<$width) {
			$answer = 'Yes, it will fit, but might need fenders rolling and pulling.';
		} else if (!empty($range['front']) && $range['front']['from']<$width) {
			$answer = 'No, the tire is too wide, it is dangerous.';
		} else if (!empty($range['front']) && $range['front']['to']<$width) {
			$answer = 'No, the rim is too wide, we don\'t recommend it';
		}
		
		$projects = array();
		if ($countProject) {
			$projects = Project::getItemsByRimTire($diametr_id, $width_id, $vehicle_class_id, $section_width_id, $aspect_ratio_id);
		}
		
		$this->render('diametr_width_tire', array(
			'header_text_block' => $header_text_block,
			'rim' => $rim,
			'section_width' => $section_width,
			'aspect_ratio' => $aspect_ratio,
			'rim_diameter' => $rim_diameter,
			'tireTitle' => $tireTitle,
			'diametr' => $diametr,
			'diametr_id' => $diametr_id,
			'width' => $width,
			'width_id' => $width_id,
			'range' => $range,
			'answer' => $answer,
			'vehicle_class_id' => $vehicle_class_id,
			'section_width_id' => $section_width_id,
			'aspect_ratio_id' => $aspect_ratio_id,
			'countProject' => $countProject,
			'projects' => $projects,
		));
	}
	
}