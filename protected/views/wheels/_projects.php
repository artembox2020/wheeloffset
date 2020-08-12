		<?php foreach ($projects as $project):?>
		<li class="js-scrolling-ajax-item">
			<div class="make__vehicle-image">
				<a title="<?=$project['make_title']?> <?=$project['model_title']?> tire size" href="/tuning/<?=$project['make_alias']?>/<?=$project['model_alias']?>/<?=$project['id']?>/">
					<img alt="<?=$project['make_title']?> <?=$project['model_title']?> tire size" src="<?=Project::thumb($project['id'], 300, 200, 'resize')?>"> 
                 </a>
			</div>	
			<h3>
				<a href="/tuning/<?=$project['make_alias']?>/<?=$project['model_alias']?>/<?=$project['id']?>/"><?=$project['year']?> <?=$project['make_title']?> <?=$project['model_title']?> <?=$rim?></a>
			</h3>
			<ul class="make__vehicle-specs">
				<li><?=Tire::format(array(
						'section_width' => $project['tire_section_width'],
						'aspect_ratio' => $project['tire_aspect_ratio'],
						'vehicle_class' => $project['tire_vehicle_class'],
						'rim_diameter' => $diametr,
				))?></li><li><?=$project['view_count']?> views</li>
			</ul>
		</li>
		<?php endforeach;?>