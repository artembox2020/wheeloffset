<?php if (!$model->isNewRecord):?>
	<?php
	    $this->widget('ext.galleryManager.GalleryManager', array(
        'gallery' => $model,
        'controllerRoute' =>'/admin/projectGallery',
    ));
	?>
<?php else:?>
	<p><?=Yii::t('admin', 'Available after adding')?></p>
<?php endif;?>