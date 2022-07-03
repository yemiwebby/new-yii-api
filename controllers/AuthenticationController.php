<?php

namespace app\controllers;

use app\models\User;
use Yii;
use yii\web\Controller;

class AuthenticationController extends Controller {

	public $enableCsrfValidation = false;

	public function actionRegister() {

	    $request = Yii::$app->request;

	    $username = $request->post('username');
	    $password = $request->post('password');

	    if (is_null($username)) {
	        return $this->errorResponse("Required field 'username' not provided");
	    }
	    if (is_null($password)) {
	        return $this->errorResponse("Required field 'password' not provided");
	    }

	    $user = new User();
	    $user->setAttributes(
	        [
	            'username'    => $username,
	            'password'    => Yii::$app->getSecurity()->generatePasswordHash($password),
	            'accessToken' => Yii::$app->security->generateRandomString(120)
	        ]
	    );

	    $user->save();

	    Yii::$app->response->statusCode = 201;

	    return $this->asJson(
	        [
	            'message' => 'Registration completed successfully'
	        ]
	    );
	}

	private function errorResponse($message, $code = 400) {

	    Yii::$app->response->statusCode = $code;

	    return $this->asJson(['error' => $message]);
	}

	public function actionLogin() {

	    $request = Yii::$app->request;

	    $username = $request->post('username');
	    $password = $request->post('password');

	    $user = User::find()->where(['username' => $username])->one();

	    if (is_null($user)) {
	        return $this->errorResponse('Invalid login credentials provided', 401);
	    }

	    if (Yii::$app->getSecurity()->validatePassword($password, $user->password)) {
	        Yii::$app->response->statusCode = 200;

	        return $this->asJson(['token' => $user->accessToken]);

	    }
	    else {
	        return $this->errorResponse('Invalid login credentials provided', 401);
	    }

	}
}