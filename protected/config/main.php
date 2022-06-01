<?php
function cmpArrayTimes ($a, $b) {
	if ($a['0_60_times']['mmin'] == $b['0_60_times']['mmin']) return 0;
		return ($a['0_60_times']['mmin'] < $b['0_60_times']['mmin']) ? -1 : 1;
}
function cmpCompletionTimes ($a, $b) {
	if ($a['specs_0_60mph__0_100kmh_s_'] == $b['specs_0_60mph__0_100kmh_s_']) return 0;
		return ($a['specs_0_60mph__0_100kmh_s_'] < $b['specs_0_60mph__0_100kmh_s_']) ? -1 : 1;
}

function cmpArrayYears ($a, $b) {
	if ($a['year'] == $b['year']) return 0;
		return ($a['year'] > $b['year']) ? -1 : 1;
}

function cmpArrayCurbWeight ($a, $b) {
	if ($a['specs_curb_weight'] == $b['specs_curb_weight']) return 0;
		return ($a['specs_curb_weight'] < $b['specs_curb_weight']) ? -1 : 1;
}

function cmpArrayFuelEconomyCity ($a, $b) {
	if ($a['specs_fuel_economy__city'] == $b['specs_fuel_economy__city']) return 0;
		return ($a['specs_fuel_economy__city'] < $b['specs_fuel_economy__city']) ? -1 : 1;
}

function emm($data) {
	if (empty($data)) {
		return true;
	} else if (empty($data['mmax']) && empty($data['mmin'])) {
		return true;
	} else {
		return false;
	}
}


function uasort_acs($a, $b) {
	if ($a == $b) return 0;
	return ($a > $b) ? 1 : -11;
}

function conf($key, $params = []) {
    $value = SiteConfig::getInstance()->getValue($key);
    if (!empty($params)) {
        $value = str_replace(array_keys($params), $params, $value);
    }
    
    return $value;
}

// uncomment the following to define a path alias
// Yii::setPathOfAlias('local','path/to/local-folder');

// This is the main Web application configuration. Any writable
// CWebApplication properties can be configured here.
return array(
	'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
	'id'=>'wheeloffset',
	'name'=>'wheeloffset',

	// preloading 'log' component
	'preload'=>array(
        'log',
    ),

	// autoloading model and component classes
	'import'=>array(
		'application.models.*',
		'application.components.*',
        'application.extensions.*'
	),

	// application components
	'components'=>array(

		'cache'=> require(dirname(__FILE__).'/cache.php'),
	
		'assetManager' => array(
			'class' => 'CAssetManager',
			'forceCopy' => YII_DEBUG,
		),	
	
        /*'ckeditor' => array(
            'class' => 'application.extensions.ckeditor.TheCKEditorWidget',
        ),*/
        'iwi' => array(
            'class' => 'application.extensions.iwi.IwiComponent',
            // GD or ImageMagick
            'driver' => 'GD',
            // ImageMagick setup path
            //'params'=>array('directory'=>'C:/ImageMagick'),
        ),
        // uncomment the following to enable URLs in path-format
	
		'urlManager'=>array(
			'urlFormat'=>'path',
			'showScriptName' => false,
			'rules'=>array(	
				//static page view
				'<alias:(about|privacy-policy)>.html' => 'site/page',
			
				'tires.html'=>'tires/index',
				'tires/r<value:[0-9]{2}>.html'=>'tires/diameter',
				'tires/<vehicle_class:[A-Z]{1,3}>-<section_width:[0-9]{3}>-<aspect_ratio:[0-9]{2,3}>r<rim_diameter:[0-9]{2}>.html'=>'tires/size',
				'tires/<makeAlias:[a-z0-9-_]{1,255}>/<vehicle_class:[A-Z]{1,3}>-<section_width:[0-9]{3}>-<aspect_ratio:[0-9]{2,3}>r<rim_diameter:[0-9]{2}>.html'=>'tires/sizeMake',
				'tires/<alias:[a-z0-9-_]{1,255}>' => 'tires/make',
				'tires/<makeAlias:[a-z0-9-_]{1,255}>/<modelAlias:[a-z-0-9_]{1,255}>' => 'tires/model',
				'tires/<makeAlias:[a-z0-9-_]{1,255}>/<modelAlias:[a-z-0-9_]{1,255}>/<year:[0-9]{4}>' => 'tires/modelYear',

				'bulbs.html'=>'bulb/index',
				'bulbs/<alias:[a-z0-9-_]{1,255}>.html' => 'bulb/type',
				'bulbs/<alias:[a-z0-9-_]{1,255}>' => 'bulb/make',
				'bulbs/<makeAlias:[a-z0-9-_]{1,255}>/<modelAlias:[a-z-0-9_]{1,255}>' => 'bulb/model',
				'bulbs/<makeAlias:[a-z0-9-_]{1,255}>/<modelAlias:[a-z-0-9_]{1,255}>/<year:[0-9]{4}>' => 'bulb/modelYear',
				//'api/bulbs/<makeAlias:[a-z0-9-_]{1,255}>/<modelAlias:[a-z-0-9_]{1,255}>/<year:[0-9]{4}>' => 'bulb/apiModelYear',
				'api/bulbs/<makeAlias:[a-z0-9-_]{1,255}>' => 'bulb/apiMake',
				'api/bulbs/<makeAlias:[a-z0-9-_]{1,255}>/<modelAlias:[a-z-0-9_]{1,255}>' => 'bulb/apiModel',

                'lugnuts.html'=>'lugnuts/index',
                'lugnuts/<alias:[a-z0-9-_]{1,255}>' => 'lugnuts/make',
                'lugnuts/<makeAlias:[a-z0-9-_]{1,255}>/<modelAlias:[a-z-0-9_]{1,255}>' => 'lugnuts/model',
                'lugnuts/<makeAlias:[a-z0-9-_]{1,255}>/<modelAlias:[a-z-0-9_]{1,255}>/<year:[0-9]{4}>' => 'lugnuts/modelYear',

                'wheels.html'=>'wheels/index',
				'wheels/bolt-pattern.html'=>'wheels/boltPattern',
				'wheels/<diametr:[0-9]{1,3}>x<width:([0-9]{1,2}.[0-9]{1,2})>/<vehicle_class:[A-Z]{1}>-<section_width:[0-9]{3}>-<aspect_ratio:[0-9]{2,3}>r<rim_diameter:[0-9]{2}>' => 'wheels/diametrWidthTire',
				'wheels/<diametr:[0-9]{1,3}>x<width:([0-9]{1,2}.[0-9]{1,2})>' => 'wheels/diametrWidth',
				'wheels/<alias:[a-z0-9-_]{1,255}>' => 'wheels/make',
				'wheels/<makeAlias:[a-z0-9-_]{1,255}>/<modelAlias:[a-z-0-9_]{1,255}>' => 'wheels/model',
				'wheels/<makeAlias:[a-z0-9-_]{1,255}>/<modelAlias:[a-z-0-9_]{1,255}>/<year:[0-9]{4}>' => 'wheels/modelYear',
			
				'dimensions.html'=>'dimensions/index',
				'dimensions/<alias:[a-z0-9-_]{1,255}>' => 'dimensions/make',
				'dimensions/<makeAlias:[a-z0-9-_]{1,255}>/<modelAlias:[a-z-0-9_]{1,255}>' => 'dimensions/model',
				'dimensions/<makeAlias:[a-z0-9-_]{1,255}>/<modelAlias:[a-z-0-9_]{1,255}>/<year:[0-9]{4}>' => 'dimensions/modelYear',
								
				'horsepower.html'=>'horsepower/index',
				'horsepower/<hp:[0-9]{1,4}>/1' => 'site/404',
				'horsepower/<hp:[0-9]{1,4}>/<page:[0-9]{1,1000}>' => 'horsepower/hp',
				'horsepower/<hp:[0-9]{1,4}>' => 'horsepower/hp',
				'horsepower/<alias:[a-z0-9-_]{1,255}>' => 'horsepower/make',
				'horsepower/<makeAlias:[a-z0-9-_]{1,255}>/<modelAlias:[a-z-0-9_]{1,255}>' => 'horsepower/model',
				'horsepower/<makeAlias:[a-z0-9-_]{1,255}>/<modelAlias:[a-z-0-9_]{1,255}>/<year:[0-9]{4}>' => 'horsepower/modelYear',
					
				'weight'=>'weight/index',
				'weight/<alias:[a-z0-9-_]{1,255}>' => 'weight/make',
				'weight/<makeAlias:[a-z0-9-_]{1,255}>/<modelAlias:[a-z-0-9_]{1,255}>' => 'weight/model',
			
				'mpg'=>'mpg/index',
				'mpg/<alias:[a-z0-9-_]{1,255}>' => 'mpg/make',
				'mpg/<makeAlias:[a-z0-9-_]{1,255}>/<modelAlias:[a-z-0-9_]{1,255}>' => 'mpg/model',
			
				'tuning.html'=>'tuning/index',
				'tuning/<alias:[a-z0-9-_]{1,255}>' => 'tuning/make',
				'tuning/<makeAlias:[a-z0-9-_]{1,255}>/<modelAlias:[a-z-0-9_]{1,255}>/<filter:wheels-[0-9]{2}x[0-9]{1,2}.[0-9]{1,2}\/tire-[0-9]{3}\/offset[-]{0,1}[0-9]{1,2}>' => 'tuning/model',
				'tuning/<makeAlias:[a-z0-9-_]{1,255}>/<modelAlias:[a-z-0-9_]{1,255}>/<filter:wheels-[0-9]{2}>' => 'tuning/model',
				'tuning/<makeAlias:[a-z0-9-_]{1,255}>/<modelAlias:[a-z-0-9_]{1,255}>/<filter:wheels-[0-9]{1,2}.[0-9]{1,2}-width>' => 'tuning/model',
				'tuning/<makeAlias:[a-z0-9-_]{1,255}>/<modelAlias:[a-z-0-9_]{1,255}>/<filter:tire-[0-9]{3}>' => 'tuning/model',
				'tuning/<makeAlias:[a-z0-9-_]{1,255}>/<modelAlias:[a-z-0-9_]{1,255}>/<filter:offset[-]{0,1}[0-9]{1,2}>' => 'tuning/model',
				'tuning/<makeAlias:[a-z0-9-_]{1,255}>/<modelAlias:[a-z-0-9_]{1,255}>/<filter:wheels-[0-9]{2}x[0-9]{1,2}.[0-9]{1,2}>' => 'tuning/model',
				'tuning/<makeAlias:[a-z0-9-_]{1,255}>/<modelAlias:[a-z-0-9_]{1,255}>/<filter:tire-[0-9]{3}\/offset[-]{0,1}[0-9]{1,2}>' => 'tuning/model',
				'tuning/<makeAlias:[a-z0-9-_]{1,255}>/<modelAlias:[a-z-0-9_]{1,255}>/<filter:wheels-[0-9]{2}\/tire-[0-9]{3}>' => 'tuning/model',
				'tuning/<makeAlias:[a-z0-9-_]{1,255}>/<modelAlias:[a-z-0-9_]{1,255}>/<filter:wheels-[0-9]{2}\/offset[-]{0,1}[0-9]{1,2}>' => 'tuning/model',
				'tuning/<makeAlias:[a-z0-9-_]{1,255}>/<modelAlias:[a-z-0-9_]{1,255}>/<filter:wheels-[0-9]{1,2}.[0-9]{1,2}-width\/tire-[0-9]{3}>' => 'tuning/model',
				'tuning/<makeAlias:[a-z0-9-_]{1,255}>/<modelAlias:[a-z-0-9_]{1,255}>/<filter:wheels-[0-9]{1,2}.[0-9]{1,2}-width\/offset[-]{0,1}[0-9]{1,2}>' => 'tuning/model',
				'tuning/<makeAlias:[a-z0-9-_]{1,255}>/<modelAlias:[a-z-0-9_]{1,255}>/<filter:wheels-[0-9]{2}\/tire-[0-9]{3}\/offset[-]{0,1}[0-9]{1,2}>' => 'tuning/model',
				'tuning/<makeAlias:[a-z0-9-_]{1,255}>/<modelAlias:[a-z-0-9_]{1,255}>/<filter:wheels-[0-9]{1,2}.[0-9]{1,2}-width\/tire-[0-9]{3}\/offset[-]{0,1}[0-9]{1,2}>' => 'tuning/model',
				'tuning/<makeAlias:[a-z0-9-_]{1,255}>/<modelAlias:[a-z-0-9_]{1,255}>/<filter:wheels-[0-9]{2}x[0-9]{1,2}.[0-9]{1,2}\/offset[-]{0,1}[0-9]{1,2}>' => 'tuning/model',
				'tuning/<makeAlias:[a-z0-9-_]{1,255}>/<modelAlias:[a-z-0-9_]{1,255}>/<filter:wheels-[0-9]{2}x[0-9]{1,2}.[0-9]{1,2}\/tire-[0-9]{3}>' => 'tuning/model',
				'tuning/<makeAlias:[a-z0-9-_]{1,255}>/<modelAlias:[a-z-0-9_]{1,255}>' => 'tuning/model',
				'tuning/<makeAlias:[a-z0-9-_]{1,255}>/<modelAlias:[a-z-0-9_]{1,255}>/<id:[0-9]{1,11}>' => 'tuning/project',
                
                //guides				
                'guides/page-<page:[0-9]{1,1000}>' => 'parts/guides',
                'guides' => 'parts/guides',
				'<categoryAlias:[a-z0-9-_]{1,255}>-parts/guides/<page:[0-9]{1,1000}>' => 'parts/categoryGuides',    
				'<categoryAlias:[a-z0-9-_]{1,255}>-parts/guides' => 'parts/categoryGuides',    
				'<categoryAlias:[a-z0-9-_]{1,255}>-parts/guides/<guideAlias:[a-z0-9-_]{1,255}>' => 'parts/categoryGuidesItem',    
				'<categoryAlias:[a-z0-9-_]{1,255}>-parts/<childAlias:[a-z0-9-_]{1,255}>/guides/<page:[0-9]{1,1000}>' => 'parts/categoryChildGuides',    
				'<categoryAlias:[a-z0-9-_]{1,255}>-parts/<childAlias:[a-z0-9-_]{1,255}>/guides' => 'parts/categoryChildGuides',    
				'<categoryAlias:[a-z0-9-_]{1,255}>-parts/<childAlias:[a-z0-9-_]{1,255}>/guides/<guideAlias:[a-z0-9-_]{1,255}>' => 'parts/categoryChildGuidesItem',    

                
                // parts
                'parts'=>'parts/index', 
				'parts/make-<alias:[a-z0-9-_]{1,255}>' => 'parts/make',
				'parts/make-<makeAlias:[a-z0-9-_]{1,255}>/<modelAlias:[a-z-0-9_]{1,255}>' => 'parts/model',

                // category model
                //'<categoryAlias:[a-z0-9-_]{1,255}>-parts/<childAlias:[a-z0-9-_]{1,255}>/<subAlias:[a-z-0-9_]{1,255}>/make-<makeAlias:[a-z-0-9_]{1,255}>/<modelAlias:[a-z-0-9_]{1,255}>' => 'parts/categoryChildSubModel',    
				'<categoryAlias:[a-z0-9-_]{1,255}>-parts/<childAlias:[a-z0-9-_]{1,255}>/make-<makeAlias:[a-z-0-9_]{1,255}>/<modelAlias:[a-z-0-9_]{1,255}>' => 'parts/categoryChildModel',    
				'<categoryAlias:[a-z0-9-_]{1,255}>-parts/make-<makeAlias:[a-z0-9-_]{1,255}>/<modelAlias:[a-z-0-9_]{1,255}>' => 'parts/categoryModel',

                // category make
                //'<categoryAlias:[a-z0-9-_]{1,255}>-parts/<childAlias:[a-z0-9-_]{1,255}>/<subAlias:[a-z-0-9_]{1,255}>/make-<makeAlias:[a-z-0-9_]{1,255}>' => 'parts/categoryChildSubMake',    
				'<categoryAlias:[a-z0-9-_]{1,255}>-parts/<childAlias:[a-z0-9-_]{1,255}>/make-<makeAlias:[a-z-0-9_]{1,255}>' => 'parts/categoryChildMake',    
				'<categoryAlias:[a-z0-9-_]{1,255}>-parts/make-<makeAlias:[a-z0-9-_]{1,255}>' => 'parts/categoryMake',
	
                // simple category
				//'<categoryAlias:[a-z0-9-_]{1,255}>-parts/<childAlias:[a-z0-9-_]{1,255}>/<subAlias:[a-z-0-9_]{1,255}>' => 'parts/categoryChildSub',    
				'<categoryAlias:[a-z0-9-_]{1,255}>-parts/<childAlias:[a-z0-9-_]{1,255}>' => 'parts/categoryChild',    
				'<categoryAlias:[a-z0-9-_]{1,255}>-parts' => 'parts/category', 
            	
				'site/<action:\w+>'=>'site/<action>',
				'ajax/<action:\w+>'=>'ajax/<action>',
				'0-60-times.html'=>'specs/060times',
				'0-60-times/<alias:[a-z0-9-_]{1,255}>'=>'specs/060timesMake',
				'0-60-times/<makeAlias:[a-z0-9-_]{1,255}>/<modelAlias:[a-z-0-9_]{1,255}>'=>'specs/060timesModel',
				'<makeAlias:[a-z0-9-_]{1,255}>/<modelAlias:[a-z-0-9_]{1,255}>/<year:[0-9]{4}>' => 'site/modelYear',
				'<makeAlias:[a-z0-9-_]{1,255}>/<modelAlias:[a-z-0-9_]{1,255}>/<year:[0-9]{4}>/photos.html' => 'site/modelYearPhotos',
				'<makeAlias:[a-z0-9-_]{1,255}>/<modelAlias:[a-z-0-9_]{1,255}>' => 'site/model',
				'<alias:[a-z0-9-_]{1,255}>' => 'site/make',
			),
		),
	
		
		'db'=> require(dirname(__FILE__).'/db.php'),
		
		'errorHandler'=>array(
			// use 'site/error' action to display errors
			'errorAction'=>'site/error',
		),
		'file' => array(
            'class'=>'application.extensions.file.CFile',
        ),		
	),

    'language' => 'en',

	// application-level parameters that can be accessed
	// using Yii::app()->params['paramName']
	'params'=>array(
		// this is used in contact page
		'adminEmail'=>'webmaster@example.com',
		'defaultPerPage'=>50,
		'perPages'=>array(10=>10,20=>20,50=>50,100=>100,250=>250),
	), 
	'defaultController' => 'site/index'
);