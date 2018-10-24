<?php

use yii\db\Migration;

/**
 * Handles the creation of table `shop_product`.
 */
class m180124_142808_create_shop_product_table extends Migration
{
    public $tableName;

    public function init()
    {
        parent::init();
        $this->tableName = '{{%shop_product}}';
    }

    /**
     * @inheritdoc
     */
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable($this->tableName, [
            'id' => $this->primaryKey(),
            'price' => $this->decimal(9, 2)->notNull()->defaultValue(0.00),
            'quantity' => $this->integer()->notNull()->defaultValue(0),
            'weight' => $this->decimal(9, 2)->notNull()->defaultValue(0.00),
            'slug' => $this->string(),
            'title' => $this->string(),
            'content' => $this->text(),
            'description' => $this->text(),
            'image' => $this->string(),
            'images' => $this->text(),
            'view_count' => $this->integer()->defaultValue(0),
            'extra_data' => $this->text(),
            'sorting' => $this->integer()->notNull()->defaultValue(0),
            'status' => $this->smallInteger(1)->notNull()->defaultValue(1),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
            'deleted_at' => $this->integer(),
        ], $tableOptions);

        // creates index for column `slug`
        $this->createIndex(
            'idx-shop-product-slug',
            $this->tableName,
            'slug'
        );

        // creates index for column `created_at`
        $this->createIndex(
            'idx-shop-product-created_at',
            $this->tableName,
            'created_at'
        );

        $faker = \Faker\Factory::create();

        // Add fake data
        for ($i = 0; $i < 10; $i++) {
            $this->insert($this->tableName, [
                'price' => $faker->randomFloat(2, 50, 500),
                'quantity' => $faker->numberBetween(50, 200),
                'weight' => $faker->randomFloat(2, 1, 20),
                'slug' => $faker->slug(3),
                'title' => $faker->text(32),
                'content' => $faker->randomHtml(4, 6),
                'description' => $faker->text(250),
                'created_at' => time(),
                'updated_at' => time(),
            ]);
        }
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        // drops index for column `slug`
        $this->dropIndex(
            'idx-shop-product-slug',
            $this->tableName
        );

        // drops index for column `created_at`
        $this->dropIndex(
            'idx-shop-product-created_at',
            $this->tableName
        );

        $this->dropTable($this->tableName);
    }
}
