<div>
    <?php $form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
        'type' => 'horizontal',
        'id' => 'cityForm',
		'htmlOptions' => array(
			'enctype' => 'multipart/form-data',
		),		
    ))?>

        <?php echo $form->textFieldRow($model, 'title')?>
        <?php echo $form->textFieldRow($model, 'alias')?>
		<?php echo $form->fileFieldRow($model, 'file')?>
        <br/>
		
		<?php if (!$model->isNewRecord):?>
			<img id="image_preview" src="<?=$model->image_preview?>"/>
		<?php endif;?>
		<br>
			
		<?php echo $form->textAreaRow($model, 'description', array('class'=>'ckeditor'))?>	
			
			
		<?php $this->renderPartial('application.views.admin._form_actions', array('model'=>$model))?>		
				
    <?php $this->endWidget()?>
</div>
