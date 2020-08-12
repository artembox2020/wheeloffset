<?php

class DimensionsController extends Controller
{
	public function actionIndex()
	{
		$this->pageTitle = SiteConfig::getInstance()->getValue('seo_dimensions_title');
		$this->meta_keywords = SiteConfig::getInstance()->getValue('seo_dimensions_meta_keywords');
		$this->meta_description = SiteConfig::getInstance()->getValue('seo_dimensions_meta_description');		
		
		$this->breadcrumbs = array(
			'/' => 'Home',
			'#' => 'Dimensions',
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
		
		$this->pageTitle = str_replace('[make]', $make['title'], SiteConfig::getInstance()->getValue('seo_dimensions_make_title'));
		$this->meta_keywords = str_replace('[make]', $make['title'], SiteConfig::getInstance()->getValue('seo_dimensions_make_meta_keywords'));
		$this->meta_description = str_replace('[make]', $make['title'], SiteConfig::getInstance()->getValue('seo_dimensions_make_meta_description'));		
		$header_text_block = str_replace('[make]', $make['title'], SiteConfig::getInstance()->getValue('dimensions_make_header_text_block'));		
		
		$this->breadcrumbs = array(
			'/' => 'Home',
			'/dimensions.html' => 'Dimensions',
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
		
		$this->pageTitle = str_replace(array('[make]', '[model]'), array($make['title'], $model['title']), SiteConfig::getInstance()->getValue('seo_dimensions_model_title'));
		$this->meta_keywords = str_replace(array('[make]', '[model]'), array($make['title'], $model['title']), SiteConfig::getInstance()->getValue('seo_dimensions_model_meta_keywords'));
		$this->meta_description = str_replace(array('[make]', '[model]'), array($make['title'], $model['title']), SiteConfig::getInstance()->getValue('seo_dimensions_model_meta_description'));		
		$header_text_block = str_replace(array('[make]', '[model]'), array($make['title'], $model['title']), SiteConfig::getInstance()->getValue('dimensions_model_header_text_block'));		
			
		$this->breadcrumbs = array(
			'/' => 'Home',
			'/dimensions.html' => 'Dimensions',
			'/dimensions'.$make['url'] => $make['title'] . ' dimensions',
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
	
		$this->pageTitle = str_replace(array('[make]', '[model]', '[year]'), array($make['title'], $model['title'], $modelYear['year']), SiteConfig::getInstance()->getValue('seo_dimensions_model_year_title'));
		$this->meta_keywords = str_replace(array('[make]', '[model]', '[year]'), array($make['title'], $model['title'], $modelYear['year']), SiteConfig::getInstance()->getValue('seo_dimensions_model_year_meta_keywords'));
		$this->meta_description = str_replace(array('[make]', '[model]', '[year]'), array($make['title'], $model['title'], $modelYear['year']), SiteConfig::getInstance()->getValue('seo_dimensions_model_year_meta_description'));		
		$header_text_block = str_replace(array('[make]', '[model]', '[year]'), array($make['title'], $model['title'], $modelYear['year']), SiteConfig::getInstance()->getValue('dimensions_model_year_header_text_block'));		
		$content_text_1 = str_replace(array('[make]', '[model]', '[year]'), array($make['title'], $model['title'], $modelYear['year']), SiteConfig::getInstance()->getValue('dimensions_model_year_content_text_1'));		
		$content_text_2 = str_replace(array('[make]', '[model]', '[year]'), array($make['title'], $model['title'], $modelYear['year']), SiteConfig::getInstance()->getValue('dimensions_model_year_content_text_2'));		
			
		$this->breadcrumbs = array(
			'/' => 'Home',
			'/dimensions.html' => 'Dimensions',
			'/dimensions'.$make['url'] => array('anchor'=>$make['title'], 'title'=>$make['title'] . ' dimensions'),
			'/dimensions'.$model['url'] => array('anchor'=>$model['title'], 'title'=>$make['title'] . ' ' . $model['title'] . ' dimensions'),
			'#' => $modelYear['year'] . ' ' .$make['title'] . ' ' . $model['title'],
		);	
		
		$completions = AutoModelYear::getFrontCompetitorsHp($modelYear['id']);
		
		$models = AutoModelYear::getModelsByMakeAndYear($make['id'], $modelYear['year']);
			
		$modelYear['curb_weight'] = AutoModelYear::getMinMaxSpecs('curb_weight', $modelYear['id']);	
			
		$this->render('model_year', array(
			'completions' => $completions,
			'make' => $make,
			'model' => $model,
			'modelYear' => $modelYear,
			'modelYears' => AutoModel::getYears($model['id']),
			'competitors' => AutoModelYear::getFrontCompetitors($modelYear['id']),
			'otherModels' => AutoModelYear::getOtherMakeYear($models, $modelYear['id']),
			'header_text_block' => $header_text_block,
			'content_text_1' => $content_text_1,
			'content_text_2' => $content_text_2,
			
		));	
	}	

	
}