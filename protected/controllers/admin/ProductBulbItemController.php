<?php

class ProductBulbItemController extends BackendController
{

    public function actions()
    {
        return array(
            'delete' => array(
                'class' => 'application.actions.MultipleDeleteAction',
                'modelName' => 'ProductBulbItem',
                'accessAlias' => 'productBulbItem.delete',
            ),
        );
    }

    public function actionIndex()
    {
        Access::is('productBulbItem', 403);

        $model = new ProductBulbItem();
        $model->unsetAttributes();

        if (isset($_GET['ProductBulbItem'])) {
            $model->attributes = $_GET['ProductBulbItem'];
        }

        $this->render("index", array(
            'model' => $model,
            'pageSize' => Yii::app()->request->getParam('pageSize', Yii::app()->params->defaultPerPage),
        ));
    }

    public function actionCreate()
    {
        Access::is('productBulbItem.create', 403);

        $model = new ProductBulbItem();

        if (isset($_POST['ProductBulbItem'])) {
            $model->attributes = $_POST['ProductBulbItem'];
            $model->file = CUploadedFile::getInstance($model, 'file');
            if ($model->validate() && $model->save()) {
                Yii::app()->admin->setFlash('success', Yii::t('admin', 'Bulb Product successfully added'));
                $this->afterSaveRedirect($model);
            }
        }

        $this->render('create', array(
            'model' => $model,
        ));
    }

    public function actionUpdate($id)
    {
        Access::is('productBulbItem.update', 403);

        $model = $this->loadModel($id);

        if (isset($_POST['ProductBulbItem'])) {
            $model->attributes = $_POST['ProductBulbItem'];
            $model->file = CUploadedFile::getInstance($model, 'file');

            if ($model->validate() && $model->save()) {
                Yii::app()->admin->setFlash('success', Yii::t('admin', 'Bulb Product successfully edited'));
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
        $model = ProductBulbItem::model()->findByPk($id);

        if (!empty($model))
            return $model;
        else
            throw new CHttpException(404, 'Page not found');
    }
}
