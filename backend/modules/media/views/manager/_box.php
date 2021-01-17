<?php

use yii\helpers\Url;
use yii\helpers\Html;

$path = Yii::$app->request->get('path');
$disableUrl = isset($disableUrl) && $disableUrl === true

?>
<div class="media-paths">
    <ol class="breadcrumb">
        <?php
        $linkOptions = ['class' => 'media-link', 'data-path' => ''];

        echo Html::beginTag('li');
        echo Html::a('Home', $disableUrl ? 'javascript:;' : ['index'], $linkOptions);
        echo Html::endTag('li');

        $pathArr = !empty(trim($path)) ? explode('/', $path) : [];
        if (!empty($pathArr)) {
            $nextPath = null;
            foreach ($pathArr as $val) {
                $nextPath = $nextPath === null ? $val : $nextPath . '/' . $val;
                $linkOptions['data-path'] = $nextPath;
                echo Html::beginTag('li');
                echo Html::a($val, $disableUrl ? 'javascript:;' : ['index', 'path' => $nextPath], $linkOptions);
                echo Html::endTag('li');
            }
        }
        ?>
        <?php if (isset($uploadIcon) && $uploadIcon): ?>
            <div class="upload-icon">
                <svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="upload"
                     class="svg-inline--fa fa-upload fa-w-16" role="img" xmlns="http://www.w3.org/2000/svg"
                     viewBox="0 0 512 512">
                    <path fill="currentColor"
                          d="M296 384h-80c-13.3 0-24-10.7-24-24V192h-87.7c-17.8 0-26.7-21.5-14.1-34.1L242.3 5.7c7.5-7.5 19.8-7.5 27.3 0l152.2 152.2c12.6 12.6 3.7 34.1-14.1 34.1H320v168c0 13.3-10.7 24-24 24zm216-8v112c0 13.3-10.7 24-24 24H24c-13.3 0-24-10.7-24-24V376c0-13.3 10.7-24 24-24h136v8c0 30.9 25.1 56 56 56h80c30.9 0 56-25.1 56-56v-8h136c13.3 0 24 10.7 24 24zm-124 88c0-11-9-20-20-20s-20 9-20 20 9 20 20 20 20-9 20-20zm64 0c0-11-9-20-20-20s-20 9-20 20 9 20 20 20 20-9 20-20z"></path>
                </svg>
            </div>
        <?php endif; ?>
    </ol>
</div>

<div class="media-box">
    <?php foreach ($list as $item): ?>
        <div class="media-item media-<?= $item['type'] ?>">
            <div class="media-thumbnail">
                <?php
                $img = null;
                $target = null;
                $linkOptions = [
                    'class' => 'media-link',
                    'data-dir' => $item['dirname'],
                    'data-url' => '',
                    'data-path' => $item['path'],
                ];
                switch ($item['type']) {
                    case 'dir':
                        $url = Url::to(['index', 'path' => $item['path']]);
                        $img = '<svg width="100px" height="100px" viewBox="0 0 16 16" class="bi bi-folder-fill" fill="black" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M9.828 3h3.982a2 2 0 0 1 1.992 2.181l-.637 7A2 2 0 0 1 13.174 14H2.826a2 2 0 0 1-1.991-1.819l-.637-7a1.99 1.99 0 0 1 .342-1.31L.5 3a2 2 0 0 1 2-2h3.672a2 2 0 0 1 1.414.586l.828.828A2 2 0 0 0 9.828 3zm-8.322.12C1.72 3.042 1.95 3 2.19 3h5.396l-.707-.707A1 1 0 0 0 6.172 2H2.5a1 1 0 0 0-1 .981l.006.139z"/></svg>';
                        break;
                    case 'file':
                        $target = '_blank';
                        $url = $item['url'];
                        $linkOptions['data-url'] = $item['url'];
                        $linkOptions['data-image'] = $item['image'];

                        if ($item['image'] === 1) {
                            $img = Html::img($item['url']);
                        } else {
                            $img = '<svg width="90px" height="100px" viewBox="0 0 16 16" class="bi bi-file-text-fill" fill="black" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M12 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2zM5 4a.5.5 0 0 0 0 1h6a.5.5 0 0 0 0-1H5zm-.5 2.5A.5.5 0 0 1 5 6h6a.5.5 0 0 1 0 1H5a.5.5 0 0 1-.5-.5zM5 8a.5.5 0 0 0 0 1h6a.5.5 0 0 0 0-1H5zm0 2a.5.5 0 0 0 0 1h3a.5.5 0 0 0 0-1H5z"/></svg>';
                        }

                        break;
                }

                if (isset($disableUrl) && $disableUrl) {
                    $url = 'javascript:;';
                    $target = null;
                }

                if ($img !== null) {
                    $linkOptions['target'] = $target;
                    echo Html::a($img, $url, $linkOptions);
                }
                ?>
            </div>

            <?php if (isset($caption) && $caption): ?>
                <div class="media-caption">
                    <span class="name">
                        <?= Html::a($item['basename'], $url, ['title' => $item['basename'], 'target' => $target]) ?>
                    </span>

                    <a href="<?= Url::to(['delete', 'path' => $item['path'], 'type' => $item['type']]) ?>"
                       data-confirm="<?= Yii::t('app', 'Are you sure you want to delete this item?') ?>"
                       data-method="post"><i class="glyphicon glyphicon-trash"></i></a>

                    <?php if (false && $item['type'] !== 'dir') { ?>
                        <a href="javascript:;" class="copy-text" data-text="<?= $item['path'] ?>"><i
                                    class="glyphicon glyphicon-ok"></i></a>
                    <?php } ?>
                </div>
            <?php endif; ?>

            <?php if (isset($mediaName) && $mediaName): ?>
                <div class="media-name">
                    <span class="name"><?= $item['basename'] ?></span>
                </div>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
</div>
<div class="clearfix"></div>
