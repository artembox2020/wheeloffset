<?php

class TireRimWidthRangeController extends BackendController
{
    public function actionIndex()
    {
		Access::is('tire', 403);
		
		$import = Yii::app()->request->getParam('import');
		if (!empty($import)) {
			TireRimWidthRange::model()->import();
		}
		
        $model = new TireRimWidthRange();
        if (isset($_GET['TireRimWidthRange'])) {
            $model->attributes = $_GET['TireRimWidthRange'];
        }

        $this->render("index", array(
            'model' => $model,
			'pageSize' => Yii::app()->request->getParam('pageSize', Yii::app()->params->defaultPerPage),
        ));
    }

	public function actionCreate() {
		Access::is('tire.create', 403);
	
        $model = new TireRimWidthRange();

        if (isset($_POST['TireRimWidthRange'])) {
			$model->attributes = $_POST['TireRimWidthRange'];
			if ($model->validate() && $model->save()) {
				Yii::app()->admin->setFlash('success', Yii::t('admin', 'Tire Rim Width range successfully added'));
				$this->afterSaveRedirect($model);
			}
        }		

        $this->render('create', array(
            'model' => $model,
        ));
    }

    public function actionUpdate($id) {
		Access::is('tire.update', 403);
	
        $model = $this->loadModel($id);

        if (isset($_POST['TireRimWidthRange'])) {
			$model->attributes = $_POST['TireRimWidthRange'];

			if ($model->validate() && $model->save()) {
				Yii::app()->admin->setFlash('success', Yii::t('admin', 'Tire Rim Width range successfully edited'));
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
		$model = TireRimWidthRange::model()->findByPk($id);
        
		if (!empty($model))
			return $model;
		else 
			throw new CHttpException(404, 'Page not found');
    }

    public function actionDelete($id) {
		$model = $this->loadModel($id);
		$model->delete();
		Yii::app()->admin->setFlash('success', Yii::t('admin', 'Tire Rim Width range successfully deleted'));
		$this->redirect('/admin/tireType');
	}
}