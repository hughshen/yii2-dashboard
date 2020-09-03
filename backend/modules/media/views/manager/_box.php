<?php

use yii\helpers\Url;
use yii\helpers\Html;

?>
<div class="media-box">
    <?php foreach ($list as $item): ?>
        <div class="media-item media-<?= $item['type'] ?>">
            <div class="media-thumbnail" data-path="<?= $item['path'] ?>">
                <?php
                $img = null;
                $target = null;
                switch ($item['type']) {
                    case 'folder':
                        $url = Url::to(['index', 'folder' => $item['path']]);
                        $img = '<svg height="120px" width="120px" style="enable-background:new 0 0 512 512;" version="1.1" viewBox="0 0 512 512" xml:space="preserve" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><g><path d="M430.1,192H81.9c-17.7,0-18.6,9.2-17.6,20.5l13,183c0.9,11.2,3.5,20.5,21.1,20.5h316.2c18,0,20.1-9.2,21.1-20.5l12.1-185.3   C448.7,199,447.8,192,430.1,192z"/><g><path d="M426.2,143.3c-0.5-12.4-4.5-15.3-15.1-15.3c0,0-121.4,0-143.2,0c-21.8,0-24.4,0.3-40.9-17.4C213.3,95.8,218.7,96,190.4,96    c-22.6,0-75.3,0-75.3,0c-17.4,0-23.6-1.5-25.2,16.6c-1.5,16.7-5,57.2-5.5,63.4h343.4L426.2,143.3z"/></g></g></svg>';
                        break;
                    default:
                        $target = '_blank';
                        $url = $item['path'];
                        $img = Html::img($item['thumb']);
                        break;
                }

                if (isset($disableUrl) && $disableUrl) {
                    $url = 'javascript:;';
                    $target = null;
                }

                if ($img !== null) {
                    echo Html::a($img, $url, ['target' => $target]);
                }
                ?>
            </div>

            <?php if (isset($caption) && $caption): ?>
                <div class="media-caption">
                    <span class="name">
                        <?= Html::a($item['name'], $url, ['title' => $item['name'], 'target' => $target]) ?>
                    </span>

                    <?php if ((!isset($item['trash']) || $item['trash']) && $item['name'] != '..') { ?>
                        <a href="<?= Url::to(['delete', 'path' => $item['path'], 'type' => $item['type']]) ?>"
                           data-confirm="<?= Yii::t('app', 'Are you sure you want to delete this item?') ?>"
                           data-method="post"><i class="glyphicon glyphicon-trash"></i></a>
                    <?php } ?>

                    <?php if (false && $item['type'] == 'image') { ?>
                        <a href="javascript:;" class="copy-text" data-text="<?= $item['path'] ?>"><i
                                class="glyphicon glyphicon-ok"></i></a>
                    <?php } ?>
                </div>
            <?php endif; ?>

            <?php if (isset($mediaName) && $mediaName): ?>
                <div class="media-name">
                    <span class="name"><?= $item['name'] ?></span>
                </div>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
</div>
<div class="clearfix"></div>