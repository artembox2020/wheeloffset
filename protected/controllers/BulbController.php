<?php

class BulbController extends Controller
{
	public function actionIndex()
	{
		$this->pageTitle = SiteConfig::getInstance()->getValue('product_bulb_meta_title');
		$this->meta_keywords = SiteConfig::getInstance()->getValue('product_bulb_meta_keywords');
		$this->meta_description = SiteConfig::getInstance()->getValue('product_bulb_meta_description');		
		
		$this->breadcrumbs = array(
			'/' => 'Home',
			'#' => 'Bulbs',
		);		
		
		$this->render('index', array(
			'header_text' => SiteConfig::getInstance()->getValue('product_bulb_seo_header_text'),
			'footer_text' => SiteConfig::getInstance()->getValue('product_bulb_seo_footer_text'),
		));
	}
	
	public function actionMake($alias)
	{
		$make = AutoMake::getMakeByAlias($alias);
		
		if (empty($make)) {
			 throw new CHttpException(404,'Page cannot be found.');
		}
		
		$this->pageTitle = str_replace('[make]', $make['title'], SiteConfig::getInstance()->getValue('product_bulb_make_meta_title'));
		$this->meta_keywords = str_replace('[make]', $make['title'], SiteConfig::getInstance()->getValue('product_bulb_make_meta_keywords'));
		$this->meta_description = str_replace('[make]', $make['title'], SiteConfig::getInstance()->getValue('product_bulb_make_meta_description'));		
		$header_text = str_replace('[make]', $make['title'], SiteConfig::getInstance()->getValue('product_bulb_make_seo_header_text'));		
		$footer_text = str_replace('[make]', $make['title'], SiteConfig::getInstance()->getValue('product_bulb_make_seo_footer_text'));		
		
		$this->breadcrumbs = array(
			'/' => 'Home',
			'/bulbs.html' => 'Bulbs',
			'#' => $make['title'],
		);
		
		$dataModels = AutoMake::getModels($make['id']);
		
		$this->render('make', array(
			'header_text' => $header_text,
			'footer_text' => $footer_text,
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
		
		if (!$model['is_bulb']) {
			 throw new CHttpException(404,'Page cannot be found.');
		}

		$this->pageTitle = str_replace(array('[make]', '[model]'), array($make['title'], $model['title']), SiteConfig::getInstance()->getValue('product_bulb_make_model_meta_title'));
		$this->meta_keywords = str_replace(array('[make]', '[model]'), array($make['title'], $model['title']), SiteConfig::getInstance()->getValue('product_bulb_make_model_meta_keywords'));
		$this->meta_description = str_replace(array('[make]', '[model]'), array($make['title'], $model['title']), SiteConfig::getInstance()->getValue('product_bulb_make_model_meta_description'));		
		$header_text = str_replace(array('[make]', '[model]'), array($make['title'], $model['title']), SiteConfig::getInstance()->getValue('product_bulb_make_model_seo_header_text'));		
		$footer_text = str_replace(array('[make]', '[model]'), array($make['title'], $model['title']), SiteConfig::getInstance()->getValue('product_bulb_make_model_seo_footer_text'));		
		
		$this->breadcrumbs = array(
			'/' => 'Home',
			'/bulbs.html' => 'Bulbs',
			'/bulbs'.$make['url'] => $make['title'] . ' bulbs',
			'#' => $model['title'],
		);
			
		$modelByYears = AutoModel::getYears($model['id']);
		
		$lastModelYear = AutoModel::getLastYear($model['id']);
        
        
        $yIds = [];
        foreach ($modelByYears as $modelByYear) {
            $yIds[] = $modelByYear['id'];
        }
        
        $bulbItemsByYear = ProductBulbItem::getForwardItemsByYears($yIds);
        
		$this->render('model', array(
			'make' => $make,
			'model' => $model,
			'modelByYears' => $modelByYears,
			'lastModelYear' => $lastModelYear,
			'bulbItemsByYear' => $bulbItemsByYear,
			'header_text' => $header_text,
			'footer_text' => $footer_text,
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
		if (empty($model) || !$model['is_bulb']) {
			throw new CHttpException(404,'Page cannot be found.');
		}	
	
		$modelYear = AutoModelYear::getYearByMakeAndModelAndAlias($make['id'], $model['id'], $year);
		if (empty($modelYear)) {
			throw new CHttpException(404,'Page cannot be found.');
		}		
	
		$bulbs = ProductBulb::getItemsByYear($modelYear['id']);
		
        $header_text_static = SeoText::getText('bulb_model_year_header', $modelYear['id'], [
            '[make]' => $make['title'],
            '[model]' => $model['title'],
            '[year]' => $modelYear['year'],
        ]);

        $this->pageTitle = str_replace(array('[make]', '[model]', '[year]'), array($make['title'], $model['title'], $modelYear['year']), SiteConfig::getInstance()->getValue('product_bulb_make_model_year_meta_title'));
		$this->meta_keywords = str_replace(array('[make]', '[model]', '[year]'), array($make['title'], $model['title'], $modelYear['year']), SiteConfig::getInstance()->getValue('product_bulb_make_model_year_meta_keywords'));
		$this->meta_description = str_replace(array('[make]', '[model]', '[year]'), array($make['title'], $model['title'], $modelYear['year']), SiteConfig::getInstance()->getValue('product_bulb_make_model_year_meta_description'));		
		$footer_text = str_replace(array('[make]', '[model]', '[year]'), array($make['title'], $model['title'], $modelYear['year']), SiteConfig::getInstance()->getValue('product_bulb_make_model_year_seo_footer_text'));		
		$header_text = !empty($header_text_static) ? $header_text_static : 
            str_replace(array('[make]', '[model]', '[year]'), array($make['title'], $model['title'], $modelYear['year']), SiteConfig::getInstance()->getValue('product_bulb_make_model_year_seo_header_text'));		
			
		$this->breadcrumbs = array(
			'/' => 'Home',
			'/bulbs.html' => 'Bulbs',
			'/bulbs'.$make['url'] => array('anchor'=>$make['title'], 'title'=>$make['title'] . ' bulbs'),
			'/bulbs'.$model['url'] => array('anchor'=>$model['title'], 'title'=>$make['title'] . ' ' . $model['title'] . ' bulbs'),
			'#' => $modelYear['year'] . ' ' .$make['title'] . ' ' . $model['title'],
		);	
		
		
		$models = AutoModelYear::getModelsByMakeAndYear($make['id'], $modelYear['year']);
		
        $bIds = [];
        foreach ($bulbs as $bulb) {
            $bIds[] = $bulb['bulb_id'];
        }
        $bulbItems = ProductBulbItem::getItemsByType($bIds);
        
        //d($bulbItems);
        
        $this->render('model_year', array(
			'make' => $make,
			'bulbs' => $bulbs,
			'model' => $model,
			'modelYear' => $modelYear,
			'modelYears' => AutoModel::getYears($model['id']),
			'header_text' => $header_text,
			'footer_text' => $footer_text,			
			'bulbItems' => $bulbItems,			
		));	
	}	
	
	public function actionType($alias)
	{	
		$bulb = ProductBulb::getItemByAlias($alias);
		if (empty($bulb)) {
			throw new CHttpException(404,'Page cannot be found.');
		}		
        
        //TODO
        $bulb['app'] = '';
	
		$this->pageTitle = str_replace(['[part]', '[app]'], [$bulb['part'], $bulb['app']], SiteConfig::getInstance()->getValue('product_bulb_type_meta_title'));
		$this->meta_keywords = str_replace(['[part]', '[app]'], [$bulb['part'], $bulb['app']], SiteConfig::getInstance()->getValue('product_bulb_type_meta_keywords'));
		$this->meta_description = str_replace(['[part]', '[app]'], [$bulb['part'], $bulb['app']], SiteConfig::getInstance()->getValue('product_bulb_type_meta_description'));		
		$header_text = !empty($bulb['seo_header_text']) ? $bulb['seo_header_text'] :
            str_replace(['[part]', '[app]'], [$bulb['part'], $bulb['app']], SiteConfig::getInstance()->getValue('product_bulb_type_seo_header_text'));		
		$footer_text = !empty($bulb['seo_footer_text']) ? $bulb['seo_footer_text'] :
            str_replace(['[part]', '[app]'], [$bulb['part'], $bulb['app']], SiteConfig::getInstance()->getValue('product_bulb_type_seo_footer_text'));		
		
		$this->breadcrumbs = array(
			'/' => 'Home',
			'/bulbs.html' => 'Bulbs',
			'#' => $bulb['part'],
		);	
		
		
		$this->render('type', array(
			'bulb' => $bulb,
			'header_text' => $header_text,
			'footer_text' => $footer_text,			
		));	
	}	
	
	public function actionApiModel($makeAlias, $modelAlias)
	{
		$make = AutoMake::getMakeByAlias($makeAlias);
		if (empty($make)) {
			throw new CHttpException(404,'Page cannot be found.');
		}	
	
		$model = AutoModel::getModelByMakeAndAlias($make['id'], $modelAlias);
		if (empty($model)) {
			throw new CHttpException(404,'Page cannot be found.');
		}	
        
        $data = [];
        
        foreach (AutoModel::getYears($model['id']) as $yearItem) {
            $items = ProductBulb::getItemsByYear($yearItem['id']);
            if (!empty($items)) {
                //$data[$yearItem['year']]['photo'] = 'https://autotk.com' . $yearItem['photo'];
                //$data[$yearItem['year']]['bulbs'] = $items;
                $data[$yearItem['year']] = $items;
            }
        }
        
		echo json_encode($data);	
	}    
    
	public function actionApiMake($makeAlias)
	{
		$make = AutoMake::getMakeByAlias($makeAlias);
		if (empty($make)) {
			throw new CHttpException(404,'Page cannot be found.');
		}	
	
        $sql = "SELECT
                    auto_model.title AS title,
                    auto_model.alias AS alias,
                    (SELECT 
                        COUNT(*) 
                        FROM auto_model_year_bulb AS myb
                        LEFT JOIN auto_model_year AS y ON myb.model_year_id = y.id
                        WHERE y.model_id = auto_model.id
                    ) AS count_bulbs
                FROM auto_model
                WHERE  auto_model.make_id = {$make['id']}
                HAVING count_bulbs > 0";
                
        $data = Yii::app()->db->createCommand($sql)->queryAll();   
        
		echo json_encode($data);	
	}    
}