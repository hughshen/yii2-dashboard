<?php

use yii\db\Migration;

/**
 * Handles the creation of table `site_translate`.
 */
class m180113_103619_create_site_translate_table extends Migration
{
    public $tableName;

    public function init()
    {
        parent::init();
        $this->tableName = '{{%site_translate}}';
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
            'table_name' => $this->string(32)->notNull()->defaultValue(''),
            'table_id' => $this->integer()->notNull()->defaultValue(0),
            'table_field' => $this->string()->notNull()->defaultValue(''),
            'field_value' => $this->text()->defaultValue(''),
            'language' => $this->string(32)->notNull()->defaultValue(''),
            'created_at' => $this->integer()->notNull()->defaultValue(0),
            'updated_at' => $this->integer()->notNull()->defaultValue(0),
        ], $tableOptions);

        // creates index for column `table_name`
        $this->createIndex(
            'idx-site-translate-table_name',
            $this->tableName,
            'table_name'
        );

        // creates index for column `table_id`
        $this->createIndex(
            'idx-site-translate-table_id',
            $this->tableName,
            'table_id'
        );

        // creates index for column `table_field`
        $this->createIndex(
            'idx-site-translate-table_field',
            $this->tableName,
            'table_field'
        );

        // creates index for column `language`
        $this->createIndex(
            'idx-site-translate-language',
            $this->tableName,
            'language'
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
