<?php

class CompletionController extends BackendController
{
	public function actions()
	{
		return array(
			'toggle' => array(
				'class'=>'bootstrap.actions.TbToggleAction',
				'modelName' => 'AutoCompletion',
			),
			'active' => array(
				'class'=>'application.actions.MultipleCheckboxAction',
				'modelName' => 'AutoCompletion',
				'attributeName' => 'is_active',
				'accessAlias' => 'completion.update',
			),
			'trash' => array(
				'class'=>'application.actions.MultipleCheckboxAction',
				'modelName' => 'AutoCompletion',
				'attributeName' => 'is_deleted',
				'accessAlias' => 'completion.delete',
			),
			'delete' => array(
				'class'=>'application.actions.MultipleDeleteAction',
				'modelName' => 'AutoCompletion',
				'accessAlias' => 'completion.delete',
			),
		);
	}
	
    public function actionIndex()
    {
		Access::is('completion', 403);
	
        $model = new AutoCompletion();
		$model->unsetAttributes();
		$model->is_deleted = 0;			
		
        if (isset($_GET['AutoCompletion'])) {
            $model->attributes = $_GET['AutoCompletion'];
        }

        $this->render("index", array(
            'model' => $model,
			'pageSize' => Yii::app()->request->getParam('pageSize', Yii::app()->params->defaultPerPage),
        ));
    }
	
    public function actionBasket()
    {
		Access::is('completion.basket', 403);
	
        $model = new AutoCompletion();
		$model->unsetAttributes();
		$model->is_deleted = 1;
        
		if (isset($_GET['AutoCompletion'])) {
            $model->attributes = $_GET['AutoCompletion'];
        }

        $this->render("basket", array(
            'model' => $model,
			'pageSize' => Yii::app()->request->getParam('pageSize', Yii::app()->params->defaultPerPage),
        ));
    }
	
	public function actionCreate() {
		Access::is('completion.create', 403);
	
        $model = new AutoCompletion();

        if (isset($_POST['AutoCompletion'])) {
			$model->attributes = $_POST['AutoCompletion'];
			$model->file = CUploadedFile::getInstance($model, 'file');

			if ($model->validate() && $model->save()) {
				Yii::app()->admin->setFlash('success', Yii::t('admin', 'Completion successfully added'));
				$this->afterSaveRedirect($model);
			}
        }		

        $this->render('create', array(
            'model' => $model,
        ));
    }

    public function actionUpdate($id) {
		Access::is('completion.update', 403);
	
        $model = $this->loadModelYear($id);

        if (isset($_POST['AutoCompletion'])) {
			$model->attributes = $_POST['AutoCompletion'];
			$model->file = CUploadedFile::getInstance($model, 'file');

			if ($model->validate() && $model->save()) {
				Yii::app()->admin->setFlash('success', Yii::t('admin', 'Completion successfully edited'));
				$this->afterSaveRedirect($model);
			} else {
				//d($model->errors);
			}
        }

        $this->render('update', array(
            'model' => $model,
        ));
    }


    /**
     * @param $id integer
     * @return Page
     */
    private function loadModelYear($id)
	{
		$model = AutoCompletion::model()->findByPk($id);
        
		if (!empty($model))
			return $model;
		else 
			throw new CHttpException(404, 'Page not found');
    }

    public function actionCopy() {
		if (Yii::app()->getRequest()->isPostRequest && Yii::app()->getRequest()->isAjaxRequest) {			
			$response = array();	
			if (Access::is('completion.update')) {
		
				$model_year_id = (int)Yii::app()->request->getParam('model_year_id');	
				if ($model_year_id) {				
					$models = AutoCompletion::model()->findAllByPk(Yii::app()->request->getParam('ids'));
					foreach ($models as $model) {
						$clone = new AutoCompletion;
						$clone->attributes = $model->attributes;
						$clone->model_year_id = $model_year_id;
						$clone->id = null;
						$clone->save();
					}
					$response['status'] = 1;
				} else {
					$response['status'] = 0;
					$response['error'] = Yii::t('admin', 'Error - Select Year!');					
				}
			} else {
				$response['status'] = 0;
				$response['error'] = Yii::t('admin', 'Have No Rights');			
			}
			
			echo json_encode($response);
				
		} else
			throw new CHttpException(Yii::t('admin', 'Invalid request'));
    }	

    public function actionMove() {
		if (Yii::app()->getRequest()->isPostRequest && Yii::app()->getRequest()->isAjaxRequest) {			
			$response = array();	
			if (Access::is('completion.update')) {
					
				$model_year_id = (int)Yii::app()->request->getParam('model_year_id');	
				if ($model_year_id) {
					$models = AutoCompletion::model()->findAllByPk(Yii::app()->request->getParam('ids'));
					foreach ($models as $model) {
						$model->model_year_id = $model_year_id;
						$model->save();
					}
					$response['status'] = 1;
				} else {
					$response['status'] = 0;
					$response['error'] = Yii::t('admin', 'Error - Select Year!');					
				}
			} else {
				$response['status'] = 0;
				$response['error'] = Yii::t('admin', 'Have No Rights');			
			}
			
			echo json_encode($response);
				
		} else
			throw new CHttpException(Yii::t('admin', 'Invalid request'));
    }	
	
	
}