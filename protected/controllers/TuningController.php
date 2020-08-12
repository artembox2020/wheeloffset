<?php

class TuningController extends Controller
{
	public function actionIndex()
	{
		$this->pageTitle = SiteConfig::getInstance()->getValue('seo_tuning_title');
		$this->meta_keywords = SiteConfig::getInstance()->getValue('seo_tuning_meta_keywords');
		$this->meta_description = SiteConfig::getInstance()->getValue('seo_tuning_meta_description');	

		$this->breadcrumbs = array(
			'/' => 'Home',
			'#' => 'Tuning',
		);	
		
		$makes = AutoMake::getAllFrontFull();
		
		foreach ($makes as $k=>$v) {
			$makes[$k]['projects'] = Project::getCountByMake($k);
		}
 	
		$projects = Project::getFastest(10);
		$newProjects = Project::getNew(30);
		//d($projects);	
			
		$this->render('index', array(
			'makes' 	=> $makes,
			'projects' 	=> $projects,
			'newProjects' 	=> $newProjects,
		));
	}
	
	public function actionMake($alias)
	{
		$make = AutoMake::getMakeByAlias($alias);
		
		if (empty($make)) {
			 throw new CHttpException(404,'Page cannot be found.');
		}	
		
		$countProjects = Project::getCountByMake($make['id']);
		
		$this->pageTitle = str_replace(array('[make]', '[num]'), array($make['title'], $countProjects), SiteConfig::getInstance()->getValue('seo_tuning_make_title'));
		$this->meta_keywords = str_replace(array('[make]', '[num]'), array($make['title'], $countProjects), SiteConfig::getInstance()->getValue('seo_tuning_make_meta_keywords'));
		$this->meta_description = str_replace(array('[make]', '[num]'), array($make['title'], $countProjects), SiteConfig::getInstance()->getValue('seo_tuning_make_meta_description'));		
	
	
		$models = AutoMake::getModels($make['id']);
		foreach ($models as $k=>$v) {
			$count = Project::getCountByModel($v['id']);
			if ($count)
				$models[$k]['projects'] = $count;
			else
				unset($models[$k]);	
		}
 			
		$this->breadcrumbs = array(
			'/' => 'Home',
			'/tuning.html' => array(
				'anchor'=>'Tuning',
				'title'=>SiteConfig::getInstance()->getValue('seo_tuning_title'),
			),
			'#' => $make['title'],
		);	
		
		$this->render('make', array(
			'make' => $make,
			'models' => $models,
			'description' => str_replace(array('[make]', '[num]'), array($make['title'], $countProjects), SiteConfig::getInstance()->getValue('tuning_make_description')),
		));
	}
	
	public function actionModel($makeAlias, $modelAlias, $filter='')
	{
		$make = AutoMake::getMakeByAlias($makeAlias);
		if (empty($make)) {
			 throw new CHttpException(404,'Page cannot be found.');
		}	
	
		$model = AutoModel::getModelByMakeAndAlias($make['id'], $modelAlias);

		if (empty($model)) {
			 throw new CHttpException(404,'Page cannot be found.');
		}
		
		$filter = FilterHelper::tuningParse($filter);
		
		$offset = (int)Yii::app()->request->getParam('offset');
		$limit	= 50;
		
		$key    	= Tags::TAG_PROJECT . '__list_' . $model['id'] . '_' . $limit;
		$projects   = Yii::app()->cache->get($key);
		
		
		$where = array();
		$where[] = 'p.is_active=1';
		if ($projects === false || !empty($filter) || $offset > 0) {
			$where[] = "p.model_id = " . $model['id'];
			
			if (!empty($filter['rim_diameter_id'])) {
				$rim_diameter_id = (int) $filter['rim_diameter_id'];
				$where[] = "(p.rim_diameter_id = {$rim_diameter_id} OR p.rear_rim_diameter_id = {$rim_diameter_id})";
			}
			
			if (!empty($filter['rim_width_id'])) {
				$rim_width_id = (float) $filter['rim_width_id'];
				$where[] = "(p.rim_width_id = {$rim_width_id} OR p.rear_rim_width_id = {$rim_width_id})";
			}
			
			if (!empty($filter['rim_offset_range_id'])) {
				$rim_offset_range_id = (int) $filter['rim_offset_range_id'];
				$where[] = "(p.rim_offset_range_id = {$rim_offset_range_id} OR p.rear_rim_offset_range_id = {$rim_offset_range_id})";
			}
			
			if (!empty($filter['tire_section_width_id'])) {
				$tire_section_width_id = (int) $filter['tire_section_width_id'];
				$where[] = "(p.tire_section_width_id = {$tire_section_width_id} OR p.rear_tire_section_width_id = {$tire_section_width_id})";
			}
			
			if (!empty($where))
				$where = 'WHERE ' . implode(' AND ', $where);
			else 
				$where = '';
			
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
						y.year AS year,
						y.id AS year_id
					FROM project AS p
					LEFT JOIN auto_model_year AS y ON p.model_year_id = y.id
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
				'make' => $make,
				'model' => $model,
			));
			Yii::app()->end();
		}
		
		$countProjects = Project::getCountByModel($model['id'], $filter);
		$countProjectsMake = Project::getCountByMake($make['id']);
		
			
			
		$rFrom = array(
			'[make]', 
			'[model]', 
			'[num]', 
			'[diameter]', 
			'[width]', 
			'[tire]', 
			'[offset]'
		);
		
		$rTo = array(
			$make['title'], 
			$model['title'], 
			$countProjects, 
			isset($filter['diameter'])?$filter['diameter']:'',
			isset($filter['width'])?$filter['width']:'',
			isset($filter['tire'])?$filter['tire']:'',
			isset($filter['offset'])?$filter['offset']:'',
		);
		
		$key_seo = 'tuning_model';
		if (!empty($filter)) {
			if (isset($filter['diameter'])) { $key_seo.= '_diameter';}
			if (isset($filter['width'])) { $key_seo.= '_width';}
			if (isset($filter['tire'])) { $key_seo.= '_tire';}
			if (isset($filter['offset'])) { $key_seo.= '_offset';}
		}
		
		$this->pageTitle = str_replace($rFrom, $rTo, SiteConfig::getInstance()->getValue('seo_'.$key_seo.'_title'));
		$this->meta_keywords = str_replace($rFrom, $rTo, SiteConfig::getInstance()->getValue('seo_'.$key_seo.'_meta_keywords'));
		$this->meta_description = str_replace($rFrom, $rTo, SiteConfig::getInstance()->getValue('seo_'.$key_seo.'_meta_description'));		
		$description = str_replace($rFrom, $rTo, SiteConfig::getInstance()->getValue($key_seo.'_description'));		
		$lastModelYear = AutoModel::getLastYear($model['id']);
				
		$this->breadcrumbs = array(
			'/' => 'Home',
			'/tuning.html' => array(
				'anchor'=>'Tuning',
				'title'=>SiteConfig::getInstance()->getValue('seo_tuning_title'),
			),
			'/tuning' . $make['url'] => array(
				'anchor'=>$make['title'],
				'title' => str_replace(array('[make]', '[num]'), array($make['title'], $countProjectsMake), SiteConfig::getInstance()->getValue('seo_tuning_make_title')),
			),
			'#' => $model['title'],
		);	
		
		$this->render('model', array(
			'filter' => $filter,
			'lastModelYear' => $lastModelYear,
			'projects' => $projects,
			'make' => $make,
			'model' => $model,
			'countProjects' => $countProjects,
			'description' => $description,
		));
	}

	public function _actionModel($makeAlias, $modelAlias, $filter='')
	{
		d($filter);
		
		
		$make = AutoMake::getMakeByAlias($makeAlias);
		if (empty($make)) {
			 throw new CHttpException(404,'Page cannot be found.');
		}	
	
		$model = AutoModel::getModelByMakeAndAlias($make['id'], $modelAlias);

		if (empty($model)) {
			 throw new CHttpException(404,'Page cannot be found.');
		}
		
		$filter = Yii::app()->request->getParam('filter');
		$offset = (int)Yii::app()->request->getParam('offset');
		$limit	= 50;
		
		$key    	= Tags::TAG_PROJECT . '__list_' . $model['id'] . '_' . $limit;
		$projects   = Yii::app()->cache->get($key);
		
		
		$where = array();
		$where[] = 'p.is_active=1';
		if ($projects === false || !empty($filter) || $offset > 0) {
			$where[] = "p.model_id = " . $model['id'];
			
			if (!empty($filter['rim_diameter_id'])) {
				$rim_diameter_id = (int) $filter['rim_diameter_id'];
				$where[] = "(p.rim_diameter_id = {$rim_diameter_id} OR p.rear_rim_diameter_id = {$rim_diameter_id})";
			}
			
			if (!empty($filter['rim_width_id'])) {
				$rim_width_id = (float) $filter['rim_width_id'];
				$where[] = "(p.rim_width_id = {$rim_width_id} OR p.rear_rim_width_id = {$rim_width_id})";
			}
			
			if (!empty($filter['rim_offset_range_id'])) {
				$rim_offset_range_id = (int) $filter['rim_offset_range_id'];
				$where[] = "(p.rim_offset_range_id = {$rim_offset_range_id} OR p.rear_rim_offset_range_id = {$rim_offset_range_id})";
			}
			
			if (!empty($filter['tire_section_width_id'])) {
				$tire_section_width_id = (int) $filter['tire_section_width_id'];
				$where[] = "(p.tire_section_width_id = {$tire_section_width_id} OR p.rear_tire_section_width_id = {$tire_section_width_id})";
			}
			
			if (!empty($where))
				$where = 'WHERE ' . implode(' AND ', $where);
			else 
				$where = '';
			
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
						y.year AS year,
						y.id AS year_id
					FROM project AS p
					LEFT JOIN auto_model_year AS y ON p.model_year_id = y.id
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
				'make' => $make,
				'model' => $model,
			));
			Yii::app()->end();
		}
		
		$countProjects = Project::getCountByModel($model['id'], $filter);
		$countProjectsMake = Project::getCountByMake($make['id']);
		
		$rim_diameter = !empty($filter['rim_diameter_id'])?TireRimDiameter::getValueById($filter['rim_diameter_id']):'';
		$rim_width = !empty($filter['rim_width_id'])?RimWidth::getValueById($filter['rim_width_id']):'';
		
		$rFrom = array('[make]', '[model]', '[num]', '[rim_diameter]', '[rim_width]');
		$rTo = array($make['title'], $model['title'], $countProjects, $rim_diameter, $rim_width);
		
		$key_seo = 'tuning_model';
		if (!empty($filter)) {
			$key_seo = 'tuning_model_filter';
		}
		
		$this->pageTitle = str_replace($rFrom, $rTo, SiteConfig::getInstance()->getValue('seo_'.$key_seo.'_title'));
		$this->meta_keywords = str_replace($rFrom, $rTo, SiteConfig::getInstance()->getValue('seo_'.$key_seo.'_meta_keywords'));
		$this->meta_description = str_replace($rFrom, $rTo, SiteConfig::getInstance()->getValue('seo_'.$key_seo.'_meta_description'));		
		$description = str_replace($rFrom, $rTo, SiteConfig::getInstance()->getValue($key_seo.'_description'));		
		$lastModelYear = AutoModel::getLastYear($model['id']);
				
		$this->breadcrumbs = array(
			'/' => 'Home',
			'/tuning.html' => array(
				'anchor'=>'Tuning',
				'title'=>SiteConfig::getInstance()->getValue('seo_tuning_title'),
			),
			'/tuning' . $make['url'] => array(
				'anchor'=>$make['title'],
				'title' => str_replace(array('[make]', '[num]'), array($make['title'], $countProjectsMake), SiteConfig::getInstance()->getValue('seo_tuning_make_title')),
			),
			'#' => $model['title'],
		);	
		
		$this->render('model', array(
			'filter' => $filter,
			'lastModelYear' => $lastModelYear,
			'projects' => $projects,
			'make' => $make,
			'model' => $model,
			'countProjects' => $countProjects,
			'description' => $description,
		));
	}

	public function actionProject($makeAlias, $modelAlias, $id)
	{
		$make = AutoMake::getMakeByAlias($makeAlias);
		if (empty($make)) {
			 throw new CHttpException(404,'Page cannot be found.');
		}	
	
		$model = AutoModel::getModelByMakeAndAlias($make['id'], $modelAlias);

		if (empty($model)) {
			 throw new CHttpException(404,'Page cannot be found.');
		}
		
		$project = Project::getById($make['id'], $model['id'], $id);

		if (empty($project)) {
			throw new CHttpException(404,'Page cannot be found.');
		}
		
		SqlHelper::addView('Project', $project['id']);
		
		$replaceFrom = array(
			'[make]', 
			'[model]', 
			'[front_rim_diameter]', 
			'[front_rim_width]', 
			'[front_offset]', 
			'[front_tiresize]', 
			'[rear_rim_diameter]', 
			'[rear_rim_width]', 
			'[rear_offset]', 
			'[rear_tiresize]',
			'[rims_brand]'
		);


		$rearTire = '';
			
		if (!empty($project['rear_tire_section_width']) && !empty($project['rear_tire_section_width']) && !empty($project['rear_tire_section_width'])) {
			$rearTire = Tire::format(array(
				'section_width' => $project['rear_tire_section_width'],
				'aspect_ratio' 	=> $project['rear_tire_aspect_ratio'],
				'rim_diameter' 	=> $project['rear_rim_diameter'],
			), false);
		}
		
		$replaceTo = array(
			$make['title'],
			$model['title'],
			$project['rim_diameter'],
			$project['rim_width'],
			(($project['rim_offset_range']>0)?'+':'').$project['rim_offset_range'],
			Tire::format(array(
				'section_width' => $project['tire_section_width'],
				'aspect_ratio' 	=> $project['tire_aspect_ratio'],
				'rim_diameter' 	=> $project['rim_diameter'],
			), false),
			$project['rear_rim_diameter'],
			$project['rear_rim_width'],
			(($project['rear_rim_offset_range']>0)?'+':'').$project['rear_rim_offset_range'],
			$rearTire,
			$project['wheel_manufacturer'] . ' ' . $project['wheel_model'],
		);
		
		$this->pageTitle 		= str_replace($replaceFrom, $replaceTo, SiteConfig::getInstance()->getValue('seo_tuning_project_title'));
		$this->meta_keywords 	= str_replace($replaceFrom, $replaceTo, SiteConfig::getInstance()->getValue('seo_tuning_project_meta_keywords'));
		$this->meta_description = str_replace($replaceFrom, $replaceTo, SiteConfig::getInstance()->getValue('seo_tuning_project_meta_description'));		
		
		$countProjects = Project::getCountByModel($model['id']);
		$countProjectsMake = Project::getCountByMake($make['id']);		
		
		$this->breadcrumbs = array(
			'/' 						=> 'Home',
			'/tuning.html' => array(
				'anchor'=>'Tuning',
				'title'=>SiteConfig::getInstance()->getValue('seo_tuning_title'),
			),
			'/tuning' . $make['url'] => array(
				'anchor'=>$make['title'],
				'title' => str_replace(array('[make]', '[num]'), array($make['title'], $countProjectsMake), SiteConfig::getInstance()->getValue('seo_tuning_make_title')),
			),
			'/tuning' . $model['url'] 	=> array(
				'title' => str_replace(array('[make]', '[model]', '[num]'), array($make['title'], $model['title'], $countProjects), SiteConfig::getInstance()->getValue('seo_tuning_model_title')),
				'anchor'=>$model['title'],
			),
			'#' => $this->pageTitle,
		);	
				 	
		$key    = Tags::TAG_PROJECT_PHOTO . '_photos_' . $project['id'];		
		$photos = Yii::app()->cache->get($key);
		if ($photos === false) {
			$photos = array();
			
			$criteria = new CDbCriteria;
			$criteria->compare('project_id', $project['id']);
			$criteria->order = 'rank';
			$items = ProjectPhoto::model()->findAll($criteria);
			
			foreach ($items as $item) {
				$photos[] = $item->getThumb();
			}
			
			Yii::app()->cache->get($key, $photos, 0, new Tags(Tags::TAG_PROJECT_PHOTO . $project['id']));		
		}
		
		$modelYear = false;
		if (is_numeric($project['year'])) {
			$modelYear = AutoModelYear::getYearByMakeAndModelAndAlias($make['id'], $model['id'], $project['year']);
		}
		
		$key = Tags::TAG_PROJECT . '__nextProject__' . $project['id'];
		$nextProject = Yii::app()->cache->get($key);
		if ($nextProject === false) {
			$sqlTemplate = "SELECT 
						p.id AS id,
						p.view_count AS view_count,
						p.tire_model AS tire_model,
						p.tire_manufacturer AS tire_manufacturer,
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
						y.year AS year,
						y.id AS year_id,
						m.title AS model_title,
						m.alias AS model_alias,
						k.title AS make_title,
						k.alias AS make_alias
					FROM project AS p
					LEFT JOIN auto_model_year AS y ON p.model_year_id = y.id
					LEFT JOIN auto_model AS m ON y.model_id = m.id
					LEFT JOIN auto_make AS k ON m.make_id = k.id
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
					WHERE %s AND p.model_id = ".$model['id']." AND p.is_active=1
					ORDER BY %s
					LIMIT 1";
						
				$where = 'p.id > '.$project['id'];		
				$order = 'p.id ASC';		
				$nextProject = Yii::app()->db->createCommand(sprintf($sqlTemplate, $where, $order))->queryRow();		
				if (empty($nextProject)) {
					$where = 'p.id < '.$project['id'];		
					$order = 'p.id ASC';
					$nextProject = Yii::app()->db->createCommand(sprintf($sqlTemplate, $where, $order))->queryRow();					
				}		
			

			Yii::app()->cache->set($key, $nextProject, 0, new Tags(Tags::TAG_PROJECT));
		}
		
		if (!empty($nextProject)) {
			$nextProject['photo'] = Project::thumb($nextProject['id'], 300, 200, 'resize');
		}

		$this->render('project', array(
			'project' 		=> $project,
			'make' 			=> $make,
			'model' 		=> $model,
			'photos' 		=> $photos,
			'modelYear' 	=> $modelYear,
			'nextProject' 	=> $nextProject,
		));
	}
	
}