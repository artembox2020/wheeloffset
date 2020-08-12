<?php $this->pageTitle = Yii::app()->name.' - ' . Yii::t('admin', 'Creating Role');?>

<div class="container inner-page white img-rounded">
    <div class="page-header">
        <h3><?=Yii::t('admin', 'Creating Role')?></h3>
    </div>
	
    <?php $form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
        'type' => 'horizontal',
        'id' => 'cityForm'
    ))?>	

        <?php $this->widget('bootstrap.widgets.TbTabs', array(
            'tabs'=>array(
                array(
					'label'=>Yii::t('admin', 'General'), 
					//'active'=>true, 
					'content' => $this->renderPartial('_form', array(
						'model'=> $model, 
						'form'=>$form
					), 
					true
				)),
                array(
					'label'=>Yii::t('admin', 'Permissions'),
					'active'=>true, 
					'content' => $this->renderPartial('_access', array(
						'model'=> $model, 
						'form'=>$form
					), 
					true
				)),
			)
		));?>
		
		<?php $this->renderPartial('application.views.admin._form_actions', array('model'=>$model))?>

	<?php $this->endWidget()?>	
	
</div>