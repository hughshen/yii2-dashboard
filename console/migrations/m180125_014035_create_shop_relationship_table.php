<?php

use yii\db\Migration;

/**
 * Handles the creation of table `shop_relationship`.
 */
class m180125_014035_create_shop_relationship_table extends Migration
{
    public $tableName;
    public $productTable;
    public $categoryTable;

    public function init()
    {
        parent::init();
        $this->tableName = '{{%shop_relationship}}';
        $this->productTable = '{{%shop_product}}';
        $this->categoryTable = '{{%shop_category}}';
    }

    /**
     * @inheritdoc
     */
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci ENGINE=InnoDB';
        }

        $this->createTable($this->tableName, [
            'id' => $this->primaryKey(),
            'product_id' => $this->integer()->notNull(),
            'category_id' => $this->integer()->notNull(),
        ], $tableOptions);

        // creates index for column `product_id`
        $this->createIndex(
            'idx-shop-relationship-product_id',
            $this->tableName,
            'product_id'
        );

        // creates index for column `category_id`
        $this->createIndex(
            'idx-shop-relationship-category_id',
            $this->tableName,
            'category_id'
        );
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable($this->tableName);
    }
}
