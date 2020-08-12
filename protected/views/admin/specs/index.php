<?php $this->pageTitle = Yii::app()->name.' - ' . Yii::t('admin', 'Specs');?>

<div class="container inner-page white img-rounded">
    <div class="page-header">
        <h3><?=Yii::t('admin', 'Specs')?></h3>
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
		<?php if (Access::is('specs.create')):?>
			<? $this->widget("bootstrap.widgets.TbButtonGroup", array(
				'type' => 'success',
				'buttons' => array(
					array('label' => Yii::t('admin', 'Create'), 'url' => Yii::app()->createUrl("admin/specs/create"), 'icon' => 'file white'),
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
				array(
					'class'=>'ELinkUpdateColumn',
					'name' => 'title',
					'htmlOptions' => array(
						'access' => 'specs.update', 
					),	
				),
				array(
					'name' => 'group_id',
					'value' => '$data->Group?$data->Group->title:"-"',	
					'filter' => AutoSpecsGroup::getAll(),
					'htmlOptions' => array(
						'width' => 80, 
					),						
				),				
				'alias',				
				array(
					'name' => 'type',
					'value' => '$data->typeTitle',	
					'filter' => AutoSpecs::getTypes(),
					'htmlOptions' => array(
						'width' => 80, 
					),						
				),				
				array(
					'name' => 'is_required',
					'value' => 'HtmlHelper::getYesNoValue($data->is_required)',	
					'filter' => HtmlHelper::getYesNoFilter(),
					'htmlOptions' => array(
						'width' => 60, 
					),						
				),				
				array(
					'name' => 'is_filter',
					'value' => 'HtmlHelper::getYesNoValue($data->is_filter)',	
					'filter' => HtmlHelper::getYesNoFilter(),
					'htmlOptions' => array(
						'width' => 60, 
					),						
				),	
				array(
					'name'=>'append',
					'value'=>'$data->append',
					'htmlOptions' => array(
						'width' => 60, 
					),					
				),				
				array(
					'name'=>'rank',
					'value'=>'$data->rank',
					'htmlOptions' => array(
						'width' => 40, 
					),					
				),				
            );
			
		if (Access::is('specs.update') || Access::is('specs.delete')) {
			$template = array();
			if (Access::is('specs.update')) $template[] = '{update}';
			if (Access::is('specs.delete')) $template[] = '{delete}';
			
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