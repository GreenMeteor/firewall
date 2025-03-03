<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
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
        <p>
            <?= Button::defaultType(Yii::t('FirewallModule.base', 'Back to Rules'))
                ->link(['index'])
                ->icon('arrow-left'); ?>

            <?= Button::danger(Yii::t('FirewallModule.base', 'Clear Logs'))
                ->action('ui.modal.confirm', \yii\helpers\Url::to(['clear-logs']), [
                    'confirmText' => Yii::t('FirewallModule.base', 'Are you sure you want to clear all firewall logs? This action cannot be undone.'),
                    'confirmTitle' => Yii::t('FirewallModule.base', '<strong>Confirm</strong> log deletion'),
                    'buttonTrue' => Yii::t('FirewallModule.base', 'Clear Logs'),
                    'buttonFalse' => Yii::t('FirewallModule.base', 'Cancel'),
                ]); ?>
        </p>

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