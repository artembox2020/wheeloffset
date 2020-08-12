<?php

class TireSectionWidthController extends BackendController
{
    public function actionIndex()
    {
		Access::is('tire.section_width', 403);
	
        $model = new TireSectionWidth();
        if (isset($_GET['TireSectionWidth'])) {
            $model->attributes = $_GET['TireSectionWidth'];
        }

        $this->render("index", array(
            'model' => $model,
			'pageSize' => Yii::app()->request->getParam('pageSize', Yii::app()->params->defaultPerPage),
        ));
    }

	public function actionCreate() {
		Access::is('tire.section_width.create', 403);
	
        $model = new TireSectionWidth();

        if (isset($_POST['TireSectionWidth'])) {
			$model->attributes = $_POST['TireSectionWidth'];
			if ($model->validate() && $model->save()) {
				Yii::app()->admin->setFlash('success', Yii::t('admin', 'Tire Section Width successfully added'));
				$this->afterSaveRedirect($model);
			}
        }		

        $this->render('create', array(
            'model' => $model,
        ));
    }

    public function actionUpdate($id) {
		Access::is('tire.section_width.update', 403);
	
        $model = $this->loadModel($id);

        if (isset($_POST['TireSectionWidth'])) {
			$model->attributes = $_POST['TireSectionWidth'];

			if ($model->validate() && $model->save()) {
				Yii::app()->admin->setFlash('success', Yii::t('admin', 'Tire Section Width successfully edited'));
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
		$model = TireSectionWidth::model()->findByPk($id);
        
		if (!empty($model))
			return $model;
		else 
			throw new CHttpException(404, 'Page not found');
    }

    public function actionDelete($id) {
		$model = $this->loadModel($id);
		$model->delete();
		Yii::app()->admin->setFlash('success', Yii::t('admin', 'Tire Section Width successfully deleted'));
		$this->redirect('/admin/tireSectionWidth');
	}
}