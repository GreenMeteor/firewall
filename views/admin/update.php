<?php

use yii\helpers\Html;
use humhub\widgets\Button;

/**
 * @var $this yii\web\View
 * @var $model humhub\modules\firewall\models\FirewallRule
 */

$this->title = Yii::t('FirewallModule.base', 'Update Firewall Rule: {ip}', ['ip' => $model->ip_range]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('FirewallModule.base', 'Firewall Rules'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('FirewallModule.base', 'Update');
?>
<div class="panel panel-default">
    <div class="panel-heading">
        <?= Yii::t('FirewallModule.base', '<strong>Update</strong> firewall rule'); ?>
    </div>
    <div class="panel-body">
        <?= $this->render('_form', [
            'model' => $model,
        ]); ?>
    </div>
</div>