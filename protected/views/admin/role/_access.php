<?php $accessItems = Access::getTree();
$checkedIds = $model->getAccessForForm();
?>

<label> <input type="checkbox" id="checkbox-on-off-all"> <?=Yii::t('admin', 'check / uncheck all')?></label>

<ul class="access-list">
<?php foreach ($accessItems as $accessItem):?>
	<li>
		<label>
			<input class="js-level-0" type="checkbox" name="AdminRole[post_access][]" value="<?php echo $accessItem['id']?>" <?=in_array($accessItem['id'],$checkedIds)?'checked="checked"':''?>>
			<?php echo $accessItem['title']?>
		</label>
		
		<?php if (isset($accessItem['items']) && is_array($accessItem['items'])):?>
		<ul>
			<?php foreach ($accessItem['items'] as $accessItemLevel1):?>
			<li>
				<label>
					<input class="js-level-1" type="checkbox" name="AdminRole[post_access][]" value="<?php echo $accessItemLevel1['id']?>" <?=in_array($accessItemLevel1['id'],$checkedIds)?'checked="checked"':''?>>
					<?php echo $accessItemLevel1['title']?>
				</label>
				
				<?php if (isset($accessItemLevel1['items']) && is_array($accessItemLevel1['items'])):?>
				<ul>
					<?php foreach ($accessItemLevel1['items'] as $accessItemLevel2):?>
					<li>
						<label>
							<input class="js-level-2" type="checkbox" name="AdminRole[post_access][]" value="<?php echo $accessItemLevel2['id']?>" <?=in_array($accessItemLevel2['id'],$checkedIds)?'checked="checked"':''?>>
							<?php echo $accessItemLevel2['title']?>
						</label>

						<?php if (isset($accessItemLevel2['items']) && is_array($accessItemLevel2['items'])):?>
						<ul>
							<?php foreach ($accessItemLevel2['items'] as $accessItemLevel3):?>
							<label>
								<input class="js-level-3" type="checkbox" name="AdminRole[post_access][]" value="<?php echo $accessItemLevel3['id']?>" <?=in_array($accessItemLevel3['id'],$checkedIds)?'checked="checked"':''?>>
								<?php echo $accessItemLevel3['title']?>
							</label>								
							<?php endforeach;?>
						</ul>
						<?php endif;?>
					</li>
					<?php endforeach;?>
				</ul>
				<?php endif;?>
				
			</li>
			<?php endforeach;?>
		</ul>
		<?php endif;?>
	</li>
<?php endforeach;?>
</ul>

<style>
.access-list, .access-list ul{
	list-style-type: none;
}
.access-list li ul li ul li {
}
</style>

<script>
$('.js-level-1').change(function(){
	if ($(this).is(':checked'))
		$(this).parent().parent().parent().parent().find('.js-level-0').attr('checked', true);
})
$('.js-level-2').change(function(){
	if ($(this).is(':checked')) {
		$(this).parent().parent().parent().parent().find('.js-level-1').attr('checked', true);
		$(this).parent().parent().parent().parent().parent().parent().find('.js-level-0').attr('checked', true);
	}
})
$('#checkbox-on-off-all').change(function(){
	checked = $(this).is(':checked');
	$('.access-list input[type="checkbox"]').each(function(i,v) {
		$(this).attr('checked', checked);
	})
})
</script>