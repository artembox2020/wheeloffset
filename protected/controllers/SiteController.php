<?php

class SiteController extends Controller
{
	/**
	 * This is the default 'index' action that is invoked
	 * when an action is not explicitly requested by users.
	 */
	public function actionIndex()
	{
		$this->pageTitle = SiteConfig::getInstance()->getValue('seo_home_title');
		$this->meta_keywords = SiteConfig::getInstance()->getValue('seo_home_meta_keywords');
		$this->meta_description = SiteConfig::getInstance()->getValue('seo_home_meta_description');
		
		$this->render('index', array(
			
		));
	}
	
	public function actionMake($alias)
	{
		$make = AutoMake::getMakeByAlias($alias);
		
		if (empty($make)) {
			 throw new CHttpException(404,'Page cannot be found.');
		}
		
		$this->pageTitle = str_replace('[make]', $make['title'], SiteConfig::getInstance()->getValue('seo_make_title'));
		$this->meta_keywords = str_replace('[make]', $make['title'], SiteConfig::getInstance()->getValue('seo_make_meta_keywords'));
		$this->meta_description = str_replace('[make]', $make['title'], SiteConfig::getInstance()->getValue('seo_make_meta_description'));		
		
		$this->breadcrumbs = array(
			'/' => 'Home',
			'#' => $make['title'],
		);
		
		SqlHelper::addView('AutoMake', $make['id']);
		
		$dataModels = AutoMake::getModels($make['id']);
		//d($dataModels);
		
		$this->render('make', array(
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
		
		$this->pageTitle = str_replace(array('[make]', '[model]'), array($make['title'], $model['title']), SiteConfig::getInstance()->getValue('seo_model_title'));
		$this->meta_keywords = str_replace(array('[make]', '[model]'), array($make['title'], $model['title']), SiteConfig::getInstance()->getValue('seo_model_meta_keywords'));
		$this->meta_description = str_replace(array('[make]', '[model]'), array($make['title'], $model['title']), SiteConfig::getInstance()->getValue('seo_model_meta_description'));		
			
		$this->breadcrumbs = array(
			'/' => 'Home',
			$make['url'] => $make['title'],
			'#' => $model['title'],
		);
			
		$lastModelYear = AutoModel::getLastYear($model['id']);
		$modelByYears = AutoModel::getYears($model['id']);
		$models = AutoMake::getModels($make['id']);
		
		SqlHelper::addView('AutoModel', $model['id']);
		
		//d($completionsTime);		
				
		$this->render('model', array(
			'make' => $make,
			'model' => $model,
			'lastModelYear' => $lastModelYear,
			'completion' => AutoModel::getLastCompletion($model['id']),
			'modelByYears' => $modelByYears,
			'models' => $models,
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
	
		$this->pageTitle = str_replace(array('[make]', '[model]', '[year]'), array($make['title'], $model['title'], $modelYear['year']), SiteConfig::getInstance()->getValue('seo_model_year_title'));
		$this->meta_keywords = str_replace(array('[make]', '[model]', '[year]'), array($make['title'], $model['title'], $modelYear['year']), SiteConfig::getInstance()->getValue('seo_model_year_meta_keywords'));
		$this->meta_description = str_replace(array('[make]', '[model]', '[year]'), array($make['title'], $model['title'], $modelYear['year']), SiteConfig::getInstance()->getValue('seo_model_year_meta_description'));		
			
		SqlHelper::addView('AutoModelYear', $modelYear['id']);	
			
		$this->breadcrumbs = array(
			'/' => 'Home',
			$make['url'] => $make['title'],
			$model['url'] => $model['title'],
			'#' => $modelYear['year'] . ' ' .$make['title'] . ' ' . $model['title'],
		);	
			
		$completions = AutoCompletion::getItemsByYear($modelYear['id']);
		$models = AutoModelYear::getModelsByMakeAndYear($make['id'], $modelYear['year']);
		
		$models = AutoModelYear::getOtherMakeYear($models, $modelYear['id']);
		
		$competitors = AutoModelYear::getFrontCompetitors($modelYear['id']);
		$carSpecsAndDimensions = AutoModelYear::getCarSpecsAndDimensions($modelYear['id']);
		$lastCompletion = AutoModelYear::getLastCompletion($modelYear['id']);
			
		$this->render('model_year', array(
			'make' => $make,
			'model' => $model,
			'modelYear' => $modelYear,
			'modelYears' => AutoModel::getYears($model['id']),
			'completions' => $completions,
			'models' => $models,
			'competitors' => $competitors,
			'lastCompletion' => $lastCompletion,
		));	
	}	
	
	public function actionModelYearPhotos($makeAlias, $modelAlias, $year)
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
	
		$this->pageTitle = str_replace(array('[make]', '[model]', '[year]'), array($make['title'], $model['title'], $modelYear['year']), SiteConfig::getInstance()->getValue('seo_model_year_photos_title'));
		$this->meta_keywords = str_replace(array('[make]', '[model]', '[year]'), array($make['title'], $model['title'], $modelYear['year']), SiteConfig::getInstance()->getValue('seo_model_year_photos_meta_keywords'));
		$this->meta_description = str_replace(array('[make]', '[model]', '[year]'), array($make['title'], $model['title'], $modelYear['year']), SiteConfig::getInstance()->getValue('seo_model_year_photos_meta_description'));		
			

		$this->breadcrumbs = array(
			'/' => 'Home',
			$make['url'] => $make['title'],
			$model['url'] => $model['title'],
			$model['url'].$modelYear['year'].'/' => $modelYear['year'] . ' ' .$make['title'] . ' ' . $model['title'],
			'#' => 'Photos',
		);	
			
		$photos = AutoModelYearPhoto::getYearPhotos($modelYear['id']);	
			
		$this->render('model_year_photos', array(
			'make' => $make,
			'model' => $model,
			'modelYear' => $modelYear,
			'photos' => $photos,
			'description' => str_replace(array('[make]', '[model]', '[year]'), array($make['title'], $model['title'], $modelYear['year']), SiteConfig::getInstance()->getValue('model_year_photos_description')),
		));	
	}	
	
	/**
	 * This is the action to handle external exceptions.
	 */
	public function actionError()
	{
		if($error=Yii::app()->errorHandler->error)
		{
			if(Yii::app()->request->isAjaxRequest)
				echo $error['message'];
			else
				$this->render('error', $error);
		}
	}	
		
	private function _getModelId($modelName, $attributes) {

		$criteria=new CDbCriteria;
		foreach ($attributes as $attribute=>$value) {
			if ($value === null) {
				$criteria->addCondition("{$attribute} IS NULL");
			} else {
				$criteria->compare($attribute, $value);
			}
		}
		
		$model = CActiveRecord::model($modelName)->find($criteria);
		if (empty($model)) {
			$attributes['rank'] = 0;
			$model = new $modelName;
			$model->attributes = $attributes;
			$model->save();
		}
		
		return $model->id;
	}

	public function actionPage($alias)
	{
		$title = SiteConfig::getInstance()->getValue('static_'.$alias.'_title');
		$this->pageTitle = $title;
		$this->meta_keywords = SiteConfig::getInstance()->getValue('static_'.$alias.'_meta_keywords');
		$this->meta_description = SiteConfig::getInstance()->getValue('static_'.$alias.'_meta_description');
		$content = SiteConfig::getInstance()->getValue('static_'.$alias.'_content');
		
		$this->breadcrumbs = array(
			'/' => 'Home',
			'#' => $title,
		);		
		
		$this->render('page', array(
			'title' => $title,
			'content' => $content,
		));
	}	
	
	public function actionFlush()
	{
		Yii::app()->cache->flush();
	}	
	
	public function actionConfigFlush()
	{
		Yii::app()->cache->delete(SiteConfig::CACHE_KEY);
	}	
	
	public function actionTest()
	{
		$sql = "SELECT CONCAT(model_id, '_', year) AS c, COUNT(*) AS a FROM auto_model_year GROUP BY model_id, year HAVING a > 1";
		$items = Yii::app()->db->createCommand($sql)->queryAll();
		
		d($items, 0);
		
		foreach ($items as $item) {
			$exl = explode('_', $item['c']);

			$model_id = $exl[0];
			$year = $exl[1];
			
			$criteria = new CDbCriteria;
			$criteria->compare('model_id', $model_id);
			$criteria->compare('year', $year);
			$criteria->order = 'id DESC';
			$model = AutoModelYear::model()->find($criteria);
			if (!empty($model)) {
				$model->delete();
			}
		}
	}
	
	public function actionTestRun()
	{
		ini_set('max_execution_time', 1000);
		
		$criteria = new CDbCriteria;		
		$criteria->with = array('Model', 'Model.Make');
		$modelYears = AutoModelYear::model()->findAll($criteria);
		foreach ($modelYears as $modelYear) {
			$tires = AutoModelYear::getTires($modelYear['id']);
			$sizes = array();
			foreach ($tires as $tire) {
				$d = Tire::diameter($tire);
				if ($d < 22)
				$sizes[]  = $d;
			}
			
			if (!empty($sizes)) {
			
				$min = min($sizes);
				$max = max($sizes);
					
				$percent = 0;	
				if ($min && $max && $min!=$max) {
					$percent = ($max - $min)/$min*100;
			
				}
				
				if ($percent >= 2) {
					echo $modelYear->Model->Make->title . ' ' . $modelYear->Model->title . ' ' . $modelYear->year . ' ' . round($percent, 2);
					echo "<br/>";
				}
			}
		}
	}
		
	public function actionR()
	{
		ini_set('max_execution_time', 10000);
		
		$items = AutoModelYearCompetitor::model()->findAll();
		
		$models = AutoModel::model()->findAll();
		foreach ($models as $model) {
			$modelCompetitorIds = array();
			
			$criteria = new CDbCriteria;
			$criteria->compare('model_id', $model->id);
			$modelYears = AutoModelYear::model()->findAll($criteria);
			
			foreach ($modelYears as $modelYear) {
				
				$sql = "SELECT y.model_id AS model_id FROM  auto_model_year_competitor AS c
						LEFT JOIN auto_model_year AS y ON c.competitor_id = y.id
						WHERE c.model_year_id = {$modelYear->id} AND y.year >= 2014 
				";
				
				$items = Yii::app()->db->createCommand($sql)->queryAll();
				
				foreach ($items as $item) {
					$modelCompetitorIds[$item['model_id']] = $item['model_id'];
				}
			}
			
			
			$model->scenario = 'updateAdmin';
			$model->post_competitors = $modelCompetitorIds;
			$model->save();
		}
		
	}
	
}