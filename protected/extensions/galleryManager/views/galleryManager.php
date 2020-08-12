<?php
/**
 * @var $this GalleryManager
 * @var $model GalleryPhoto
 *
 * @author Bogdan Savluk <savluk.bogdan@gmail.com>
 */
?>
<?php echo CHtml::openTag('div', $this->htmlOptions); ?>
<!-- Gallery Toolbar -->
<div class="gform">
        <span class="btn btn-success fileinput-button">
            <i class="icon-plus icon-white"></i>
            <?php echo Yii::t('galleryManager.main', 'Add images…');?>
            <input class="afile" accept="image/*" multiple="multiple" name="GalleryPhoto[image]" id="GalleryPhoto_image" type="file">
        </span>

    <span class="btn disabled edit_selected"><?php echo Yii::t('galleryManager.main', 'Edit selected');?></span>
    <span class="btn disabled remove_selected"><?php echo Yii::t('galleryManager.main', 'Remove selected');?></span>

    <label for="select_all_<?php echo $this->id?>" class="btn">
        <input type="checkbox" style="margin-bottom: 4px;"
               id="select_all_<?php echo $this->id?>"
               class="select_all"/>
        <?php echo Yii::t('galleryManager.main', 'Select all');?>
    </label>
	
	
    <?php
    echo CHtml::hiddenField('returnUrl', Yii::app()->getRequest()->getUrl() . '#' . $this->id);
    ?>
</div>
<hr/>
<!-- Gallery Photos -->
<div class="sorter">
    <div class="images"></div>
</div>
<script type="text/javascript">
	$('.images').on('click','.caption', function() {
		$(this).parent().find('.editPhoto').click();
	})
	/*$('.images').on('click','.image-preview', function(e) {
		$('.photo-select', $(this).parent()).click();
		e.stopPropagation();
	})*/
	
</script>
<!-- Modal window to edit photo information -->
<div class="modal hide editor-modal"> <!-- fade removed because of opera -->
    <div class="modal-header">
        <a class="close" data-dismiss="modal">×</a>

        <h3><?php echo Yii::t('galleryManager.main', 'Edit information')?></h3>
    </div>
    <div class="modal-body">
        <div class="form"></div>
    </div>
    <div class="modal-footer">
        <a href="#" class="btn btn-success save-changes">
            <?php echo Yii::t('galleryManager.main', 'Save changes')?>
        </a>
        <a href="#" class="btn" data-dismiss="modal"><?php echo Yii::t('galleryManager.main', 'Close')?></a>
    </div>
</div>
<?php echo CHtml::closeTag('div'); ?>

<script>
$('#btn_set_cover').click(function(){
	if($('.photo-select:checked').size() != 1) {
		alert('Нужно выбрать только одно фото');
		return;
	}
	
	var photo_id = $('.ui-sortable').find('.photo-select:checked').parent().find('input[type="hidden"]').val();
	
	$.post('/?r=newsGallery/setCover', {'photo_id':photo_id, 'news_id':<?=Yii::app()->request->getParam('id')?>}, function(response){
		if (response.success) {
			window.location = window.location;
		} else {
			alert(response.error);
		}
    }, 'json')
	
	
})

$('.set_watemark').click(function(){
	value = $(this).is(':checked')?1:0;
	$.post('/?r=site/setWatemark', {'value':value}, function(response){
		
    }, 'text')
})

</script>