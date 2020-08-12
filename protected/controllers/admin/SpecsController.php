<?php

class SpecsController extends BackendController
{
    public function actionIndex()
    {
		Access::is('specs', 403);
	
        $model = new AutoSpecs();
        if (isset($_GET['AutoSpecs'])) {
            $model->attributes = $_GET['AutoSpecs'];
        }

        $this->render("index", array(
            'model' => $model,
			'pageSize' => Yii::app()->request->getParam('pageSize', Yii::app()->params->defaultPerPage),
        ));
    }

	public function actionCreate() {
		Access::is('specs.create', 403);
	
        $model = new AutoSpecs();

        if (isset($_POST['AutoSpecs'])) {
			$model->attributes = $_POST['AutoSpecs'];
			if ($model->validate() && $model->save()) {
				Yii::app()->admin->setFlash('success', Yii::t('admin', 'Specs successfully added'));
				$this->afterSaveRedirect($model);
			}
        }		

        $this->render('create', array(
            'model' => $model,
        ));
    }

    public function actionUpdate($id) {
		Access::is('specs.update', 403);
	
        $model = $this->loadModelYear($id);
        $model->scenario = 'updateAdmin';

        if (isset($_POST['AutoSpecs'])) {
			$model->attributes = $_POST['AutoSpecs'];

			if ($model->validate() && $model->save()) {
				Yii::app()->admin->setFlash('success', Yii::t('admin', 'Specs successfully edited'));
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
    private function loadModelYear($id)
	{
		$model = AutoSpecs::model()->findByPk($id);
        
		if (!empty($model))
			return $model;
		else 
			throw new CHttpException(404, 'Page not found');
    }

    public function actionDelete($id) {
		$model = $this->loadModelYear($id);
		$model->delete();
		Yii::app()->admin->setFlash('success', Yii::t('admin', 'Specs successfully deleted'));
		$this->redirect('/admin/specs');
	}
}