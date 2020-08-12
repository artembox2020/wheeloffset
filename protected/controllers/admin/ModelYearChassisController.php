<?php

class ModelYearChassisController extends BackendController
{
    public function actionIndex()
    {
		Access::is('modelYear.chassis', 403);
		
		$model = new AutoModelYearChassis();
	
        if (isset($_GET['AutoModelYearChassis'])) {
            $model->attributes = $_GET['AutoModelYearChassis'];
        }

        $this->render("index", array(
            'model' => $model,
			'pageSize' => Yii::app()->request->getParam('pageSize', Yii::app()->params->defaultPerPage),
        ));
    }

	public function actionCreate() {
		Access::is('modelYear.chassis.create', 403);
	
        $model = new AutoModelYearChassis();

        if (isset($_POST['AutoModelYearChassis'])) {
			$model->attributes = $_POST['AutoModelYearChassis'];
			if ($model->validate() && $model->save()) {
				Yii::app()->admin->setFlash('success', Yii::t('admin', 'Chassis successfully added'));
				$this->afterSaveRedirect($model);
			}
        }		

        $this->render('create', array(
            'model' => $model,
        ));
    }

    public function actionUpdate($id) {
		Access::is('modelYear.chassis.update', 403);
	
        $model = $this->loadModel($id);
        $model->scenario = 'updateAdmin';

        if (isset($_POST['AutoModelYearChassis'])) {
			$model->attributes = $_POST['AutoModelYearChassis'];
			
			if ($model->validate() && $model->save()) {
				Yii::app()->admin->setFlash('success', Yii::t('admin', 'Chassis successfully edited'));
				$this->afterSaveRedirect($model);
			}
        }

        $this->render('update', array(
            'model' => $model,
        ));
    }
	
    public function actionDelete($id) {
		$model = $this->loadModel($id);
		$model->delete();
		Yii::app()->admin->setFlash('success', Yii::t('admin', 'Chassis successfully deleted'));
		$this->redirect('/admin/modelYearChassis');
	}	

    /**
     * @param $id integer
     * @return Page
     */
    private function loadModel($id)
	{
		$model = AutoModelYearChassis::model()->findByPk($id);
        
		if (!empty($model))
			return $model;
		else 
			throw new CHttpException(404, 'Page not found');
    }
}