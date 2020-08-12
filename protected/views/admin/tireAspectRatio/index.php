<?php $this->pageTitle = Yii::app()->name.' - ' . Yii::t('admin', 'Tire Aspect Ratios');?>

<div class="container inner-page white img-rounded">
    <div class="page-header">
        <h3><?=Yii::t('admin', 'Tire Aspect Ratios')?></h3>
    </div>
    
	<?php $this->widget('AdminTbAlert', array(
        'block'=>true, 
        'fade'=>true,
        'closeText'=>'&times;',
        'alerts'=>array(
            'success'=>array('block'=>true, 'fade'=>true, 'closeText'=>'&times;'),
        ))); 
	?> 	
	
	<div class="button">
		<?php if (Access::is('tire.aspect_ratio.create')):?>
			<? $this->widget("bootstrap.widgets.TbButtonGroup", array(
				'type' => 'success',
				'buttons' => array(
					array('label' => Yii::t('admin', 'Create'), 'url' => Yii::app()->createUrl("admin/tireAspectRatio/create"), 'icon' => 'file white'),
				),
			))?>
		<?php endif;?>
        <div class="btn clear-filter"><?=Yii::t('admin', 'Clear Search')?></div>
    </div>
	
	
    <div class="grid">
        <?php 
		
		$columns = array(
				array(
					'name'=>'id',
					'value'=>'$data->id',
					'htmlOptions' => array(
						'width' => 40, 
					),					
				),
				'value',		
				'rank',				
            );
			
		if (Access::is('tire.aspect_ratio.update') || Access::is('tire.aspect_ratio.delete')) {
			$template = array();
			if (Access::is('tire.aspect_ratio.update')) $template[] = '{update}';
			if (Access::is('tire.aspect_ratio.delete')) $template[] = '{delete}';
			
			$columns[] = array(
				'htmlOptions' => array('nowrap'=>'nowrap', 'style' => 'text-align: center;'),
                 'class'=>'bootstrap.widgets.TbButtonColumn',
                 'template' => implode('', $template),
            );
		}
		
		$this->widget('bootstrap.widgets.TbGridView', array(
            'dataProvider' => $model->search(),
            'filter' => $model,
            'type' => 'striped bordered condensed',
            'columns' => $columns,
			'id' => 'list-grid',
			'pager' => array(
				  'class' => 'bootstrap.widgets.TbPager',
				  'displayFirstAndLast' => true,
			),			
			'template'=>'{summary}{items}<div style="float:left">{pager}</div> <div style="float:right;margin-top:15px;"><span style="color:#666;">per page:</span> '.
			CHtml::dropDownList(
				'pageSize',
				$pageSize,
				Yii::app()->params->perPages,
				array('class'=>'change-pageSize', 'style'=>'width: 70px;')) . '</div>',			
        ))?>
    </div>

</div>

<?php Yii::app()->clientScript->registerScript('initPageSize',<<<EOD
    $('.change-pageSize').live('change', function() {
        $.fn.yiiGridView.update('list-grid',{ data:{ pageSize: $(this).val() }})
    });
EOD
,CClientScript::POS_READY); ?>