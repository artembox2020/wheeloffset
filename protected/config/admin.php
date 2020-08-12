<?php

// uncomment the following to define a path alias
// Yii::setPathOfAlias('local','path/to/local-folder');

// This is the main Web application configuration. Any writable
// CWebApplication properties can be configured here.
return array(
	'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
	'id'=>'AutoFiles',
	'name'=>'Auto',
	
	// preloading 'log' component
	'preload'=>array(
        'log',
        'bootstrap',
    ),

	// autoloading model and component classes
	'import'=>array(
		'application.models.*',
		'application.components.*',
		'application.components.admin.*',
        'application.extensions.*'
	),

	// application components
	'components'=>array(
	
        'cache'=> require(dirname(__FILE__).'/cache.php'),
	
		'assetManager' => array(
			'class' => 'CAssetManager',
			'forceCopy' => YII_DEBUG,
		),	
        'bootstrap' => array(
            'class' => 'ext.bootstrap.components.Bootstrap',
            'responsiveCss' => true,
        ),		
		'admin'=>array(
			// enable cookie-based authentication
			'allowAutoLogin'=>true,
            'class' => 'WebAdmin',
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
				'admin' => 'admin/index/index',
				'admin/login' => 'admin/index/login',
				'admin/<controller:\w+>/<action:\w+>' => 'admin/<controller>/<action>',
	
				'<controller:\w+>/<id:\d+>'=>'<controller>/view',
				'<controller:\w+>/<action:\w+>/<id:\d+>'=>'<controller>/<action>',
				'<controller:\w+>/<action:\w+>'=>'<controller>/<action>',
			),
		),
		
		'db'=> require(dirname(__FILE__).'/db.php'),

		'errorHandler'=>array(
			// use 'site/error' action to display errors
			'errorAction'=>'site/error',
		),		
		/*
		'log'=>array(
			'class'=>'CLogRouter',
			'routes'=>array(
				array(
					'class' => 'CWebLogRoute',
                    'enabled' =>YII_DEBUG_SHOW_PROFILER,
                    'categories' => 'system.db.*',
				),
			),
		),
		*/
		
		 'log'=>array(
			'class'=>'CLogRouter',
			'routes'=>array(      
			  array(
				'class'=>'CProfileLogRoute',
				'report'=>'summary',
			  ),			  
			),
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