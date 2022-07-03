<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%user}}`.
 */
class m220703_103735_create_user_table extends Migration
{
    public function safeUp() {

        $this->createTable(
            'user',
            [
                'id'          => $this->primaryKey(),
                'username'    => $this->string()->notNull(),
                'password'    => $this->string()->notNull(),
                'accessToken' => $this->string()->notNull(),
            ]
        );
    }
    
    public function safeDown() {
    
    $this->dropTable('user');
    }
}
