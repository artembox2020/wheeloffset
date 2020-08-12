<main>
	<div class="l-col1">
		<!-- section 1 -->
			<section class="times">
			<h1 class="section-name_2"><?=$make['title']?> 0-60 times</h1>
				<div class="times__container">
				<div class="google_links f_left p_rel">
					<?php $this->widget('application.widgets.BannerWidget', array('banner' => '336x280')); ?>	
				</div>
				<div class="text_size">
					<?=$description?>
				</div>
			</div>
		</section>

<?php $this->widget('application.widgets.BannerWidget', array('banner' => 'horizontal')); ?>

		<section class="table-container">
			<h2 class="section-name_2">The fastest <?=$make['title']?> cars</h2>
			<table>
			<?php foreach ($models as $item):?>
				<tr>
					<td><a href="/0-60-times<?=$item['url']?>"> <?=$make['title']?> <?=$item['title']?> 0-60</a></td>
					<td>
					<?php if ($item['0_60_times']['mmax'] == $item['0_60_times']['mmin']):?>
						<?=$item['0_60_times']['mmin']?>
					<?php else:?>
						<?=$item['0_60_times']['mmin']?> - <?=$item['0_60_times']['mmax']?>
					<?php endif;?>	
						sec
					</td>
				
				</tr>
			<?php endforeach;?>
			</table>
		</section>		

<br>	
<h2 class="section-name_2">See More: <a title="<?=$make['title']?> weight" href="http://autotk.com/weight/<?=$make['alias']?>/"><?=$make['title']?> Weight</a>, <a title="<?=$make['title']?> mpg" href="http://autotk.com/mpg/<?=$make['alias']?>/">MPG</a></h2>


		<?php $this->widget('application.widgets.BannerWidget', array('banner' => 'horizontal')); ?>
		
	</div>
	<div class="l-col2">	
		<?php $this->widget('application.widgets.BannerWidget', array('banner' => 'vertical')); ?>		
		
		<section class="right-block">
			<?php $this->renderPartial('application.views.specs._right_make', array('make'=>$make))?>
		</section>	
		
	</div>
</main>