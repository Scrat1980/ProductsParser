<?php /** @noinspection PhpUndefinedClassInspection */

/** @var yii\web\View $this */

use yii\helpers\Html;

$this->title = 'Parse results';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-about">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        Parsed letter data:
    </p>

    <?php /** @var array $content */?>
    <?php

        echo "<pre>";
        var_dump($content);
        echo "</pre>";

    ?>
</div>
