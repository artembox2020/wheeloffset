<?php $this->pageTitle = Yii::app()->name.' - ' . Yii::t('admin', 'Editing Rim Center Bore');?>

<div class="container inner-page white img-rounded">
    <div class="page-header">
        <h3><?=Yii::t('admin', 'Editing Rim Center Bore')?></h3>
    </div>
	
	<?php $this->renderPartial('_form', array('model'=>$model));?>
</div>