<?php
/**
 * @Author: Hzhihua
 * @Time: 17-12-12 上午11:44
 * @Email: cnzhihua@gmail.com
 */

namespace hzhihua\frappe;


use yii\web\AssetBundle;

/**
 * charts asset source
 *
 * @package hzhihua\frappe
 */
class ChartsAsset extends AssetBundle
{
    public $sourcePath = '@npm/frappe-charts/dist';

    public $js = [
        'frappe-charts.min.iife.js',
    ];
}