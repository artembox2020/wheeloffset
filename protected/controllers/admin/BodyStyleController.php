<?php

class BodyStyleController extends BackendController
{
    public function actionIndex()
    {
		Access::is('bodyStyle', 403);
	
        $model = new AutoBodyStyle();
        if (isset($_GET['AutoBodyStyle'])) {
            $model->attributes = $_GET['AutoBodyStyle'];
        }

        $this->render("index", array(
            'model' => $model,
			'pageSize' => Yii::app()->request->getParam('pageSize', Yii::app()->params->defaultPerPage),
        ));
    }

	public function actionCreate() {
		Access::is('bodyStyle.create', 403);
	
        $model = new AutoBodyStyle();

        if (isset($_POST['AutoBodyStyle'])) {
			$model->attributes = $_POST['AutoBodyStyle'];
			$model->file = CUploadedFile::getInstance($model, 'file');
			if ($model->validate() && $model->save()) {
				Yii::app()->admin->setFlash('success', Yii::t('admin', 'Body style successfully added'));
				$this->afterSaveRedirect($model);
			}
        }		

        $this->render('create', array(
            'model' => $model,
        ));
    }

    public function actionUpdate($id) {
		Access::is('bodyStyle.update', 403);
	
        $model = $this->loadModel($id);

        if (isset($_POST['AutoBodyStyle'])) {
			$model->attributes = $_POST['AutoBodyStyle'];
			$model->file = CUploadedFile::getInstance($model, 'file');

			if ($model->validate() && $model->save()) {
				Yii::app()->admin->setFlash('success', Yii::t('admin', 'Body style successfully edited'));
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
		$model = AutoBodyStyle::model()->findByPk($id);
        
		if (!empty($model))
			return $model;
		else 
			throw new CHttpException(404, 'Page not found');
    }

    public function actionDelete($id) {
		$model = $this->loadModel($id);
		$model->delete();
		Yii::app()->admin->setFlash('success', Yii::t('admin', 'Body style successfully deleted'));
		$this->redirect('/admin/bodyStyle');
	}
}