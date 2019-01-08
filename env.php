<?php
/**
 * yii2项目中安装了包 "vlucas/phpdotenv": "^2.4", 之后增加env.php文件
 * env.php 和  .env 放到项目的根目录(../web)下，然后在web/index.php里面添加代码 require(__DIR__ . '/../env.php');
 * 在项目中直接使用getenv()函数调用 .env里面的配置, 注意在正式环境下一定要关闭debug模式，不然报错信息里面会暴露配置信息
 * Created by PhpStorm.
 * Date: 17/4/10
 * Time: 下午8:07
 */
$dotenv = new \Dotenv\Dotenv(__DIR__);
$ret = $dotenv->load();
defined('YII_DEBUG') or define('YII_DEBUG', getenv('YII_DEBUG') == 'true');
defined('YII_ENV') or define('YII_ENV', getenv('YII_ENV') ?: 'prod');