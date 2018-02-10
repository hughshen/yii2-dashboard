<?php

use yii\db\Migration;

/**
 * Handles the creation of table `cms_relationship`.
 */
class m180117_132018_create_cms_relationship_table extends Migration
{
    public $tableName;
    public $postTable;
    public $categoryTable;

    public function init()
    {
        parent::init();
        $this->tableName = '{{%cms_relationship}}';
        $this->postTable = '{{%cms_post}}';
        $this->categoryTable = '{{%cms_category}}';
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
            'post_id' => $this->integer()->notNull(),
            'category_id' => $this->integer()->notNull(),
        ], $tableOptions);

        // creates index for column `post_id`
        $this->createIndex(
            'idx-cms-relationship-post_id',
            $this->tableName,
            'post_id'
        );

        // add foreign key for table `post`
        $this->addForeignKey(
            'fk-cms-relationship-post_id',
            $this->tableName,
            'post_id',
            $this->postTable,
            'id',
            'CASCADE'
        );

        // creates index for column `category_id`
        $this->createIndex(
            'idx-cms-relationship-category_id',
            $this->tableName,
            'category_id'
        );

        // add foreign key for table `category`
        $this->addForeignKey(
            'fk-cms-relationship-category_id',
            $this->tableName,
            'category_id',
            $this->categoryTable,
            'id',
            'CASCADE'
        );
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        // drops foreign key for table `post`
        $this->dropForeignKey(
            'fk-cms-relationship-post_id',
            $this->tableName
        );

        // drops index for column `post_id`
        $this->dropIndex(
            'idx-cms-relationship-post_id',
            $this->tableName
        );

        // drops foreign key for table `category`
        $this->dropForeignKey(
            'fk-cms-relationship-category_id',
            $this->tableName
        );

        // drops index for column `category_id`
        $this->dropIndex(
            'idx-cms-relationship-category_id',
            $this->tableName
        );

        $this->dropTable($this->tableName);
    }
}
