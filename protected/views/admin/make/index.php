<?php $this->pageTitle = Yii::app()->name.' - ' . Yii::t('admin', 'Makes');?>

<div class="container inner-page white img-rounded">
    <div class="page-header">
        <h3><?=Yii::t('admin', 'Makes')?></h3>
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
		<?php if (Access::is('make.create')):?>
			<? $this->widget("bootstrap.widgets.TbButtonGroup", array(
				'type' => 'success',
				'buttons' => array(
					array('label' => Yii::t('admin', 'Create'), 'url' => Yii::app()->createUrl("admin/make/create"), 'icon' => 'file white'),
				),
			))?>
		<?php endif;?>
        <div class="btn clear-filter"><?=Yii::t('admin', 'Clear Search')?></div>
    </div>
	
	
    <div class="grid">
        <?php 
		
		
		$actionButtons = array();
		if (Access::is('make.update')) {
			$actionButtons[] = 	array(
				'id' => 'btn_activate',
				'buttonType' => 'button',
				'type' => 'success',
				'size' => 'small',
				'label' => Yii::t('admin', 'Show'),
				'click' => 'js:function(values){	
					var ids = new Array();
					values.each(function(idx){
						 ids.push($(this).val())	
					});	
					$.ajax({
						type: "POST",
						url: "/admin/make/active/?value=1",
						data: {"ids":ids},
						dataType:"json",
						success: function(response){
							if(response.status == 1){
							   $.fn.yiiGridView.update("list-grid");
							}else{
								alert(response.error);
							}
						}
					});		
				}',
			);
			$actionButtons[] = 	array(
				'id' => 'btn_deactivate',
				'buttonType' => 'button',
				'type' => 'warning',
				'size' => 'small',
				'label' => Yii::t('admin', 'Hide'),
				'click' => 'js:function(values){	
					var ids = new Array();
					values.each(function(idx){
						 ids.push($(this).val())	
					});	
					$.ajax({
						type: "POST",
						url: "/admin/make/active/?value=0",
						data: {"ids":ids},
						dataType:"json",
						success: function(response){
							if(response.status == 1){
							   $.fn.yiiGridView.update("list-grid");
							}else{
								alert(response.error);
							}
						}
					});		
				}',
			);
			
			$actionButtons[] = 	array(
				'id' => 'btn_copy',
				'buttonType' => 'button',
				'type' => 'info',
				'size' => 'small',
				'label' => Yii::t('admin', 'Copy'),
				'click' => 'js:function(values){	
					var ids = new Array();
					values.each(function(idx){
						 ids.push($(this).val())	
					});	
					$.ajax({
						type: "POST",
						url: "/admin/make/copy",
						data: {"ids":ids},
						dataType:"json",
						success: function(response){
							if(response.status == 1){
							   $.fn.yiiGridView.update("list-grid");
							}else{
								alert(response.error);
							}
						}
					});				
				}',
			);
		}
		
		if (Access::is('make.delete')) {
			$actionButtons[] = array(
						'id' => 'btn_delete',
						'buttonType' => 'button',
						'type' => 'danger',
						'size' => 'small',
						'label' => Yii::t('admin', 'Delete'),
						'click' => 'js:function(values){
							var ids = new Array();
							values.each(function(idx){
								 ids.push($(this).val())	
							});	
							$.ajax({
								type: "POST",
								url: "/admin/make/trash/?value=1",
								data: {"ids":ids},
								dataType:"json",
								success: function(response){
									if(response.status == 1){
									   $.fn.yiiGridView.update("list-grid");
									}else{
										alert(response.error);
									}
								}
							});								
						}',
					);
		}
		$bulkActions = array();
		if (!empty($actionButtons)) {
			$bulkActions = array(
				'actionButtons' => $actionButtons,
				'checkBoxColumnConfig' => array(
					'name' => 'id'
				),
			);
		}		
		
		$columns = array(
				array(
					'name'=>'id',
					'value'=>'$data->id',
					'htmlOptions' => array(
						'width' => 60, 
					),					
				),
				array(
					'name' => 'image_preview',
					'value' => 'CHtml::link("<img src=\"{$data->image_preview}\">", "#")',
					'type' => 'raw',
					'htmlOptions' => array(
						'width' => 100, 
						'height' => 60, 
					),	
					'filter' => false,
				),				
				array(
					'class'=>'ELinkUpdateColumn',
					'name' => 'title',
					'htmlOptions' => array(
						'access' => 'make.update', 
					),	
					'type' => 'raw',
				),					
            );
			
		if (Access::is('make.update')) {
			$columns[] = array(
					'class' => 'bootstrap.widgets.TbToggleColumn',
					'name' => 'is_active',
					'filter' => HtmlHelper::getYesNoFilter(),
					'htmlOptions' => array(
						'width' => 80, 
						'style' => 'text-align: center;', 
					),						
				);
		}	
			
		if (Access::is('make.update') || Access::is('make.delete')) {
			$template = array();
			$buttons = array();
			
			if (Access::is('make.update')) {
				$template[] = '{update}';
				$buttons['update'] = array(
                    'url'=>'Yii::app()->createUrl("admin/make/update", array("id"=>$data->id))',
                );
			}
			
			if (Access::is('make.delete')) {
				$template[] = '{delete}';
				$buttons['delete'] = array(
                    'url'=>'Yii::app()->createUrl("admin/make/trash", array("ids"=>array($data->id), "value"=>1))',
                );				
			}
			
			$columns[] = array(
				'htmlOptions' => array('nowrap'=>'nowrap', 'style' => 'text-align: center;'),
                 'class'=>'bootstrap.widgets.TbButtonColumn',
                 'template' => implode('', $template),
                 'buttons' => $buttons,
            );
		
		}
		
		$this->widget('bootstrap.widgets.TbExtendedGridView', array(
            'dataProvider' => $model->search(),
			'ajaxUrl'=> $this->createUrl('/admin/make/index'),
            'filter' => $model,
            'type' => 'striped bordered condensed',
            'columns' => $columns,
			'id' => 'list-grid',
			'bulkActions' => $bulkActions,
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