<?php

class TireVehicleClassController extends BackendController
{
    public function actionIndex()
    {
		Access::is('tire.vehicle_class', 403);
	
        $model = new TireVehicleClass();
        if (isset($_GET['TireVehicleClass'])) {
            $model->attributes = $_GET['TireVehicleClass'];
        }

        $this->render("index", array(
            'model' => $model,
			'pageSize' => Yii::app()->request->getParam('pageSize', Yii::app()->params->defaultPerPage),
        ));
    }

	public function actionCreate() {
		Access::is('tire.vehicle_class.create', 403);
	
        $model = new TireVehicleClass();

        if (isset($_POST['TireVehicleClass'])) {
			$model->attributes = $_POST['TireVehicleClass'];
			if ($model->validate() && $model->save()) {
				Yii::app()->admin->setFlash('success', Yii::t('admin', 'Tire Vehicle Class successfully added'));
				$this->afterSaveRedirect($model);
			}
        }		

        $this->render('create', array(
            'model' => $model,
        ));
    }

    public function actionUpdate($id) {
		Access::is('tire.vehicle_class.update', 403);
	
        $model = $this->loadModel($id);

        if (isset($_POST['TireVehicleClass'])) {
			$model->attributes = $_POST['TireVehicleClass'];

			if ($model->validate() && $model->save()) {
				Yii::app()->admin->setFlash('success', Yii::t('admin', 'Tire Vehicle Class successfully edited'));
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
		$model = TireVehicleClass::model()->findByPk($id);
        
		if (!empty($model))
			return $model;
		else 
			throw new CHttpException(404, 'Page not found');
    }

    public function actionDelete($id) {
		$model = $this->loadModel($id);
		$model->delete();
		Yii::app()->admin->setFlash('success', Yii::t('admin', 'Tire Vehicle Class successfully deleted'));
		$this->redirect('/admin/tireVehicleClass');
	}
}