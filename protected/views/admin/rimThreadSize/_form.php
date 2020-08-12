<div>
    <?php $form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
        'type' => 'horizontal',
        'id' => 'cityForm',
		'htmlOptions' => array(
			'enctype' => 'multipart/form-data',
		),		
    ))?>

		<?php echo $form->textFieldRow($model, 'value', array('class'=>'span6'))?>
	
		<?php $this->renderPartial('application.views.admin._form_actions', array('model'=>$model))?>
		
		
    <?php $this->endWidget()?>
</div>

