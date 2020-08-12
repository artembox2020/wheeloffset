<?php 
$this->pageTitle = Yii::app()->name. ' - ' . $title;
?>

<div class="container inner-page white img-rounded">

    <div class="page-header">
        <h3><?=$title?></h3>
    </div>
 
	<?php $this->widget('AdminTbAlert', array(
        'block'=>true, 
        'fade'=>true,
        'closeText'=>'&times;',
        'alerts'=>array(
            'success'=>array('block'=>true, 'fade'=>true, 'closeText'=>'&times;'),
        ))); 
	?> 
	
	<div>
        <?php $form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
            'type' => 'horizontal',
            'id'=>'userForm'
        ))?>
        <?php echo $form->textFieldRow($model, 'email')?>
      
        <?php echo $form->textFieldRow($model, 'first_name')?>
        <?php echo $form->textFieldRow($model, 'last_name')?>			
		
        <div class="control-group">
            <?php echo CHtml::activeLabelEx($model, 'phone', array('class'=>'control-label'))?>
            <div class="controls">
                <?php
                $this->widget('CMaskedTextField', array(
                    'model' => $model,
                    'attribute' => 'phone',
                    'mask' => '+7-999-999-99-99',
                    'placeholder'=>'_',
                    'htmlOptions' => array('maxlength'=>10,'placeholder'=>'+7-___-___-__-__','class'=>'phonereg')
                ));
                ?></div>
        </div>
		
		<?php if ($id > 0):?>		
			<?php echo $form->dropDownListRow($model, 'role_id', AdminRole::getAll());?>
			<?php echo $form->toggleButtonRow($model, 'active', array());?>		
		<?php else :?>
			<?php echo $form->hiddenField($model, 'active')?>
			<?php echo $form->hiddenField($model, 'role_id')?>
        <?php endif;?>
		<div class="form-actions">
            <?php $this->widget('bootstrap.widgets.TbButton', array('buttonType' => 'submit', 'label' => 'Сохранить'))?>
        </div>
        <?php $this->endWidget()?>
    </div>
</div>

<div class='popUp changePass'>
    <?php $this->renderPartial('change_password', array(
		'modelPassword' => $modelPassword,
	)); ?>
</div>

