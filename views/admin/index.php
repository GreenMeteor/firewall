<?php

use yii\widgets\DetailView;
use humhub\widgets\Button;
use yii\helpers\Html;

/**
 * @var $this yii\web\View
 * @var $dataProvider yii\data\ActiveDataProvider
 */

$this->title = Yii::t('FirewallModule.base', 'Firewall Rules');
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="panel panel-default">
    <div class="panel-heading">
        <?= Yii::t('FirewallModule.base', '<strong>Manage</strong> Firewall Rules'); ?>
    </div>
    <div class="panel-body">
        <?= Button::success(Yii::t('FirewallModule.base', 'Create Rule'))
            ->link(['create'])
            ->icon('plus'); ?>

        <?= Button::primary(Yii::t('FirewallModule.base', 'Settings'))
            ->link(['settings'])
            ->icon('cogs'); ?>

        <?= Button::danger(Yii::t('FirewallModule.base', 'Logs'))
            ->link(['logs'])
            ->icon('list'); ?>
        <hr>

        <?php if ($dataProvider->getCount() == 0): ?>
            <div class="alert alert-info text-center">
                <strong><?= Yii::t('FirewallModule.base', 'No firewall rules have been added yet.'); ?></strong>
            </div>
        <?php else: ?>
            <div class="row">
                <?php foreach ($dataProvider->getModels() as $model): ?>
                    <div class="col-md-4">
                        <div class="firewall-rule-card">
                            <div class="firewall-rule-header">
                                <h4><?= Yii::t('FirewallModule.base', 'Firewall Rule #{id}', ['id' => $model->id]); ?></h4>
                            </div>
                            
                            <div class="firewall-rule-body">
                                <?= DetailView::widget([
                                    'model' => $model,
                                    'options' => ['class' => 'detail-view-custom'],
                                    'template' => '<div class="detail-item"><span class="label">{label}:</span> <span class="value">{value}</span></div>',
                                    'attributes' => [
                                        'ip_range',
                                        [
                                            'attribute' => 'action',
                                            'format' => 'raw',
                                            'value' => $model->getActionLabel(),
                                        ],
                                        'description',
                                        'priority',
                                        [
                                            'attribute' => 'status',
                                            'format' => 'raw',
                                            'value' => function($model) {
                                                $statusLabel = $model->getStatusLabel();
                                                $toggleButton = Html::a(
                                                    '<i class="fa fa-toggle-' . ($model->status ? 'on' : 'off') . '"></i>',
                                                    ['toggle-status', 'id' => $model->id],
                                                    [
                                                        'class' => 'btn btn-xs btn-' . ($model->status ? 'primary' : 'default'),
                                                        'title' => Yii::t('FirewallModule.base', 'Toggle Status'),
                                                        'data-pjax' => '0',
                                                    ]
                                                );
                                                return $statusLabel . ' ' . $toggleButton;
                                            },
                                        ],
                                        'created_at:datetime',
                                    ],
                                ]); ?>
                            </div>

                            <div class="firewall-rule-footer">
                                <?= Button::primary(Yii::t('FirewallModule.base', 'Update'))
                                    ->link(['update', 'id' => $model->id])
                                    ->icon('pencil'); ?>

                                <?= Button::danger(Yii::t('FirewallModule.base', 'Delete'))
                                    ->link(['delete', 'id' => $model->id])
                                    ->icon('trash')
                                    ->confirm(Yii::t('FirewallModule.base', 'Are you sure you want to delete this rule?')); ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
/* Custom styling for DetailView without tables */
.detail-view-custom {
    display: flex;
    flex-direction: column;
    gap: 8px;
    padding: 10px 15px;
}

.detail-item {
    display: flex;
    justify-content: space-between;
    border-bottom: 1px solid #e0e0e0;
    padding: 8px 0;
}

.detail-item:last-child {
    border-bottom: none;
}

.detail-item .label {
    font-weight: 600;
    color: #333;
}

.detail-item .value {
    color: #666;
}

/* Toggle button styling */
.detail-item .value .btn {
    margin-left: 10px;
    vertical-align: middle;
}

.detail-item .value .label {
    vertical-align: middle;
}

/* Firewall rule card styling */
.firewall-rule-card {
    background: #fff;
    border-radius: 8px;
    border: 1px solid #e0e0e0;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
    padding: 15px;
    margin-bottom: 20px;
    display: flex;
    flex-direction: column;
    height: 100%;
}

.firewall-rule-header h4 {
    margin: 0;
    font-size: 16px;
    font-weight: 700;
    border-bottom: 2px solid #0070d1;
    padding-bottom: 8px;
    margin-bottom: 12px;
}

.firewall-rule-body {
    flex-grow: 1;
}

.firewall-rule-footer {
    display: flex;
    justify-content: space-between;
    padding-top: 10px;
    border-top: 1px solid #e0e0e0;
}

@media (max-width: 991px) {
    .col-md-4 {
        width: 50%;
    }
}

@media (max-width: 767px) {
    .col-md-4 {
        width: 100%;
    }
}
</style>
