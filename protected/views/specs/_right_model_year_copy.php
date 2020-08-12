<?php $carSpecsAndDimensions = AutoModelYear::getCarSpecsAndDimensions($modelYear['id']);?>
			
			<section class="right-block w78">
				<h2 class="section-name"><?=$modelYear['year']?> <?=$make['title']?> <?=$model['title']?> specs</h2>
		
				<a href="/<?=$make['alias']?>/<?=$model['alias']?>/<?=$modelYear['year']?>/" title="<?=$make['title']?> <?=$model['title']?> <?=$modelYear['year']?> info ">
					<img src="<?=$modelYear['photo']?>">
				</a>				
				
				<table class="right-block__specs-list">
					<tbody>
					
					<?php if (!empty($carSpecsAndDimensions['0_60_times']['mmin'])):?>
					<tr>
						<td><a class="speed" title="<?=$modelYear['year']?> <?=$make['title']?> <?=$model['title']?> 0-60" href="/0-60-times/<?=$make['alias']?>/<?=$model['alias']?>/">0-60 times</a></td>
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

					<?php $rangeTireSize = AutoModel::getMinMaxTireSizeYear($modelYear['id']);?>
					<?php if (!empty($rangeTireSize)):?>
					<tr>
						<td><a class="tire" title="<?=$modelYear['year']?> <?=$make['title']?> <?=$model['title']?> Tire size" href="/tires/<?=$make['alias']?>/<?=$model['alias']?>/<?=$modelYear['year']?>/">Tire size</a></td>
						<td class="spec-value">
							<?=$rangeTireSize['min']?> ...
						</td>
					</tr>
					<?php endif;?>			
						
					
					<?php if (!empty($carSpecsAndDimensions['hp']['mmin'])):?>
					<tr>
						<td><a class="horsepower" href="/horsepower/<?=$make['alias']?>/<?=$model['alias']?>/<?=$modelYear['year']?>/">Horsepower</a></td>
						<td class="spec-value">
						<?php if ($carSpecsAndDimensions['hp']['mmin'] != $carSpecsAndDimensions['hp']['mmax']):?>
							<?=(float)$carSpecsAndDimensions['hp']['mmin']?> - <?=(float)$carSpecsAndDimensions['hp']['mmax']?>
						<?php else:?>
							<?=(float)$carSpecsAndDimensions['hp']['mmin']?>
						<?php endif;?>	
						hp</td>
					</tr>
					<?php endif;?>
					
                                        <?php if (!empty($carSpecsAndDimensions['engine'])):?>
					<tr>
						<td><a class="engine" href="#">Engine specs</a></td>
						<td class="spec-value"><?=$carSpecsAndDimensions['engine']?></td>
					</tr>
					<?php endif;?>
					
					<?php if (!empty($carSpecsAndDimensions['horsepower'])):?>
					<tr>
						<td><a class="gas" href="#">Gas mileage</a></td>
						<td class="spec-value"><?=$carSpecsAndDimensions['gas_mileage']?></td>
					</tr>
					<?php endif;?>
					
					<?php if (!empty($carSpecsAndDimensions['towing_capacity']['mmin'])):?>
					<tr>
						<td><a class="towing" href="#">Towing capacity</a></td>
						<td class="spec-value">
						<?php if ($carSpecsAndDimensions['towing_capacity']['mmin'] != $carSpecsAndDimensions['towing_capacity']['mmax']):?>
							<?=(float)$carSpecsAndDimensions['towing_capacity']['mmin']?> - <?=(float)$carSpecsAndDimensions['towing_capacity']['mmax']?>
						<?php else:?>
							<?=(float)$carSpecsAndDimensions['towing_capacity']['mmin']?>
						<?php endif;?>					
						lbs</td>
					</tr>
					<?php endif;?>	
					
					<?php if (!empty($carSpecsAndDimensions['length']['mmin'])):?>
					<tr>
						<td><a class="length" href="#">Length</a></td>
						<td class="spec-value">
						<?php if ($carSpecsAndDimensions['length']['mmin'] != $carSpecsAndDimensions['length']['mmax']):?>
							<?=(float)$carSpecsAndDimensions['length']['mmin']?> - <?=(float)$carSpecsAndDimensions['length']['mmax']?>
						<?php else:?>
							<?=(float)$carSpecsAndDimensions['length']['mmin']?>
						<?php endif;?>					
						”</td>
					</tr>
					<?php endif;?>	
					
					<?php if (!empty($carSpecsAndDimensions['wheelbase']['mmin'])):?>
					<tr>
						<td><a class="wheelbase" href="#">Wheelbase</a></td>
						<td class="spec-value">
						<?php if ($carSpecsAndDimensions['wheelbase']['mmin'] != $carSpecsAndDimensions['wheelbase']['mmax']):?>
							<?=(float)$carSpecsAndDimensions['wheelbase']['mmin']?> - <?=(float)$carSpecsAndDimensions['wheelbase']['mmax']?>
						<?php else:?>
							<?=(float)$carSpecsAndDimensions['wheelbase']['mmin']?>
						<?php endif;?>						
						”</td>
					</tr>
					<?php endif;?>	
					
					<?php if (!empty($carSpecsAndDimensions['clearance']['mmin'])):?>
					<tr>
						<td><a class="clearance" href="#">Clearance</a></td>
						<td class="spec-value">
						<?php if ($carSpecsAndDimensions['clearance']['mmin'] != $carSpecsAndDimensions['clearance']['mmax']):?>
							<?=(float)$carSpecsAndDimensions['clearance']['mmin']?> - <?=(float)$carSpecsAndDimensions['clearance']['mmax']?>
						<?php else:?>
							<?=(float)$carSpecsAndDimensions['clearance']['mmin']?>
						<?php endif;?>						
						”</td>
					</tr>
					<?php endif;?>
					
					<?php if (!empty($carSpecsAndDimensions['curb_weight']['mmin'])):?>
					<tr>
						<td><a class="weight" href="#">Curb weight</a></td>
						<td class="spec-value">
						<?php if ($carSpecsAndDimensions['curb_weight']['mmin'] != $carSpecsAndDimensions['curb_weight']['mmax']):?>
							<?=(float)$carSpecsAndDimensions['curb_weight']['mmin']?> - <?=(float)$carSpecsAndDimensions['curb_weight']['mmax']?>
						<?php else:?>
							<?=(float)$carSpecsAndDimensions['curb_weight']['mmin']?>
						<?php endif;?>	
						lbs</td>
					</tr>
					<?php endif;?>
					
					<?php if (!empty($carSpecsAndDimensions['cargo_space']['mmin'])):?>
					<tr>
						<td><a class="cargo" href="#">Cargo space</a></td>
						<td class="spec-value">
						<?php if ($carSpecsAndDimensions['cargo_space']['mmin'] != $carSpecsAndDimensions['cargo_space']['mmax']):?>
							<?=(float)$carSpecsAndDimensions['cargo_space']['mmin']?> - <?=(float)$carSpecsAndDimensions['cargo_space']['mmax']?>
						<?php else:?>
							<?=(float)$carSpecsAndDimensions['cargo_space']['mmin']?>
						<?php endif;?>	
						cu.ft</td>
					</tr>
					<?php endif;?>

					
				</tbody>
				</table>
   <script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
<!-- autof_250 -->
<ins class="adsbygoogle"
     style="display:block"
     data-ad-client="ca-pub-3243264408777652"
     data-ad-slot="2242919653"
     data-ad-format="auto"></ins>
<script>
(adsbygoogle = window.adsbygoogle || []).push({});
</script>

			</section>