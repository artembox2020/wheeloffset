<?php

class TireAspectRatioController extends BackendController
{
    public function actionIndex()
    {
		Access::is('tire.aspect_ratio', 403);
	
        $model = new TireAspectRatio();
        if (isset($_GET['TireAspectRatio'])) {
            $model->attributes = $_GET['TireAspectRatio'];
        }

        $this->render("index", array(
            'model' => $model,
			'pageSize' => Yii::app()->request->getParam('pageSize', Yii::app()->params->defaultPerPage),
        ));
    }

	public function actionCreate() {
		Access::is('tire.aspect_ratio.create', 403);
	
        $model = new TireAspectRatio();

        if (isset($_POST['TireAspectRatio'])) {
			$model->attributes = $_POST['TireAspectRatio'];
			if ($model->validate() && $model->save()) {
				Yii::app()->admin->setFlash('success', Yii::t('admin', 'Tire Aspect Ratio successfully added'));
				$this->afterSaveRedirect($model);
			}
        }		

        $this->render('create', array(
            'model' => $model,
        ));
    }

    public function actionUpdate($id) {
		Access::is('tire.aspect_ratio.update', 403);
	
        $model = $this->loadModel($id);

        if (isset($_POST['TireAspectRatio'])) {
			$model->attributes = $_POST['TireAspectRatio'];

			if ($model->validate() && $model->save()) {
				Yii::app()->admin->setFlash('success', Yii::t('admin', 'Tire Aspect Ratio successfully edited'));
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
		$model = TireAspectRatio::model()->findByPk($id);
        
		if (!empty($model))
			return $model;
		else 
			throw new CHttpException(404, 'Page not found');
    }

    public function actionDelete($id) {
		$model = $this->loadModel($id);
		$model->delete();
		Yii::app()->admin->setFlash('success', Yii::t('admin', 'Tire Aspect Ratio successfully deleted'));
		$this->redirect('/admin/tireAspectRatio');
	}
}