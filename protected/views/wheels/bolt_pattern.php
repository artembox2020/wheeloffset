<main>
	<div class="l-col1">
		<!-- section 1 -->
		<section class="times clearfix">
			<h2 class="section-name pb18">Bolt pattern - PCD main</h2>
			<div class="google_links f_left p_rel">
				<?php $this->widget('application.widgets.BannerWidget', array('banner' => '336x280')); ?>
			</div>
			<div class="text_size">
				<?=SiteConfig::getInstance()->getValue('wheels_bolt_pattern_header_text_block')?>
			</div>
		</section>
			
		<h4 class="title_tire">The list of bolt patterns</h4><br>
		
		<section class="table-container">
			<table>
			<tbody>
				<tr>
					<td><b>Millimeters</b></td>
					<td><b>Inches</b></td>
					<td><b>Popularity</b></td>
				</tr>
				<?php foreach ($list as $item):?>
				<tr>
					<td><a href="/wheels/bolt-pattern/<?=$item['value']?>/"><?=$item['value']?></td>
					<td><?=$item['value_inches']?></td>
					<td><?=$item['percent']?>%</td>
				</tr>
				<?php endforeach;?>
			</tbody>
			</table>
			
		</section>
		
		<?php $years = AutoModelYear::getYears();?>
		<section class="section_tabs">
				<section class="reviews">
						
					<div id="b1" class="reviews__container active vehickle">
						<div class="vehicle_div_img p_rel"></div>
						<h4 class="title_tire pt20">Find your car's bolt pattern:</h4>
						<div class="options">
							<div class="options__block">
								<div class="options__item">
									<strong>Year</strong>
									<select id="Filter_year">
										<option>-no select-</option>
										<?php foreach ($years as $year):?>
										<option value="<?=$year?>"><?=$year?></option>
										<?php endforeach;?>
									</select>
								</div>
								<div class="options__item">
									<strong>Make</strong>
									<select id="Filter_make_id">
										<option>-no select-</option>
									</select>
								</div>
								<div class="options__item">
									<strong>Model</strong>
									<select id="Filter_model_id">
										<option>-no select-</option>
									</select>
								</div>
								<button style="display: none;" type="submit" class="btn btn_options" id="btn_submit_filter">GO</button>
							</div>
							<div id="search_list">
							</div>
						</div>
					</div>
					
				</section>
			</section>
			
	</div>
	
	<div class="l-col2">
		<section class="">
			<?php $this->widget('application.widgets.BannerWidget', array('banner' => 'vertical')); ?>
		</section>

	</div>
</main>

<script src="/js/lib/jquery.js"></script>
<script>
$('#Filter_year').change(function(e) {
	$('#Filter_make_id').empty().append('<option value="">-no select-</option>');
	$('#Filter_model_id').empty().append('<option value="">-no select-</option>');
	$.post('/ajax/getMakesByYear', {'year': $(this).val()} , function(response) {
		$.each(response.items, function(value, lable){
			$('#Filter_make_id').append('<option value="'+value+'">'+lable+'</option>');
		});
	}, 'json');
});

$('#Filter_make_id').change(function(e) {
	$('#Filter_model_id').empty();
	$('#Filter_model_id').append('<option value="">-no select-</option>');
	$.post('/ajax/getModelsByMake', {'alias': $(this).val(), 'year': $('#Filter_year').val()} , function(response) {
		$.each(response.items, function(value, lable){
			$('#Filter_model_id').append('<option value="'+value+'">'+lable+'</option>');
		});
	}, 'json');
});

$('#Filter_model_id').change(function(e) {
	if ($(this).val() == '') {
		$('#btn_submit_filter').hide();
	} else {
		$('#btn_submit_filter').show();
	} 
});


$('#btn_submit_filter').click(function(e) {
	$.post('/wheels/bolt-pattern.html', {'make': $('#Filter_make_id').val(), 'year': $('#Filter_year').val(), 'model':$('#Filter_model_id').val()} , function(response) {
		$('#search_list').html(response);
	}, 'html');	
	
});
</script>	
