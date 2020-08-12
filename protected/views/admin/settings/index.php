<?php $this->pageTitle = Yii::app()->name.' - ' . Yii::t('admin', 'Settings');?>

<div class="container inner-page white img-rounded">
    <div class="page-header">
        <h3><?=Yii::t('admin', 'Settings')?></h3>
    </div>
	
	<?php $this->widget('AdminTbAlert', array(
        'block'=>true, 
        'fade'=>true,
        'closeText'=>'&times;',
        'alerts'=>array(
            'success'=>array('block'=>true, 'fade'=>true, 'closeText'=>'&times;'),
        ))); 
	?> 		
	
    <?php $form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
        'type' => 'horizontal',
        'id' => 'cityForm'
    ))?>	

        <?php $this->widget('bootstrap.widgets.TbTabs', array(
            'tabs'=>array(
                array(
					'label'=>Yii::t('admin', 'General'), 
					'active'=>false, 
					'content' => $this->renderPartial('_general', array(
						'form'=>$form,
						'values'=>$values,
					), 
					true
				)),
                array(
					'label'=>Yii::t('admin', 'Seo'),
					'active'=>true, 
					'content' => $this->renderPartial('_seo', array(
						'form'=>$form,
						'values'=>$values,
					), 
					true
				)),
                array(
					'label'=>Yii::t('admin', 'Static pages'),
					'active'=>false, 
					'content' => $this->renderPartial('_static_pages', array(
						'form'=>$form,
						'values'=>$values,
					), 
					true
				)),
			)
		));?>

		<?php echo CHtml::htmlButton(Yii::t('admin', 'Save'), array('class'=>'btn btn-success', 'type'=>'submit'));?>
	
	<?php $this->endWidget()?>	
	
</div>