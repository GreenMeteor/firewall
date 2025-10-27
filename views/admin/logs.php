<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use humhub\widgets\modal\Modal;
use humhub\widgets\modal\ModalButton;
use humhub\modules\ui\icon\widgets\Icon;
use humhub\modules\firewall\assets\FirewallAssets;

/**
 * @var $this yii\web\View
 * @var $dataProvider yii\data\ActiveDataProvider
 */

FirewallAssets::register($this);

$this->title = Yii::t('FirewallModule.base', 'Firewall Logs');
$this->params['breadcrumbs'][] = $this->title;
?>

<?php Modal::beginDialog([
    'title' => Yii::t('FirewallModule.base', '<strong>Firewall</strong> access logs'),
    'size' => Modal::SIZE_EXTRA_LARGE,
    'footer' => Html::a(
        Yii::t('FirewallModule.base', 'Clear Logs'),
        ['clear-logs'],
        [
            'class' => 'btn btn-danger',
            'data-bs-toggle' => 'modal',
            'data-bs-target' => '#globalModal',
            'data' => [
                'method' => 'post',
                'confirm' => Yii::t('FirewallModule.base', 'Are you sure you want to clear all firewall logs? This action cannot be undone.'),
            ]
        ]
    ) . ' ' . ModalButton::cancel(),
]); ?>

    <?php if ($dataProvider->getCount() == 0): ?>
        <div class="alert alert-info text-center">
            <strong><?= Yii::t('FirewallModule.base', 'No firewall logs available.'); ?></strong>
        </div>
    <?php else: ?>
        <div class="row">
            <?php foreach ($dataProvider->getModels() as $log): ?>
                <div class="col-md-4">
                    <div class="firewall-log-card">
                        <div class="firewall-log-header">
                            <h4><?= Yii::t('FirewallModule.base', 'Log #{id}', ['id' => $log->id]); ?></h4>
                        </div>
                        
                        <div class="firewall-log-body">
                            <?= DetailView::widget([
                                'model' => $log,
                                'options' => ['class' => 'detail-view-custom'],
                                'template' => '<div class="detail-item"><span class="label">{label}:</span> <span class="value">{value}</span></div>',
                                'attributes' => [
                                    'ip',
                                    'url:ntext',
                                    'user_agent:ntext',
                                    'created_at:datetime',
                                ],
                            ]); ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

<?php Modal::endDialog(); ?>