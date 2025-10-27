<?php

use yii\helpers\Url;
use humhub\helpers\Html;
use humhub\modules\ui\icon\widgets\Icon;

/** @var array $accessData */
/** @var bool $isAdmin */
/** @var array $guestIps */
/** @var array $loggedInIps */
/** @var array $suspiciousIps */
/** @var int $uniqueIpsCount */
/** @var int $totalAccessesCount */
?>

<div class="panel panel-default">
    <div class="panel-heading">
        <?= Yii::t('FirewallModule.base', '<strong>IP</strong> Monitoring') ?>
    </div>

    <div class="panel-body">
        <div class="row text-center">
            <div class="col-md-3">
                <?= Yii::t('FirewallModule.base', '<strong>Your IP Address</strong>') ?>
                <p>
                    <?= Html::encode($accessData['ip']) ?>
                    <?php if (isset($accessData['suspicious']) && $accessData['suspicious']): ?>
                        <span class="badge text-bg-warning" data-bs-toggle="tooltip" title="<?= Yii::t('FirewallModule.base', 'This IP may be using a proxy or VPN') ?>">
                            <?= Icon::get('exclamation-triangle') ?>
                        </span>
                    <?php endif; ?>
                </p>
            </div>
            <div class="col-md-3">
                <?= Yii::t('FirewallModule.base', '<strong>Your Status</strong>') ?>
                <p><?= $accessData['isGuest'] ? Yii::t('FirewallModule.base', 'Guest') : Yii::t('FirewallModule.base', 'Logged-In') ?></p>
            </div>
            <div class="col-md-3">
                <?= Yii::t('FirewallModule.base', '<strong>Your Username</strong>') ?>
                <p><?= Html::encode($accessData['username']) ?></p>
            </div>
            <div class="col-md-3">
                <?= Yii::t('FirewallModule.base', '<strong>Rate Limit</strong>') ?>
                <p>
                    <?php if (isset($accessData['rateLimited']) && $accessData['rateLimited']): ?>
                        <span class="badge text-bg-danger">
                            <?= Icon::get('ban') ?><?= Yii::t('FirewallModule.base', 'Limited') ?>
                        </span>
                    <?php else: ?>
                        <span class="badge text-bg-success">
                            <?= Icon::get('check') ?><?= Yii::t('FirewallModule.base', 'Normal') ?>
                        </span>
                    <?php endif; ?>
                </p>
            </div>
        </div>

        <?php if ($isAdmin): ?>
            <hr>

            <div class="row text-center">
                <div class="col-md-4">
                    <div class="bg-light p-3 rounded">
                        <h4><?= Yii::t('FirewallModule.base', 'Unique IPs') ?></h4>
                        <p class="lead"><?= $uniqueIpsCount ?></p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="bg-light p-3 rounded">
                        <h4><?= Yii::t('FirewallModule.base', 'Total Accesses') ?></h4>
                        <p class="lead"><?= $totalAccessesCount ?></p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="bg-light p-3 rounded">
                        <h4><?= Yii::t('FirewallModule.base', 'Suspicious IPs') ?></h4>
                        <p class="lead"><?= count($suspiciousIps) ?></p>
                    </div>
                </div>
            </div>

            <div class="text-end">
                <a href="<?= Url::to(['clear-ips']) ?>" class="btn btn-danger btn-sm">
                    <i class="fa fa-trash"></i><?= Yii::t('FirewallModule.base', 'Clear IP Log') ?>
                </a>
            </div>

            <hr>

            <ul class="nav nav-tabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <a class="nav-link active" data-bs-toggle="tab" href="#guest-ips" role="tab" aria-selected="true">
                        <?= Yii::t('FirewallModule.base', 'Guest IPs') ?> 
                        <span class="badge text-bg-secondary"><?= count($guestIps) ?></span>
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link" data-bs-toggle="tab" href="#logged-in-ips" role="tab" aria-selected="false">
                        <?= Yii::t('FirewallModule.base', 'Logged-in Users') ?> 
                        <span class="badge text-bg-secondary"><?= count($loggedInIps) ?></span>
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link" data-bs-toggle="tab" href="#suspicious-ips" role="tab" aria-selected="false">
                        <?= Yii::t('FirewallModule.base', 'Suspicious IPs') ?> 
                        <span class="badge text-bg-secondary"><?= count($suspiciousIps) ?></span>
                    </a>
                </li>
            </ul>

            <div class="tab-content">
                <div id="guest-ips" class="tab-pane fade show active" role="tabpanel">
                    <?php if (!empty($guestIps)): ?>
                        <div class="table-responsive">
                            <table class="table table-hover table-striped">
                                <thead>
                                    <tr>
                                        <th><?= Yii::t('FirewallModule.base', 'IP Address') ?></th>
                                        <th><?= Yii::t('FirewallModule.base', 'Last Access') ?></th>
                                        <th><?= Yii::t('FirewallModule.base', 'User Agent') ?></th>
                                        <th><?= Yii::t('FirewallModule.base', 'Actions') ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($guestIps as $ipData): ?>
                                        <tr<?= isset($ipData['suspicious']) && $ipData['suspicious'] ? ' class="table-warning"' : '' ?>>
                                            <td>
                                                <?= Html::encode($ipData['ip']) ?>
                                                <?php if (isset($ipData['suspicious']) && $ipData['suspicious']): ?>
                                                    <span class="badge text-bg-warning">
                                                        <?= Icon::get('exclamation-triangle') ?>
                                                    </span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?= Yii::$app->formatter->asRelativeTime($ipData['timestamp']) ?></td>
                                            <td>
                                                <?php if (isset($ipData['user_agent'])): ?>
                                                    <small class="text-body-secondary"><?= Html::encode(substr($ipData['user_agent'], 0, 50)) ?>...</small>
                                                <?php else: ?>
                                                    <small class="text-body-secondary">-</small>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?= Html::a(Icon::get('ban') . Yii::t('FirewallModule.base', 'Block'), ['block-ip', 'ip' => $ipData['ip']], [
                                                    'class' => 'btn btn-danger btn-sm',
                                                    'data-bs-toggle' => 'modal',
                                                    'data-bs-target' => '#globalModal'
                                                ]); ?>
                                                <?= Html::a(Icon::get('step-backward') . Yii::t('FirewallModule.base', 'Reset'), ['reset', 'ip' => $ipData['ip']], [
                                                    'class' => 'btn btn-primary btn-sm',
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

                <div id="logged-in-ips" class="tab-pane fade" role="tabpanel">
                    <?php if (!empty($loggedInIps)): ?>
                        <div class="table-responsive">
                            <table class="table table-hover table-striped">
                                <thead>
                                    <tr>
                                        <th><?= Yii::t('FirewallModule.base', 'IP Address') ?></th>
                                        <th><?= Yii::t('FirewallModule.base', 'Username') ?></th>
                                        <th><?= Yii::t('FirewallModule.base', 'Last Access') ?></th>
                                        <th><?= Yii::t('FirewallModule.base', 'Page') ?></th>
                                        <th><?= Yii::t('FirewallModule.base', 'Actions') ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($loggedInIps as $ipData): ?>
                                        <tr<?= isset($ipData['suspicious']) && $ipData['suspicious'] ? ' class="table-warning"' : '' ?>>
                                            <td>
                                                <?= Html::encode($ipData['ip']) ?>
                                                <?php if (isset($ipData['suspicious']) && $ipData['suspicious']): ?>
                                                    <span class="badge text-bg-warning">
                                                        <?= Icon::get('exclamation-triangle') ?>
                                                    </span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?= Html::encode($ipData['username']) ?></td>
                                            <td><?= Yii::$app->formatter->asRelativeTime($ipData['timestamp']) ?></td>
                                            <td>
                                                <?php if (isset($ipData['request_uri'])): ?>
                                                    <small class="text-body-secondary"><?= Html::encode(basename($ipData['request_uri'])) ?></small>
                                                <?php else: ?>
                                                    <small class="text-body-secondary">-</small>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?= Html::a(Icon::get('ban') . Yii::t('FirewallModule.base', 'Block'), ['block-ip', 'ip' => $ipData['ip']], [
                                                    'class' => 'btn btn-danger btn-sm',
                                                    'data-bs-toggle' => 'modal',
                                                    'data-bs-target' => '#globalModal'
                                                ]); ?>
                                                <?= Html::a(Icon::get('step-backward') . Yii::t('FirewallModule.base', 'Reset'), ['reset', 'ip' => $ipData['ip']], [
                                                    'class' => 'btn btn-primary btn-sm',
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

                <div id="suspicious-ips" class="tab-pane fade" role="tabpanel">
                    <?php if (!empty($suspiciousIps)): ?>
                        <div class="table-responsive">
                            <table class="table table-hover table-striped">
                                <thead>
                                    <tr>
                                        <th><?= Yii::t('FirewallModule.base', 'IP Address') ?></th>
                                        <th><?= Yii::t('FirewallModule.base', 'User Type') ?></th>
                                        <th><?= Yii::t('FirewallModule.base', 'Username') ?></th>
                                        <th><?= Yii::t('FirewallModule.base', 'Last Access') ?></th>
                                        <th><?= Yii::t('FirewallModule.base', 'User Agent') ?></th>
                                        <th><?= Yii::t('FirewallModule.base', 'Actions') ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($suspiciousIps as $ipData): ?>
                                        <tr class="table-warning">
                                            <td>
                                                <?= Html::encode($ipData['ip']) ?>
                                                <span class="badge text-bg-warning">
                                                    <?= Icon::get('exclamation-triangle') ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?= $ipData['isGuest'] ? 
                                                    '<span class="badge text-bg-secondary">' . Yii::t('FirewallModule.base', 'Guest') . '</span>' : 
                                                    '<span class="badge text-bg-primary">' . Yii::t('FirewallModule.base', 'User') . '</span>' ?>
                                            </td>
                                            <td><?= Html::encode($ipData['username']) ?></td>
                                            <td><?= Yii::$app->formatter->asRelativeTime($ipData['timestamp']) ?></td>
                                            <td>
                                                <?php if (isset($ipData['user_agent'])): ?>
                                                    <small class="text-body-secondary"><?= Html::encode(substr($ipData['user_agent'], 0, 50)) ?>...</small>
                                                <?php else: ?>
                                                    <small class="text-body-secondary">-</small>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?= Html::a(Icon::get('ban') . Yii::t('FirewallModule.base', 'Block'), ['block-ip', 'ip' => $ipData['ip']], [
                                                    'class' => 'btn btn-danger btn-sm',
                                                    'data-bs-toggle' => 'modal',
                                                    'data-bs-target' => '#globalModal'
                                                ]); ?>
                                                <?= Html::a(Icon::get('step-backward') . Yii::t('FirewallModule.base', 'Reset'), ['reset', 'ip' => $ipData['ip']], [
                                                    'class' => 'btn btn-primary btn-sm',
                                                ]); ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info">
                            <?= Yii::t('FirewallModule.base', 'No suspicious IPs detected yet.') ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<script <?= Html::nonce() ?>>
$(document).ready(function(){
    $('[data-bs-toggle="tooltip"]').tooltip();
});
</script>