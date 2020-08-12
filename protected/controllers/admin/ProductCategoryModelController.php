<?php

class ProductCategoryModelController extends BackendController
{

    public function actions()
    {
        return array(
			'toggle' => array(
				'class'=>'bootstrap.actions.TbToggleAction',
				'modelName' => 'ProductCategoryModel',
			),
			'active' => array(
				'class'=>'application.actions.MultipleCheckboxAction',
				'modelName' => 'ProductCategoryModel',
				'attributeName' => 'is_active',
				'accessAlias' => 'productCategoryModel.update',
			),
            'delete' => array(
                'class' => 'application.actions.MultipleDeleteAction',
                'modelName' => 'ProductCategoryModel',
                'accessAlias' => 'productCategoryModel.delete',
            ),
        );
    }

    public function actionIndex()
    {
        Access::is('productCategoryModel', 403);

        $model = new ProductCategoryModel();
        $model->unsetAttributes();

        if (isset($_GET['ProductCategoryModel'])) {
            $model->attributes = $_GET['ProductCategoryModel'];
        }

        $this->render("index", array(
            'model' => $model,
            'pageSize' => Yii::app()->request->getParam('pageSize', Yii::app()->params->defaultPerPage),
        ));
    }

    public function actionCreate()
    {
        Access::is('productCategoryModel.create', 403);

        $model = new ProductCategoryModel();

        if (isset($_POST['ProductCategoryModel'])) {
            $model->attributes = $_POST['ProductCategoryModel'];
            if ($model->validate() && $model->save()) {
                Yii::app()->admin->setFlash('success', Yii::t('admin', 'Product category model successfully added'));
                $this->afterSaveRedirect($model);
            }
        }

        $this->render('create', array(
            'model' => $model,
        ));
    }

    public function actionUpdate($id)
    {
        Access::is('productCategoryModel.update', 403);

        $model = $this->loadModel($id);

        if (isset($_POST['ProductCategoryModel'])) {
            $model->attributes = $_POST['ProductCategoryModel'];
 
            if ($model->validate() && $model->save()) {
                Yii::app()->admin->setFlash('success', Yii::t('admin', 'Product category model successfully edited'));
                $this->afterSaveRedirect($model);
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
    private function loadModel($id)
    {
        $model = ProductCategoryModel::model()->findByPk($id);

        if (!empty($model))
            return $model;
        else
            throw new CHttpException(404, 'Page not found');
    }
}
