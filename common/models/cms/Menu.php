<?php

namespace common\models\cms;

use Yii;

class Menu extends Category
{
    /**
     * Return type
     */
    public static function typeName()
    {
        return 'menu';
    }
}
