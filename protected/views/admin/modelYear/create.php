<?php $this->pageTitle = Yii::app()->name.' - ' . Yii::t('admin', 'Creating Model by Year');?>

<div class="container inner-page white img-rounded">
    <div class="page-header">
        <h3><?=Yii::t('admin', 'Creating Model by Year')?></h3>
    </div>
	
	
	<?php $form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
			'id' => 'cityForm',
			'htmlOptions' => array(
				'enctype' => 'multipart/form-data',
			),		
		));		
	
		$this->widget('bootstrap.widgets.TbTabs', array(
            'tabs'=> array(
				array(
					'label'=> Yii::t('admin', 'General'), 
					'active'=>true, 
					'content' => $this->renderPartial('_form', array(
						'model'=> $model, 
						'form'=> $form, 
						'disabled' => false, 
					), 
					true
				)),
				
				array(
					'label'=> Yii::t('admin', 'Tires'), 
					'content' => $this->renderPartial('_tires', array(
						'model'=> $model, 
						'form'=> $form, 
						'disabled' => false, 
					), 
					true
				)),
				
				array(
					'label'=>Yii::t('admin', 'Photos'), 'content' => $this->renderPartial('_photos', array(
                        'model'=> $model,
                    ), 
					true
				))
			)
		));	
		
		$this->renderPartial('application.views.admin._form_actions', array('model'=>$model));
		
	$this->endWidget();?>

</div>