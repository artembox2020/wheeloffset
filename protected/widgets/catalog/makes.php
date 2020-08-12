		<section class="all-makes">
				<h1 class="section-name_2">Select the Car make</h1>
				<ul>
				<?php $key=1;foreach ($makes as $makeUrl=>$makeTitle):?>
					<li><a href="<?=$makeUrl?>"><?=$makeTitle?></a></li>
					<?php if ($key%7 ==0):?>
					</ul><ul>
					<?php endif;?>
				<?php $key++;endforeach;?>
				</ul>
		</section>