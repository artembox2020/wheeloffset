<?php

class TireLoadIndexController extends BackendController
{
    public function actionIndex()
    {
		Access::is('tire.load_index', 403);
	
        $model = new TireLoadIndex();
        if (isset($_GET['TireLoadIndex'])) {
            $model->attributes = $_GET['TireLoadIndex'];
        }

        $this->render("index", array(
            'model' => $model,
			'pageSize' => Yii::app()->request->getParam('pageSize', Yii::app()->params->defaultPerPage),
        ));
    }

	public function actionCreate() {
		Access::is('tire.load_index.create', 403);
	
        $model = new TireLoadIndex();

        if (isset($_POST['TireLoadIndex'])) {
			$model->attributes = $_POST['TireLoadIndex'];
			if ($model->validate() && $model->save()) {
				Yii::app()->admin->setFlash('success', Yii::t('admin', 'Tire Load Index successfully added'));
				$this->afterSaveRedirect($model);
			}
        }		

        $this->render('create', array(
            'model' => $model,
        ));
    }

    public function actionUpdate($id) {
		Access::is('tire.load_index.update', 403);
	
        $model = $this->loadModel($id);

        if (isset($_POST['TireLoadIndex'])) {
			$model->attributes = $_POST['TireLoadIndex'];

			if ($model->validate() && $model->save()) {
				Yii::app()->admin->setFlash('success', Yii::t('admin', 'Tire Load Index successfully edited'));
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
		$model = TireLoadIndex::model()->findByPk($id);
        
		if (!empty($model))
			return $model;
		else 
			throw new CHttpException(404, 'Page not found');
    }

    public function actionDelete($id) {
		$model = $this->loadModel($id);
		$model->delete();
		Yii::app()->admin->setFlash('success', Yii::t('admin', 'Tire Load Index successfully deleted'));
		$this->redirect('/admin/tireLoadIndex');
	}
}