<?php

class SpecsController extends Controller
{
	public function _actionIndex()
	{
		$this->render('index', array(
			
		));
	}
	
	public function action060times()
	{
		$this->pageTitle = SiteConfig::getInstance()->getValue('seo_0_60_times_title');
		$this->meta_keywords = SiteConfig::getInstance()->getValue('seo_0_60_times_meta_keywords');
		$this->meta_description = SiteConfig::getInstance()->getValue('seo_0_60_times_meta_description');	
	
		$makes = AutoMake::getAllFront();
		
		$this->breadcrumbs = array(
			'/' => 'Home',
			'#' => '0-60 times',
		);	
	
		$fastests = AutoCompletion::getFastest(6);
		
		$this->render('0_60_times', array(
			'makes' => $makes,
			'fastests' => $fastests,
		));
	}
	
	public function action060timesMake($alias)
	{
		$make = AutoMake::getMakeByAlias($alias);
		
		if (empty($make)) {
			 throw new CHttpException(404,'Page cannot be found.');
		}	
	
		$this->pageTitle = str_replace('[make]', $make['title'], SiteConfig::getInstance()->getValue('seo_0_60_times_make_title'));
		$this->meta_keywords = str_replace('[make]', $make['title'], SiteConfig::getInstance()->getValue('seo_0_60_times_make_meta_keywords'));
		$this->meta_description = str_replace('[make]', $make['title'], SiteConfig::getInstance()->getValue('seo_0_60_times_make_meta_description'));		
	
	
		$this->breadcrumbs = array(
			'/' => 'Home',
			'/0-60-times.html' => '0-60 times',
			'#' => $make['title'],
		);	
	
		$models = AutoCompletion::getMakeTimes($make['id']);
		
		//d($models);
		
		$this->render('0_60_times_make', array(
			'make' => $make,
			'models' => $models,
			'description' => str_replace('[make]', $make['title'], SiteConfig::getInstance()->getValue('0_60_times_make_description')),
		));
	}
	
	public function action060timesModel($makeAlias, $modelAlias)
	{
		$make = AutoMake::getMakeByAlias($makeAlias);
		if (empty($make)) {
			 throw new CHttpException(404,'Page cannot be found.');
		}	
	
		$model = AutoModel::getModelByMakeAndAlias($make['id'], $modelAlias);

		if (empty($model)) {
			 throw new CHttpException(404,'Page cannot be found.');
		}
		
		$this->pageTitle = str_replace(array('[make]', '[model]'), array($make['title'], $model['title']), SiteConfig::getInstance()->getValue('seo_0_60_times_model_title'));
		$this->meta_keywords = str_replace(array('[make]', '[model]'), array($make['title'], $model['title']), SiteConfig::getInstance()->getValue('seo_0_60_times_model_meta_keywords'));
		$this->meta_description = str_replace(array('[make]', '[model]'), array($make['title'], $model['title']), SiteConfig::getInstance()->getValue('seo_0_60_times_model_meta_description'));		
		
		$lastModelYear = AutoModel::getLastYear($model['id']);
		$models = AutoCompletion::getAccelerationAcrossYears($model['id']);	
		//$competitors = AutoCompletion::getCompetitorsAcceleration($model['id']);
		$competitors = AutoModel::getFrontCompetitors($model['id']);
		usort ($competitors, "cmpArrayTimes");	
		
		$completionsCarsWithSame060Time = AutoCompletion::getCarsWithSame060Time($model['id'], $lastModelYear);		
		//d($competitors);	
		
		$this->breadcrumbs = array(
			'/' => 'Home',
			'/0-60-times.html' => '0-60 times',
			'/0-60-times' . $make['url'] => $make['title'] . ' 0-60 times acceleration',
			'#' => $model['title'],
		);	
		
		$years = AutoModelYear::getAllByModel($model['id']);
		$yearsIds = array();
		$i=0;
		foreach ($years as $k=>$v) {
			$yearsIds[] = $k;
			$i++;
			if ($i==3) {
				break;
			}
		}
		
		$completionsTimes = AutoCompletion::getItemsByYearOrderTime($yearsIds);
		
		foreach ($completionsTimes as $k=>&$v) {
			$v['year'] = $years[$k];
		}
		
		//d($completionsTimes);
		
		//$fastests = AutoCompletion::getFastest(6);
		
        if (isset($_GET['t'])) {
            d($model);
        }
        
		$this->render('0_60_times_model', array(
			'completionsCarsWithSame060Time' => $completionsCarsWithSame060Time,
			'lastModelYear' => $lastModelYear,
			'make' => $make,
			'model' => $model,
			'models' => $models,
			'competitors' => $competitors,
			'completionsTimes' => $completionsTimes,
			'description' => str_replace(array('[make]', '[model]'), array($make['title'], $model['title']), SiteConfig::getInstance()->getValue('0_60_times_model_description')),
			'descriptionFooter' => str_replace(array('[make]', '[model]'), array($make['title'], $model['title']), SiteConfig::getInstance()->getValue('0_60_times_footer_seo_text')),
		));
	}
	
}