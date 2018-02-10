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
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable($this->tableName, [
            'id' => $this->primaryKey(),
            'table_name' => $this->string(32)->notNull(),
            'table_id' => $this->integer()->notNull(),
            'table_field' => $this->string()->notNull(),
            'field_value' => $this->text(),
            'language' => $this->string(32)->notNull(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
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
        // drops index for column `table_name`
        $this->dropIndex(
            'idx-site-translate-table_name',
            $this->tableName
        );

        // drops index for column `table_id`
        $this->dropIndex(
            'idx-site-translate-table_id',
            $this->tableName
        );

        // drops index for column `table_field`
        $this->dropIndex(
            'idx-site-translate-table_field',
            $this->tableName
        );

        // drops index for column `language`
        $this->dropIndex(
            'idx-site-translate-language',
            $this->tableName
        );
        
        $this->dropTable($this->tableName);
    }
}
