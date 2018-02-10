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
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable($this->tableName, [
            'id' => $this->primaryKey(),
            'parent' => $this->integer()->defaultValue(0),
            'slug' => $this->string(),
            'title' => $this->string(),
            'description' => $this->text(),
            'type' => $this->string(32)->notNull()->defaultValue('category'),
            'extra_data' => $this->text(),
            'sorting' => $this->integer()->notNull()->defaultValue(0),
            'status' => $this->smallInteger(1)->notNull()->defaultValue(1),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
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

        $this->insert($this->tableName, [
            'type' => 'category',
            'slug' => 'test-category',
            'title' => 'Test Category',
            'created_at' => time(),
            'updated_at' => time(),
        ]);

        $this->insert($this->tableName, [
            'type' => 'tag',
            'slug' => 'test-tag',
            'title' => 'Test Tag',
            'created_at' => time(),
            'updated_at' => time(),
        ]);

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
        // drops index for column `type`
        $this->dropIndex(
            'idx-cms-category-type',
            $this->tableName
        );

        // drops index for column `slug`
        $this->dropIndex(
            'idx-cms-category-slug',
            $this->tableName
        );

        // drops index for column `created_at`
        $this->dropIndex(
            'idx-cms-category-created_at',
            $this->tableName
        );

        $this->dropTable($this->tableName);
    }
}
