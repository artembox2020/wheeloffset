<?php

class WeightController extends Controller
{
	public function actionIndex()
	{
		$this->pageTitle 		= SiteConfig::getInstance()->getValue('seo_weight_title');
		$this->meta_keywords 	= SiteConfig::getInstance()->getValue('seo_weight_meta_keywords');
		$this->meta_description = SiteConfig::getInstance()->getValue('seo_weight_meta_description');		
		
		$this->breadcrumbs = array(
			'/' => 'Home',
			'#' => 'Weight',
		);		

		$makes 		  	= AutoMake::getAllFrontFull();
		$easiestItems 	= AutoCompletion::getItemsCurbWeight(5, 'ASC');
		$heaviestItems 	= AutoCompletion::getItemsCurbWeight(5, 'DESC');
		
		$this->render('index', array(
			'makes' 		=> $makes,
			'easiestItems' 	=> $easiestItems,
			'heaviestItems' => $heaviestItems,
		));
	}
	
	public function actionMake($alias)
	{
		$make = AutoMake::getMakeByAlias($alias);
		
		if (empty($make)) {
			 throw new CHttpException(404,'Page cannot be found.');
		}
		
		$this->pageTitle = str_replace('[make]', $make['title'], SiteConfig::getInstance()->getValue('seo_weight_make_title'));
		$this->meta_keywords = str_replace('[make]', $make['title'], SiteConfig::getInstance()->getValue('seo_weight_make_meta_keywords'));
		$this->meta_description = str_replace('[make]', $make['title'], SiteConfig::getInstance()->getValue('seo_weight_make_meta_description'));		
		$header_text_block = str_replace('[make]', $make['title'], SiteConfig::getInstance()->getValue('weight_make_header_text_block'));		
		
		$this->breadcrumbs = array(
			'/' => 'Home',
			'/weight/' => 'Weight',
			'#' => $make['title'],
		);
		
		$dataModels = AutoMake::getModels($make['id']);

		//d($dataModels);
		
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
		
		$this->pageTitle = str_replace(array('[make]', '[model]'), array($make['title'], $model['title']), SiteConfig::getInstance()->getValue('seo_weight_model_title'));
		$this->meta_keywords = str_replace(array('[make]', '[model]'), array($make['title'], $model['title']), SiteConfig::getInstance()->getValue('seo_weight_model_meta_keywords'));
		$this->meta_description = str_replace(array('[make]', '[model]'), array($make['title'], $model['title']), SiteConfig::getInstance()->getValue('seo_weight_model_meta_description'));		
		$header_text_block = str_replace(array('[make]', '[model]'), array($make['title'], $model['title']), SiteConfig::getInstance()->getValue('weight_model_header_text_block'));		
			
		$this->breadcrumbs = array(
			'/' => 'Home',
			'/weight/' => 'Weight',
			'/weight'.$make['url'] => $make['title'] . ' weight',
			'#' => $model['title'],
		);
			
		$modelByYears = AutoModel::getYears($model['id']);
		$lastModelYear = AutoModel::getLastYear($model['id']);
		
		$lastYearCompletions = AutoCompletion::getCurbWeightByModelYear($lastModelYear['id']);
		
		$years = AutoModel::getYears($model['id']);
		//d($years);
		
		$otherYearsCompletions = array();
		foreach ($years as $yearItem) {
			if ($yearItem['year'] != $lastModelYear['year']) {
				$otherYearsCompletions[] = array(
					'year'		  => AutoModelYear::getYearByMakeAndModelAndAlias($make['id'], $model['id'], $yearItem['year']),
					'completions' => AutoCompletion::getCurbWeightByModelYear($yearItem['id']),
				); 
			}
		}
		 
		//d($otherYearsCompletions); 
		
		$competitors = AutoModel::getFrontCompetitors($model['id']);
		//usort ($competitors, "cmpArrayTimes");	
		
		///d($competitors);
		
		$this->render('model', array(
			'lastModelYear' 		=> $lastModelYear,
			'lastYearCompletions' 	=> $lastYearCompletions,
			'competitors' 			=> $competitors,
			'make' => $make,
			'model' => $model,
			'modelByYears' => $modelByYears,
			'header_text_block' => $header_text_block,
			'years' => $years,
			'otherYearsCompletions' => $otherYearsCompletions,
		));
	}
		
}