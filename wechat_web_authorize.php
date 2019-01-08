<?php
namespace app\models;
use Gaoming13\WechatPhpSdk\Wechat as Wechat;
use Gaoming13\WechatPhpSdk\Api;
use yii;

class Weixin extends \yii\db\ActiveRecord
{
    /*
     * 返回 wechat 对象
     * */
    public static function getWechat(){
        $wechat = new Wechat(array(
            'appId' => Yii::$app->params['appId'],
            'token' => Yii::$app->params['token'],
            'encodingAESKey' =>	Yii::$app->params['encodingAESKey']
        ));
        return $wechat;
    }

    public static  function getApi(){
        $m = Yii::$app->cache;
        // api模块 - 包含各种系统主动发起的功能
        $api = new Api(
            array(
                'appId' => Yii::$app->params['appId'],
                'appSecret'	=> Yii::$app->params['appSecret'],
                'get_access_token' => function() use ($m) {
                    // 用户需要自己实现access_token的返回
                    return $m->get('access_token');
                },
                'save_access_token' => function($token) use ($m) {
                    // 用户需要自己实现access_token的保存
                    $m->set('access_token', $token, 7200);
                }
            )
        );
        return $api;
    }

    public static  function getOpenid()
    {
        //通过code获得openid
        if (!isset($_GET['code'])){
            //触发微信返回code码
            $baseUrl = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'];
            $api = Weixin::getApi();
            $url = $api->get_authorize_url('snsapi_base', $baseUrl);
            Header("Location:$url");
            exit();
        } else {
            $api = Weixin::getApi();
            //微信网页授权
            $auth_data = $api->get_userinfo_by_authorize('snsapi_base');
            if ($auth_data[1]) {
                $data = $auth_data[1];
                $openid = $data->openid;
                return $openid;
            }
        }
    }
}
