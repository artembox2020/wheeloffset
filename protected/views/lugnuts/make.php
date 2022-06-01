<main>
<div class="l-col1">
<!-- section 1 -->
<section class="times clearfix">
	<h2 class="section-name pb18"><?=$make['title']?> lug nut sizes</h2>
	<div class="google_links f_left p_rel">
		<?php $this->widget('application.widgets.BannerWidget', array('banner' => '336x280')); ?>
	</div>
	<div class="text_size">
<p></p>	Many users tend to ask themselves will these lug nut size fir for my <?=$make['title']?>. It is perfectly normal for users to have challenges when it comes to figuring out the right lug nut size for their <?=$make['title']?>.
But with our fitment guide, users can now get all the information they need in as far as all the models of <?=$make['title']?> are concerned.
</p>
	</div>
</section>
<section class="make">
	<h2 class="section-name_2">Lug nut sizes</h2>
 <?php $this->widget('application.widgets.BannerWidget', array('banner' => 'horizontal')); ?>
	<ul class="make__vehicle">
	<?php foreach ($dataModels as $dataModel):?>
		<li>
			<?php if (!empty($dataModel['photo'])):?>
			<div class="make__vehicle-image">
				<!--<a title="<?=$make['title']?> <?=$dataModel['title']?> rims for sale, factory wheel size, bolt pattern, offset" href="/Lug nut sizes<?=$dataModel['url']?>">-->
					<img src="<?=$dataModel['photo']?>" alt="<?=$make['title']?> <?=$dataModel['title']?> rims and lug nut sizes photo">
				<!--</a>-->
			</div>
			<?php endif;?>	
			<h4>
				<a title="<?=$make['title']?> <?=$dataModel['title']?> rims for sale, factory wheel size, bolt pattern, offset" href="/lugnuts<?=$dataModel['url']?>"><?=$make['title']?> <?=$dataModel['title']?> lug nut sizes</a>
			</h4>
			<?php if (isset($dataModelsLugnuts[$dataModel['id']]) && $dataLugnuts=$dataModelsLugnuts[$dataModel['id']]):?>
					<ul class="make__vehicle-specs lugnuts">
					<?php if (!empty($dataLugnuts['center_bore_sizes'])):?>
						<li>Center Bore Size<?= count($dataLugnuts['center_bore_sizes']) > 1 ? 's' : '' ?>: <?= join(' | ', $dataLugnuts['center_bore_sizes']) ?></li>
					<?php endif;?>	
					
					<?php if (!empty($dataLugnuts['thread_sizes'])):?>
						<li>Thread Diameter<?= count($dataLugnuts['thread_sizes']) > 1 ? 's' : '' ?>: <?= join(' | ', $dataLugnuts['thread_sizes']) ?></li>
					<?php endif;?>
					</ul>
			<?php endif;?>
		</li>
	<?php endforeach;?>	
	</ul>

	
	
</section>

</div>

	<div class="l-col2">
		<?php $this->widget('application.widgets.BannerWidget', array('banner' => 'vertical')); ?>

		<section class="right-block">
			<?php $this->renderPartial('application.views.specs._right_make', array('make'=>$make))?>
		</section>	
	</div>
</main>