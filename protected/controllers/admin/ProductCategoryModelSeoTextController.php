<?php

class ProductCategoryModelSeoTextController extends BackendController
{
    public function actionIndex()
    {
        Access::is('productCategoryModel', 403);

        $model = new ProductCategoryModelSeoTextImportForm();

        if (Yii::app()->request->isPostRequest) {
            $model->file = CUploadedFile::getInstance($model, 'file');

            if ($model->validate() && $model->import()) {
                Yii::app()->admin->setFlash('success', Yii::t('admin', 'Seo texts were successfully imported'));
                $this->redirect('/admin/productCategoryModelSeoText');
            }
        }
        
        $this->render("index", array(
            'model' => $model,
        ));
    }
}