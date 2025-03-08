<?php

use yii\helpers\Url;
use humhub\libs\Html;
use humhub\modules\ui\icon\widgets\Icon;

/** @var array $accessData */
/** @var bool $isAdmin */
/** @var array $guestIps */
/** @var array $loggedInIps */
/** @var int $uniqueIpsCount */
/** @var int $totalAccessesCount */
?>

<div class="panel panel-default">
    <div class="panel-heading">
        <?= Yii::t('FirewallModule.base', '<strong>IP</strong> Monitoring') ?>
    </div>

    <div class="panel-body">
        <div class="row text-center">
            <div class="col-md-4">
                <?= Yii::t('FirewallModule.base', '<strong>Your IP Address</strong>') ?>
                <p><?= Html::encode($accessData['ip']) ?></p>
            </div>
            <div class="col-md-4">
                <?= Yii::t('FirewallModule.base', '<strong>Your Status</strong>') ?>
                <p><?= $accessData['isGuest'] ? Yii::t('FirewallModule.base', 'Guest') : Yii::t('FirewallModule.base', 'Logged-In') ?></p>
            </div>
            <div class="col-md-4">
                <?= Yii::t('FirewallModule.base', '<strong>Your Username</strong>') ?>
                <p><?= Html::encode($accessData['username']) ?></p>
            </div>
        </div>

        <?php if ($isAdmin): ?>
            <hr>

            <div class="row text-center">
                <div class="col-md-6">
                    <div class="well">
                        <h4><?= Yii::t('FirewallModule.base', 'Unique IPs') ?></h4>
                        <p class="lead"><?= $uniqueIpsCount ?></p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="well">
                        <h4><?= Yii::t('FirewallModule.base', 'Total Accesses') ?></h4>
                        <p class="lead"><?= $totalAccessesCount ?></p>
                    </div>
                </div>
            </div>

            <div class="text-right">
                <a href="<?= Url::to(['/firewall/admin/clear-ips']) ?>" class="btn btn-danger btn-sm">
                    <i class="fa fa-trash"></i> <?= Yii::t('FirewallModule.base', 'Clear IP Log') ?>
                </a>
            </div>

            <hr>

            <ul class="nav nav-tabs">
                <li class="active">
                    <a data-toggle="tab" href="#guest-ips">
                        <?= Yii::t('FirewallModule.base', 'Guest IPs') ?> 
                        <span class="badge"><?= count($guestIps) ?></span>
                    </a>
                </li>
                <li>
                    <a data-toggle="tab" href="#logged-in-ips">
                        <?= Yii::t('FirewallModule.base', 'Logged-in Users') ?> 
                        <span class="badge"><?= count($loggedInIps) ?></span>
                    </a>
                </li>
            </ul>

            <div class="tab-content">
                <div id="guest-ips" class="tab-pane fade in active">
                    <?php if (!empty($guestIps)): ?>
                        <div class="table-responsive">
                            <table class="table table-hover table-striped">
                                <thead>
                                    <tr>
                                        <th><?= Yii::t('FirewallModule.base', 'IP Address') ?></th>
                                        <th><?= Yii::t('FirewallModule.base', 'Last Access') ?></th>
                                        <th><?= Yii::t('FirewallModule.base', 'Actions') ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($guestIps as $ipData): ?>
                                        <tr>
                                            <td><?= Html::encode($ipData['ip']) ?></td>
                                            <td><?= Yii::$app->formatter->asRelativeTime($ipData['timestamp']) ?></td>
                                            <td>
                                                <?= Html::a(Icon::get('ban') . Yii::t('FirewallModule.base', 'Block'), ['block-ip', 'ip' => $ipData['ip']], [
                                                    'class' => 'btn btn-primary btn-xs',
                                                    'data-toggle' => 'modal',
                                                    'data-target' => '#globalModal'
                                                ]); ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info">
                            <?= Yii::t('FirewallModule.base', 'No guest IPs recorded yet.') ?>
                        </div>
                    <?php endif; ?>
                </div>

                <div id="logged-in-ips" class="tab-pane fade">
                    <?php if (!empty($loggedInIps)): ?>
                        <div class="table-responsive">
                            <table class="table table-hover table-striped">
                                <thead>
                                    <tr>
                                        <th><?= Yii::t('FirewallModule.base', 'IP Address') ?></th>
                                        <th><?= Yii::t('FirewallModule.base', 'Username') ?></th>
                                        <th><?= Yii::t('FirewallModule.base', 'Last Access') ?></th>
                                        <th><?= Yii::t('FirewallModule.base', 'Actions') ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($loggedInIps as $ipData): ?>
                                        <tr>
                                            <td><?= Html::encode($ipData['ip']) ?></td>
                                            <td><?= Html::encode($ipData['username']) ?></td>
                                            <td><?= Yii::$app->formatter->asRelativeTime($ipData['timestamp']) ?></td>
                                            <td>
                                                <?= Html::a(Icon::get('ban') . Yii::t('FirewallModule.base', 'Block'), ['block-ip', 'ip' => $ipData['ip']], [
                                                    'class' => 'btn btn-primary btn-xs',
                                                    'data-toggle' => 'modal',
                                                    'data-target' => '#globalModal'
                                                ]); ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info">
                            <?= Yii::t('FirewallModule.base', 'No logged-in users recorded yet.') ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
