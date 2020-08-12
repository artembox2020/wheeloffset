<div>
	<div class="l-col1">
		<!-- section 1 -->
		<section class="times">
		<h1 class="section-name_2">0-60 times acceleration stats</h1>
			<div class="times__container">
				<div class="google_links f_left p_rel">
					<?php $this->widget('application.widgets.BannerWidget', array('banner' => '336x280')); ?>	
				</div>
				<div class="text_size">
					<?=SiteConfig::getInstance()->getValue('0_60_times_header_text_block');?>
				</div>
			</div>
		</section>
		
		<!-- section 2 -->
		<section class="all-makes">
			<h2 class="section-name_2">Browse by make and check your car's 0-60 times</h2>	
			<ul>
				<?php $key=1;foreach ($makes as $makeUrl=>$makeTitle):?>
					<li><a title="<?=$makeTitle?> 0-60 times" href="/0-60-times<?=$makeUrl?>"><?=$makeTitle?></a></li>
					<?php if ($key%7 ==0):?>
					</ul><ul>
					<?php endif;?>
				<?php $key++;endforeach;?>
			</ul>		
		</section>
		<!-- section 3 -->
<br>
<div id="amzn-assoc-ad-9d11cc38-19e9-46a6-a1cb-c91de9b5a349"></div><script async src="//z-na.amazon-adsystem.com/widgets/onejs?MarketPlace=US&adInstanceId=9d11cc38-19e9-46a6-a1cb-c91de9b5a349"></script>

                <?php $this->widget('application.widgets.BannerWidget', array('banner' => '580x400')); ?>
		<section class="make make_fastest-cars">
			<h2 class="section-name_2">The fastest cars in our database</h2>
			<ul class="make__vehicle">
			<?php foreach ($fastests as $fastest):?>
				<li>
					<div class="make__vehicle-image"><a title="<?=$fastest['make_title']?> <?=$fastest['model_title']?> 0-60 times" href="/0-60-times/<?=$fastest['make_alias']?>/<?=$fastest['model_alias']?>/"><img src="<?=$fastest['photo']?>"></a></div>
					<h3><a title="<?=$fastest['make_title']?> <?=$fastest['model_title']?> 0-60 times" href="/0-60-times/<?=$fastest['make_alias']?>/<?=$fastest['model_alias']?>/"><?=$fastest['year']?> <?=$fastest['make_title']?> <?=$fastest['model_title']?></a><span class="acceleration-time">0-60 <?=$fastest['speed']?> sec.</span></h3>
					<ul class="make__vehicle-specs">
						<li>1/4 mile <?=$fastest['mile_time']?> @ <?=$fastest['mile_speed']?> mph</li>
						<li>Engine <?=$fastest['engine']?></li>
						<li><?=$fastest['horsepower']?> hp</li>
						<li><?=$fastest['torque']?> ft. lbs.</li>
					</ul>
				</li>
			<?php endforeach;?>
			</ul>

		</section>
		<!-- section 4 -->

<?php $this->widget('application.widgets.BannerWidget', array('banner' => 'horizontal')); ?>

		<section class="seo-text">
			<?=SiteConfig::getInstance()->getValue('0_60_times_footer_text_block');?>
		</section>
		
		
	</div>
	<div class="l-col2">
		
		<br>		
		<section class="right-block">				
			<?php $this->renderPartial('application.views.specs._right_index')?>		
		</section>		
<?php $this->widget('application.widgets.BannerWidget', array('banner' => 'vertical')); ?>

	</div>
</div>