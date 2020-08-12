<main>
	<div class="l-col1">
		<!-- section 1 -->
		<section class="times clearfix">
			<h2 class="section-name pb18"><?=$section_width?>/<?=$aspect_ratio?> on <?=$rim?> rim</h2>
			<div class="google_links f_left p_rel">
				<?php $this->widget('application.widgets.BannerWidget', array('banner' => '336x280')); ?>
			</div>
			<div class="text_size">
				<?=$header_text_block?>
			</div>
		</section>
			
		<h4 class="title_tire">Will a <?=$tireTitle?> tire fit <?=$rim?> rim?</h4><br>
		<p><?=$answer?></p>

		<section class="table-container">
			<table>
			<tbody>
				<tr>
					<td>Rim width</td>
					<td><?=(int)(25.4*$width)?> mm</td>
				</tr>
				<tr>
					<td>Tire width</td>
					<td><?=$section_width?> mm</td>
				</tr>
				
				<tr>
					<td>Aspect ratio</td>
					<td><?=$aspect_ratio?>%</td>
					
				</tr>
				<tr>
					<td>Rim diameter</td>
					<td>R<?=$rim_diameter?></td>
					
				</tr>
				<?php if (!empty($range['front'])):?>
				<tr>
					<td>Recommended rim width</td>
					<td><?=$range['front']['from']?> - <?=$range['front']['to']?></td>
				</tr>
				<?php endif;?>
				<?php $popularTireSizes = Project::getMostPopularTireSizesRim($diametr_id, $width_id);?>
				<?php if (!empty($popularTireSizes)):?>
				<tr>
					<td>Popular tire sizes for a <?=$rim?> rim</td>
					<td>
					<?php $items = array();
					foreach ($popularTireSizes as $size) {
						$itemTitle = Tire::format(array(
							'section_width' => $size['tire_section_width'],
							'aspect_ratio' 	=> $size['tire_aspect_ratio'],
							'rim_diameter' 	=> $size['rim_diameter'],
							'vehicle_class'	=> $size['tire_vehicle_class'],
						), true);
							
						if ($size['is_staggered_tires']) {
							$itemTitle.= '&ndash;' . Tire::format(array(
								'section_width' => $size['rear_tire_section_width'],
								'aspect_ratio' 	=> $size['rear_tire_aspect_ratio'],
								'rim_diameter' 	=> $size['rear_rim_diameter'],
								'vehicle_class'	=> $size['tire_vehicle_class'],
							), true);
						}
							
						$itemUrl = Tire::url(array(
							'section_width' => $size['tire_section_width'],
							'aspect_ratio' 	=> $size['tire_aspect_ratio'],
							'rim_diameter' 	=> $size['rim_diameter'],
							'vehicle_class'	=> $size['tire_vehicle_class'],
						));
						
						$items[] = '<a href="'.$itemUrl.'"><nobr>'.$itemTitle.'</nobr></a>';
					}
					?>
					<?=implode(', ', $items)?>
					</td>
				</tr>
				<?php endif;?>
				
				<?php $popularRimSizes = Project::getMostPopularRimSizesTire($vehicle_class_id, $section_width_id, $aspect_ratio_id, $diametr_id);?>
				<?php if (!empty($popularRimSizes)):?>
				<tr>
					<td>Popular rim widths for a <nobr><?=$tireTitle?></nobr> tire size</td>
					<td>
					<?php 
					$items = array();
					foreach ($popularRimSizes as $size) {
						$itemRim = $size['rim_diameter'] .'x'.$size['rim_width'];
						$items[] = '<a href="/wheels/'.$itemRim.'/">'.$itemRim.'</a>';
					}
					?>
					<?=implode(', ', $items)?>
					</td>
				</tr>
				<?php endif;?>
			</tbody>
			</table>
			
		</section>
	
	<br>

		<section class="make">[<?=$countProject?>]
			<?php if (!empty($projects)):?>
				<h2 class="section-name_2">See how a tire <?=$tireTitle?> looks on <?=$rim?> rim</h2>
				<ul class="make__vehicle" id="list_update">	
				<?php foreach ($projects as $project):?>	
					<li class="js-scrolling-ajax-item">
						<div class="make__vehicle-image">
							<a title="<?=$project['year']?> <?=$project['make_title']?> <?=$project['model_title']?> project" href="/tuning/<?=$project['make_alias']?>/<?=$project['model_alias']?>/<?=$project['id']?>/"><img src="<?=Project::thumb($project['id'], 300, 200, 'resize')?>"></a>
						</div>
						<h3><a title="<?=$project['year']?> <?=$project['make_title']?> <?=$project['model_title']?> project" href="/tuning/<?=$project['make_alias']?>/<?=$project['model_alias']?>/<?=$project['id']?>/"><?=$project['year']?> <?=$project['make_title']?> <?=$project['model_title']?></a></h3>
						<ul class="make__vehicle-specs">
							<li><?=$tireTitle?> on <?=$rim?> rim</li>
						<li><?=$project['view_count']?> views</li>
						</ul>
					</li>	
				<?php endforeach;?>				
				</ul>
		
			<br>
				<?php if ($countProject > 50 && false):?>
					<p><a href="#">See all car projects</a></p>
				<?php endif;?>
			<?php endif;?>
			
			<?php $this->widget('application.widgets.BannerWidget', array('banner' => '580x400')); ?>
		</section>
	</div>
	<div class="l-col2">
		<?php $this->widget('application.widgets.BannerWidget', array('banner' => 'vertical')); ?>
	</div>
</main>