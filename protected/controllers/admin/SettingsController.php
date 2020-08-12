<?php

class SettingsController extends BackendController
{
    public function actionIndex()
    {
		Access::is('settings', 403);
		
		$config = Yii::app()->getRequest()->getParam('Config', null);
		
		if (!empty($config)) {
			SiteConfigModel::model()->saveData($config);
			Yii::app()->admin->setFlash('success', Yii::t('admin', 'Successfully saved'));
        }

        $this->render("index", array(
            'values' => SiteConfig::getInstance()->getData(),
	    ));
    }

}