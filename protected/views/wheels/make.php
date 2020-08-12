<main>
<div class="l-col1">
<!-- section 1 -->
<section class="times clearfix">
	<h2 class="section-name pb18"><?=$make['title']?> wheels</h2>
	<div class="google_links f_left p_rel">
		<?php $this->widget('application.widgets.BannerWidget', array('banner' => '336x280')); ?>
	</div>
	<div class="text_size">
<p></p>	Many users tend to ask themselves will these wheel size fir for my <?=$make['title']?>. It is perfectly normal for users to have challenges when it comes to figuring out the right wheel size for their <?=$make['title']?>.
But with our fitment guide, users can now get all the information they need in as far as all the models of <?=$make['title']?> are concerned.
</p>
	</div>
</section>
<section class="make">
	<h2 class="section-name_2">Wheels Sizes</h2>
 <?php $this->widget('application.widgets.BannerWidget', array('banner' => 'horizontal')); ?>
	<ul class="make__vehicle">
	<?php foreach ($dataModels as $dataModel):?>
		<li>
			<?php if (!empty($dataModel['photo'])):?>
			<div class="make__vehicle-image">
				<!--<a title="<?=$make['title']?> <?=$dataModel['title']?> rims for sale, factory wheel size, bolt pattern, offset" href="/wheels<?=$dataModel['url']?>">-->
					<img src="<?=$dataModel['photo']?>" alt="<?=$make['title']?> <?=$dataModel['title']?> rims and wheels photo"> 
				<!--</a>-->
			</div>
			<?php endif;?>	
			<h4>
				<a title="<?=$make['title']?> <?=$dataModel['title']?> rims for sale, factory wheel size, bolt pattern, offset" href="/wheels<?=$dataModel['url']?>"><?=$make['title']?> <?=$dataModel['title']?> wheels</a>
			</h4>
			<?php if (isset($dataModelsWheels[$dataModel['id']]) && $dataWheels=$dataModelsWheels[$dataModel['id']]):?>
					<?php 
						$rwd_to = array();
						$rwd_from = array();
						$or = array();
						$p_rwd_to = array();
						$p_rwd_from = array();
						$p_or = array();
						
						if (!empty($dataWheels['trd_min'])) { $rwd_from[] = $dataWheels['trd_min'];}
						if (!empty($dataWheels['trw_min'])) { $rwd_from[] = $dataWheels['trw_min'];}
						
						if (!empty($dataWheels['trd_max'])) { $rwd_to[] = $dataWheels['trd_max'];}
						if (!empty($dataWheels['trw_max'])) { $rwd_to[] = $dataWheels['trw_max'];}
						
						if (!empty($dataWheels['or_min'])) { $or[$dataWheels['or_min']] = $dataWheels['or_min'];}
						if (!empty($dataWheels['or_max'])) { $or[$dataWheels['or_max']] = $dataWheels['or_max'];}

						if (!empty($dataWheels['p_rd_min'])) { $p_rwd_from[] = $dataWheels['p_rd_min'];}
						if (!empty($dataWheels['p_rw_min'])) { $p_rwd_from[] = $dataWheels['p_rw_min'];}
						
						if (!empty($dataWheels['p_rd_max'])) { $p_rwd_to[] = $dataWheels['p_rd_max'];}
						if (!empty($dataWheels['p_rw_max'])) { $p_rwd_to[] = $dataWheels['p_rw_max'];}
						
						if (!empty($dataWheels['p_or_min'])) { $p_or[$dataWheels['p_or_min']] = $dataWheels['p_or_min'];}
						if (!empty($dataWheels['p_or_max'])) { $p_or[$dataWheels['p_or_max']] = $dataWheels['p_or_max'];}
							
					?>	
					<ul class="make__vehicle-specs">
					<?php if (!empty($rwd_from) || !empty($rwd_to)):?>	
						<li>Stock rim width <?=implode('x', $rwd_from)?><?php if(!empty($rwd_to) && $rwd_to!==$rwd_from):?><?=(!empty($rwd_from) && !empty($rwd_to))?' &ndash; ':''?><?=implode('x', $rwd_to)?><?php endif;?></li>
					<?php endif;?>	
					
					<?php if (!empty($or)):?>
						<li>Offset <?=(count($or)==2)?'range ':'';?><?=implode(' &ndash; ', $or)?></li>
					<?php endif;?>
					</ul>
					
					<ul class="make__vehicle-specs">
									
					<?php if (!empty($p_or)):?>
						<li>Custom Wheel Offset <?=(count($p_or)==2)?'range ':'';?><?=implode(' &ndash; ', $p_or)?></li>
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