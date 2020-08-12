<?php $this->pageTitle = Yii::app()->name.' - ' . Yii::t('admin', 'Creating Body style');?>

<div class="container inner-page white img-rounded">
    <div class="page-header">
        <h3><?=Yii::t('admin', 'Creating Body style')?></h3>
    </div>
    <? $this->renderPartial('_form', array(
        'model' => $model,
    ))?>
</div>