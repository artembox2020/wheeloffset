<?php

class LugnutsController extends Controller
{
	public function actionIndex()
	{
		$this->pageTitle = SiteConfig::getInstance()->getValue('seo_lug_nuts_title');
		$this->meta_keywords = SiteConfig::getInstance()->getValue('seo_lug_nuts_meta_keywords');
		$this->meta_description = SiteConfig::getInstance()->getValue('seo_lug_nuts_meta_description');
		
		$this->breadcrumbs = array(
			'/' => 'Home',
			'#' => 'Lug Nut Sizes',
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

        $this->pageTitle = str_replace('[make]', $make['title'], SiteConfig::getInstance()->getValue('seo_lug_nuts_make_title'));
        $this->meta_keywords = str_replace('[make]', $make['title'], SiteConfig::getInstance()->getValue('seo_lug_nuts_make_meta_keywords'));
        $this->meta_description = str_replace('[make]', $make['title'], SiteConfig::getInstance()->getValue('seo_lug_nuts_make_meta_description'));
        $header_text_block = str_replace('[make]', $make['title'], SiteConfig::getInstance()->getValue('lug_nuts_make_header_text_block'));

        $this->breadcrumbs = array(
            '/' => 'Home',
            '/lugnuts.html' => 'Lug Nut Sizes',
            '#' => $make['title'],
        );

        $dataModels = AutoMake::getModels($make['id']);


        $this->render('make', array(
            'header_text_block' => $header_text_block,
            'make' => $make,
            'dataModels' => $dataModels,
            'dataModelsLugnuts' => AutoMake::getLugnutsData($make['id']),
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

        $this->pageTitle = str_replace(array('[make]', '[model]'), array($make['title'], $model['title']), SiteConfig::getInstance()->getValue('seo_lug_nuts_model_title'));
        $this->meta_keywords = str_replace(array('[make]', '[model]'), array($make['title'], $model['title']), SiteConfig::getInstance()->getValue('seo_lug_nuts_model_meta_keywords'));
        $this->meta_description = str_replace(array('[make]', '[model]'), array($make['title'], $model['title']), SiteConfig::getInstance()->getValue('seo_lug_nuts_model_meta_description'));
        $header_text_block = str_replace(array('[make]', '[model]'), array($make['title'], $model['title']), SiteConfig::getInstance()->getValue('lug_nuts_model_header_text_block'));

        $this->breadcrumbs = array(
            '/' => 'Home',
            '/lugnuts.html' => 'Lug Nut Sizes',
            '/lugnuts'.$make['url'] => $make['title'] . ' Lug Nuts',
            '#' => $model['title'],
        );

        $modelByYears = AutoModel::getYears($model['id']);
        $lastModelYear = AutoModel::getLastYear($model['id']);
        $lugnutsDataItems = AutoModel::getWheelsDataFull($model['id']);

        $this->render('model', array(
            'lastModelYear' => $lastModelYear,
            'make' => $make,
            'model' => $model,
            'modelByYears' => $modelByYears,
            'header_text_block' => $header_text_block,
            'lugnutsDataItems' => $lugnutsDataItems,
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

        $this->pageTitle = str_replace(array('[make]', '[model]', '[year]'), array($make['title'], $model['title'], $year), SiteConfig::getInstance()->getValue('seo_lug_nuts_model_year_title'));
        $this->meta_keywords = str_replace(array('[make]', '[model]', '[year]'), array($make['title'], $model['title'], $year), SiteConfig::getInstance()->getValue('seo_lug_nuts_model_year_meta_keywords'));
        $this->meta_description = str_replace(array('[make]', '[model]', '[year]'), array($make['title'], $model['title'], $year), SiteConfig::getInstance()->getValue('seo_lug_nuts_model_year_meta_description'));
        $header_text_block = str_replace(array('[make]', '[model]', '[year]'), array($make['title'], $model['title'], $year), SiteConfig::getInstance()->getValue('lug_nuts_model_year_header_text_block'));

        $this->breadcrumbs = array(
            '/' => 'Home',
            '/lugnuts.html' => 'Lug Nut Sizes',
            '/lugnuts'.$make['url'] => $make['title'] . ' Lug Nuts',
            '/lugnuts'. $make['url'] . strtolower($model['title']) => $model['title'],
            '#' => $year,
        );

        echo $this->render('model_year', array(
            'make' => $make,
            'model' => $model,
            'header_text_block' => $header_text_block,
            'wheelFasteners' => AutoModel::getWheelFasteners($model['id'], $year),
            'wheelTighteings' => AutoModel::getWheelTighteings($model['id'], $year),
            'wheelsDataItems' => AutoModel::getWheelsDataFull($model['id'], $year),
        ));
    }
}