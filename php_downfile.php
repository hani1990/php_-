 function downfile($url, $filename){ 
     try{
                ob_start();
                try{
                    $ret = @readfile($url);
                }catch (Exception $e){
                    echo $e->getMessage();
                    return false;
                }

                $img=ob_get_contents();
                ob_end_clean();
            }catch (Exception $e){
                echo $e->getMessage();
                return false;
            }

        }
        try{
            $size=strlen($img);
            //文件大小
            $pwd = getcwd()."/runtime/img/";
            $filename = $pwd.$filename;
            $fp2=@fopen($filename, 'a');
            @fwrite($fp2,$img);
            @fclose($fp2);
        }catch (Exception $e){
            echo $e->getMessage();
            return false;
        }
  }
