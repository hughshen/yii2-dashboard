<?php

namespace backend\modules\shop\controllers;

use Yii;
use backend\modules\shop\models\Product;
use backend\modules\shop\models\ProductSearch;

/**
 * ProductController implements the CRUD actions for Product model.
 */
class ProductController extends \backend\controllers\BackendController
{
    use \common\traits\CrudControllerTrait;

    protected function getSearchClassName()
    {
        return ProductSearch::className();
    }

    protected function getModelClassName()
    {
        return Product::className();
    }
}
