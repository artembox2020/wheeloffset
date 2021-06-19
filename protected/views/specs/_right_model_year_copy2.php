<?php $carSpecsAndDimensions = AutoModelYear::getCarSpecsAndDimensions($modelYear['id']);?>
			
			<section class="right-block w78">
				<h2 class="section-name_2"><?=$modelYear['year']?> <?=$make['title']?> <?=$model['title']?> specs</h2>
		
						
				
				<table class="right-block__specs-list">
					<tbody>
					
					<?php if (!empty($carSpecsAndDimensions['0_60_times']['mmin'])):?>
					<tr>
						<td><a class="speed" title="<?=$modelYear['year']?> <?=$make['title']?> <?=$model['title']?> 0-60 times" href="/0-60-times/<?=$make['alias']?>/<?=$model['alias']?>/">0-60 times</a></td>
						<td class="spec-value">
						<?php if ($carSpecsAndDimensions['0_60_times']['mmin'] != $carSpecsAndDimensions['0_60_times']['mmax']):?>
							<?=(float)$carSpecsAndDimensions['0_60_times']['mmin']?> - <?=(float)$carSpecsAndDimensions['0_60_times']['mmax']?>
						<?php else:?>
							<?=(float)$carSpecsAndDimensions['0_60_times']['mmin']?>
						<?php endif;?>
						sec
						</td>
					</tr>
					<?php endif;?>
		
						
					
					<?php if (!empty($carSpecsAndDimensions['hp']['mmin'])):?>
					<tr>
						<td><a class="horsepower" title="<?=$modelYear['year']?> <?=$make['title']?> <?=$model['title']?> horsepower, hp" href="/horsepower/<?=$make['alias']?>/<?=$model['alias']?>/<?=$modelYear['year']?>/">Horsepower</a></td>
						<td class="spec-value">
						<?php if ($carSpecsAndDimensions['hp']['mmin'] != $carSpecsAndDimensions['hp']['mmax']):?>
							<?=(float)$carSpecsAndDimensions['hp']['mmin']?> - <?=(float)$carSpecsAndDimensions['hp']['mmax']?>
						<?php else:?>
							<?=(float)$carSpecsAndDimensions['hp']['mmin']?>
						<?php endif;?>	
						hp</td>
					</tr>
					<?php endif;?>
					
					<tr>
						<td><a class="dim" title="<?=$modelYear['year']?> <?=$make['title']?> <?=$model['title']?> dimensions" href="/dimensions/<?=$make['alias']?>/<?=$model['alias']?>/<?=$modelYear['year']?>/">Dimensions</a></td>
						<td class="spec-value">...</td>
					</tr>						
					
					<?php $rim = AutoModelYear::getRimRange($modelYear['id'])?>
					<tr>
						<td>
							<a class="rim" title="<?=$modelYear['year']?> <?=$make['title']?> <?=$model['title']?> wheel bolt pattern" href="/wheels/<?=$make['alias']?>/<?=$model['alias']?>#<?=$modelYear['year']?>">Wheels</a>
						</td>		
						<td class="spec-value"><?=$rim['diameter_from']?>x<?=$rim['width_from']?> &ndash; <?=$rim['diameter_to']?>x<?=$rim['width_to']?></td>
					</tr>					
 
					<?php $rangeTireSize = AutoModel::getMinMaxTireSizeYear($modelYear['id']);?>
					<?php if (!empty($rangeTireSize)):?>
					<tr>
						<td><a class="tire" title="<?=$modelYear['year']?> <?=$make['title']?> <?=$model['title']?> Tire size" href="/tires/<?=$make['alias']?>/<?=$model['alias']?>/<?=$modelYear['year']?>/">Tire size</a></td>
						<td class="spec-value">
							<?=$rangeTireSize['min']?> ...
						</td>
					</tr>
					<?php endif;?>	                  
					
					<tr>
						<td>
							<a title="Modified, custom <?=$make['title']?> <?=$model['title']?>, car tuning" class="tuning" href="/tuning/<?=$make['alias']?>/<?=$model['alias']?>/">Custom cars</a>
						</td>
						<td class="spec-value">
							<?php $countProjects = Project::getCountByModel($model['id'])?>
							<?php if ($countProjects):?>
								<?=$countProjects?> projects
							<?php endif;?>
						</td>
					</tr>					
					
				</tbody>
				</table>
   
			</section>