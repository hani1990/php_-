 <?php
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


static public function curl_download($remote, $local, $timeout=60) {
		$hander = curl_init();
		$fp = fopen($local,'wb');
		curl_setopt($hander,CURLOPT_URL,$remote);
		curl_setopt($hander,CURLOPT_FILE,$fp);
		curl_setopt($hander,CURLOPT_HEADER,0);
		curl_setopt($hander,CURLOPT_FOLLOWLOCATION,1);
		curl_setopt($hander,CURLOPT_TIMEOUT,$timeout);
		$curl_ret = curl_exec($hander);
		curl_close($hander);
		fclose($fp);

		if($curl_ret)
			return true;
		
		return false;
	}
