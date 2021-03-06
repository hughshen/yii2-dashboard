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
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci ENGINE=InnoDB';
        }
        
        $this->createTable($this->tableName, [
            'id' => $this->primaryKey(),
            'parent' => $this->integer()->notNull()->defaultValue(0),
            'author' => $this->integer()->notNull()->defaultValue(0),
            'slug' => $this->string()->notNull()->defaultValue(''),
            'title' => $this->string()->notNull()->defaultValue(''),
            'content' => $this->text()->notNull()->defaultValue(''),
            'excerpt' => $this->text()->notNull()->defaultValue(''),
            'guid' => $this->string()->notNull()->defaultValue(''),
            'type' => $this->string(16)->notNull()->defaultValue(''),
            'image' => $this->string()->notNull()->defaultValue(''),
            'images' => $this->text()->notNull()->defaultValue(''),
            'view_count' => $this->integer()->notNull()->defaultValue(0),
            'extra_data' => $this->text()->notNull()->defaultValue(''),
            'sorting' => $this->integer()->notNull()->defaultValue(0),
            'status' => $this->string()->notNull()->defaultValue('publish'),
            'created_at' => $this->integer()->notNull()->defaultValue(0),
            'updated_at' => $this->integer()->notNull()->defaultValue(0),
            'deleted_at' => $this->integer()->notNull()->defaultValue(0),
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

        $faker = \Faker\Factory::create();

        // Add fake data
        for ($i = 0; $i < 12; $i++) {
            $this->insert($this->tableName, [
                'author' => 1,
                'slug' => $faker->slug(3),
                'title' => $faker->text(32),
                'content' => $faker->randomHtml(4, 6),
                'excerpt' => $faker->text(250),
                'type' => $faker->randomElement(['post', 'page']),
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
        $this->dropTable($this->tableName);
    }
}
