<?php

class TireRimDiameterController extends BackendController
{
    public function actionIndex()
    {
		Access::is('tire.rim_diameter', 403);
	
        $model = new TireRimDiameter();
        if (isset($_GET['TireRimDiameter'])) {
            $model->attributes = $_GET['TireRimDiameter'];
        }

        $this->render("index", array(
            'model' => $model,
			'pageSize' => Yii::app()->request->getParam('pageSize', Yii::app()->params->defaultPerPage),
        ));
    }

	public function actionCreate() {
		Access::is('tire.rim_diameter.create', 403);
	
        $model = new TireRimDiameter();

        if (isset($_POST['TireRimDiameter'])) {
			$model->attributes = $_POST['TireRimDiameter'];
			if ($model->validate() && $model->save()) {
				Yii::app()->admin->setFlash('success', Yii::t('admin', 'Tire Rim Diameter successfully added'));
				$this->afterSaveRedirect($model);
			}
        }		

        $this->render('create', array(
            'model' => $model,
        ));
    }

    public function actionUpdate($id) {
		Access::is('tire.rim_diameter.update', 403);
	
        $model = $this->loadModel($id);

        if (isset($_POST['TireRimDiameter'])) {
			$model->attributes = $_POST['TireRimDiameter'];

			if ($model->validate() && $model->save()) {
				Yii::app()->admin->setFlash('success', Yii::t('admin', 'Tire Rim Diameter successfully edited'));
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
		$model = TireRimDiameter::model()->findByPk($id);
        
		if (!empty($model))
			return $model;
		else 
			throw new CHttpException(404, 'Page not found');
    }

    public function actionDelete($id) {
		$model = $this->loadModel($id);
		$model->delete();
		Yii::app()->admin->setFlash('success', Yii::t('admin', 'Tire Rim Diameter successfully deleted'));
		$this->redirect('/admin/tireType');
	}
}