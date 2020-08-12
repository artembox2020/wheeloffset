<?php $years = AutoModelYear::getListYears((int)$model->model_id);?>	
	
	<div class="control-group">
		<?php $model->post_tires = $model->getPost_tires()?>
			<label class="control-label required" for="AutoModelYear_post_tires"><?php echo $model->getAttributeLabel('post_tires')?></label>
			<div class="controls">
			<?php $this->widget('ext.chosen.Chosen',array(
				'model' => $model, 
				'attribute' => 'post_tires', 
				'data' => Tire::model()->getList(),
				'multiple' => true,
				'itemUrl' => '/admin/tire/update?id=',
				'noResults' => Yii::t('admin', 'Not found'),
				'noResultsUrl' => '/admin/tire/create',
				'placeholderMultiple'=>$model->getAttributeLabel('post_tires'),
				'htmlOptions'=>array(
					'style' => 'width:800px;'
				),		   
			));?>
			</div>
	</div>
	
	<?php if (!$model->isNewRecord):?>
	<div class="control-group">
		<?php $model->post_tires_related = $model->getPost_tires_related()?>
			<label class="control-label required">Copy to</label>
			<div class="controls" style="margin-left: 30px;">
			<?php 	
				$modelYearTires = $model->getPost_tires(false); 
				foreach ($years as $item): if($item['id']==$model->id){continue;}?>
				<label><input type="checkbox" name="AutoModelYear[post_tires_related][]" value="<?=$item['id']?>" <?=in_array($item['id'], $model->post_tires_related)?'checked="checked"':''?>> <span <?=($modelYearTires==$item['tires'])?'style="font-weight: bold;"':''?>><?=$item['year']?></span> <?=(count($item['tires'])>0)?'('.count($item['tires']).')':''?></label>
			<?php endforeach;?>
			</div>
	</div>
	<?php endif;?>
	
	<hr/>
	
	<div class="control-group">
		<div class="controls">
			Rim Width &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			
			<?php echo $form->dropDownList($model, 'tire_rim_diameter_from_id', TireRimDiameter::getList(),array('empty'=>'', 'style'=>'width:70px;'))?> x
			<?php echo $form->dropDownList($model, 'rim_width_from_id', RimWidth::getAll(),array('empty'=>'', 'style'=>'width:70px;'))?> &nbsp;&nbsp;&nbsp; to &nbsp;&nbsp;&nbsp; 
			<?php echo $form->dropDownList($model, 'tire_rim_diameter_to_id', TireRimDiameter::getList(),array('empty'=>'', 'style'=>'width:70px;'))?> x
			<?php echo $form->dropDownList($model, 'rim_width_to_id', RimWidth::getAll(),array('empty'=>'', 'style'=>'width:70px;'))?>
		</div>
	</div>	
	
	<div class="control-group">
		<div class="controls">
			Rim Offset &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			<?php echo $form->dropDownList($model, 'offset_range_from_id', RimOffsetRange::getAll(),array('empty'=>'', 'style'=>'width:70px;'))?> to
			<?php echo $form->dropDownList($model, 'offset_range_to_id', RimOffsetRange::getAll(),array('empty'=>'', 'style'=>'width:70px;'))?> 
		</div>
	</div>	
	
	<div class="control-group">
		<div class="controls">
			<?=$model->getAttributeLabel('bolt_pattern_id')?> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			<?php echo $form->dropDownList($model, 'bolt_pattern_id', RimBoltPattern::getAll(),array('empty'=>'', 'style'=>'width:170px;'))?>
		</div>
	</div>	
	
	<div class="control-group">
		<div class="controls">
			<?=$model->getAttributeLabel('thread_size_id')?> &nbsp;&nbsp;&nbsp;&nbsp;
			<?php echo $form->dropDownList($model, 'thread_size_id', RimThreadSize::getAll(),array('empty'=>'', 'style'=>'width:170px;'))?>
		</div>
	</div>	
	
	<div class="control-group">
		<div class="controls">
			<?=$model->getAttributeLabel('center_bore_id')?> &nbsp;&nbsp;&nbsp;&nbsp;
			<?php echo $form->dropDownList($model, 'center_bore_id', RimCenterBore::getAll(),array('empty'=>'', 'style'=>'width:170px;'))?>
		</div>
	</div>	
	
	<?php if (!$model->isNewRecord):?>
	<div class="control-group">
		<?php $model->post_rims_related = $model->getPost_rims_related()?>
			<label class="control-label required">Copy to</label>
			<div class="controls" style="margin-left: 30px;">
			<?php $rimModels = $model->getRimModels();	
				foreach ($years as $item): 
				$rimData = $rimModels[$item['id']];
				if($item['id']==$model->id){continue;}?>
				<label><input type="checkbox" name="AutoModelYear[post_rims_related][]" value="<?=$item['id']?>" <?=in_array($item['id'], $model->post_rims_related)?'checked="checked"':''?>> <span
				<?php if ($rimData['tire_rim_diameter_from_id'] == $model->tire_rim_diameter_from_id &&
					      $rimData['rim_width_from_id'] == $model->rim_width_from_id &&
					      $rimData['tire_rim_diameter_to_id'] == $model->tire_rim_diameter_to_id &&
					      $rimData['rim_width_to_id'] == $model->rim_width_to_id &&
					      $rimData['offset_range_from_id'] == $model->offset_range_from_id &&
					      $rimData['offset_range_to_id'] == $model->offset_range_to_id &&
					      $rimData['bolt_pattern_id'] == $model->bolt_pattern_id &&
					      $rimData['thread_size_id'] == $model->thread_size_id &&
					      $rimData['center_bore_id'] == $model->center_bore_id
				):?>
					style="font-weight:bold;"
				<?php endif;?>
				><?=$item['year']?></span></label>
			<?php endforeach;?>
			</div>
	</div>
	<?php endif;?>	
