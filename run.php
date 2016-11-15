<?php
defined('MAX') or define('MAX', 10);
$pnum = 0;
//确保这个函数只能运行在shell中
if(substr(php_sapi_name(), 0, 3) !== 'cli'){
    die('this program can only be run in CLI mode');
}

//关闭最大执行时间限制，在cli模式下，这个语句其实不必要
set_time_limit(0);

$ppid = posix_getpid();//取得主里程ID
$user = posix_getlogin();//取得用户名

echo <<<EOD

this is a test,the user is {$user},and main program is {$ppid}


EOD;



for ($i = 1; $i <= MAX; $i++) { 
    # code...

    //如果已达到最大进程数则等待子进程结束
    if($pnum > MAX){
        $cpid = pcntl_wait($status);//等待取得子进程结束状态
        if($cpid <= 0){
            exit;
        }
        //子进程正常结束则计数减1
        if(pcntl_wifexited($status)){
            echo "the sub process {$cpid} is exited with {$status}\n";
            $pnum--;
        }
    }

    //--------子进程开始-----------------------------------------
    $pid = pcntl_fork();//创建子进程
    if($pid == 0){//子进程
        $pid = posix_getpid();
        echo "* process {$pid} was created, and executed:\n\n";
        sleep(2);
        if( ($ppid + $i ) == $pid ){
            $begin = $i * 100;
            $end = $begin + 100;

            echo "\n {$i} 处理编号 {$begin} --- {$end} \n\n";
        }

        exit;
    }else if($pid > 0){//主进程
        $pnum++;
    }else{
        echo "fail to create sub process\n";
    }
    //--------子进程结束-----------------------------------------

}