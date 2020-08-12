<?php

class PlatformModelController extends BackendController
{
    public function actionIndex()
    {
		Access::is('modelYear.platform', 403);
	
        $model = new PlatformModel();
		$model->unsetAttributes();
		
        if (isset($_GET['PlatformModel'])) {
            $model->attributes = $_GET['PlatformModel'];
        }

        $this->render("index", array(
            'model' => $model,
			'pageSize' => Yii::app()->request->getParam('pageSize', Yii::app()->params->defaultPerPage),
        ));
    }

	public function actionCreate() {
		Access::is('modelYear.platform.create', 403);
	
        $model = new PlatformModel();

        if (isset($_POST['PlatformModel'])) {
			$model->attributes = $_POST['PlatformModel'];
			if ($model->validate() && $model->save()) {
				Yii::app()->admin->setFlash('success', Yii::t('admin', 'Platform Model successfully added'));
				$this->afterSaveRedirect($model);
			}
        }		

        $this->render('create', array(
            'model' => $model,
        ));
    }

    public function actionUpdate($id) {
		Access::is('modelYear.platform.update', 403);
	
        $model = $this->loadModel($id);

        if (isset($_POST['PlatformModel'])) {
			$model->attributes = $_POST['PlatformModel'];
	
			if ($model->validate() && $model->save()) {
				Yii::app()->admin->setFlash('success', Yii::t('admin', 'Platform Model successfully edited'));
				$this->afterSaveRedirect($model);
			}
        }

        $this->render('update', array(
            'model' => $model,
        ));
    }
    
	public function actionDelete($id) {
		Access::is('modelYear.platform.delete', 403);
	
        $model = $this->loadModel($id);
        $model->delete();
    }
	
	public function actionGetByModel() {
		$id = Yii::app()->request->getParam('id');
		$response['items'] = PlatformModel::getListByModel($id);
		
		echo json_encode($response);
	}
    
	
	/**
     * @param $id integer
     * @return Page
     */
    private function loadModel($id)
	{
		$model = PlatformModel::model()->findByPk($id);
        
		if (!empty($model))
			return $model;
		else 
			throw new CHttpException(404, 'Page not found');
    }

}