<main>
    <div class="l-col1">
        <section class="times clearfix">
            <div class="google_links f_left p_rel">
                <?php $this->widget('application.widgets.BannerWidget', array('banner' => '336x280')); ?>
            </div>
            <div class="text_size">
                <p>We are going to give you some tips on <?=$make['title']?> <?=$model['title']?> wheel offset you should know before you pick up a set of wheels for your vehicle.</p>
                <p>A good wheel in the right setup is going to make the difference between something that just was not thought out and pretty much guessed.</p>
                <p>If you are trying to figure out what your set of wheels for <?=$make['title']?> <?=$model['title']?> should be and after visiting tons of forums still left with more questions than answers, than this is your final stop. Lots of people do not understand the principles of diameter, width and offsets.</p>
            </div>
        </section>

        <section class="seo-text">
            <?=  $model['text_wheels'] ?>
        </section>
        <?php echo $this->renderPartial('_wheels_data_items', array(
            'make' => $make,
            'model'=> $model,
            'wheelsDataItems' => $wheelsDataItems
        )); ?>
        <br>
        <br><br>
        <?php $this->widget('application.widgets.BannerWidget', array('banner' => 'horizontal')); ?>
    </div>
</main>