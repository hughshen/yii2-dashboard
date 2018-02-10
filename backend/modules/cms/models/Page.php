<?php

namespace backend\modules\cms\models;

use Yii;

class Page extends Post
{
    /**
     * Return type name
     */
    public static function typeName()
    {
        return 'page';
    }
}
