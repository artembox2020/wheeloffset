<?php $this->pageTitle = Yii::app()->name.' - ' . Yii::t('admin', 'Tires');?>

<div class="container inner-page white img-rounded">
    <div class="page-header">
        <h3><?=Yii::t('admin', 'Tires')?></h3>
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
		<?php if (Access::is('tire.create')):?>
			<? $this->widget("bootstrap.widgets.TbButtonGroup", array(
				'type' => 'success',
				'buttons' => array(
					array('label' => Yii::t('admin', 'Create'), 'url' => Yii::app()->createUrl("admin/tire/create"), 'icon' => 'file white'),
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
					'name'=>'vehicle_class_id',
					'value'=>'!empty($data->VehicleClass)?$data->VehicleClass->code:"-"',
					'filter' => CHtml::listData(TireVehicleClass::getAll(), 'id', 'code'),				
				),			
				array(
					'name'=>'section_width_id',
					'value'=>'!empty($data->SectionWidth)?$data->SectionWidth->value:"-"',
					'filter' => CHtml::listData(TireSectionWidth::getAll(), 'id', 'value'),				
				),			
				array(
					'name'=>'aspect_ratio_id',
					'value'=>'!empty($data->AspectRatio)?$data->AspectRatio->value:"-"',
					'filter' => CHtml::listData(TireAspectRatio::getAll(), 'id', 'value'),				
				),			
				array(
					'name'=>'rim_diameter_id',
					'value'=>'!empty($data->RimDiameter)?$data->RimDiameter->value:"-"',
					'filter' => CHtml::listData(TireRimDiameter::getAll(), 'id', 'value'),				
				),			
				array(
					'name'=>'load_index_id',
					'value'=>'!empty($data->LoadIndex)?$data->LoadIndex->index:"-"',
					'filter' => CHtml::listData(TireLoadIndex::getAll(), 'id', 'index'),				
				),	
				array(
					'value'=>'$data->is_rear?"Yes":"No"',
					'name' => 'is_rear',
					'filter' => HtmlHelper::getYesNoFilter(),				
				),	
				array(
					'name'=>'rear_section_width_id',
					'value'=>'!empty($data->RearSectionWidth)?$data->RearSectionWidth->value:"-"',
					'filter' => CHtml::listData(TireSectionWidth::getAll(), 'id', 'value'),				
				),			
				array(
					'name'=>'rear_aspect_ratio_id',
					'value'=>'!empty($data->RearAspectRatio)?$data->RearAspectRatio->value:"-"',
					'filter' => CHtml::listData(TireAspectRatio::getAll(), 'id', 'value'),				
				),			
				array(
					'name'=>'rear_rim_diameter_id',
					'value'=>'!empty($data->RearRimDiameter)?$data->RearRimDiameter->value:"-"',
					'filter' => CHtml::listData(TireRimDiameter::getAll(), 'id', 'value'),				
				),
				
				array(
					'name'=>'is_runflat',
					'value'=>'$data->is_runflat?"runflat":"non-runflat"',
					'filter' => array(0=>'non-runflat', 1=>'runflat'),					
				),				
            );
			
		if (Access::is('tire.update') || Access::is('tire.delete')) {
			$template = array();
			if (Access::is('tire.update')) $template[] = '{update}';
			if (Access::is('tire.delete')) $template[] = '{delete}';
			
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