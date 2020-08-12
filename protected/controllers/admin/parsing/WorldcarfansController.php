<?php

class WorldcarfansController extends BackendController
{

	public function actions()
	{
		return array(
			'deleteAll' => array(
				'class'=>'application.actions.MultipleDeleteAction',
				'modelName' => 'ParsingWorldcarfansAlbum',
				'accessAlias' => 'parsing',
			),
		);
	}

    public function actionIndex()
    {
		Access::is('parsing', 403);
	
        $model = new ParsingWorldcarfansAlbum();
		$model->unsetAttributes();
		
        if (isset($_GET['ParsingWorldcarfansAlbum'])) {
            $model->attributes = $_GET['ParsingWorldcarfansAlbum'];
        }

        $this->render("index", array(
            'model' => $model,
			'pageSize' => Yii::app()->request->getParam('pageSize', Yii::app()->params->defaultPerPage),
        ));
    }
	
    public function actionMove() {
		if (Yii::app()->getRequest()->isPostRequest && Yii::app()->getRequest()->isAjaxRequest) {			
			$response = array();	
			if (Access::is('parsing')) {
					
				$model_year_id = (int)Yii::app()->request->getParam('model_year_id');	
				$models = ParsingWorldcarfansAlbum::model()->findAllByPk(Yii::app()->request->getParam('ids'));
				foreach ($models as $model) {
					$model->model_year_id = $model_year_id;
					$model->save();
				}
				$response['status'] = 1;
			} else {
				$response['status'] = 0;
				$response['error'] = Yii::t('admin', 'Have No Rights');			
			}
			
			echo json_encode($response);
				
		} else
			throw new CHttpException(Yii::t('admin', 'Invalid request'));
    }

    public function actionDelete($id) 
	{
		Access::is('parsing', 403);
	
		$model = ParsingWorldcarfansAlbum::model()->findByPk($id);
		if (!empty($model))
			$model->delete();
	}	
	
    public function actionModelYear() 
	{
		Access::is('parsing', 403);
		ParsingWorldcarfansAlbum::moveToModelYear();
		Yii::app()->admin->setFlash('success', Yii::t('admin', 'Photos successfully moved'));
		$this->redirect(array('index'));		
	}	
	
	
	
	
	
}