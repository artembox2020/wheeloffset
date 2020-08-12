<?php

class RimOffsetRangeController extends BackendController
{
    public function actionIndex()
    {
		Access::is('tire.rim.offset_range', 403);
	
        $model = new RimOffsetRange();
        if (isset($_GET['RimOffsetRange'])) {
            $model->attributes = $_GET['RimOffsetRange'];
        }

        $this->render("index", array(
            'model' => $model,
			'pageSize' => Yii::app()->request->getParam('pageSize', Yii::app()->params->defaultPerPage),
        ));
    }

	public function actionCreate() {
		Access::is('tire.rim.offset_range.create', 403);
	
        $model = new RimOffsetRange();

        if (isset($_POST['RimOffsetRange'])) {
			$model->attributes = $_POST['RimOffsetRange'];
			if ($model->validate() && $model->save()) {
				Yii::app()->admin->setFlash('success', Yii::t('admin', 'Rim Offset successfully added'));
				$this->afterSaveRedirect($model);
			}
        }		

        $this->render('create', array(
            'model' => $model,
        ));
    }

    public function actionUpdate($id) {
		Access::is('tire.rim.offset_range.update', 403);
	
        $model = $this->loadModel($id);

        if (isset($_POST['RimOffsetRange'])) {
			$model->attributes = $_POST['RimOffsetRange'];

			if ($model->validate() && $model->save()) {
				Yii::app()->admin->setFlash('success', Yii::t('admin', 'Rim Offset successfully edited'));
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
		$model = RimOffsetRange::model()->findByPk($id);
        
		if (!empty($model))
			return $model;
		else 
			throw new CHttpException(404, 'Page not found');
    }

    public function actionDelete($id) {
		$model = $this->loadModel($id);
		$model->delete();
		Yii::app()->admin->setFlash('success', Yii::t('admin', 'Rim Offset successfully deleted'));
		$this->redirect('/admin/rimOffsetRange');
	}
}