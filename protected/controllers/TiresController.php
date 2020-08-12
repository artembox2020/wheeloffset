<?php

class TiresController extends Controller
{
	public function actionIndex()
	{
		$this->pageTitle = SiteConfig::getInstance()->getValue('seo_tires_title');
		$this->meta_keywords = SiteConfig::getInstance()->getValue('seo_tires_meta_keywords');
		$this->meta_description = SiteConfig::getInstance()->getValue('seo_tires_meta_description');		
		
		$this->breadcrumbs = array(
			'/' => 'Home',
			'#' => 'Tires',
		);		
		
		$this->render('index', array(
			
		));
	}
	
	public function actionMake($alias)
	{
		$make = AutoMake::getMakeByAlias($alias);
		
		if (empty($make)) {
			 throw new CHttpException(404,'Page cannot be found.');
		}
		
		$this->pageTitle = str_replace('[make]', $make['title'], SiteConfig::getInstance()->getValue('seo_tires_make_title'));
		$this->meta_keywords = str_replace('[make]', $make['title'], SiteConfig::getInstance()->getValue('seo_tires_make_meta_keywords'));
		$this->meta_description = str_replace('[make]', $make['title'], SiteConfig::getInstance()->getValue('seo_tires_make_meta_description'));		
		$header_text_block = str_replace('[make]', $make['title'], SiteConfig::getInstance()->getValue('tires_make_header_text_block'));		
		
		$this->breadcrumbs = array(
			'/' => 'Home',
			'/tires.html' => 'Tires',
			'#' => $make['title'],
		);
		
		//SqlHelper::addView('AutoMake', $make['id']);
		
		$dataModels = AutoMake::getModels($make['id']);

		$this->render('make', array(
			'header_text_block' => $header_text_block,
			'make' => $make,
			'dataModels' => $dataModels,
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
		
		$this->pageTitle = str_replace(array('[make]', '[model]'), array($make['title'], $model['title']), SiteConfig::getInstance()->getValue('seo_tires_model_title'));
		$this->meta_keywords = str_replace(array('[make]', '[model]'), array($make['title'], $model['title']), SiteConfig::getInstance()->getValue('seo_tires_model_meta_keywords'));
		$this->meta_description = str_replace(array('[make]', '[model]'), array($make['title'], $model['title']), SiteConfig::getInstance()->getValue('seo_tires_model_meta_description'));		
		$header_text_block = str_replace(array('[make]', '[model]'), array($make['title'], $model['title']), SiteConfig::getInstance()->getValue('tires_model_header_text_block'));		
			
		$this->breadcrumbs = array(
			'/' => 'Home',
			'/tires.html' => 'Tires',
			'/tires'.$make['url'] => $make['title'] . ' tires',
			'#' => $model['title'],
		);
			
		$modelByYears = AutoModel::getYears($model['id']);
		$lastModelYear = AutoModel::getLastYear($model['id']);

		$this->render('model', array(
			'lastModelYear' => $lastModelYear,
			'make' => $make,
			'model' => $model,
			'modelByYears' => $modelByYears,
			'header_text_block' => $header_text_block,
			'lastYear' => AutoModel::getLastYear($model['id']),
		));
	}
	
	public function actionModelYear($makeAlias, $modelAlias, $year)
	{
		$make = AutoMake::getMakeByAlias($makeAlias);
		if (empty($make)) {
			 throw new CHttpException(404,'Page cannot be found.');
		}	
	
		$model = AutoModel::getModelByMakeAndAlias($make['id'], $modelAlias);
		if (empty($model)) {
			 throw new CHttpException(404,'Page cannot be found.');
		}	
	
		$modelYear = AutoModelYear::getYearByMakeAndModelAndAlias($make['id'], $model['id'], $year);
		if (empty($modelYear)) {
			 throw new CHttpException(404,'Page cannot be found.');
		}		
	
		$this->pageTitle = str_replace(array('[make]', '[model]', '[year]'), array($make['title'], $model['title'], $modelYear['year']), SiteConfig::getInstance()->getValue('seo_tires_model_year_title'));
		$this->meta_keywords = str_replace(array('[make]', '[model]', '[year]'), array($make['title'], $model['title'], $modelYear['year']), SiteConfig::getInstance()->getValue('seo_tires_model_year_meta_keywords'));
		$this->meta_description = str_replace(array('[make]', '[model]', '[year]'), array($make['title'], $model['title'], $modelYear['year']), SiteConfig::getInstance()->getValue('seo_tires_model_year_meta_description'));		
		$header_text_block = str_replace(array('[make]', '[model]', '[year]'), array($make['title'], $model['title'], $modelYear['year']), SiteConfig::getInstance()->getValue('tires_model_year_header_text_block'));		
			
		//SqlHelper::addView('AutoModelYear', $modelYear['id']);	
			
		$this->breadcrumbs = array(
			'/' => 'Home',
			'/tires.html' => 'Tires',
			'/tires'.$make['url'] => array('anchor'=>$make['title'], 'title'=>$make['title'] . ' tires'),
			'/tires'.$model['url'] => array('anchor'=>$model['title'], 'title'=>$make['title'] . ' ' . $model['title'] . ' tires'),
			'#' => $modelYear['year'] . ' ' .$make['title'] . ' ' . $model['title'],
		);	
			
		$models = AutoModelYear::getModelsByMakeAndYear($make['id'], $modelYear['year']);

		$carSpecsAndDimensions = AutoModelYear::getCarSpecsAndDimensions($modelYear['id']);
			
		$tires = AutoModelYear::getTires($modelYear['id']);	
		//d($tires);
		
		$this->render('model_year', array(
			'make' => $make,
			'model' => $model,
			'modelYear' => $modelYear,
			'modelYears' => AutoModel::getYears($model['id']),
			'competitors' => AutoModelYear::getFrontCompetitors($modelYear['id']),
			'otherModels' => AutoModelYear::getOtherMakeYear($models, $modelYear['id']),
			'header_text_block' => $header_text_block,
			'tires' => $tires,
		));	
	}	
	
	public function actionDiameter($value)
	{
		$model = TireRimDiameter::getItemByValue($value);

		if (empty($model)) {
			 throw new CHttpException(404,'Page cannot be found.');
		}	
			
		$this->pageTitle = str_replace(array('[diameter]'), array($value), SiteConfig::getInstance()->getValue('seo_tires_diameter_title'));
		$this->meta_keywords = str_replace(array('[diameter]'), array($value), SiteConfig::getInstance()->getValue('seo_tires_diameter_meta_keywords'));
		$this->meta_description = str_replace(array('[diameter]'), array($value), SiteConfig::getInstance()->getValue('seo_tires_diameter_meta_description'));		
		$header_text_block = str_replace(array('[diameter]'), array($value), SiteConfig::getInstance()->getValue('tires_diameter_header_text_block'));		
			
		$this->breadcrumbs = array(
			'/' => 'Home',
			'/tires.html' => 'Tires',
			'#' => 'R'.$value,
		);	
					
		$this->render('diameter', array(
			'tires' => Tire::getItemsByRimDiameterNonRunflat($model['id']),
			'model' => $model,
			'header_text_block' => $header_text_block,
		));	
	}	
	
	public function actionSize($vehicle_class, $section_width, $aspect_ratio, $rim_diameter)
	{
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
		
		
		$offset = (int)Yii::app()->request->getParam('offset', 0);
		$limit	= 20;
		
		$key    	= Tags::TAG_PROJECT . '_list_by_tire_' . $vehicle_class . '_' . $section_width . '_' . $aspect_ratio . '_' . $rim_diameter . '_' . $limit;
		$projects   = Yii::app()->cache->get($key);
		
		
		if ($projects === false || $offset<>0) {
			$where = " WHERE
						p.is_active = 1 AND 
						((
							p.rim_diameter_id=".$tire['rim_diameter_id']." AND
							p.tire_section_width_id=".$tire['section_width_id']." AND
							p.tire_aspect_ratio_id=".$tire['aspect_ratio_id']."
						) OR (
							p.rear_rim_diameter_id=".$tire['rim_diameter_id']." AND
							p.rear_tire_section_width_id=".$tire['section_width_id']." AND
							p.rear_tire_aspect_ratio_id=".$tire['aspect_ratio_id']."						
						) )";
			
			$sql = "SELECT 
						p.id AS id,
						p.view_count AS view_count,
						p.wheel_manufacturer AS wheel_manufacturer,
						p.wheel_model AS wheel_model,
						rd.value AS rim_diameter,
						rw.value AS rim_width,
						ror.value AS rim_offset_range,
						p.is_staggered_wheels AS is_staggered_wheels,
						r_rd.value AS rear_rim_diameter,
						r_rw.value AS rear_rim_width,
						tvc.code AS tire_vehicle_class,													
						r_ror.value AS rear_rim_offset_range,						
						tsw.value AS tire_section_width,						
						tar.value AS tire_aspect_ratio,						
						p.is_staggered_tires AS is_staggered_tires,
						r_tsw.value AS rear_tire_section_width,						
						r_tar.value AS rear_tire_aspect_ratio,						
						m.alias AS model_alias,
						m.title AS model_title,
						k.alias AS make_alias,
						k.title AS make_title,
						y.year AS year,
						y.id AS year_id
					FROM project AS p
					LEFT JOIN auto_model_year AS y ON p.model_year_id = y.id
					LEFT JOIN auto_model AS m ON p.model_id = m.id
					LEFT JOIN auto_make AS k ON p.make_id = k.id
					LEFT JOIN tire_rim_diameter AS rd ON p.rim_diameter_id = rd.id
					LEFT JOIN rim_width AS rw ON p.rim_width_id = rw.id
					LEFT JOIN rim_offset_range AS ror ON p.rim_offset_range_id = ror.id
					LEFT JOIN tire_rim_diameter AS r_rd ON p.rear_rim_diameter_id = r_rd.id
					LEFT JOIN rim_width AS r_rw ON p.rear_rim_width_id = r_rw.id
					LEFT JOIN rim_offset_range AS r_ror ON p.rear_rim_offset_range_id = r_ror.id
					LEFT JOIN tire_section_width AS tsw ON p.tire_section_width_id = tsw.id
					LEFT JOIN tire_aspect_ratio AS tar ON p.tire_aspect_ratio_id = tar.id
					LEFT JOIN tire_section_width AS r_tsw ON p.rear_tire_section_width_id = r_tsw.id
					LEFT JOIN tire_aspect_ratio AS r_tar ON p.rear_tire_aspect_ratio_id = r_tar.id
					LEFT JOIN tire_vehicle_class AS tvc ON p.tire_vehicle_class_id = tvc.id						
					{$where}
					ORDER BY p.view_count DESC
					LIMIT {$offset}, {$limit}";
					
			$projects = Yii::app()->db->createCommand($sql)->queryAll();	
			foreach ($projects as $k=>$project) {
				$projects[$k]['photo'] = Project::thumb($project['id'], 300, 200, 'resize');
			}			
			
			if (empty($filter) && $offset==0)
				Yii::app()->cache->get($key, $projects, 0, new Tags(Tags::TAG_PROJECT));			
		}
		
		if (Yii::app()->request->isAjaxRequest) {
			$this->renderPartial('_projects', array(
				'projects' => $projects,
			));
			Yii::app()->end();
		}				
		
		$ids = array();
		foreach ($tires as $item) {
			$ids[] = $item['id'];
		}
		
		$makeModels = Tire::getMakeModelsByTireIds($ids);
		
		$replaceFrom = array('[vehicle_class]', '[section_width]', '[aspect_ratio]', '[rim_diameter]');
		$replaceTo = array($vehicle_class, $section_width, $aspect_ratio, $rim_diameter);
		
		$this->pageTitle = str_replace($replaceFrom, $replaceTo, SiteConfig::getInstance()->getValue('seo_tires_size_title'));
		$this->meta_keywords = str_replace($replaceFrom, $replaceTo, SiteConfig::getInstance()->getValue('seo_tires_size_meta_keywords'));
		$this->meta_description = str_replace($replaceFrom, $replaceTo, SiteConfig::getInstance()->getValue('seo_tires_size_meta_description'));		
			
		$this->breadcrumbs = array(
			'/' => 'Home',
			'/tires.html' => 'Tires',
			'#' => Tire::format($tire),
		);	
			
		$similarSizes = Tire::getSimilarSizes($tire);
			
			
			
			
		$this->render('size', array(
			'projects' => $projects,
			'tire' => $tire,
			'makeModels' => $makeModels,
			'similarSizes' => $similarSizes,
		));	
	}	
	
	public function actionSizeMake($makeAlias, $vehicle_class, $section_width, $aspect_ratio, $rim_diameter)
	{
		$make = AutoMake::getMakeByAlias($makeAlias);
		
		if (empty($make)) {
			 throw new CHttpException(404,'Page cannot be found.');
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
		
		$ids = array();
		foreach ($tires as $item) {
			$ids[] = $item['id'];
		}
		
		$modelYears = Tire::getModelYearsByMakeTireIds($make['id'], $ids);
		
		$replaceFrom = array('[make]', '[vehicle_class]', '[section_width]', '[aspect_ratio]', '[rim_diameter]');
		$replaceTo = array($make['title'], $vehicle_class, $section_width, $aspect_ratio, $rim_diameter);

		$this->pageTitle = str_replace($replaceFrom, $replaceTo, SiteConfig::getInstance()->getValue('seo_tires_size_make_title'));
		$this->meta_keywords = str_replace($replaceFrom, $replaceTo, SiteConfig::getInstance()->getValue('seo_tires_size_make_meta_keywords'));
		$this->meta_description = str_replace($replaceFrom, $replaceTo, SiteConfig::getInstance()->getValue('seo_tires_size_make_meta_description'));		
		$header_text_block = str_replace($replaceFrom, $replaceTo, SiteConfig::getInstance()->getValue('tires_size_make_header_text_block'));		
        $this->canonical = 'https://autotk.com' . Tire::url($tire);
			
		$this->breadcrumbs = array(
			'/' => 'Home',
			'/tires.html' => 'Tires',
			Tire::url($tire) => Tire::format($tire),
			'#' => $make['title'],
		);	
        
		$this->render('size_make', array(
			'make' => $make,
			'tire' => $tire,
			'modelYears' => $modelYears,
			'header_text_block' => $header_text_block,
		));	
	}	
	

	
}