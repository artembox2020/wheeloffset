<main>
	<div class="l-col1">
		<!-- section 1 -->
				<section class="times">
			<h1 class="section-name_2"><?=$make['title']?> <?=$model['title']?> 0-60 times</h1>
			<div class="times__container">
				<div class="google_links f_left p_rel">
					<?php $this->widget('application.widgets.BannerWidget', array('banner' => '336x280')); ?>	
				</div>
				<div class="text_size">
					<?=$description?>
				</div>
		</section>
<br>
		<div id="amzn-assoc-ad-9d11cc38-19e9-46a6-a1cb-c91de9b5a349"></div><script async src="//z-na.amazon-adsystem.com/widgets/onejs?MarketPlace=US&adInstanceId=9d11cc38-19e9-46a6-a1cb-c91de9b5a349"></script>

		
		<?php if (!empty($completionsTimes)):?>
		<?php foreach ($completionsTimes as $completionsTime):?>
		<section class="table-container">	
			<h2 class="section-name_2"><?=$completionsTime['year']?> <?=$make['title']?> <?=$model['title']?> 0-60 times, all trims</h2>
			<table>
            <tr><td><b>Trim, HP, Engine, Transmission</b></td><td><b>0-60 times</b></td><td><b>1/4 mile times</b></td></tr>
			<?php foreach ($completionsTime['items'] as $item):?>
				<tr>
					<td>
						<?php $expl = explode('@', $item['specs_horsepower']); $hp = trim($expl[0])?>
						<?=$item['title']?><?php if (!empty($hp)):?>,<?= str_replace('hp', '', trim($hp))?> hp <?php endif;?><br/>
						<small><?php 
						$engine = trim(AutoCompletion::getSpecsOptionTitle(AutoSpecs::SPEC_ENGINE, $item['specs_engine']));
						$transmission = AutoCompletion::getSpecsOptionTitle(AutoSpecs::SPEC_TRANSMISSION, $item['specs_transmission'])?>
						<?=!empty($item['specs_turbocharger'])?'turbo, ':''?><?php if (!empty($engine)):?><?=($engine!='Hybrid Electric Motor')?str_replace('Electric Motor', '', $engine):$engine?><?php endif;?><?php if (!empty($transmission)):?>, <?=str_replace('w/OD', '', $transmission)?><?php endif;?></small>
					</td>
					<td><h3>
						<?=(float)$item['specs_0_60mph__0_100kmh_s_']?> sec
					</h3></td>
					<td>	
						<?=(float)$item['specs_1_4_mile_time']?> @ <?=(float)$item['specs_1_4_mile_speed']?> mph
					</td>					
				</tr>
			<?php endforeach;?>
			</table>
		</section>		
		<?php endforeach;?>
		<?php endif;?>		
<br><br>
<div id="amzn-assoc-ad-9d11cc38-19e9-46a6-a1cb-c91de9b5a349"></div><script async src="//z-na.amazon-adsystem.com/widgets/onejs?MarketPlace=US&adInstanceId=9d11cc38-19e9-46a6-a1cb-c91de9b5a349"></script>

		<section class="table-container">
			<h2 class="section-name_2"><?=$make['title']?> <?=$model['title']?> 0-60 mph acceleration across years</h2>

			<table>
				<tr><td><b>Year of a Model</b></td><td><b>0-60 times</b></td><td><b>1/4 mile times</b></td></tr>
				<?php foreach ($models as $item):?>
				 
                                 <tr>
					<td><?=$item['year']?></td>
					<td><h3>
					<?php if ($item['0_60_times']['mmax'] == $item['0_60_times']['mmin']):?>
						<?=$item['0_60_times']['mmin']?>
					<?php else:?>
						<?=$item['0_60_times']['mmin']?> - <?=$item['0_60_times']['mmax']?>
					<?php endif;?>	
						sec
					</h3></td>
					<td>
						<?php if ($item['mile_time']['min'] == 0):?>
							-
						<?php else:?>
							<?php if ($item['mile_time']['min'] == $item['mile_time']['max']):?>
								<?=$item['mile_time']['min']?> @ <?=$item['mile_speed']['min']?> mph
							<?php else:?>
								<?=$item['mile_time']['min']?> @ <?=$item['mile_speed']['max']?> - <?=$item['mile_time']['max']?> @ <?=$item['mile_speed']['min']?> mph
							<?php endif;?>	
						<?php endif;?>
					</td>					
				</tr>
			<?php endforeach;?>
			</table>
		</section>		
<br>
               <?php $this->widget('application.widgets.BannerWidget', array('banner' => 'horizontal')); ?>

<!--<?php if (!empty($competitors)):?>			   
	<section class="make">
	 <h2 class="section-name_2"><?=$make['title']?> <?=$model['title']?> competitors' 0-60 mph acceleration</h2>
	 <ul class="make__vehicle">
	 <?php foreach ($competitors as $competitor):?> 
	  <li>
	   <div class="make__vehicle-image">
		<a title="<?= $competitor['make']?> <?= $competitor['model']?> 0-60" href="/0-60-times/<?= $competitor['make_alias']?>/<?= $competitor['model_alias']?>/">
		 <img src="<?= $competitor['year']['photo_270']?>">
		</a>
	   </div>
	   <h3>
		<a href="/0-60-times/<?= $competitor['make_alias']?>/<?= $competitor['model_alias']?>/"><?= $competitor['make']?> <?= $competitor['model']?> 0-60</a>
	   </h3>
	   
	   <ul class="make__vehicle-specs">    
		<li>
			0-60	
			<?php if ($competitor['0_60_times']['mmax'] == $competitor['0_60_times']['mmin']):?>
				<?=$competitor['0_60_times']['mmin']?>
			<?php else:?>
				<?=$competitor['0_60_times']['mmin']?> - <?=$competitor['0_60_times']['mmax']?>
			<?php endif;?>	
			sec
		</li>
		<li>quarter mile 

						<?php if ($competitor['mile_time']['min'] == 0):?>
							-
						<?php else:?>
							<?php if ($competitor['mile_time']['min'] == $competitor['mile_time']['max']):?>
								<?=$competitor['mile_time']['min']?> @ <?=$competitor['mile_speed']['min']?> mph
							<?php else:?>
								<?=$competitor['mile_time']['min']?> @ <?=$competitor['mile_speed']['max']?> - <?=$competitor['mile_time']['max']?> @ <?=$competitor['mile_speed']['min']?> mph
							<?php endif;?>	
						<?php endif;?>
		
		
		</li>
	   </ul>      
	  </li>
	  <?php endforeach;?>
	 </ul>
	</section>

<?php endif;?>			   
		-->	   
	
		
		
		<?php $this->renderPartial('application.views.site._reviews', array(
			'items' => ReviewVsModelYear::getTextModel(ReviewVsModelYear::MARKER_060, $model['id']),
		)); ?>		
		
		<section class="seo-text">
			<?= !empty($model['text_times']) ? $model['text_times'] : $descriptionFooter?>
		</section>		
		
<br>	





	</div>
	<div class="l-col2">
		
		<br>
		<?php $this->renderPartial('application.views.specs._right_model', array(
			'lastModelYear'=>$lastModelYear,
			'make'=>$make,
			'model'=>$model,
		))?>		
		
		<?php $this->widget('application.widgets.BannerWidget', array('banner' => 'vertical')); ?>
		
	</div>
	</div>
</main>