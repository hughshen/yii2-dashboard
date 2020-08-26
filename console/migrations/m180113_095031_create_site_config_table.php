<?php

use yii\db\Migration;

/**
 * Handles the creation of table `site_config`.
 */
class m180113_095031_create_site_config_table extends Migration
{
    public $tableName;

    public function init()
    {
        parent::init();
        $this->tableName = '{{%site_config}}';
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
            'option_name' => $this->string()->notNull()->defaultValue(''),
            'option_value' => $this->text()->defaultValue(''),
            'autoload' => $this->smallInteger(1)->notNull()->defaultValue(0),
        ], $tableOptions);

        // creates index for column `option_name`
        $this->createIndex(
            'idx-site-config-option_name',
            $this->tableName,
            'option_name'
        );

        $this->insert($this->tableName, [
            'option_name' => 'site_title',
            'option_value' => 'Yii Dashboard',
            'autoload' => 1,
        ]);

        $this->insert($this->tableName, [
            'option_name' => 'site_keywords',
            'option_value' => '',
            'autoload' => 1,
        ]);

        $this->insert($this->tableName, [
            'option_name' => 'site_description',
            'option_value' => '',
            'autoload' => 1,
        ]);

        $this->insert($this->tableName, [
            'option_name' => 'site_copyright',
            'option_value' => '',
            'autoload' => 1,
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
