<?php

use yii\db\Migration;

/**
 * Handles the creation of table `cms_post`.
 */
class m180117_131906_create_cms_post_table extends Migration
{
    public $tableName;

    public function init()
    {
        parent::init();
        $this->tableName = '{{%cms_post}}';
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
            'parent' => $this->integer()->notNull()->defaultValue(0),
            'author' => $this->integer()->notNull()->defaultValue(0),
            'slug' => $this->string(),
            'title' => $this->string(),
            'content' => $this->text(),
            'excerpt' => $this->text(),
            'guid' => $this->string(),
            'type' => $this->string(16)->notNull()->defaultValue('post'),
            'extra_data' => $this->text(),
            'sorting' => $this->integer()->notNull()->defaultValue(0),
            'status' => $this->string()->notNull()->defaultValue('publish'),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
            'deleted_at' => $this->integer(),
        ], $tableOptions);

        // creates index for column `type`
        $this->createIndex(
            'idx-cms-post-type',
            $this->tableName,
            'type'
        );

        // creates index for column `slug`
        $this->createIndex(
            'idx-cms-post-slug',
            $this->tableName,
            'slug'
        );

        // creates index for column `status`
        $this->createIndex(
            'idx-cms-post-status',
            $this->tableName,
            'status'
        );

        // creates index for column `created_at`
        $this->createIndex(
            'idx-cms-post-created_at',
            $this->tableName,
            'created_at'
        );

        // Add data
        $this->insert($this->tableName, [
            'author' => 1,
            'slug' => 'hello-world',
            'title' => 'Hello world!',
            'content' => 'This is your first post. Edit or delete it, then start writing!',
            'created_at' => time(),
            'updated_at' => time(),
        ]);

        $this->insert($this->tableName, [
            'author' => 1,
            'slug' => 'sample-page',
            'title' => 'Sample Page',
            'content' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aliquam mi urna, ultricies a interdum sed, euismod a urna. Nulla malesuada consectetur mi, in malesuada neque dignissim sed. Vivamus vitae enim quis erat molestie malesuada pellentesque a diam. Mauris nisl leo, bibendum nec eleifend ut, feugiat sed lectus. Duis at nisi eget augue lacinia eleifend. Sed hendrerit justo vitae leo finibus, et pretium massa convallis. Proin enim velit, viverra et quam ut, viverra facilisis magna. Mauris sollicitudin aliquam ultricies.',
            'type' => 'page',
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
            'idx-cms-post-type',
            $this->tableName
        );

        // drops index for column `slug`
        $this->dropIndex(
            'idx-cms-post-slug',
            $this->tableName
        );

        // drops index for column `status`
        $this->dropIndex(
            'idx-cms-post-status',
            $this->tableName
        );

        // drops index for column `created_at`
        $this->dropIndex(
            'idx-cms-post-created_at',
            $this->tableName
        );

        $this->dropTable($this->tableName);
    }
}
