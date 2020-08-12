<?php

class MakeController extends BackendController
{

	public function actions()
	{
		return array(
			'toggle' => array(
				'class'=>'bootstrap.actions.TbToggleAction',
				'modelName' => 'AutoMake',
			),
			'active' => array(
				'class'=>'application.actions.MultipleCheckboxAction',
				'modelName' => 'AutoMake',
				'attributeName' => 'is_active',
				'accessAlias' => 'make.update',
			),
			'trash' => array(
				'class'=>'application.actions.MultipleCheckboxAction',
				'modelName' => 'AutoMake',
				'attributeName' => 'is_deleted',
				'accessAlias' => 'make.delete',
			),
			'delete' => array(
				'class'=>'application.actions.MultipleDeleteAction',
				'modelName' => 'AutoMake',
				'accessAlias' => 'make.delete',
			),
		);
	}


    public function actionIndex()
    {
		Access::is('make', 403);
	
        $model = new AutoMake();
		$model->unsetAttributes();
		
        $model->is_deleted = 0;
        if (isset($_GET['AutoMake'])) {
            $model->attributes = $_GET['AutoMake'];
        }

        $this->render("index", array(
            'model' => $model,
			'pageSize' => Yii::app()->request->getParam('pageSize', Yii::app()->params->defaultPerPage),
        ));
    }

    public function actionBasket()
    {
		Access::is('make.basket', 403);
	
        $model = new AutoMake();
		$model->unsetAttributes();
		
        $model->is_deleted = 1;
        if (isset($_GET['AutoMake'])) {
            $model->attributes = $_GET['AutoMake'];
        }

        $this->render("basket", array(
            'model' => $model,
			'pageSize' => Yii::app()->request->getParam('pageSize', Yii::app()->params->defaultPerPage),
        ));
    }

	public function actionCreate() {
		Access::is('make.create', 403);
	
        $model = new AutoMake();

        if (isset($_POST['AutoMake'])) {
			$model->attributes = $_POST['AutoMake'];
			$model->file = CUploadedFile::getInstance($model, 'file');
			if ($model->validate() && $model->save()) {
				Yii::app()->admin->setFlash('success', Yii::t('admin', 'Make successfully added'));
				$this->afterSaveRedirect($model);
			}
        }		

        $this->render('create', array(
            'model' => $model,
        ));
    }

    public function actionUpdate($id) {
		Access::is('make.update', 403);
	
        $model = $this->loadModel($id);

        if (isset($_POST['AutoMake'])) {
			$model->attributes = $_POST['AutoMake'];
			$model->file = CUploadedFile::getInstance($model, 'file');

			if ($model->validate() && $model->save()) {
				Yii::app()->admin->setFlash('success', Yii::t('admin', 'Make successfully edited'));
				$this->afterSaveRedirect($model);
			}
        }

        $this->render('update', array(
            'model' => $model,
        ));
    }

    public function actionCopy() {
		if (Yii::app()->getRequest()->isPostRequest && Yii::app()->getRequest()->isAjaxRequest) {			
			$response = array();	
			if (Access::is('make.update')) {
		
				$models = AutoMake::model()->findAllByPk(Yii::app()->request->getParam('ids'));
				foreach ($models as $model) {
					$clone = new AutoMake;
					$clone->attributes = $model->attributes;
					$clone->id = null;
					$clone->image_ext = null;
					$clone->alias .= time().rand(1000,9999);
					$clone->save();
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


    /**
     * @param $id integer
     * @return Page
     */
    private function loadModel($id)
	{
		$model = AutoMake::model()->findByPk($id);
        
		if (!empty($model))
			return $model;
		else 
			throw new CHttpException(404, 'Page not found');
    }
	
	
}