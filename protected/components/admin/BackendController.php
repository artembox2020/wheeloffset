<?php
/**
 * Controller is the customized base controller class.
 * All controller classes for this application should extend from this base class.
 */
class BackendController extends CController
{
	/**
	 * @var string the default layout for the controller view. Defaults to '//layouts/column1',
	 * meaning using a single column layout. See 'protected/views/layouts/column1.php'.
	 */
	public $layout='//admin/layouts/main';
	/**
	 * @var array context menu items. This property will be assigned to {@link CMenu::items}.
	 */
	public $menu=array();
	/**
	 * @var array the breadcrumbs of the current page. The value of this property will
	 * be assigned to {@link CBreadcrumbs::links}. Please refer to {@link CBreadcrumbs::links}
	 * for more details on how to specify this property.
	 */
	public $breadcrumbs=array();
	
	public function init()
	{
		$this->layout='//admin/layouts/main';
		
		if (Yii::app()->admin->isGuest) {
			$this->redirect('/admin/login');
		} 
				
		return parent::init();
	}
	
	/**
     * Редирект после сохранения модели в зависимости от входных параметров (на эту же страницу,
     * на пред страницу или просто на список моделей)
     */
    protected function afterSaveRedirect($model = null) 
    {
    	$apply = Yii::app()->getRequest()->getParam('apply', null);
        if ($apply !== null && !is_null($model)) {
        	
            $redirectData = array('update', 'id' => $model->id);

            if (Yii::app()->request->getParam('from', false)) {
                $redirectData += array('from' => Yii::app()->request->getParam('from'));
            }

        } else if (Yii::app()->request->getParam('from', false)) {
        	
            $redirectData = base64_decode(Yii::app()->request->getParam('from')) . '&from='.Yii::app()->request->getParam('from');
            
        } else {
        	
            $redirectData = array('index',);
            
        }
        $this->redirect($redirectData);
    }	
	
}