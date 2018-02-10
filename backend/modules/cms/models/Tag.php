<?php

namespace backend\modules\cms\models;

use Yii;

class Tag extends Category
{
    /**
     * Return type
     */
    public static function typeName()
    {
        return 'tag';
    }
}
