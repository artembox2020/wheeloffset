<div class="l">
	<div class="l-col1">
		<section class="seo-text">
			<h3><?=$title?></h3>
			<?=$content?>
		</section>
	</div>
	<div class="l-col2">
		<?php $this->widget('application.widgets.BannerWidget', array('banner' => 'vertical')); ?>
	</div>
</div>