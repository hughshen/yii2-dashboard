<?php

namespace common\models\cms;

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
