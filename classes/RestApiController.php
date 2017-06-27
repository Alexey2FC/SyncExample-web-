<?php
namespace app\classes;
use app\classes\ApiHelper;
use Yii;
use yii\base\Exception;
use yii\web\Response;

class RestApiController extends \yii\web\Controller
{
    const DEFAULT_OK = "OK";
    const DEFAULT_FAIL = "FAIL";
    const INVALID_TOKEN = "INVALID_TOKEN";
    const JSON_DECODE_FAIL = "JSON_DECODE_FAIL";

    protected $user = null;
    protected $token = null;
    public $enableCsrfValidation = false;

    public function beforeAction($action) {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $token = Yii::$app->request->get("token");

        if( $token == null ) {
            $token = Yii::$app->request->post("token");
        }
        if( empty($token) ) {
            throw new Exception(self::INVALID_TOKEN);
        }

        $this->token = $token;
        $this->user = ApiHelper::getUserByToken($token);

        if( $this->user === null ) {
            throw new Exception(self::INVALID_TOKEN);
        }
        if (parent::beforeAction($action)) {
            Yii::$app->user->setIdentity($this->user);
            return true;
        }
        return false;
    }
}
