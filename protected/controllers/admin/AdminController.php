<?php

class AdminController extends BackendController
{	
    public function actionIndex() {
	
		Access::is('admin', 403);
	
        Yii::app()->admin->setReturnUrl(Yii::app()->createUrl('admin/index'));
        $model = new Admin();
        if (isset($_GET['Admin'])) {
            $model->attributes = $_GET['Admin'];
        }
        $this->render("index", array(
            'model' => $model,
			'pageSize' => Yii::app()->request->getParam('pageSize', Yii::app()->params->defaultPerPage),
        ));
    }

    private function loadModel($id)
    {
        return Admin::model()->findByPk($id);
    }


    public function actionDelete($id) {
		Access::is('admin.delete', 403);
	
        $this->loadModel($id)->delete();
    }
	
    public function actionCreate() {
		Access::is('admin.create', 403);
	
        $model = new Admin();
		
        if (isset($_POST['Admin'])) {
            $model->attributes = $_POST['Admin'];
			if ($model->validate() && $model->save()) {
				Yii::app()->admin->setFlash('success', Yii::t('admin', 'Admin successfully added'));
				$this->redirect('/admin/admin');
			} 
        }

        $this->render('create', array(
            'model' => $model,
        ));
    }

    public function actionUpdate($id = null) {
		$id = (int)$id;
        $model = ($id==0)?$this->loadModel(Yii::app()->admin->id):$this->loadModel($id);
		$title = ($id==0) ? Yii::t('admin', 'Profile') : Yii::t('admin', 'Editing Admin');
		
		if ($id > 0 && !Access::is('admin.update')) {
			$this->redirect('/admin/admin/view');
		}
		
        if (isset($_POST['Admin'])) {
            $model->attributes = $_POST['Admin'];
            if ($model->validate() && $model->save()) {
				Yii::app()->admin->setFlash('success', Yii::t('admin', 'Successfully saved'));
				if ($id > 0)
					$this->redirect('/admin/admin');
			} 
        }
		
		$modelPassword = new AdminPasswordForm;
		
		if (isset($_POST['AdminPasswordForm'])) {
            $modelPassword->attributes = $_POST['AdminPasswordForm'];
			if ($modelPassword->validate()) {
				$model->scenario = 'changePasswordInProfile';
				$model->new_password = $modelPassword->password;
				if ($model->save()) {
					Yii::app()->admin->setFlash('success', Yii::t('admin', 'Password successfully changed'));
					if ($id > 0)
						$this->redirect('/admin/admin');
				}
			}
		}		
		
        $this->render('update', array(
            'id' => $id,
            'model' => $model,
            'title' => $title,
			'modelPassword' => $modelPassword,
        ));
    }	
	
	/**
	 * Logs out the current admin and redirect to homepage.
	 */
	public function actionLogout()
	{
		Yii::app()->admin->logout();
		$this->redirect('/admin/login');
	}	
}