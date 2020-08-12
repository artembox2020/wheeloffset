<?php

class HorsepowerController extends Controller
{
	public function actionIndex()
	{
		$this->pageTitle = SiteConfig::getInstance()->getValue('seo_horsepower_title');
		$this->meta_keywords = SiteConfig::getInstance()->getValue('seo_horsepower_meta_keywords');
		$this->meta_description = SiteConfig::getInstance()->getValue('seo_horsepower_meta_description');		
		
		$this->breadcrumbs = array(
			'/' => 'Home',
			'#' => 'Horsepower',
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
		
		$this->pageTitle = str_replace('[make]', $make['title'], SiteConfig::getInstance()->getValue('seo_horsepower_make_title'));
		$this->meta_keywords = str_replace('[make]', $make['title'], SiteConfig::getInstance()->getValue('seo_horsepower_make_meta_keywords'));
		$this->meta_description = str_replace('[make]', $make['title'], SiteConfig::getInstance()->getValue('seo_horsepower_make_meta_description'));		
		$header_text_block = str_replace('[make]', $make['title'], SiteConfig::getInstance()->getValue('horsepower_make_header_text_block'));		
		
		$this->breadcrumbs = array(
			'/' => 'Home',
			'/horsepower.html' => 'Horsepower',
			'#' => $make['title'],
		);
		
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
		
		$this->pageTitle = str_replace(array('[make]', '[model]'), array($make['title'], $model['title']), SiteConfig::getInstance()->getValue('seo_horsepower_model_title'));
		$this->meta_keywords = str_replace(array('[make]', '[model]'), array($make['title'], $model['title']), SiteConfig::getInstance()->getValue('seo_horsepower_model_meta_keywords'));
		$this->meta_description = str_replace(array('[make]', '[model]'), array($make['title'], $model['title']), SiteConfig::getInstance()->getValue('seo_horsepower_model_meta_description'));		
		$header_text_block = str_replace(array('[make]', '[model]'), array($make['title'], $model['title']), SiteConfig::getInstance()->getValue('horsepower_model_header_text_block'));		
			
		$this->breadcrumbs = array(
			'/' => 'Home',
			'/horsepower.html' => 'Horsepower',
			'/horsepower'.$make['url'] => $make['title'] . ' horsepower',
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
	
		$this->pageTitle = str_replace(array('[make]', '[model]', '[year]'), array($make['title'], $model['title'], $modelYear['year']), SiteConfig::getInstance()->getValue('seo_horsepower_model_year_title'));
		$this->meta_keywords = str_replace(array('[make]', '[model]', '[year]'), array($make['title'], $model['title'], $modelYear['year']), SiteConfig::getInstance()->getValue('seo_horsepower_model_year_meta_keywords'));
		$this->meta_description = str_replace(array('[make]', '[model]', '[year]'), array($make['title'], $model['title'], $modelYear['year']), SiteConfig::getInstance()->getValue('seo_horsepower_model_year_meta_description'));		
		$header_text_block = str_replace(array('[make]', '[model]', '[year]'), array($make['title'], $model['title'], $modelYear['year']), SiteConfig::getInstance()->getValue('horsepower_model_year_header_text_block'));		
			
		//SqlHelper::addView('AutoModelYear', $modelYear['id']);	
			
		$this->breadcrumbs = array(
			'/' => 'Home',
			'/horsepower.html' => 'Horsepower',
			'/horsepower'.$make['url'] => array('anchor'=>$make['title'], 'title'=>$make['title'] . ' horsepower'),
			'/horsepower'.$model['url'] => array('anchor'=>$model['title'], 'title'=>$make['title'] . ' ' . $model['title'] . ' horsepower'),
			'#' => $modelYear['year'] . ' ' .$make['title'] . ' ' . $model['title'],
		);	
		
		$completions = AutoModelYear::getFrontCompetitorsHp($modelYear['id']);
		
		$models = AutoModelYear::getModelsByMakeAndYear($make['id'], $modelYear['year']);
		
		$this->render('model_year', array(
			'completions' => $completions,
			'make' => $make,
			'model' => $model,
			'modelYear' => $modelYear,
			'modelYears' => AutoModel::getYears($model['id']),
			'competitors' => AutoModelYear::getFrontCompetitors($modelYear['id']),
			'otherModels' => AutoModelYear::getOtherMakeYear($models, $modelYear['id']),
			'header_text_block' => $header_text_block,
			
		));	
	}	
	
	public function actionHp($hp,$page=1)
	{	
		$this->pageTitle = str_replace(array('[hp]'), array($hp), SiteConfig::getInstance()->getValue('seo_horsepower_hp_title'));
		$this->meta_keywords = str_replace(array('[hp]'), array($hp), SiteConfig::getInstance()->getValue('seo_horsepower_hp_meta_keywords'));
		$this->meta_description = str_replace(array('[hp]'), array($hp), SiteConfig::getInstance()->getValue('seo_horsepower_hp_meta_description'));		
			
		if ($page > 1) {
			$this->pageTitle .= " page {$page}";
		}	
			
		$this->breadcrumbs = array(
			'/' => 'Home',
			'/horsepower.html' => 'Horsepower',
			'#' => $hp,
		);	
		
		$count = AutoModelYear::getCountItemsByHp($hp);
		
		$limit = 50;
		$offset = $limit*($page-1);
		$modelYears = AutoModelYear::getItemsByHp($hp, $limit, $offset);
		
		if (empty($modelYears)) {
			throw new CHttpException(404,'Page cannot be found.');
		}		
		
		$countPage = (int)($count/$limit);
		if (($count % $limit) != 0) {
			$countPage++;
		}
		
		$hps = AutoCompletion::getHpList();
		$currentHps = array();
		$index = array_search($hp, $hps);
		if (isset($hps[$index-50])) $currentHps[] =  $hps[$index-50];
		if (isset($hps[$index-10])) $currentHps[] =  $hps[$index-10];
		if (isset($hps[$index-1])) $currentHps[] =  $hps[$index-1];
			$currentHps[] =  $hp;
		if (isset($hps[$index+1])) $currentHps[] =  $hps[$index+1];
		if (isset($hps[$index+10])) $currentHps[] =  $hps[$index+10];
		if (isset($hps[$index+50])) $currentHps[] =  $hps[$index+50];	
		
		$this->render('hp', array(
			'hp' => $hp,
			'page' => $page,
			'modelYears' => $modelYears,
			'countPage' => $countPage,
			'currentHps' => $currentHps,
		));	
	}	
	
}