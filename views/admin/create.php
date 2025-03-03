<?php

use yii\helpers\Html;
use humhub\widgets\Button;

/**
 * @var $this yii\web\View
 * @var $model humhub\modules\firewall\models\FirewallRule
 */

$this->title = Yii::t('FirewallModule.base', 'Create Firewall Rule');
$this->params['breadcrumbs'][] = ['label' => Yii::t('FirewallModule.base', 'Firewall Rules'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="panel panel-default">
    <div class="panel-heading">
        <?= Yii::t('FirewallModule.base', '<strong>Create</strong> firewall rule'); ?>
    </div>
    <div class="panel-body">
        <?= $this->render('_form', [
            'model' => $model,
        ]); ?>
    </div>
</div>