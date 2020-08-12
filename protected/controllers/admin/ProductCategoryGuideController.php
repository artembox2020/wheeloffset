<?php

class ProductCategoryGuideController extends BackendController
{

    public function actions()
    {
        return array(
			'toggle' => array(
				'class'=>'bootstrap.actions.TbToggleAction',
				'modelName' => 'ProductCategoryGuide',
			),
			'active' => array(
				'class'=>'application.actions.MultipleCheckboxAction',
				'modelName' => 'ProductCategoryGuide',
				'attributeName' => 'is_active',
				'accessAlias' => 'productCategoryGuide.update',
			),
            'delete' => array(
                'class' => 'application.actions.MultipleDeleteAction',
                'modelName' => 'ProductCategoryGuide',
                'accessAlias' => 'productCategoryGuide.delete',
            ),
        );
    }

    public function actionIndex()
    {
        Access::is('productCategoryGuide', 403);

        $model = new ProductCategoryGuide();
        $model->unsetAttributes();

        if (isset($_GET['ProductCategoryGuide'])) {
            $model->attributes = $_GET['ProductCategoryGuide'];
        }

        $this->render("index", array(
            'model' => $model,
            'pageSize' => Yii::app()->request->getParam('pageSize', Yii::app()->params->defaultPerPage),
        ));
    }

    public function actionCreate()
    {
        Access::is('productCategoryGuide.create', 403);

        $model = new ProductCategoryGuide();

        if (isset($_POST['ProductCategoryGuide'])) {
            $model->attributes = $_POST['ProductCategoryGuide'];
            $model->file = CUploadedFile::getInstance($model, 'file');
            if ($model->validate() && $model->save()) {
                Yii::app()->admin->setFlash('success', Yii::t('admin', 'Product category guide successfully added'));
                $this->afterSaveRedirect($model);
            }
        }

        $this->render('create', array(
            'model' => $model,
        ));
    }

    public function actionUpdate($id)
    {
        Access::is('productCategoryGuide.update', 403);

        $model = $this->loadModel($id);

        if (isset($_POST['ProductCategoryGuide'])) {
            $model->attributes = $_POST['ProductCategoryGuide'];
            $model->file = CUploadedFile::getInstance($model, 'file');
 
            if ($model->validate() && $model->save()) {
                Yii::app()->admin->setFlash('success', Yii::t('admin', 'Product category guide successfully edited'));
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
        $model = ProductCategoryGuide::model()->findByPk($id);

        if (!empty($model))
            return $model;
        else
            throw new CHttpException(404, 'Page not found');
    }
}
