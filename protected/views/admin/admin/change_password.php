<div class="center-block">
    <div class="page-header">
        <h3><?=Yii::t('admin', 'Change Password')?></h3>
    </div>	
	
    <div class="close-button"></div>
    <hr>
    <div>
        <?php $form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
            'type' => 'horizontal',
            'htmlOptions'=>array('class'=>'changePassForm'),
        ))?>
        <?= $form->passwordFieldRow($modelPassword, 'password')?>
        <?= $form->passwordFieldRow($modelPassword, 'confirmPassword')?>
        <div class="form-actions">
            <? $this->widget('bootstrap.widgets.TbButton', array('buttonType' => 'submit', 'label' => Yii::t('admin', 'Save')))?>
        </div>
        <? $this->endWidget()?>
    </div>
</div>