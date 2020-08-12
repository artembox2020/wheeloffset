	<div class="form-actions">
		<?php $this->widget('bootstrap.widgets.TbButton', array(
		'buttonType'=>'submit',
		'type'=>'success',
		'label'=>$model->isNewRecord ? Yii::t('admin', 'Add') : Yii::t('admin', 'Save'))); ?>

		<?php $this->widget('bootstrap.widgets.TbButton', array(
		'type'=>'primary',
		'buttonType'=>'submit',
		'htmlOptions'=>array('name'=>'apply', 'class'=>'btn-apply'),
		'label'=>Yii::t('admin', 'Apply'))); ?>

		<?php $this->widget('bootstrap.widgets.TbButton', array(
		'buttonType'=>'reset',
		'htmlOptions'=>array(
			'class'=>'btn-cancel',
			'onclick' => "document.location='" . $this->createUrl('index') . "';return false;",
		),
		'label'=>Yii::t('admin', 'Cancel'))); ?>
	</div>	