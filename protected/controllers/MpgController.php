<?php

class MpgController extends Controller
{
	public function actionIndex()
	{
		$this->pageTitle 		= SiteConfig::getInstance()->getValue('seo_mpg_title');
		$this->meta_keywords 	= SiteConfig::getInstance()->getValue('seo_mpg_meta_keywords');
		$this->meta_description = SiteConfig::getInstance()->getValue('seo_mpg_meta_description');		
		
		$this->breadcrumbs = array(
			'/' => 'Home',
			'#' => 'Gas Mileage',
		);		

		$makes 		= AutoMake::getAllFrontFull();
		$bestItems 	= AutoCompletion::getItemsFuelEconomy(10, 'DESC');
		
		$this->render('index', array(
			'makes' 		=> $makes,
			'bestItems' 	=> $bestItems,
		));
	}
	
	public function actionMake($alias)
	{
		$make = AutoMake::getMakeByAlias($alias);
		
		if (empty($make)) {
			 throw new CHttpException(404,'Page cannot be found.');
		}
		
		$this->pageTitle = str_replace('[make]', $make['title'], SiteConfig::getInstance()->getValue('seo_mpg_make_title'));
		$this->meta_keywords = str_replace('[make]', $make['title'], SiteConfig::getInstance()->getValue('seo_mpg_make_meta_keywords'));
		$this->meta_description = str_replace('[make]', $make['title'], SiteConfig::getInstance()->getValue('seo_mpg_make_meta_description'));		
		$header_text_block = str_replace('[make]', $make['title'], SiteConfig::getInstance()->getValue('mpg_make_header_text_block'));		
		
		$this->breadcrumbs = array(
			'/' => 'Home',
			'/mpg/' => 'Gas Mileage',
			'#' => $make['title'],
		);
		
		$dataModels = AutoMake::getModels($make['id']);
		$dataModels = AutoModel::preparateMpg($make['id'], $dataModels);
		
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
		
		$this->pageTitle = str_replace(array('[make]', '[model]'), array($make['title'], $model['title']), SiteConfig::getInstance()->getValue('seo_mpg_model_title'));
		$this->meta_keywords = str_replace(array('[make]', '[model]'), array($make['title'], $model['title']), SiteConfig::getInstance()->getValue('seo_mpg_model_meta_keywords'));
		$this->meta_description = str_replace(array('[make]', '[model]'), array($make['title'], $model['title']), SiteConfig::getInstance()->getValue('seo_mpg_model_meta_description'));		
		$header_text_block = str_replace(array('[make]', '[model]'), array($make['title'], $model['title']), SiteConfig::getInstance()->getValue('mpg_model_header_text_block'));		
			
		$this->breadcrumbs = array(
			'/' => 'Home',
			'/mpg/' => 'Gas Mileage',
			'/mpg'.$make['url'] => $make['title'],
			'#' => $model['title'],
		);
			
		$modelByYears = AutoModel::getYears($model['id']);
		$lastModelYear = AutoModel::getLastYear($model['id']);
		
		$lastYearCompletions = AutoCompletion::getMpgByModelYear($lastModelYear['id']);
		
		$years = AutoModel::getYears($model['id']);
		//d($years);
		
		$otherYearsCompletions = array();
		foreach ($years as $yearItem) {
			if ($yearItem['year'] != $lastModelYear['year']) {
				$otherYearsCompletions[] = array(
					'year'		  => AutoModelYear::getYearByMakeAndModelAndAlias($make['id'], $model['id'], $yearItem['year']),
					'completions' => AutoCompletion::getMpgByModelYear($yearItem['id']),
				); 
			}
		}
		
		$competitors = AutoModel::getFrontCompetitors($model['id']);
		
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