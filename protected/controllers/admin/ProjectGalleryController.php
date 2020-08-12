<?php
/**
 * Backend controller for GalleryManager widget.
 * Provides following features:
 *  - Image removal
 *  - Image upload/Multiple upload
 *  - Arrange images in Project
 *  - Changing name/description associated with image
 *
 */

class ProjectGalleryController extends CController
{
	/**
	 * Removes image with id specified in post request.
	 * On success returns 'OK'
	 */
	public function actionDelete()
	{
		if (Yii::app()->getRequest()->getIsPostRequest()) {
			$id = $_POST['id'];
			/** @var $photo ProjectPhoto */
			$photo = ProjectPhoto::model()->findByPk($id);
			if ($photo !== null && $photo->delete()) {
				echo 'OK';
			} else {
				echo 'FAIL';
			}
		} else {
			echo 'FAIL';
		}
	}

	/**
	 * Method to handle file upload thought XHR2
	 * On success returns JSON object with image info.
	 * @param $gallery_id string Gallery Id to upload images
	 * @throws CHttpException
	 */
	public function actionAjaxUpload($gallery_id = null)
	{
		if (Yii::app()->getRequest()->getIsPostRequest()) {

			$model = new ProjectPhoto();
			$model->watemark = !empty(Yii::app()->session['image_set_watemark']);
			$model->project_id = $gallery_id;
			$model->file = CUploadedFile::getInstanceByName('GalleryPhoto[image]');
			$model->save();

			header("Content-Type: application/json");
			echo CJSON::encode(
				array(
					'id' => $model->id,
					'rank' => $model->rank,
					'name' => (string)$model->name, //todo: something wrong with model - it returns null, but it must return an empty string
					'description' => (string)$model->description,
					'preview' => $model->getPreview(),
				));
		} else {
			throw new CHttpException(403);
		}
	}
		
	/**
	 * Saves images order according to request.
	 * Variable $_POST['order'] - new arrange of image ids, to be saved
	 * @throws CHttpException
	 */
	public function actionOrder()
	{
		if (Yii::app()->getRequest()->getIsPostRequest()) {
			$gp = $_POST['order'];
			$orders = array();
			$i = 0;
			foreach ($gp as $k => $v) {
				if (!$v) {
					$gp[$k] = $k;
				}
				$orders[] = $gp[$k];
				$i++;
			}
			sort($orders);
			$i = 0;
			foreach ($gp as $k => $v) {
				/** @var $p ProjectPhoto */
				$p = ProjectPhoto::model()->findByPk($k);
				$p->rank = $orders[$i];
				$p->save(false);
				$i++;
			}
			if ($_POST['ajax'] == true) {
				echo CJSON::encode(array('result' => 'ok'));
			} else {
				$this->redirect($_POST['returnUrl']);
			}
		} else {
			throw new CHttpException(403);
		}
	}

	/**
	 * Method to update images name/description via AJAX.
	 * On success returns JSON array od objects with new image info.
	 * @throws CHttpException
	 */
	public function actionChangeData()
	{
		if (Yii::app()->getRequest()->getIsPostRequest()) {
			$data = $_POST['photo'];
			$criteria = new CDbCriteria();
			$criteria->index = 'id';
			$criteria->addInCondition('id', array_keys($data));
			/** @var $models ProjectPhoto[] */
			$models = ProjectPhoto::model()->findAll($criteria);
			foreach ($data as $id => $attributes) {
				if (isset($attributes['name'])) {
					$models[$id]->name = $attributes['name'];
				}
				if (isset($attributes['description'])) {
					$models[$id]->description = $attributes['description'];
				}
				$models[$id]->save();
			}
			$resp = array();
			foreach ($models as $model) {
				$resp[] = array(
					'id' => $model->id,
					'rank' => $model->rank,
					'name' => (string)$model->name,
					'description' => (string)$model->description,
					'preview' => $model->getPreview(),
				);
			}
			echo CJSON::encode($resp);
		} else {
			throw new CHttpException(403);
		}
	}
}
