<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

/**
* This is the model class for table "user".
*
* @property int    $id
* @property string $username
* @property string $password
* @property string $accessToken
*/
class User extends ActiveRecord implements IdentityInterface {

/**
 * {@inheritdoc}
 */
public static function tableName() {

    return 'user';
}

/**
 * {@inheritdoc}
 */
public function rules() {

    return [
        [['username', 'password', 'accessToken'], 'required'],
        [['username', 'password', 'accessToken'], 'string', 'max' => 255],
    ];
}

/**
 * {@inheritdoc}
 */
public function attributeLabels() {

    return [
        'id'          => 'ID',
        'username'    => 'Username',
        'password'    => 'Password',
        'accessToken' => 'Access Token',
    ];
}

public static function findIdentity($id) {

    return static::findOne($id);
}

public static function findIdentityByAccessToken($token, $type = null) {

    return static::findOne(['accessToken' => $token]);
}

public function getId() {
}

public function getAuthKey() {
}

public function validateAuthKey($authKey) {
}
}