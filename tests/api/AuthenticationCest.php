<?php

use app\models\User;
use Codeception\Util\HttpCode;
use Faker\Factory;

class AuthenticationCest {

    private $faker;

    public function _before(ApiTester $I) {

        $this->faker = Factory::create();
    }

    public function registerSuccessfully(ApiTester $I) {

        $username = $this->faker->username();

        $I->sendPost(
            'register',
            [
                'username' => $username,
                'password' => $this->faker->password()
            ]
        );

        $I->seeResponseCodeIs(HttpCode::CREATED);
        $I->seeResponseContains('"message":"Registration completed successfully"');
        $I->seeRecord(
            User::class,
            [
                'username' => $username
            ]
        );
    }

    public function loginSuccessfully(ApiTester $I) {

        $username = $this->faker->username();
        $password = $this->faker->password();
        $hashedPassword = Yii::$app->getSecurity()->generatePasswordHash($password);
        $accessToken = Yii::$app->security->generateRandomString(120);

        $I->haveRecord(
            User::class,
            [
                'username'    => $username,
                'password'    => $hashedPassword,
                'accessToken' => $accessToken
            ]
        );

        $I->sendPost(
            'login',
            [
                'username' => $username,
                'password' => $password
            ]
        );

        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseMatchesJsonType(
            [
                'token' => 'string:!empty',
            ]
        );

        $actualToken = $I->grabDataFromResponseByJsonPath('token')[0];
        $I->assertEquals($accessToken, $actualToken);
    }

    public function tryToRegisterWithoutUsernameAndFail(ApiTester $I) {

        $I->sendPost(
            'register',
            [
                'password' => $this->faker->password()
            ]
        );

        $I->seeResponseCodeIs(HttpCode::BAD_REQUEST);
        $I->seeResponseContains('"error":"Required field \'username\' not provided"');
    }

    public function tryToRegisterWithoutPasswordAndFail(ApiTester $I) {

        $I->sendPost(
            'register',
            [
                'username' => $this->faker->username(),
            ]
        );

        $I->seeResponseCodeIs(HttpCode::BAD_REQUEST);
        $I->seeResponseContains('"error":"Required field \'password\' not provided"');
    }

    public function tryToLoginWithInvalidPasswordAndFail(ApiTester $I) {

        $username = $this->faker->username();
        $password = $this->faker->password();
        $hashedPassword = Yii::$app->getSecurity()->generatePasswordHash($password);
        $accessToken = Yii::$app->security->generateRandomString(120);

        $I->haveRecord(
            User::class,
            [
                'username'    => $username,
                'password'    => $hashedPassword,
                'accessToken' => $accessToken
            ]
        );

        $I->sendPost(
            'login',
            [
                'username' => $username,
                'password' => "$password _invalid"
            ]
        );

        $I->seeResponseCodeIs(HttpCode::UNAUTHORIZED);
        $I->seeResponseContains('"error":"Invalid login credentials provided"');
    }

    public function tryToLoginWithInvalidUsernameAndFail(ApiTester $I) {

        $username = $this->faker->username();
        $password = $this->faker->password();
        $hashedPassword = Yii::$app->getSecurity()->generatePasswordHash($password);
        $accessToken = Yii::$app->security->generateRandomString(120);

        $I->haveRecord(
            User::class,
            [
                'username'    => $username,
                'password'    => $hashedPassword,
                'accessToken' => $accessToken
            ]
        );

        $I->sendPost(
            'login',
            [
                'username' => "$username _invalid",
                'password' => $password
            ]
        );

        $I->seeResponseCodeIs(HttpCode::UNAUTHORIZED);
        $I->seeResponseContains('"error":"Invalid login credentials provided"');
    }

    public function tryToLoginWithNonExistentUserAndFail(ApiTester $I) {

        $I->sendPost(
            'login',
            [
                'username' => $this->faker->username(),
                'password' => $this->faker->password()
            ]
        );

        $I->seeResponseCodeIs(HttpCode::UNAUTHORIZED);
        $I->seeResponseContains('"error":"Invalid login credentials provided"');
    }
}