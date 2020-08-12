<?php

class PlatformCategoryController extends BackendController
{
    public function actionIndex()
    {
		Access::is('modelYear.platformCategory', 403);
	
        $model = new PlatformCategory();
		$model->unsetAttributes();
		
        if (isset($_GET['PlatformCategory'])) {
            $model->attributes = $_GET['PlatformCategory'];
        }

        $this->render("index", array(
            'model' => $model,
			'pageSize' => Yii::app()->request->getParam('pageSize', Yii::app()->params->defaultPerPage),
        ));
    }

	public function actionCreate() {
		Access::is('modelYear.platformCategory.create', 403);
	
        $model = new PlatformCategory();

        if (isset($_POST['PlatformCategory'])) {
			$model->attributes = $_POST['PlatformCategory'];
			if ($model->validate() && $model->save()) {
				Yii::app()->admin->setFlash('success', Yii::t('admin', 'Platform Category successfully added'));
				$this->afterSaveRedirect($model);
			}
        }		

        $this->render('create', array(
            'model' => $model,
        ));
    }

    public function actionUpdate($id) {
		Access::is('modelYear.platformCategory.update', 403);
	
        $model = $this->loadModel($id);

        if (isset($_POST['PlatformCategory'])) {
			$model->attributes = $_POST['PlatformCategory'];
	
			if ($model->validate() && $model->save()) {
				Yii::app()->admin->setFlash('success', Yii::t('admin', 'Platform Category successfully edited'));
				$this->afterSaveRedirect($model);
			}
        }

        $this->render('update', array(
            'model' => $model,
        ));
    }
    
	public function actionDelete($id) {
		Access::is('modelYear.platformCategory.delete', 403);
	
        $model = $this->loadModel($id);
        $model->delete();
    }
    /**
     * @param $id integer
     * @return Page
     */
    private function loadModel($id)
	{
		$model = PlatformCategory::model()->findByPk($id);
        
		if (!empty($model))
			return $model;
		else 
			throw new CHttpException(404, 'Page not found');
    }

}