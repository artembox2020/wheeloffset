<main>
	<div class="l-col1">
		<section class="years_box make">
			<h1 class="section-name_2"><?=$make['title']?> <?=$model['title']?> Lug Nut Chart</h1>
			<ul class="years_list">
			<?php foreach ($lugnutsDataItems as $lugnutsDataItem):?>
				<?php foreach ($lugnutsDataItem['years'] as $y):?>
					<li class="years_list_item"><a href="<?= "/lugnuts" . $model['url'] . "$y/" ?>" class="btn years_list_link"><?=$y?></a></li>
				<?php endforeach;?>
			<?php endforeach;?>
			</ul>
		</section>
			
		<section class="times clearfix">

			<div class="google_links f_left p_rel">
				<?php $this->widget('application.widgets.BannerWidget', array('banner' => '336x280')); ?>	
			</div>
			<div class="text_size">
			
			<p>We are going to give you some tips on <?=$make['title']?> <?=$model['title']?> lug nut size you should know before you pick up a set of wheels for your vehicle.</p>

<p>A good wheel in the right setup is going to make the difference between something that just was not thought out and pretty much guessed.</p>

<p>If you are trying to figure out what your set of wheels for <?=$make['title']?> <?=$model['title']?> should be and after visiting tons of forums still left with more questions than answers, than this is your final stop. Lots of people do not understand the principles of diameter, width and offsets.</p>


			</div>
		</section>
		

        <section class="seo-text">
            <?=  $model['text_wheels'] ?>
        </section>

        <?php foreach ($lugnutsDataItems as $lugnutsDataItem):?>
            <section class="table-container">
                <img alt="<?=$make['title']?> <?=$model['title']?> bolt pattern" src="<?=AutoModelYear::thumb($lugnutsDataItem['ids'][0], 150, null, 'resize')?>"><h4 class="title_tire"><?=$make['title']?> <?=$model['title']?> Lug Nut Size</h4>
                <?php foreach ($lugnutsDataItem['years'] as $y):?>
                    <a name="<?=$y?>" style="color:#000;"><small><?=$y?></small></a>
                <?php endforeach;?><br>
                <a rel="nofollow" href="https://www.anrdoezrs.net/links/100377875/type/dlg/https://www.tirerack.com/content/tirerack/desktop/en/tires.html">Shop for Tires</a> at Tire Rack.
                <table>
                    <tbody>

                    <?php if (!empty($lugnutsDataItem['center_bore'])):?>
                        <tr>
                            <td>Center bore (hub bore)</td>
                            <td><?=$lugnutsDataItem['center_bore']?><br> <a target="_blank" rel="nofollow" href="http://www.amazon.com/gp/search?ie=UTF8&camp=1789&creative=9325&index=automotive&keywords=Hub%20Centric%20Rings&linkCode=ur2&tag=wheeloffset-20&linkId=SAMMREJTPRAVTXQ4"><img style="vertical-align:middle;padding-right: 5px;" src="https://upload.wikimedia.org/wikipedia/commons/thumb/4/4a/Amazon_icon.svg/16px-Amazon_icon.svg.png"><b>Use Hub Centric Rings</b></a><img src="http://ir-na.amazon-adsystem.com/e/ir?t=wheeloffset-20&l=ur2&o=1" width="1" height="1" border="0" alt="" style="border:none !important; margin:0px !important;" /></td>
                        </tr>
                    <?php endif;?>

                    <?php if (!empty($lugnutsDataItem['thread_size'])):?>
                        <tr>
                            <td>Thread Diameter</td>
                            <td><?=$lugnutsDataItem['thread_size']?><br> <a target="_blank" rel="nofollow" href="http://www.amazon.com/gp/search?ie=UTF8&camp=1789&creative=9325&index=automotive&keywords=Lug%20Nuts&linkCode=ur2&tag=wheeloffset-20&linkId=SAMMREJTPRAVTXQ4"><img style="vertical-align:middle;padding-right: 5px;" src="https://upload.wikimedia.org/wikipedia/commons/thumb/4/4a/Amazon_icon.svg/16px-Amazon_icon.svg.png"><b>Lug Nuts</b></a><img src="http://ir-na.amazon-adsystem.com/e/ir?t=wheeloffset-20&l=ur2&o=1" width="1" height="1" border="0" alt="" style="border:none !important; margin:0px !important;" /></td>
                        </tr>
                    <?php endif;?>

                    </tbody>
                </table>
            </section>
            <br/>

        <?php endforeach;?>

        <br>

<br><br>		
 <?php $this->widget('application.widgets.BannerWidget', array('banner' => 'horizontal')); ?>



	</div>
	<div class="l-col2">
		<br>		
		<?php $this->renderPartial('application.views.specs._right_model', array(
			'make'=>$make,
			'model'=>$model,
			'lastModelYear'=>$lastModelYear,
		))?>
<?php $this->widget('application.widgets.BannerWidget', array('banner' => 'vertical')); ?>

	</div>
</main>