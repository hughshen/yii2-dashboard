<?php

use yii\db\Migration;

/**
 * Handles the creation of table `site_user`.
 */
class m180113_084143_create_site_user_table extends Migration
{
    public $tableName;

    public function init()
    {
        parent::init();
        $this->tableName = '{{%site_user}}';
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
            'username' => $this->string()->notNull()->unique(),
            'auth_key' => $this->string(32)->notNull(),
            'password_hash' => $this->string()->notNull(),
            'password_reset_token' => $this->string()->unique(),
            'email' => $this->string()->notNull()->unique(),
            'role' => $this->string(32)->notNull(),
            'role_group' => $this->string(32)->notNull(),
            'extra_data' => $this->text(),
            'status' => $this->smallInteger(1)->notNull()->defaultValue(10),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
            'deleted_at' => $this->integer(),
        ], $tableOptions);

        // Add manager user
        $this->insert($this->tableName, [
            'username' => 'admin',
            'auth_key' => \Yii::$app->security->generateRandomString(),
            'password_hash' => \Yii::$app->security->generatePasswordHash('admin'),
            'email' => 'manager@example.com',
            'role' => 'manager',
            'role_group' => 'backend',
            'created_at' => time(),
            'updated_at' => time(),
        ]);

        // Add frontend user
        $this->insert($this->tableName, [
            'username' => 'test',
            'auth_key' => \Yii::$app->security->generateRandomString(),
            'password_hash' => \Yii::$app->security->generatePasswordHash('test'),
            'email' => 'user@example.com',
            'role' => 'user',
            'role_group' => 'frontend',
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
