<?php

class SpecsGroupController extends BackendController
{
    public function actionIndex()
    {
		Access::is('specsGroup', 403);
	
        $model = new AutoSpecsGroup();
        if (isset($_GET['AutoSpecsGroup'])) {
            $model->attributes = $_GET['AutoSpecsGroup'];
        }

        $this->render("index", array(
            'model' => $model,
			'pageSize' => Yii::app()->request->getParam('pageSize', Yii::app()->params->defaultPerPage),
        ));
    }

	public function actionCreate() {
		Access::is('specsGroup.create', 403);
	
        $model = new AutoSpecsGroup();

        if (isset($_POST['AutoSpecsGroup'])) {
			$model->attributes = $_POST['AutoSpecsGroup'];
			if ($model->validate() && $model->save()) {
				Yii::app()->admin->setFlash('success', Yii::t('admin', 'Specs Group successfully added'));
				$this->afterSaveRedirect($model);
			}
        }		

        $this->render('create', array(
            'model' => $model,
        ));
    }

    public function actionUpdate($id) {
		Access::is('specsGroup.update', 403);
	
        $model = $this->loadModelYear($id);

        if (isset($_POST['AutoSpecsGroup'])) {
			$model->attributes = $_POST['AutoSpecsGroup'];

			if ($model->validate() && $model->save()) {
				Yii::app()->admin->setFlash('success', Yii::t('admin', 'Specs Group successfully edited'));
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
		$model = AutoSpecsGroup::model()->findByPk($id);
        
		if (!empty($model))
			return $model;
		else 
			throw new CHttpException(404, 'Page not found');
    }

    public function actionDelete($id) {
		$model = $this->loadModelYear($id);
		$model->delete();
		Yii::app()->admin->setFlash('success', Yii::t('admin', 'Specs Group successfully deleted'));
		$this->redirect('/admin/specsGroup');
	}
}