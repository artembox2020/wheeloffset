<?php
/* @var $this UserController */
/* @var $dataProvider CActiveDataProvider*/
/* @var $model User */

$this->pageTitle = Yii::app()->name.' - ' . Yii::t('admin', 'Admins');
?>

<div class="container inner-page white img-rounded">
    <div class="page-header">
        <h3><?=Yii::t('admin', 'Admins')?></h3>
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
		<?php if (Access::is('admin.create')):?>
			<? $this->widget("bootstrap.widgets.TbButtonGroup", array(
				'type' => 'success',
				'buttons' => array(
					array('label' => Yii::t('admin', 'Add'), 'url' => Yii::app()->createUrl("admin/admin/create"), 'icon' => 'file white'),
				),
			))?>
		<?php endif;?>
        <div class="btn clear-filter"><?=Yii::t('admin', 'Clear Search')?></div>
    </div>
    <div class="grid">
        <?php
		
		$template = array();
		if (Access::is('admin.update') || Access::is('admin.delete')) {
			$template = array();
			if (Access::is('admin.update')) $template[] = '{update}';
			if (Access::is('admin.delete')) $template[] = '{delete}';
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
					'class'=>'ELinkUpdateColumn',
					'name' => 'full_name',
					'htmlOptions' => array(
						'access' => 'admin.update', 
					),	
					'filter' => false,
				),					
                array('name' => 'email'),
                array(
					'name' => 'birth_date',
					'value' => '$data->birth_date=="0000-00-00"?"-":$data->birth_date',
					'filter' => false,
				),					
                array(
					'name' => 'role_id',
					'value' => '$data->role?$data->role->title:"-"',
					'filter' => AdminRole::getAll(),
				),
                array(
                    'htmlOptions' => array('nowrap'=>'nowrap', 'style' => 'text-align: center;'),
                    'class'=>'bootstrap.widgets.TbButtonColumn',
                    'template' => implode('', $template),
                ),
            );
		
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