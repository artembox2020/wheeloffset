<?php

class ProductEbayCategoryController extends BackendController
{

    public function actionIndex()
    {
        Access::is('productCategory', 403);

        $model = new ProductEbayCategory();
        $model->unsetAttributes();

        if (isset($_GET['ProductEbayCategory'])) {
            $model->attributes = $_GET['ProductEbayCategory'];
        }

        $this->render("index", array(
            'model' => $model,
            'pageSize' => Yii::app()->request->getParam('pageSize', Yii::app()->params->defaultPerPage),
        ));
    }

    public function actionUpdate($id)
    {
        Access::is('productCategory.update', 403);

        $model = $this->loadModel($id);

        if (isset($_POST['ProductEbayCategory'])) {
            $model->attributes = $_POST['ProductEbayCategory'];
           
            if ($model->validate() && $model->save()) {
                Yii::app()->admin->setFlash('success', Yii::t('admin', 'Product Ebay category successfully edited'));
                $this->afterSaveRedirect($model);
            }
        }
       
        $tabModel = new ProductSubType();
		$tabModel->unsetAttributes();
		
		if (isset($_GET['ProductSubType'])) {
            $tabModel->attributes = $_GET['ProductSubType'];
        }
        
        $this->render('update', array(
            'model' => $model,
            'tabModel' => $tabModel,
            'pageSize' => Yii::app()->request->getParam('pageSize', Yii::app()->params->defaultPerPage),
        ));
    }

    /**
     * @param $id integer
     * @return Page
     */
    private function loadModel($id)
    {
        $model = ProductEbayCategory::model()->findByPk($id);

        if (!empty($model))
            return $model;
        else
            throw new CHttpException(404, 'Page not found');
    }
}
