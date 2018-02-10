<?php

namespace common\traits;

use Yii;

trait CacheTrait
{
    public static function flushCache()
    {
        Yii::$app->cache->flush();
        if (Yii::$app->cache instanceof \yii\caching\FileCache) {
            Yii::$app->fileCacheFrontend->flush();
        }
    }

    public static function deleteCache($key)
    {
        Yii::$app->cache->delete($key);
        if (Yii::$app->cache instanceof \yii\caching\FileCache) {
            Yii::$app->fileCacheFrontend->delete($key);
        }
    }
}
