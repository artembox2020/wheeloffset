<div class="l">
	<div class="l-col1">
		<section class="gallery_box">
			<h2 class="section-name section-name_regular"><?=$modelYear['year']?> <?=$make['title']?> <?=$model['title']?></h2>
			
			<div class="box1">
				<!--
				<span class="published f_left">Published<time>11/21/2013</time></span>
				<div class="published_box d_in-block">
					<img src="/img/publish.png" alt="">
				</div>
				<a href="#" class="btn btn_gallegy f_right">Comments <span>(5)</span></a>
				-->
			</div>
			
			<div id="products" class="p_rel">
			
				<?php if (!empty($photos)):?>
				<div class="slides_container">
					<?php foreach ($photos as $photo):?>
					<img src="<?=$photo['large']?>" alt="<?=$photo['name']?>">
					<?php endforeach;?>
				</div>
				<?php endif;?>
				
				<?php $this->widget('application.widgets.BannerWidget', array('banner' => 'add_728_links')); ?>
				
				<div class="descriptoin_galler">
					<a title="<?=$modelYear['year']?> <?=$make['title']?> <?=$model['title']?>" href="<?=$model['url']?><?=$modelYear['year']?>/" class="descriptoin_galler_lk d_in-block"><?=$modelYear['year']?> <?=$make['title']?> <?=$model['title']?></a>
					<p class="descriptoin_galler_text">
						<?=$description?>
					</p>
				</div>				
				
				<?php if (!empty($photos)):?>
				<ul class="pagination clearfix">
					<?php foreach ($photos as $photo):?>
						<li><a href="#"><img src="<?=$photo['large']?>" alt="<?=$photo['name']?>"></a></li>
					<?php endforeach;?>
				</ul>
				<?php endif;?>
			</div>
			<!-- slider end -->
		</section>
		
		<!--
		<section class="related_pictures">
			<h4 class="section-name section-name_regular">Related pictures</h4>
			<ul class="related_pictures_list clearfix">
				<li class="related_pictures_it">
					<img src="/img/gallery_img8.jpg" alt="">
					<a href="#">2014 Lamborghini Gallardo Superleggera LP 570-4</a>
				</li>
				<li class="related_pictures_it">
					<img src="/img/gallery_img8.jpg" alt="">
					<a href="#">2014 Lamborghini Gallardo Superleggera LP 570-4</a>
				</li>
				<li class="related_pictures_it">
					<img src="/img/gallery_img8.jpg" alt="">
					<a href="#">2014 Lamborghini Gallardo Superleggera LP 570-4</a>
				</li>
				<li class="related_pictures_it">
					<img src="/img/gallery_img8.jpg" alt="">
					<a href="#">2014 Lamborghini Gallardo Superleggera LP 570-4</a>
				</li>
				<li class="related_pictures_it">
					<img src="/img/gallery_img8.jpg" alt="">
					<a href="#">2014 Lamborghini Gallardo Superleggera LP 570-4</a>
				</li>
			</ul>
		</section>
		-->
		
	</div>
	<div class="l-col2">
		
		<?php $this->widget('application.widgets.BannerWidget', array('banner' => 'vertical')); ?>
		
		<!--
		<section class="right-block">
			<h2 class="section-name">Car specs and dimensions</h2>
			<table class="right-block__specs-list">
				<tr>
					<td><a class="speed" href="#">0-60 times</a></td>
					<td class="spec-value">5.7 sec</td>
				</tr>
				<tr>
					<td><a class="engine" href="#">Engine specs</a></td>
					<td class="spec-value">3.5L V-6</td>
				</tr>
				<tr>
					<td><a class="horsepower" href="#">Horsepower</a></td>
					<td class="spec-value">273 hp</td>
				</tr>
				<tr>
					<td><a class="gas" href="#">Gas mileage</a></td>
					<td class="spec-value">19/27</td>
				</tr>
				<tr>
					<td><a class="towing" href="#">Towing capacity</a></td>
					<td class="spec-value">1500 lbs</td>
				</tr>
				<tr>
					<td><a class="length" href="#">Length</a></td>
					<td class="spec-value">183.5”</td>
				</tr>
				<tr>
					<td><a class="wheelbase" href="#">Wheelbase</a></td>
					<td class="spec-value">105.7”</td>
				</tr>
				<tr>
					<td><a class="clearance" href="#">Clearance</a></td>
					<td class="spec-value">5.4”</td>
				</tr>
				<tr>
					<td><a class="weight" href="#">Curb weight</a></td>
					<td class="spec-value">3.717 lbs</td>
				</tr>
				<tr>
					<td><a class="cargo" href="#">Cargo space</a></td>
					<td class="spec-value">61.3 cu.ft</td>
				</tr>
			</table>
		</section>
		-->
		
	</div>
</div>

<script src="/js/lib/head.js" data-headjs-load="/js/init.js"></script>
<script src="/js/lib/slides.min.jquery.js"></script>
<script>
		$(function(){
			$('#products').slides({
				preload: true,
				preloadImage: '/img/loading.gif',
				effect: 'slide, fade',
				crossfade: true,
				slideSpeed: 500,
				fadeSpeed: 500,
				generateNextPrev: true,
				generatePagination: false
			});
		});
</script>