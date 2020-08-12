<?php

class RoleController extends BackendController
{

    public function actionIndex()
    {
		Access::is('role', 403);
        
		$model = new AdminRole();
        if (isset($_GET['AdminRole'])) {
            $model->attributes = $_GET['AdminRole'];
        }
			
        $this->render("index", array(
            'model' => $model,
			'pageSize' => Yii::app()->request->getParam('pageSize', Yii::app()->params->defaultPerPage),
        ));
    }

    public function actionCreate() {
		Access::is('role.create', 403);
	
        $model = new AdminRole();

        if (isset($_POST['AdminRole'])) {
			$model->attributes = $_POST['AdminRole'];
			if ($model->validate() && $model->save()) {
				Yii::app()->admin->setFlash('success', Yii::t('admin', 'Role successfully added'));
				$this->afterSaveRedirect($model);
			}
        }		

        $this->render('create', array(
            'model' => $model,
        ));
    }

    public function actionUpdate($id) {
		Access::is('role.update', 403);
	
        $model = $this->loadModel($id);

        if (isset($_POST['AdminRole'])) {
			$model->attributes = $_POST['AdminRole'];
			if ($model->validate() && $model->save()) {
				Yii::app()->admin->setFlash('success', Yii::t('admin', 'Role successfully edited'));
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
		$model = AdminRole::model()->findByPk($id);
        
		if (!empty($model))
			return $model;
		else 
			throw new CHttpException(404, 'Page not found');
    }

    public function actionDelete($id) {
		Access::is('role.delete', 403);
	
		$model = $this->loadModel($id);
		$model->delete();
		Yii::app()->admin->setFlash('success', Yii::t('admin', 'Role successfully deleted'));
		$this->redirect('/admin/role');
	}
}