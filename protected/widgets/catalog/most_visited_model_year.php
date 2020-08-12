		<section class="most-visited">
			<table>
				<h2 class="section-name_2">Most visited models</h2>
				<tr>
				<?php foreach ($items as $key=>$item):?>	
					<td><a href="/<?=$item['make_alias']?>/<?=$item['model_alias']?>/<?=$item['year']?>/" title="<?=$item['year']?> <?=$item['make']?> <?=$item['model']?>"><?=$item['year']?> <?=$item['make']?> <?=$item['model']?></a></td>
					<?php if (($key+1)%3==0):?></tr><tr><?php endif;?>
				<?php endforeach;?>
				</tr>
			</table>
		</section>