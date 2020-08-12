<?php
/**
 * MultipleDeleteAction CAction Component
 *
 * It is a component that works in conjunction of TbToggleColumn widget. Just attach to the controller you wish to
 * make the calls to.
 *
 * @author: antonio ramirez <antonio@clevertech.biz>
 * Date: 10/16/12
 * Time: 5:40 PM
 */
class MultipleDeleteAction extends CAction
{
	/**
	 * @var string the name of the model we are going to toggle values to
	 */
	public $modelName;

	public $accessAlias;

	/**
	 * Widgets run function
	 * @param integer $id
	 * @param string $attribute
	 * @throws CHttpException
	 */
	public function run()
	{
		if (Yii::app()->getRequest()->isPostRequest && Yii::app()->getRequest()->isAjaxRequest)
		{
			
			$response = array();	
			if (Access::is($this->accessAlias)) {
		
				$models = CActiveRecord::model($this->modelName)->findAllByPk(Yii::app()->request->getParam('ids'));
				foreach ($models as $model) {
					$model->delete();
				}
				$response['status'] = 1;
			} else {
				$response['status'] = 0;
				$response['error'] = Yii::t('admin', 'Have No Rights');			
			}
			
			echo json_encode($response);
				
		} else
			throw new CHttpException(Yii::t('admin', 'Invalid request'));
	}
}
