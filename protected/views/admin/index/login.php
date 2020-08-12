    <div class="page-header">
        <h3><?=Yii::t('admin', 'Sign In')?></h3>
    </div>
    <div>
        <? $form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
            'type' => 'horizontal',
            'id'=>'userForm'
        ))?>
        <?= $form->textFieldRow($model, 'username')?>
        <?= $form->passwordFieldRow($model, 'password')?>
		
		<div style="margin-left:200px;">
			<div class="auth-controls">
				<?php echo $form->checkBox($model, 'rememberMe', array('checked' => 'checked', 'class' => 'remember-me'));?>
				<?php echo $form->label($model, 'rememberMe', array('class'=>'remember-label'));?>
			</div>

			<button class="btn btn-success" type="submit"><?=Yii::t('admin', 'Login')?></button>		
		</div>
			
        <? $this->endWidget()?>
    </div>