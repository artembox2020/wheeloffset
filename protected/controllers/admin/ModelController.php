<?php

class ModelController extends BackendController
{
	public function actions()
	{
		return array(
			'toggle' => array(
				'class'=>'bootstrap.actions.TbToggleAction',
				'modelName' => 'AutoModel',
			),
			'active' => array(
				'class'=>'application.actions.MultipleCheckboxAction',
				'modelName' => 'AutoModel',
				'attributeName' => 'is_active',
				'accessAlias' => 'model.update',
			),
			'trash' => array(
				'class'=>'application.actions.MultipleCheckboxAction',
				'modelName' => 'AutoModel',
				'attributeName' => 'is_deleted',
				'accessAlias' => 'model.delete',
			),
			'delete' => array(
				'class'=>'application.actions.MultipleDeleteAction',
				'modelName' => 'AutoModel',
				'accessAlias' => 'model.delete',
			),
		);
	}

    public function actionIndex()
    {
		Access::is('model', 403);
	
        $model = new AutoModel();
		$model->unsetAttributes();
		$model->is_deleted = 0;
        
		if (isset($_GET['AutoModel'])) {
            $model->attributes = $_GET['AutoModel'];
        }

        $this->render("index", array(
            'pageSize' => Yii::app()->request->getParam('pageSize', Yii::app()->params->defaultPerPage),
            'model' => $model,
        ));
    }
	
    public function actionBasket()
    {
		Access::is('model.basket', 403);
	
        $model = new AutoModel();
		$model->unsetAttributes();
		$model->is_deleted = 1;
        
		if (isset($_GET['AutoModel'])) {
            $model->attributes = $_GET['AutoModel'];
        }

        $this->render("basket", array(
            'model' => $model,
			'pageSize' => Yii::app()->request->getParam('pageSize', Yii::app()->params->defaultPerPage),
        ));
    }	

	public function actionCreate() {
		Access::is('model.create', 403);
	
        $model = new AutoModel();

        if (isset($_POST['AutoModel'])) {
			$model->attributes = $_POST['AutoModel'];
			$model->file = CUploadedFile::getInstance($model, 'file');
			if ($model->validate() && $model->save()) {
				Yii::app()->admin->setFlash('success', Yii::t('admin', 'Model successfully added'));
				$this->afterSaveRedirect($model);
			}
        }		
		
        $this->render('create', array(
            'model' => $model,
        ));
    }

    public function actionUpdate($id, $category_id=null) {
		Access::is('model.update', 403);
	
        $model = $this->loadModel($id);
        $model->scenario = 'updateAdmin';

        if (isset($_POST['AutoModel'])) {
			$model->attributes = $_POST['AutoModel'];
			$model->file = CUploadedFile::getInstance($model, 'file');

			if ($model->validate() && $model->save()) {
				Yii::app()->admin->setFlash('success', Yii::t('admin', 'Model successfully edited'));
				$this->afterSaveRedirect($model);
			}
        }
        
        $ebayModel = $this->loadEbayAutoModel($id);
        
        $modelProducts = new Product();
        $modelProducts->unsetAttributes();

        if (isset($_GET['Product'])) {
            $model->attributes = $_GET['Product'];
        }
        
        $category = null;
        if ($category_id !== null) {
            $category = $this->loadEbayCategory($category_id);
            $modelProducts->category_id = $category->id;
        }
        if ($ebayModel) {
            $modelProducts->model_id = $ebayModel->id;
        }
        
        $this->render('update', array(
            'ebayModel' => $ebayModel,
            'model' => $model,
            'category' => $category,
            'modelProducts' => $modelProducts,
            'pageSize' => Yii::app()->request->getParam('pageSize', Yii::app()->params->defaultPerPage),
        ));
    }

    public function actionCopy() {
		if (Yii::app()->getRequest()->isPostRequest && Yii::app()->getRequest()->isAjaxRequest) {			
			$response = array();	
			if (Access::is('model.update')) {
		
				$make_id = (int)Yii::app()->request->getParam('make_id');	
				if ($make_id) {				
					$models = AutoModel::model()->findAllByPk(Yii::app()->request->getParam('ids'));
					foreach ($models as $model) {
						$clone = new AutoModel;
						$clone->attributes = $model->attributes;
						$clone->make_id = $make_id;
						$clone->id = null;
						$clone->image_ext = null;
						$clone->alias .= time().rand(1000,9999);
						$clone->save();
					}
					$response['status'] = 1;
				} else {
					$response['status'] = 0;
					$response['error'] = Yii::t('admin', 'Error - Select Make!');					
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
			if (Access::is('model.update')) {
					
				$make_id = (int)Yii::app()->request->getParam('make_id');	
				if ($make_id) {
					$models = AutoModel::model()->findAllByPk(Yii::app()->request->getParam('ids'));
					foreach ($models as $model) {
						$model->make_id = Yii::app()->request->getParam('make_id');
						$model->save();
					}
					$response['status'] = 1;
				} else {
					$response['status'] = 0;
					$response['error'] = Yii::t('admin', 'Error - Select Make!');					
				}
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
		$model = AutoModel::model()->findByPk($id);
        
		if (!empty($model))
			return $model;
		else 
			throw new CHttpException(404, 'Page not found');
    }

    /**
     * @param $id integer
     * @return Page
     */
    private function loadEbayCategory($id)
	{
		$model = ProductEbayCategory::model()->findByPk($id);
        
		if (!empty($model))
			return $model;
		else 
			throw new CHttpException(404, 'Page not found');
    }

    /**
     * @param $id integer
     * @return Page
     */
    private function loadEbayAutoModel($id)
	{
        $criteria = new CDbCriteria;
        $criteria->compare('atk_id', $id);
		$model = EbayAutoModel::model()->find($criteria);
        
		if (!empty($model))
			return $model;
		else 
			return null;
    }

    public function actionGetByMake() {
		$id = (int)Yii::app()->getRequest()->getParam('id', 0);
		
		$items = AutoModel::getAllByMake($id);
		echo json_encode(array('items'=>$items));
	}	

}