<?php $this->pageTitle = Yii::app()->name.' - ' . Yii::t('admin', 'Editing Model');?>

<div class="container inner-page white img-rounded">
    <div class="page-header">
        <h3><?=Yii::t('admin', 'Editing Model')?></h3>
    </div>
    <? $this->renderPartial('_form', array(
        'model' => $model,
    ))?>
</div>