				<table class="model__specs-table">
					<tbody><tr>
						<td>MSRP <?=HtmlHelper::price($completion['specs_msrp'], 0, '', ',');?></td>
					
						<?php if (!empty($completion['specs_fuel_tank'])):?>
							<td>Fuel Tank <?=(float)$completion['specs_fuel_tank']?> gal</td>
						<?php endif;?>							
						
						<?php if (!empty($completion['specs_wheelbase'])):?>
							<td>Wheelbase <?=(float)$completion['specs_wheelbase']?> ”</td>
						<?php endif;?>							

					</tr>
					<tr>
					<?php if (!empty($completion['specs_fuel_economy__city']) && !empty($completion['specs_fuel_economy__highway'])):?>
						<td>MPG <?=$completion['specs_fuel_economy__city']?> city/<?=$completion['specs_fuel_economy__highway']?> hwy</td>
					<?php endif;?>	
					<?php if (!empty($completion['specs_0_60mph__0_100kmh_s_'])):?>
						<td>0-60 mph <?=(float)$completion['specs_0_60mph__0_100kmh_s_']?> sec</td>
					<?php endif;?>		

					<?php if (!empty($completion['specs_ground_clearance'])):?>
						<td>Clearance <?=$completion['specs_ground_clearance']?>”</td>
					<?php endif;?>	
					</tr>
					
					<tr>
						<td>Engine <?=AutoSpecsOption::getV('engine', $completion['specs_engine'])?></td>
	
					<?php if (!empty($completion['specs_maximum_trailer_weight'])):?>
						<td>Towing <?=(float)$completion['specs_maximum_trailer_weight']?> lbs</td>
					<?php endif;?>		
	
					<?php if (!empty($completion['specs_curb_weight'])):?>
						<td>Curb <?=(float)$completion['specs_curb_weight']?> lbs</td>
					<?php endif;?>													
					</tr>
					<tr>
						<?php if (!empty($completion['specs_hp_horsepower'])):?>
							<td>HP <?=$completion['specs_hp_horsepower']?> rpm</td>
						<?php endif;?>							
						
						<?php if (!empty($completion['specs_front_tires'])):?>
							<td>Tires <?=$completion['specs_front_tires']?></td>
						<?php endif;?>							
			
						<?php if (!empty($completion['specs_luggage_volume'])):?>
							<td>Cargo <?=(float)$completion['specs_luggage_volume']?> cu.ft</td>
						<?php endif;?>							
					</tr>
				</tbody></table>