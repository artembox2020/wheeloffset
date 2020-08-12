<?php

class ReviewController extends BackendController
{
    public function actionIndex()
    {
		Access::is('modelYear.review', 403);
		
		$model = new Review();
	
        if (isset($_GET['Review'])) {
            $model->attributes = $_GET['Review'];
        }

        $this->render("index", array(
            'model' => $model,
			'pageSize' => Yii::app()->request->getParam('pageSize', Yii::app()->params->defaultPerPage),
        ));
    }

	public function actionCreate() {
		Access::is('modelYear.review.create', 403);
	
        $model = new Review();

        if (isset($_POST['Review'])) {
			$model->attributes = $_POST['Review'];
			if ($model->validate() && $model->save()) {
				Yii::app()->admin->setFlash('success', Yii::t('admin', 'Review successfully added'));
				$this->afterSaveRedirect($model);
			}
        }		

        $this->render('create', array(
            'model' => $model,
        ));
    }

    public function actionUpdate($id) {
		Access::is('modelYear.review.update', 403);
	
        $model = $this->loadModel($id);
        $model->scenario = 'updateAdmin';

        if (isset($_POST['Review'])) {
			$model->attributes = $_POST['Review'];
			
			if ($model->validate() && $model->save()) {
				Yii::app()->admin->setFlash('success', Yii::t('admin', 'Review successfully edited'));
				$this->afterSaveRedirect($model);
			}
        }

        $this->render('update', array(
            'model' => $model,
        ));
    }
	
    public function actionDelete($id) {
		$model = $this->loadModel($id);
		$model->delete();
		Yii::app()->admin->setFlash('success', Yii::t('admin', 'Review successfully deleted'));
		$this->redirect('/admin/review');
	}	

    /**
     * @param $id integer
     * @return Page
     */
    private function loadModel($id)
	{
		$model = Review::model()->findByPk($id);
        
		if (!empty($model))
			return $model;
		else 
			throw new CHttpException(404, 'Page not found');
    }
}