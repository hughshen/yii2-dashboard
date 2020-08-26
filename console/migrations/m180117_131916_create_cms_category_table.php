<?php

use yii\db\Migration;

/**
 * Handles the creation of table `cms_category`.
 */
class m180117_131916_create_cms_category_table extends Migration
{
    public $tableName;

    public function init()
    {
        parent::init();
        $this->tableName = '{{%cms_category}}';
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
            'parent' => $this->integer()->notNull()->defaultValue(0),
            'slug' => $this->string()->notNull()->defaultValue(''),
            'title' => $this->string()->notNull()->defaultValue(''),
            'description' => $this->text()->notNull()->defaultValue(''),
            'type' => $this->string(32)->notNull()->defaultValue(''),
            'extra_data' => $this->text()->notNull()->defaultValue(''),
            'sorting' => $this->integer()->notNull()->defaultValue(0),
            'status' => $this->smallInteger(1)->notNull()->defaultValue(1),
            'created_at' => $this->integer()->notNull()->defaultValue(0),
            'updated_at' => $this->integer()->notNull()->defaultValue(0),
            'deleted_at' => $this->integer()->notNull()->defaultValue(0),
        ], $tableOptions);

        // creates index for column `type`
        $this->createIndex(
            'idx-cms-category-type',
            $this->tableName,
            'type'
        );

        // creates index for column `slug`
        $this->createIndex(
            'idx-cms-category-slug',
            $this->tableName,
            'slug'
        );

        // creates index for column `created_at`
        $this->createIndex(
            'idx-cms-category-created_at',
            $this->tableName,
            'created_at'
        );

        $faker = \Faker\Factory::create();

        // Add fake data
        for ($i = 0; $i < 10; $i++) {
            $this->insert($this->tableName, [
                'type' => $faker->randomElement(['category', 'tag']),
                'slug' => $faker->slug(3),
                'title' => $faker->text(32),
                'created_at' => time(),
                'updated_at' => time(),
            ]);
        }

        $this->insert($this->tableName, [
            'type' => 'menu',
            'slug' => 'header-menu',
            'title' => 'Header Menu',
            'created_at' => time(),
            'updated_at' => time(),
        ]);

        $this->insert($this->tableName, [
            'type' => 'menu',
            'slug' => 'footer-menu',
            'title' => 'Footer Menu',
            'created_at' => time(),
            'updated_at' => time(),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable($this->tableName);
    }
}
