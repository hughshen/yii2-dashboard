<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\LinkPager;

?>
<div class="row">
    <?php foreach ($list as $val) { ?>
        <div class="list-item col-md-4 col-xs-6">
            <div class="item">
                <?php
                switch ($val['type']) {
                    case 'folder':
                        echo '<svg height="90px" width="90px" class="folder-icon" data-folder="' . $val['path'] . '" style="enable-background:new 0 0 512 512;" version="1.1" viewBox="0 0 512 512" xml:space="preserve" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><g><path d="M430.1,192H81.9c-17.7,0-18.6,9.2-17.6,20.5l13,183c0.9,11.2,3.5,20.5,21.1,20.5h316.2c18,0,20.1-9.2,21.1-20.5l12.1-185.3   C448.7,199,447.8,192,430.1,192z"/><g><path d="M426.2,143.3c-0.5-12.4-4.5-15.3-15.1-15.3c0,0-121.4,0-143.2,0c-21.8,0-24.4,0.3-40.9-17.4C213.3,95.8,218.7,96,190.4,96    c-22.6,0-75.3,0-75.3,0c-17.4,0-23.6-1.5-25.2,16.6c-1.5,16.7-5,57.2-5.5,63.4h343.4L426.2,143.3z"/></g></g></svg>';
                        break;
                    default:
                        echo Html::img($val['thumb'], ['class' => 'file-thumb', 'data-url' => $val['path']]);
                        break;
                }
                ?>
                <p><?= $val['name'] ?></p>
            </div>
        </div>
    <?php } ?>
</div>
<?php
echo LinkPager::widget([
    'pagination' => $pages,
    'prevPageLabel' => '&lsaquo;',
    'nextPageLabel' => '&rsaquo;',
    'firstPageLabel' => '&laquo;',
    'lastPageLabel' => '&raquo;',
    'maxButtonCount' => 4,
]);
?>
