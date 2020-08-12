<?php

class RimCenterBoreController extends BackendController
{
    public function actionIndex()
    {
		Access::is('tire.rim.center_bore', 403);
	
        $model = new RimCenterBore();
        if (isset($_GET['RimCenterBore'])) {
            $model->attributes = $_GET['RimCenterBore'];
        }

        $this->render("index", array(
            'model' => $model,
			'pageSize' => Yii::app()->request->getParam('pageSize', Yii::app()->params->defaultPerPage),
        ));
    }

	public function actionCreate() {
		Access::is('tire.rim.center_bore.create', 403);
	
        $model = new RimCenterBore();

        if (isset($_POST['RimCenterBore'])) {
			$model->attributes = $_POST['RimCenterBore'];
			if ($model->validate() && $model->save()) {
				Yii::app()->admin->setFlash('success', Yii::t('admin', 'RimCenterBore successfully added'));
				$this->afterSaveRedirect($model);
			}
        }		

        $this->render('create', array(
            'model' => $model,
        ));
    }

    public function actionUpdate($id) {
		Access::is('tire.rim.center_bore.update', 403);
	
        $model = $this->loadModel($id);

        if (isset($_POST['RimCenterBore'])) {
			$model->attributes = $_POST['RimCenterBore'];

			if ($model->validate() && $model->save()) {
				Yii::app()->admin->setFlash('success', Yii::t('admin', 'RimCenterBore successfully edited'));
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
		$model = RimCenterBore::model()->findByPk($id);
        
		if (!empty($model))
			return $model;
		else 
			throw new CHttpException(404, 'Page not found');
    }

    public function actionDelete($id) {
		$model = $this->loadModel($id);
		$model->delete();
		Yii::app()->admin->setFlash('success', Yii::t('admin', 'RimCenterBore successfully deleted'));
		$this->redirect('/admin/rimCenterBore');
	}
}