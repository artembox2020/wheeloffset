<?php

class ProductBrandController extends BackendController
{

    public function actionIndex()
    {
        Access::is('productCategory', 403);

        $model = new ProductBrand();
        $model->unsetAttributes();

        if (isset($_GET['ProductBrand'])) {
            $model->attributes = $_GET['ProductBrand'];
        }

        $this->render("index", array(
            'model' => $model,
            'pageSize' => Yii::app()->request->getParam('pageSize', Yii::app()->params->defaultPerPage),
        ));
    }

    public function actionCategories($brandId)
    {
        Access::is('productCategory', 403);

        $brand = $this->loadModel($brandId);

        $model = new ProductBrand();
        $model->unsetAttributes();

        if (isset($_GET['ProductBrand'])) {
            $model->attributes = $_GET['ProductBrand'];
        }

        $this->render('categories', array(
            'model' => $model,
            'brand' => $brand,
            'pageSize' => Yii::app()->request->getParam('pageSize', Yii::app()->params->defaultPerPage),
        ));
    }

    public function actionProducts($brandId, $categoryId)
    {
        Access::is('productCategory', 403);

        $brand = $this->loadModel($brandId);
        $category = $this->loadCategory($categoryId);

        $model = new Product();
        $model->unsetAttributes();

        if (isset($_GET['Product'])) {
            $model->attributes = $_GET['Product'];
        }

        $this->render('products', array(
            'model' => $model,
            'brand' => $brand,
            'category' => $category,
            'pageSize' => Yii::app()->request->getParam('pageSize', Yii::app()->params->defaultPerPage),
        ));
    }

    /**
     * @param $id integer
     * @return Page
     */
    private function loadModel($id)
    {
        $model = ProductBrand::model()->findByPk($id);

        if (!empty($model))
            return $model;
        else
            throw new CHttpException(404, 'Page not found');
    }
    
    /**
     * @param $id integer
     * @return Page
     */
    private function loadCategory($id)
    {
        $model = ProductEbayCategory::model()->findByPk($id);

        if (!empty($model))
            return $model;
        else
            throw new CHttpException(404, 'Page not found');
    }
}
