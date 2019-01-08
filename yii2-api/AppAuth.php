<?php
/**
 * 安装"deepziyu/yii-fast-api": "*" 包， 添加v0模块，在v0目录下增加的代码
 * api登录权限验证
 *
 * Created by PhpStorm.
 * Date: 2018/1/8
 * Time: 下午4:50
 */

namespace app\modules\v0;  //命名空间可能与你的实际代码不一致，建议在项目中手动添加类文件(phpstorm 会帮助生成命名空间)
use deepziyu\yii\rest\ApiException;
use yii\filters\auth\AuthMethod;


class AppAuth extends AuthMethod
{
    /**
     * @var string the parameter name for passing the access token
     * token参数名称
     */
    public $tokenParam = 'token';
    /**
     * @inheritdoc
     */
    public function authenticate($user, $request, $response)
    {
        $token_param = $this->tokenParam;
        //header 头里面获取token
        $accessToken = $request->getHeaders()["$token_param"];
        if(empty($accessToken)){
            //get参数里面获取token
            $accessToken = $request->get("$token_param");
        }
        if(empty($accessToken)){
            //post参数里面获取token
            $accessToken = $request->post("$token_param");
        }
        if (is_string($accessToken)) {
            $identity = $user->loginByAccessToken($accessToken, get_class($this));
            if ($identity !== null) {
                return $identity;
            }
        }
        if ($accessToken !== null) {
            $this->handleFailure($response);
        }

        return null;
    }

    public function handleFailure($response)
    {
        throw new ApiException('401','token Invalid');
    }

}