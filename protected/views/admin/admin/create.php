<?php $this->pageTitle = Yii::app()->name.' - ' . Yii::t('admin', 'Creating Admin');?>

<div class="container inner-page white img-rounded">
	<div>
		<div class="page-header">
			<h3><?=Yii::t('admin', 'Creating Admin')?></h3>
		</div>
		<div>
			<? $form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
				'type' => 'horizontal',
				'id'=>'userForm',
			))?>

			<?php echo $form->textFieldRow($model, 'first_name')?>
			
			<?php echo $form->textFieldRow($model, 'last_name')?>
			
			<?php echo $form->passwordFieldRow($model, 'password')?>
			
			<?php echo $form->passwordFieldRow($model, 'confirmPassword')?>
			
			<?php echo $form->textFieldRow($model, 'phone')?>
			
			<?php echo $form->textFieldRow($model, 'email', array('placeholder'=>'email@domain.dd'))?>
			<?php echo $form->toggleButtonRow($model, 'active', array());?>
			<?php echo $form->dropDownListRow($model, 'role_id', AdminRole::getAll(),array('empty'=>''))?>
		  
			<div class="control-group">
				<label class="control-label" for="Task_deadline"><?php echo $model->getAttributeLabel('birth_date')?> <span class="required">*</span></label>
				<div class="controls">
					<?php $this->widget('bootstrap.widgets.TbDatePicker', array(
						'model'=>$model,
						'attribute'=>'birth_date',
						'options' => array(
							'format' => 'yyyy-mm-dd',
						),
						'htmlOptions' => array(
							'class' => 'span4',
						)
					));?>
				</div>
			</div>		  
		  
			<div class="form-actions">
				<? $this->widget('bootstrap.widgets.TbButton', array('buttonType' => 'submit', 'label' => 'Сохранить'))?>
			</div>
			<? $this->endWidget()?>
		</div>
	</div>
</div>