	<div>
		<h1>Wheel Offset & Backspacing Guide</h1>
		<div class="l-col1 brdtop_col">
			<!-- section 1 -->
		


<section class="all-makes cars_ul bdb_1">
			<h2 class="section-name_2">Choose Your Car</h2>
				<ul>
				<?php $key=1;foreach (AutoMake::getAllFront() as $makeUrl=>$makeTitle):?>
					<li><a title="<?=$makeTitle?> wheels" href="/wheels<?=$makeUrl?>"><?=$makeTitle?></a></li>
					<?php if ($key%7 ==0):?>
					</ul><ul>
					<?php endif;?>
				<?php $key++;endforeach;?>
				</ul>			
		</section>
		



		<section class="seo-text">
			<?=SiteConfig::getInstance()->getValue('wheels_footer_text_block')?>
		</section>
	</div>
	
	<div class="l-col2">
		<section class="">
			<?php $this->widget('application.widgets.BannerWidget', array('banner' => 'vertical')); ?>
		</section>
		
		<section class="right-block">				
			<?php $this->renderPartial('application.views.specs._right_index')?>				
		</section>		
		
	</div>
	
</div>


