<main>
    <div class="l-col1">
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
        <?php foreach ($wheelsDataItems as $wheelsDataItem):?>
            <section class="table-container">
                <img alt="<?=$make['title']?> <?=$model['title']?> bolt pattern" src="<?=AutoModelYear::thumb($wheelsDataItem['ids'][0], 150, null, 'resize')?>"><h4 class="title_tire"><?=$make['title']?> <?=$model['title']?> Lug Nut Size</h4>
                <?php foreach ($wheelsDataItem['years'] as $y):?>
                    <a name="<?=$y?>" style="color:#000;"><small><?=$y?></small></a>
                <?php endforeach;?><br>
                <a rel="nofollow" href="https://www.anrdoezrs.net/links/100377875/type/dlg/https://www.tirerack.com/content/tirerack/desktop/en/tires.html">Shop for Tires</a> at Tire Rack.
                <table>
                    <tbody>

                    <?php if (!empty($wheelsDataItem['bolt_pattern'])):?>
                        <tr>
                            <td><a href="https://wheelssize.com/bolt-pattern/<?=$make['alias']?>/<?=$model['alias']?>/<?= $wheelsDataItem['years'][0] ?>/"><?= $wheelsDataItem['years'][0] ?> <?=$model['title']?> Bolt Pattern</a></td>
                            <td>PCD <?=$wheelsDataItem['bolt_pattern']?></td>
                        </tr>
                    <?php endif;?>

                    <?php if (!empty($wheelFasteners)):?>
                        <tr>
                            <td>Wheel fasteners</td>
                            <td><b><?= join("-", $wheelFasteners) ?></b></td>
                        </tr>
                    <?php endif;?>

                    <?php if (!empty($wheelsDataItem['thread_size'])):?>
                        <tr>
                            <td>Thread Size</td>
                            <td>
                                <b><?=$wheelsDataItem['thread_size']?></b>
                                <br>
                                <img style="vertical-align:middle;padding-right: 5px;" src="https://upload.wikimedia.org/wikipedia/commons/thumb/4/4a/Amazon_icon.svg/16px-Amazon_icon.svg.png">
                                <a target="_blank" rel="nofollow" href="https://www.amazon.com/s?k=lug+nuts&language=en_US&linkCode=sl2&tag=wheeloffset-20&ref=as_li_ss_tl">
                                    <b>Get Lug Nuts</b>
                                </a>
                            </td>
                        </tr>
                    <?php endif;?>

                    <?php if (!empty($wheelsDataItem['bolt_pattern'])):?>
                        <tr>
                            <td>The Number of Lugs Needed</td>
                            <td>
                                <b><?=explode("x", $wheelsDataItem['bolt_pattern'])[0] . " per wheel"?></b>
                                <br>
                                <img style="vertical-align:middle;padding-right: 5px;" src="https://upload.wikimedia.org/wikipedia/commons/thumb/4/4a/Amazon_icon.svg/16px-Amazon_icon.svg.png">
                                <a target="_blank" href="https://www.amazon.com/s?k=caps+%26+covers&i=automotive&rh=n%3A15684181&linkCode=ll2&tag=wheeloffset-20&linkId=87ed8e468ec0472fc15ebea19ee51801&language=en_US&ref_=as_li_ss_tl">
                                    <b>Caps & Covers</b>
                                </a>
                            </td>
                        </tr>
                    <?php endif;?>

                    <?php if (!empty($wheelsDataItem['thread_size'])):?>
                        <tr>
                            <td>Thread Pitch</td>
                            <td><?= trim(explode("x", $wheelsDataItem['thread_size'])[1]) ?></td>
                        </tr>
                    <?php endif;?>

                        <tr>
                            <td>Lug Tightening Torque</td>
                            <td><?= !empty($wheelTighteings) ? join(" Nm- ", $wheelTighteings) . " Nm" : 'no data' ?></td>
                        </tr>

                    <?php if (!empty($wheelsDataItem['center_bore'])):?>
                        <tr>
                            <td>
                                Center bore (hub bore)
                            </td>
                            <td>
                                <?=$wheelsDataItem['center_bore']?><br>
                                <a target="_blank" rel="nofollow" href="https://www.amazon.com/s?k=center+caps&rh=n%3A15684181&linkCode=ll2&tag=wheeloffset-20&linkId=b75d40ee0be994642465625586275e2c&language=en_US&ref_=as_li_ss_tl">
                                    <img style="vertical-align:middle;padding-right: 5px;" src="https://upload.wikimedia.org/wikipedia/commons/thumb/4/4a/Amazon_icon.svg/16px-Amazon_icon.svg.png">
                                    <b>Center caps</b>
                                </a>
                            </td>
                        </tr>
                    <?php endif;?>

                   </tbody>
                </table>
            </section>
            <br>

        <?php endforeach;?>

        <br>
        <br><br>
        <?php $this->widget('application.widgets.BannerWidget', array('banner' => 'horizontal')); ?>
    </div>
</main>