<div>
    <?php $form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
        'type' => 'horizontal',
        'id' => 'cityForm',
		'htmlOptions' => array(
			'enctype' => 'multipart/form-data',
		),		
    ))?>

		<?php echo $form->textFieldRow($model, 'index', array('class'=>'span2'))?>
		<?php echo $form->textFieldRow($model, 'pounds', array('class'=>'span2'))?>
		<?php echo $form->textFieldRow($model, 'kilograms', array('class'=>'span2'))?>
		<?php echo $form->textFieldRow($model, 'rank', array('class'=>'span2'))?>

		<?php $this->renderPartial('application.views.admin._form_actions', array('model'=>$model))?>
		
    <?php $this->endWidget()?>
</div>

