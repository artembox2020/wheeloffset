<div>
    <?php $form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
        'type' => 'horizontal',
        'id' => 'cityForm',
		'htmlOptions' => array(
			'enctype' => 'multipart/form-data',
		),		
    ))?>

		<?php echo $form->dropDownListRow($model, 'platform_id', Platform::getAll(),array('empty'=>''))?>
		
		<?php echo $form->dropDownListRow($model, 'model_id', AutoModel::getAllWithMake(),array('empty'=>''))?>
	
		<?php echo $form->textFieldRow($model, 'year_from')?>
		
		<?php echo $form->textFieldRow($model, 'year_to')?>
		
		<?php $this->renderPartial('application.views.admin._form_actions', array('model'=>$model))?>
		
    <?php $this->endWidget()?>
</div>
