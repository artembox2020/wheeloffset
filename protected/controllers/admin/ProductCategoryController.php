<?php

class ProductCategoryController extends BackendController
{

    public function actions()
    {
        return array(
			'toggle' => array(
				'class'=>'bootstrap.actions.TbToggleAction',
				'modelName' => 'ProductCategory',
			),
			'active' => array(
				'class'=>'application.actions.MultipleCheckboxAction',
				'modelName' => 'ProductCategory',
				'attributeName' => 'is_active',
				'accessAlias' => 'productCategory.update',
			),
            'delete' => array(
                'class' => 'application.actions.MultipleDeleteAction',
                'modelName' => 'ProductCategory',
                'accessAlias' => 'productCategory.delete',
            ),
        );
    }

    public function actionIndex()
    {
        Access::is('productCategory', 403);

        $model = new ProductCategory();
        $model->unsetAttributes();

        if (isset($_GET['ProductCategory'])) {
            $model->attributes = $_GET['ProductCategory'];
        }

        $this->render("index", array(
            'model' => $model,
            'pageSize' => Yii::app()->request->getParam('pageSize', Yii::app()->params->defaultPerPage),
        ));
    }

    public function actionCreate()
    {
        Access::is('productCategory.create', 403);

        $model = new ProductCategory();

        if (isset($_POST['ProductCategory'])) {
            $model->attributes = $_POST['ProductCategory'];
            $model->file = CUploadedFile::getInstance($model, 'file');
            if ($model->validate() && $model->save()) {
                Yii::app()->admin->setFlash('success', Yii::t('admin', 'Product category successfully added'));
                $this->afterSaveRedirect($model);
            }
        }

        $this->render('create', array(
            'model' => $model,
        ));
    }

    public function actionUpdate($id)
    {
        Access::is('productCategory.update', 403);

        $model = $this->loadModel($id);

        if (isset($_POST['ProductCategory'])) {
            $model->attributes = $_POST['ProductCategory'];
            $model->file = CUploadedFile::getInstance($model, 'file');

            if ($model->validate() && $model->save()) {
                Yii::app()->admin->setFlash('success', Yii::t('admin', 'Product category successfully edited'));
                $this->afterSaveRedirect($model);
            }
        }

        $tabModel = new AutoModel();
		$tabModel->unsetAttributes();
		$tabModel->is_deleted = 0;
		
		if (isset($_GET['AutoModel'])) {
            $tabModel->attributes = $_GET['AutoModel'];
        }
        
        $tabModelBrand = new ProductBrand();
		$tabModelBrand->unsetAttributes();
		
		if (isset($_GET['ProductBrand'])) {
            $tabModelBrand->attributes = $_GET['ProductBrand'];
        }

        $this->render('update', array(
            'model' => $model,
            'tabModel' => $tabModel,
            'tabModelBrand' => $tabModelBrand,
            'pageSize' => Yii::app()->request->getParam('pageSize', Yii::app()->params->defaultPerPage),
        ));
    }

    /**
     * @param $id integer
     * @return Page
     */
    private function loadModel($id)
    {
        $model = ProductCategory::model()->findByPk($id);

        if (!empty($model))
            return $model;
        else
            throw new CHttpException(404, 'Page not found');
    }
}
