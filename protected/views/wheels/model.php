<main>
	<div class="l-col1">
		<section class="years_box make">
			<h1 class="section-name_2"><?=$make['title']?> <?=$model['title']?> Wheel Offset Chart</h1>
			<ul class="years_list">
			<?php foreach ($wheelsDataItems as $wheelsDataItem):?>
				<?php foreach ($wheelsDataItem['years'] as $y):?>
					<li class="years_list_item"><a href="#<?=$y?>" class="btn years_list_link"><?=$y?></a></li>
				<?php endforeach;?>
			<?php endforeach;?>
			</ul>
		</section>
			
		<section class="times clearfix">

			<div class="google_links f_left p_rel">
				<?php $this->widget('application.widgets.BannerWidget', array('banner' => '336x280')); ?>	
			</div>
			<div class="text_size">
			
			<p>We are going to give you some tips on <?=$make['title']?> <?=$model['title']?> wheel offset you should know before you pick up a set of wheels for your vehicle.</p>

<p>A good wheel in the right setup is going to make the difference between something that just was not thought out and pretty much guessed.</p>

<p>If you are trying to figure out what your set of wheels for <?=$make['title']?> <?=$model['title']?> should be and after visiting tons of forums still left with more questions than answers, than this is your final stop. Lots of people do not understand the principles of diameter, width and offsets.</p>


			</div>
		</section>
		

        <section class="seo-text">
<?=  $model['text_wheels'] ?>
        </section>	



		<?php foreach ($wheelsDataItems as $wheelsDataItem):?>
		<section class="table-container">
			<img alt="<?=$make['title']?> <?=$model['title']?> bolt pattern" src="<?=AutoModelYear::thumb($wheelsDataItem['ids'][0], 150, null, 'resize')?>"><h4 class="title_tire"><?=$make['title']?> <?=$model['title']?> Wheel Offset</h4>
			<?php foreach ($wheelsDataItem['years'] as $y):?>
			<a name="<?=$y?>" style="color:#000;"><small><?=$y?></small></a>
			<?php endforeach;?><br>
			<a rel="nofollow" href="https://www.anrdoezrs.net/links/100377875/type/dlg/https://www.tirerack.com/content/tirerack/desktop/en/tires.html">Shop for Tires</a> at Tire Rack.
			<table>
			<tbody>
				
				<?php 
					$stockWheelOffset = array();
					if (!empty($wheelsDataItem['y_ror_min'])) $stockWheelOffset[] = $wheelsDataItem['y_ror_min'];
					if (!empty($wheelsDataItem['y_ror_max']) && $wheelsDataItem['y_ror_min'] != $wheelsDataItem['y_ror_max']) $stockWheelOffset[] = $wheelsDataItem['y_ror_max'];
				
				?>
				
				<?php if (!empty($stockWheelOffset)):?>
				<tr>
					<td>Stock wheel offset</td>
					<td><?=implode(' to ', $stockWheelOffset)?> mm<br> <a target="_blank" rel="nofollow" href="http://www.amazon.com/gp/search?ie=UTF8&camp=1789&creative=9325&index=automotive&keywords=wheel%20spacers&linkCode=ur2&tag=wheeloffset-20&linkId=SAMMREJTPRAVTXQ4"><img style="vertical-align:middle;padding-right: 5px;" src="https://upload.wikimedia.org/wikipedia/commons/thumb/4/4a/Amazon_icon.svg/16px-Amazon_icon.svg.png"><b>Try Wheel Spacers</b></a><img src="http://ir-na.amazon-adsystem.com/e/ir?t=wheeloffset-20&l=ur2&o=1" width="1" height="1" border="0" alt="" style="border:none !important; margin:0px !important;" /></td>
				</tr>
				<?php endif;?>
				
				<?php if (!empty($wheelsDataItem['bolt_pattern'])):?>
				<tr>
					<td><?=$model['title']?> Bolt Pattern</td>
					<td>PCD <?=$wheelsDataItem['bolt_pattern']?><br> <a target="_blank" rel="nofollow" href="http://www.amazon.com/gp/search?ie=UTF8&camp=1789&creative=9325&index=automotive&keywords=wheel%20adapters&linkCode=ur2&tag=wheeloffset-20&linkId=SAMMREJTPRAVTXQ4"><img style="vertical-align:middle;padding-right: 5px;" src="https://upload.wikimedia.org/wikipedia/commons/thumb/4/4a/Amazon_icon.svg/16px-Amazon_icon.svg.png"><b>See Adapters</b></a><img src="http://ir-na.amazon-adsystem.com/e/ir?t=wheeloffset-20&l=ur2&o=1" width="1" height="1" border="0" alt="" style="border:none !important; margin:0px !important;" /></td>
					
				</tr>
				<?php endif;?>
				
				
				<?php 
					$customOffsetRange = array();
					
					if (is_numeric($wheelsDataItem['p_ror_min']) || is_numeric($wheelsDataItem['p_rear_ror_min'])) {
						if (is_numeric($wheelsDataItem['p_ror_min']) && is_numeric($wheelsDataItem['p_rear_ror_min'])) {
						 $customOffsetRange[] = min($wheelsDataItem['p_ror_min'], $wheelsDataItem['p_rear_ror_min']);
						} else if (is_numeric($wheelsDataItem['p_ror_min'])) {
							$customOffsetRange[] = $wheelsDataItem['p_ror_min'];
						} else if (is_numeric($wheelsDataItem['p_rear_ror_min'])) {
							$customOffsetRange[] = $wheelsDataItem['p_rear_ror_min'];
						}
					}
					if (is_numeric($wheelsDataItem['p_ror_max']) || is_numeric($wheelsDataItem['p_rear_ror_max'])) {
						if (is_numeric($wheelsDataItem['p_ror_max']) && is_numeric($wheelsDataItem['p_rear_ror_max'])) {
							$customOffsetRange[] = max($wheelsDataItem['p_ror_max'], $wheelsDataItem['p_rear_ror_max']);
						} else if (is_numeric($wheelsDataItem['p_ror_max'])) {
							$customOffsetRange[] = $wheelsDataItem['p_ror_max'];
						} else if (is_numeric($wheelsDataItem['p_rear_ror_max'])) {
							$customOffsetRange[] = $wheelsDataItem['p_rear_ror_max'];
						}
					}
				?>
				
				<?php if (!empty($customOffsetRange)):?>
				<tr>
					<td>Custom Offset Range</td>
					<td><?=implode(' to ', $customOffsetRange)?> mm</td>
					
				</tr>
				<?php endif;?>

				<?php if (!empty($wheelsDataItem['center_bore'])):?>
				<tr>
					<td>Center bore (hub bore)
</td>
					<td><?=$wheelsDataItem['center_bore']?><br> <a target="_blank" rel="nofollow" href="http://www.amazon.com/gp/search?ie=UTF8&camp=1789&creative=9325&index=automotive&keywords=Hub%20Centric%20Rings&linkCode=ur2&tag=wheeloffset-20&linkId=SAMMREJTPRAVTXQ4"><img style="vertical-align:middle;padding-right: 5px;" src="https://upload.wikimedia.org/wikipedia/commons/thumb/4/4a/Amazon_icon.svg/16px-Amazon_icon.svg.png"><b>Use Hub Centric Rings</b></a><img src="http://ir-na.amazon-adsystem.com/e/ir?t=wheeloffset-20&l=ur2&o=1" width="1" height="1" border="0" alt="" style="border:none !important; margin:0px !important;" /></td>
				</tr>
				<?php endif;?>	
				
				<?php if (!empty($wheelsDataItem['thread_size'])):?>
				<tr>
					<td>Thread Diameter</td>
					<td><?=$wheelsDataItem['thread_size']?><br> <a target="_blank" rel="nofollow" href="http://www.amazon.com/gp/search?ie=UTF8&camp=1789&creative=9325&index=automotive&keywords=Lug%20Nuts&linkCode=ur2&tag=wheeloffset-20&linkId=SAMMREJTPRAVTXQ4"><img style="vertical-align:middle;padding-right: 5px;" src="https://upload.wikimedia.org/wikipedia/commons/thumb/4/4a/Amazon_icon.svg/16px-Amazon_icon.svg.png"><b>Lug Nuts</b></a><img src="http://ir-na.amazon-adsystem.com/e/ir?t=wheeloffset-20&l=ur2&o=1" width="1" height="1" border="0" alt="" style="border:none !important; margin:0px !important;" /></td>					
				</tr>
				<?php endif;?>				
				
				<tr>
					<td>Stock Rim Sizes Range</td>
					<td><?=$wheelsDataItem['tire_rim_diameter_from']?>x<?=$wheelsDataItem['rim_width_from']?> &ndash; <?=$wheelsDataItem['tire_rim_diameter_to']?>x<?=$wheelsDataItem['rim_width_to']?><br> <a target="_blank" rel="nofollow" href="http://www.amazon.com/gp/search?ie=UTF8&camp=1789&creative=9325&index=automotive&keywords=hubcaps&linkCode=ur2&tag=wheeloffset-20&linkId=SAMMREJTPRAVTXQ4"><img style="vertical-align:middle;padding-right: 5px;" src="https://upload.wikimedia.org/wikipedia/commons/thumb/4/4a/Amazon_icon.svg/16px-Amazon_icon.svg.png"><b>Hubcaps</b></a><img src="http://ir-na.amazon-adsystem.com/e/ir?t=wheeloffset-20&l=ur2&o=1" width="1" height="1" border="0" alt="" style="border:none !important; margin:0px !important;" /></td>					
				</tr>
				
				<?php if (!empty($wheelsDataItem['custom_rim_sizes_range'])):?>
				<tr>
					<td>Custom rim sizes range</td>
					<td><?=$wheelsDataItem['custom_rim_sizes_range']?><br> <a target="_blank" rel="nofollow" href="http://www.amazon.com/gp/search?ie=UTF8&camp=1789&creative=9325&index=automotive&keywords=aftermarket%20wheels&linkCode=ur2&tag=wheeloffset-20&linkId=SAMMREJTPRAVTXQ4"><img style="vertical-align:middle;padding-right: 5px;" src="https://upload.wikimedia.org/wikipedia/commons/thumb/4/4a/Amazon_icon.svg/16px-Amazon_icon.svg.png"><b>Custom Wheels</b></a><img src="http://ir-na.amazon-adsystem.com/e/ir?t=wheeloffset-20&l=ur2&o=1" width="1" height="1" border="0" alt="" style="border:none !important; margin:0px !important;" /></td></tr>
				<?php endif;?>	
					
				<?php $rangeTire = array();
				if (!empty($wheelsDataItem['tires_range_from'])) $rangeTire[] = $wheelsDataItem['tires_range_from'];
				if (!empty($wheelsDataItem['tires_range_to'])) $rangeTire[] = $wheelsDataItem['tires_range_to'];
				?>	
					
				
			</tbody>
			</table>



		</section>
		<br>


		<?php if (!empty($wheelsDataItem['custom_rim_sizes'])):?>
		<section class="table-container">
			<h2 class="section-name_2"><a name="r17"></a><?=$wheelsDataItem['years'][0]?><?php if (end($wheelsDataItem['years'])!=$wheelsDataItem['years'][0]):?>-<?=end($wheelsDataItem['years'])?><?php endif;?> <?=$make['title']?> <?=$model['title']?> Custom Wheel Offsets</h2>
			     <table>
				<tbody>
					<tr>
						<td><b>Rim size</b></td>
						<td><b>Offset</b></td>
						
						
					</tr>
					<?php foreach ($wheelsDataItem['custom_rim_sizes'] as $item):?>
					<tr>
						<td><?=$item['rim_diameter']?>x<?=TextHelper::f($item['rim_width'])?><?php if ($item['is_staggered'] && ($item['rear_rim_diameter']!=$item['rim_diameter'] || $item['rear_rim_width']!=$item['rim_width'])):?> / <?=(!empty($item['rear_rim_diameter']))?$item['rear_rim_diameter']:$item['rim_diameter']?>x<?=(!empty($item['rear_rim_width']))?TextHelper::f($item['rear_rim_width']):TextHelper::f($item['rim_width'])?><?php endif;?></td>
			
						<td><?=$item['ror_min']?>-<?=$item['ror_max']?></td>
					
					</tr>
					<?php endforeach;?>
				</tbody>
			</table><br>
                        
		</section>
		<?php endif;?>
		
		<?php endforeach;?>
		
<br>


<br><br>		
 <?php $this->widget('application.widgets.BannerWidget', array('banner' => 'horizontal')); ?>





	</div>
	<div class="l-col2">
		<br>		
		<?php $this->renderPartial('application.views.specs._right_model', array(
			'make'=>$make,
			'model'=>$model,
			'lastModelYear'=>$lastModelYear,
		))?>
<?php $this->widget('application.widgets.BannerWidget', array('banner' => 'vertical')); ?>

	</div>
</main>