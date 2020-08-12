<?php

class RimBoltPatternController extends BackendController
{
    public function actionIndex()
    {
		Access::is('tire.rim.bolt_pattern', 403);
	
        $model = new RimBoltPattern();
        if (isset($_GET['RimBoltPattern'])) {
            $model->attributes = $_GET['RimBoltPattern'];
        }

        $this->render("index", array(
            'model' => $model,
			'pageSize' => Yii::app()->request->getParam('pageSize', Yii::app()->params->defaultPerPage),
        ));
    }

	public function actionCreate() {
		Access::is('tire.rim.bolt_pattern.create', 403);
	
        $model = new RimBoltPattern();

        if (isset($_POST['RimBoltPattern'])) {
			$model->attributes = $_POST['RimBoltPattern'];
			if ($model->validate() && $model->save()) {
				Yii::app()->admin->setFlash('success', Yii::t('admin', 'RimBoltPattern successfully added'));
				$this->afterSaveRedirect($model);
			}
        }		

        $this->render('create', array(
            'model' => $model,
        ));
    }

    public function actionUpdate($id) {
		Access::is('tire.rim.bolt_pattern.update', 403);
	
        $model = $this->loadModel($id);

        if (isset($_POST['RimBoltPattern'])) {
			$model->attributes = $_POST['RimBoltPattern'];

			if ($model->validate() && $model->save()) {
				Yii::app()->admin->setFlash('success', Yii::t('admin', 'RimBoltPattern successfully edited'));
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
		$model = RimBoltPattern::model()->findByPk($id);
        
		if (!empty($model))
			return $model;
		else 
			throw new CHttpException(404, 'Page not found');
    }

    public function actionDelete($id) {
		$model = $this->loadModel($id);
		$model->delete();
		Yii::app()->admin->setFlash('success', Yii::t('admin', 'RimBoltPattern successfully deleted'));
		$this->redirect('/admin/rimBoltPattern');
	}
}