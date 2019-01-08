<?php
namespace app\modules\v0;

use deepziyu\yii\rest\ApiException;
use yii\base\DynamicModel;
use yii\base\InlineAction;
use yii\filters\auth\AuthMethod;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\web\BadRequestHttpException;

/**
 * 带 token 验证的基类
 * Created by PhpStorm.
 * Date: 2017/12/27
 * Time: 下午3:00
 */
class AuthController extends BaseController
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => AppAuth::className(),
            'tokenParam' => 'token',
            'optional' => $this->authOptional(),

        ];

        return $behaviors;
    }

}