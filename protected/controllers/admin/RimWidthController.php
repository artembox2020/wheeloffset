<?php

class RimWidthController extends BackendController
{
    public function actionIndex()
    {
		Access::is('tire.rim.width', 403);
	
        $model = new RimWidth();
        if (isset($_GET['RimWidth'])) {
            $model->attributes = $_GET['RimWidth'];
        }

        $this->render("index", array(
            'model' => $model,
			'pageSize' => Yii::app()->request->getParam('pageSize', Yii::app()->params->defaultPerPage),
        ));
    }

	public function actionCreate() {
		Access::is('tire.rim.width.create', 403);
	
        $model = new RimWidth();

        if (isset($_POST['RimWidth'])) {
			$model->attributes = $_POST['RimWidth'];
			if ($model->validate() && $model->save()) {
				Yii::app()->admin->setFlash('success', Yii::t('admin', 'RimWidth successfully added'));
				$this->afterSaveRedirect($model);
			}
        }		

        $this->render('create', array(
            'model' => $model,
        ));
    }

    public function actionUpdate($id) {
		Access::is('tire.rim.width.update', 403);
	
        $model = $this->loadModel($id);

        if (isset($_POST['RimWidth'])) {
			$model->attributes = $_POST['RimWidth'];

			if ($model->validate() && $model->save()) {
				Yii::app()->admin->setFlash('success', Yii::t('admin', 'RimWidth successfully edited'));
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
		$model = RimWidth::model()->findByPk($id);
        
		if (!empty($model))
			return $model;
		else 
			throw new CHttpException(404, 'Page not found');
    }

    public function actionDelete($id) {
		$model = $this->loadModel($id);
		$model->delete();
		Yii::app()->admin->setFlash('success', Yii::t('admin', 'RimWidth successfully deleted'));
		$this->redirect('/admin/rimWidth');
	}
}