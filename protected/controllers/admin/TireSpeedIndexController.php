<?php

class TireSpeedIndexController extends BackendController
{
    public function actionIndex()
    {
		Access::is('tire.speed_index', 403);
	
        $model = new TireSpeedIndex();
        if (isset($_GET['TireSpeedIndex'])) {
            $model->attributes = $_GET['TireSpeedIndex'];
        }

        $this->render("index", array(
            'model' => $model,
			'pageSize' => Yii::app()->request->getParam('pageSize', Yii::app()->params->defaultPerPage),
        ));
    }

	public function actionCreate() {
		Access::is('tire.speed_index.create', 403);
	
        $model = new TireSpeedIndex();

        if (isset($_POST['TireSpeedIndex'])) {
			$model->attributes = $_POST['TireSpeedIndex'];
			if ($model->validate() && $model->save()) {
				Yii::app()->admin->setFlash('success', Yii::t('admin', 'Tire Speed Index successfully added'));
				$this->afterSaveRedirect($model);
			}
        }		

        $this->render('create', array(
            'model' => $model,
        ));
    }

    public function actionUpdate($id) {
		Access::is('tire.speed_index.update', 403);
	
        $model = $this->loadModel($id);

        if (isset($_POST['TireSpeedIndex'])) {
			$model->attributes = $_POST['TireSpeedIndex'];

			if ($model->validate() && $model->save()) {
				Yii::app()->admin->setFlash('success', Yii::t('admin', 'Tire Speed Index successfully edited'));
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
		$model = TireSpeedIndex::model()->findByPk($id);
        
		if (!empty($model))
			return $model;
		else 
			throw new CHttpException(404, 'Page not found');
    }

    public function actionDelete($id) {
		$model = $this->loadModel($id);
		$model->delete();
		Yii::app()->admin->setFlash('success', Yii::t('admin', 'Tire Speed Index successfully deleted'));
		$this->redirect('/admin/tireSpeedIndex');
	}
}