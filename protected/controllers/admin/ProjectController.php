<?php

class ProjectController extends BackendController
{
	
	public function actions()
	{
		return array(
			'toggle' => array(
				'class'=>'bootstrap.actions.TbToggleAction',
				'modelName' => 'Project',
			),
			'active' => array(
				'class'=>'application.actions.MultipleCheckboxAction',
				'modelName' => 'Project',
				'attributeName' => 'is_active',
				'accessAlias' => 'project.update',
			),
			'trash' => array(
				'class'=>'application.actions.MultipleCheckboxAction',
				'modelName' => 'Project',
				'attributeName' => 'is_deleted',
				'accessAlias' => 'project.delete',
			),
			'delete' => array(
				'class'=>'application.actions.MultipleDeleteAction',
				'modelName' => 'Project',
				'accessAlias' => 'project.delete',
			),
		);
	}	
	
	
    public function actionIndex()
    {
		Access::is('project', 403);
	
        $model = new Project();
        if (isset($_GET['Project'])) {
            $model->attributes = $_GET['Project'];
        }

        $this->render("index", array(
            'model' => $model,
			'pageSize' => Yii::app()->request->getParam('pageSize', Yii::app()->params->defaultPerPage),
        ));
    }

   public function actionStat()
    {
		Access::is('project', 403);
	
        $model = new ProjectStat();
        if (isset($_GET['ProjectStat'])) {
            $model->attributes = $_GET['ProjectStat'];
        }

        $this->render("stat", array(
            'model' => $model,
			'pageSize' => Yii::app()->request->getParam('pageSize', Yii::app()->params->defaultPerPage),
        ));
    }

   public function actionStatuser()
    {
		Access::is('project', 403);
	
        $model = new ProjectStatUser();
        if (isset($_GET['ProjectStatUser'])) {
            $model->attributes = $_GET['ProjectStatUser'];
        }

        $this->render("statuser", array(
            'model' => $model,
			'pageSize' => Yii::app()->request->getParam('pageSize', Yii::app()->params->defaultPerPage),
        ));
    }

	public function actionCreate() {
		Access::is('project.create', 403);
	
        $model = new Project();

        if (isset($_POST['Project'])) {
			$model->attributes = $_POST['Project'];
			if ($model->validate() && $model->save()) {
				Yii::app()->admin->setFlash('success', Yii::t('admin', 'Project successfully added'));
				$this->afterSaveRedirect($model);
			}
        } else {
			$model->is_active = 1;
		}		

        $this->render('create', array(
            'model' => $model,
        ));
    }

    public function actionUpdate($id) {
		Access::is('project.update', 403);
	
        $model = $this->loadModel($id);

        if (isset($_POST['Project'])) {
			$model->attributes = $_POST['Project'];

			if ($model->validate() && $model->save()) {
				Yii::app()->admin->setFlash('success', Yii::t('admin', 'Project successfully edited'));
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
		$model = Project::model()->findByPk($id);
        
		if (!empty($model))
			return $model;
		else 
			throw new CHttpException(404, 'Page not found');
    }

    public function actionDelete($id) {
		$model = $this->loadModel($id);
		$model->delete();
		Yii::app()->admin->setFlash('success', Yii::t('admin', 'Project successfully deleted'));
		$this->redirect('/admin/project');
	}
}