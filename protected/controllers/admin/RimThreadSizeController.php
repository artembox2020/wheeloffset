<?php

class RimThreadSizeController extends BackendController
{
    public function actionIndex()
    {
		Access::is('tire.rim.thread_size', 403);
	
        $model = new RimThreadSize();
        if (isset($_GET['RimThreadSize'])) {
            $model->attributes = $_GET['RimThreadSize'];
        }

        $this->render("index", array(
            'model' => $model,
			'pageSize' => Yii::app()->request->getParam('pageSize', Yii::app()->params->defaultPerPage),
        ));
    }

	public function actionCreate() {
		Access::is('tire.rim.thread_size.create', 403);
	
        $model = new RimThreadSize();

        if (isset($_POST['RimThreadSize'])) {
			$model->attributes = $_POST['RimThreadSize'];
			if ($model->validate() && $model->save()) {
				Yii::app()->admin->setFlash('success', Yii::t('admin', 'RimThreadSize successfully added'));
				$this->afterSaveRedirect($model);
			}
        }		

        $this->render('create', array(
            'model' => $model,
        ));
    }

    public function actionUpdate($id) {
		Access::is('tire.rim.thread_size.update', 403);
	
        $model = $this->loadModel($id);

        if (isset($_POST['RimThreadSize'])) {
			$model->attributes = $_POST['RimThreadSize'];

			if ($model->validate() && $model->save()) {
				Yii::app()->admin->setFlash('success', Yii::t('admin', 'RimThreadSize successfully edited'));
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
		$model = RimThreadSize::model()->findByPk($id);
        
		if (!empty($model))
			return $model;
		else 
			throw new CHttpException(404, 'Page not found');
    }

    public function actionDelete($id) {
		$model = $this->loadModel($id);
		$model->delete();
		Yii::app()->admin->setFlash('success', Yii::t('admin', 'RimThreadSize successfully deleted'));
		$this->redirect('/admin/rimThreadSize');
	}
}