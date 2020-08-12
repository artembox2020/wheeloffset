<main>
	<div class="l-col1">
		<!-- section 1 -->
		<section class="times clearfix">
			<h2 class="section-name pb18"><?=$rim?> wheels tire size</h2>
			<div class="google_links f_left p_rel"><?php $this->widget('application.widgets.BannerWidget', array('banner' => '336x280')); ?></div>
			<div class="text_size"><?=$header_text_block?></div>
		</section>
		
		<?php if (!empty($possibleTireSizes)):?>
		<section class="table-container">
			<h4 class="title_tire">Possible tire sizes for a <?=$rim?> rim</h4>
			<table>
				<tbody>
					<tr>
						<td><b>Tire size</b></td>
						<td><b>Recom. width</b></td>
						<td><b>Projects</b></td>
						
					</tr>
					<?php foreach ($possibleTireSizes as $item):?>	
					<tr>
						<?php 
						$tire = array(
							'section_width' => $item['tire_section_width'],
							'aspect_ratio' => $item['tire_aspect_ratio'],
							'vehicle_class' => $item['tire_vehicle_class'],
							'rim_diameter' => $diametr,
						);
						$tireTitle = Tire::format($tire);
						if ($item['is_rear']) {
							$tireTitle.= ' ' . Tire::format(array(
								'section_width' => $item['rear_tire_section_width'],
								'aspect_ratio' => $item['rear_tire_aspect_ratio'],
								'rim_diameter' => $diametr,
							), false);
						}
						?>
						
						<td><a href="/wheels/<?=$rim?>/<?=Tire::urlFormat($tire, true)?>/"><?=$tireTitle?></a></td>
						<td>
							<?php if (isset($tireRangeData[$tireTitle])):
							$r=array(); $r[]=$tireRangeData[$tireTitle]['front']['from']; 
							if ($tireRangeData[$tireTitle]['front']['from']!=$tireRangeData[$tireTitle]['front']['to']) {$r[]=$tireRangeData[$tireTitle]['front']['to']; }
							?>
							<?=implode(' - ', $r)?>"
							<?php endif;?>
						</td>
						<td><?=$item['c']?></td>
					</tr>
					<?php endforeach;?>						
				</tbody>
			</table>
		</section>
		<?php endif;?>
		
	
	<?php foreach ($recommendedTireSizesItems as $item): $recommendedTireSizes = $item['items']?>
	<section class="table-chart">
		<h4 class="title_tire">Recommended <?=$item['title']?> tire sizes that fit <?=$rim?> rim</h4>
		<br>
		<table>
			<tbody>
				<tr>
					<td>%<br>Width</td>
					<?php foreach ($recommendedTireSizes['aspect_ratio'] as $ar_id=>$ar_val):?>
					<td><?=$ar_val?></td>
					<?php endforeach;?>
				</tr><tr>
				<?php foreach ($recommendedTireSizes['section_width'] as $sw_id=>$sw_val):?>
				<tr>
					<td><?=$sw_val?></td>
					<?php foreach ($recommendedTireSizes['aspect_ratio'] as $ar_id=>$ar_val):?>
						<?php if (isset($recommendedTireSizes['counters'][$sw_id.'_'.$ar_id]) || in_array($sw_id.'_'.$ar_id, $recommendedTireSizes['range'])):?>
						<td <?=in_array($sw_id.'_'.$ar_id, $recommendedTireSizes['range'])?'bgcolor="#FFC37A" style="cursor:pointer"':''?>>
							<?php if (isset($recommendedTireSizes['counters'][$sw_id.'_'.$ar_id])):?>
							<a href="/wheels/<?=$rim?>/<?=Tire::urlFormat(array('vehicle_class'=>$item['title'], 'section_width'=>$sw_val, 'aspect_ratio'=>$ar_val, 'rim_diameter'=>$diametr))?>/"><?=$recommendedTireSizes['counters'][$sw_id.'_'.$ar_id]?></a>
							<?php endif;?>
						</td>
						<?php else:?>
						<td></td>
						<?php endif?>
					<?php endforeach;?>
				</tr>
					
				<?php endforeach;?>
			</tbody>
		</table><br>
		<p>Recommended tire sizes are marked with <font color="#FFC37A"><b>orange color</b></font>.</p>
		<p>Square boxes with numbers (<font color="#2fa4e7"><b>12</b></font>) stand for custom car projects. Click the number to browse them.</p>
	</section>
	<?php endforeach;?>
	
	<section class="make">
	<?php if (!empty($projects)):?>	
		<h4 class="title_tire">Modified cars with <?=$rim?> wheels</h4>
		<ul class="make__vehicle" id="list_update">
			<?php $this->renderPartial('application.views.wheels._projects', array('projects'=>$projects, 'rim'=>$rim, 'diametr'=>$diametr))?>
		</ul>
		<?php if ($count > sizeof($projects)):?>
		<br>
		<p><a href="#" id="link-see-all">See all <?=$count?> car projects with</a> <?=$rim?> rims</p>
		<?php endif;?>
	<?php endif;?>
	
		
	<?php $this->widget('application.widgets.BannerWidget', array('banner' => '580x400')); ?>	
	</section>

	
	<?php if (!empty($rimsNavigation)):?>
		<section class="years_box make">
			<h2 class="section-name_2">Rims navigation</h2>
			<ul class="years_list">
			<?php foreach ($rimsNavigation as $k=>$v):?>	
				<li class="years_list_item"><a href="/wheels/<?=$k?>" class="btn years_list_link" title="rim <?=$k?>"><?=$k?> - <?=$v?></a></li>
			<?php endforeach;?>
			</ul>
		</section>
	<?php endif;?>
	</div>
	
	<div class="l-col2">
		<?php $this->widget('application.widgets.BannerWidget', array('banner' => 'vertical')); ?>
	</div>
</main>

<script src="http://autofiles.com/js/lib/jquery.js"></script>
<script>
$('#link-see-all').click(function(e) {
	e.preventDefault()
	$(this).parent().hide();
	sendScrolingRequest = false;
	getProjects();
})

function getProjects() {
	offset = $('.js-scrolling-ajax-item').size();
	$.post('<?=Yii::app()->request->requestUri?>?offset='+offset, function(response){
		html = $.trim(response);
		if (html != '') {
			$('#list_update').append(response);
			sendScrolingRequest=false;
		} 
	}, 'text');	
}

function element_in_scroll(elem) {
    var docViewTop = $(window).scrollTop();
    var docViewBottom = docViewTop + $(window).height();
    if($(elem).length) {
        var elemTop = $(elem).offset().top;
        var elemBottom = elemTop + $(elem).height();

        return ((elemBottom <= docViewBottom) && (elemTop >= docViewTop));
    } else {
        return false
    }
}


var sendScrolingRequest = true;
$(document).scroll(function(e){
	if (element_in_scroll(".js-scrolling-ajax-item:last") && !sendScrolingRequest) {
		sendScrolingRequest = true;
		getProjects();		
    };
});
</script>