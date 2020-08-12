<?php

class PlatformController extends BackendController
{
    public function actionIndex()
    {
		Access::is('modelYear.platform', 403);
	
        $model = new Platform();
		$model->unsetAttributes();
		
        if (isset($_GET['Platform'])) {
            $model->attributes = $_GET['Platform'];
        }

        $this->render("index", array(
            'model' => $model,
			'pageSize' => Yii::app()->request->getParam('pageSize', Yii::app()->params->defaultPerPage),
        ));
    }

	public function actionCreate() {
		Access::is('modelYear.platform.create', 403);
	
        $model = new Platform();

        if (isset($_POST['Platform'])) {
			$model->attributes = $_POST['Platform'];
			if ($model->validate() && $model->save()) {
				Yii::app()->admin->setFlash('success', Yii::t('admin', 'Platform successfully added'));
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

        if (isset($_POST['Platform'])) {
			$model->attributes = $_POST['Platform'];
	
			if ($model->validate() && $model->save()) {
				Yii::app()->admin->setFlash('success', Yii::t('admin', 'Platform successfully edited'));
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
    /**
     * @param $id integer
     * @return Page
     */
    private function loadModel($id)
	{
		$model = Platform::model()->findByPk($id);
        
		if (!empty($model))
			return $model;
		else 
			throw new CHttpException(404, 'Page not found');
    }
	
	public function actionGetByModel() {
		$id = (int)Yii::app()->getRequest()->getParam('id', 0);
		
		$items = Platform::getListByModel($id);
		echo json_encode(array('items'=>$items));
	}	

}