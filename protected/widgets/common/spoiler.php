			<?php $truncate = explode('[truncate]', $text)?>
			<div class="js-<?=$text?>-0">
			<?php if (isset($make) && isset($model)):?>
				<a title="<?= $make['title']?> <?= $model['title']?> weight" href="/weight/<?= $make['alias']?>/<?= $model['alias']?>/">Car weight</a>				
			<?php endif;?>
				<?=$truncate[0]?>
			</div>
			<?php if (isset($truncate[1])):?>
				<div class="js-<?=$text?>-1" style="display: none;"><?=$truncate[1]?></div>
				<div><a href="#" class="js-<?=$text?>">show more</a></div>
				<script>
					$('.js-<?=$text?>').click(function(e){
						e.preventDefault();
						if ($(this).text() == 'show more') {
							$('.js-<?=$text?>-1').show();
							$(this).text('show less');
						} else {
							$('.js-<?=$text?>-1').hide();
							$(this).text('show more');	
						}
					})				
				</script>
			<?php endif;?>