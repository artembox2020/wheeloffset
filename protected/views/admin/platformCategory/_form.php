<div>
    <?php $form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
        'type' => 'horizontal',
        'id' => 'cityForm',
		'htmlOptions' => array(
			'enctype' => 'multipart/form-data',
		),		
    ))?>

		<?php echo $form->textFieldRow($model, 'title', array('class'=>'span6'))?>
		<?php echo $form->textFieldRow($model, 'alias', array('class'=>'span6'))?>
	
		<?php $this->renderPartial('application.views.admin._form_actions', array('model'=>$model))?>
		
    <?php $this->endWidget()?>
</div>
