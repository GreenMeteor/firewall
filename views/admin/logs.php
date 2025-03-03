<?php

use yii\helpers\Html;
use yii\widgets\Pjax;
use yii\grid\GridView;
use humhub\widgets\Button;

/**
 * @var $this yii\web\View
 * @var $dataProvider yii\data\ActiveDataProvider
 */

$this->title = Yii::t('FirewallModule.base', 'Firewall Logs');
$this->params['breadcrumbs'][] = ['label' => Yii::t('FirewallModule.base', 'Firewall Rules'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="panel panel-default">
    <div class="panel-heading">
        <?= Yii::t('FirewallModule.base', '<strong>Firewall</strong> access logs'); ?>
    </div>
    <div class="panel-body">
        <?= Button::defaultType(Yii::t('FirewallModule.base', 'Back to Rules'))
            ->link(['index'])
            ->icon('arrow-left'); ?>

        <?= Html::a(
            Yii::t('FirewallModule.base', 'Clear Logs'),
            ['clear-logs'],
            [
                'class' => 'btn btn-danger',
                'data' => [
                    'method' => 'post',
                    'confirm' => Yii::t('FirewallModule.base', 'Are you sure you want to clear all firewall logs? This action cannot be undone.'),
                ]
            ]
        ); ?>

        <?php Pjax::begin(); ?>
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'columns' => [
                'id',
                'ip',
                'url:ntext',
                'user_agent:ntext',
                'created_at:datetime',
            ],
        ]); ?>
        <?php Pjax::end(); ?>
    </div>
</div>
