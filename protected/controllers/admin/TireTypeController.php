<?php

class TireTypeController extends BackendController
{
    public function actionIndex()
    {
		Access::is('tire.type', 403);
	
        $model = new TireType();
        if (isset($_GET['TireType'])) {
            $model->attributes = $_GET['TireType'];
        }

        $this->render("index", array(
            'model' => $model,
			'pageSize' => Yii::app()->request->getParam('pageSize', Yii::app()->params->defaultPerPage),
        ));
    }

	public function actionCreate() {
		Access::is('tire.type.create', 403);
	
        $model = new TireType();

        if (isset($_POST['TireType'])) {
			$model->attributes = $_POST['TireType'];
			if ($model->validate() && $model->save()) {
				Yii::app()->admin->setFlash('success', Yii::t('admin', 'Tire Type successfully added'));
				$this->afterSaveRedirect($model);
			}
        }		

        $this->render('create', array(
            'model' => $model,
        ));
    }

    public function actionUpdate($id) {
		Access::is('tire.type.update', 403);
	
        $model = $this->loadModel($id);

        if (isset($_POST['TireType'])) {
			$model->attributes = $_POST['TireType'];

			if ($model->validate() && $model->save()) {
				Yii::app()->admin->setFlash('success', Yii::t('admin', 'Tire Type successfully edited'));
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
		$model = TireType::model()->findByPk($id);
        
		if (!empty($model))
			return $model;
		else 
			throw new CHttpException(404, 'Page not found');
    }

    public function actionDelete($id) {
		$model = $this->loadModel($id);
		$model->delete();
		Yii::app()->admin->setFlash('success', Yii::t('admin', 'Tire Type successfully deleted'));
		$this->redirect('/admin/tireType');
	}
}