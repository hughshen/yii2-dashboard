<?php

use yii\db\Migration;

/**
 * Handles the creation of table `site_language`.
 */
class m180113_104430_create_site_language_table extends Migration
{
    public $tableName;

    public function init()
    {
        parent::init();
        $this->tableName = '{{%site_language}}';
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
            'title' => $this->string()->notNull(),
            'code' => $this->string(16)->notNull(),
            'locale' => $this->string(16)->notNull(),
            'image' => $this->string(),
            'is_default' => $this->smallInteger(1)->defaultValue(0),
            'sorting' => $this->integer()->defaultValue(0),
            'status' => $this->smallInteger(1)->defaultValue(1),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ], $tableOptions);

        $this->insert($this->tableName, [
            'title' => 'English',
            'code' => 'en',
            'locale' => 'en-US',
            'is_default' => '1',
            'created_at' => time(),
            'updated_at' => time(),
        ]);

        $this->insert($this->tableName, [
            'title' => '简体中文',
            'code' => 'zh',
            'locale' => 'zh-CN',
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
