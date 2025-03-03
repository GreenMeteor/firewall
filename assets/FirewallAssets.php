<?php

namespace humhub\modules\firewall\assets;

use Yii;
use yii\web\AssetBundle;

/**
 * Firewall related assets.
 *
 * @author ArchBlood
 */
class FirewallAssets extends AssetBundle
{
    /**
     * @inheritdoc
     */
    public $sourcePath = '@firewall/resources';

    /**
     * @inheritdoc
     */
    public $publishOptions = ['forceCopy' => false];

    public $css = [
        'css/humhub-firewall.css',
    ];
}
