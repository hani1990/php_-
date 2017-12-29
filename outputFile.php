  /**
   *php 通过 header 强制文件下载
  */
  function outputFile($fullPath) {
            $file_name = $fullPath;//图片链接
            $path_parts = pathinfo($fullPath);
            $mime = 'application/force-download';

            header('Pragma: public'); // required

            header('Expires: 0'); // no cache

            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');

            header('Cache-Control: private',false);

            header('Content-Type: '.$mime);

            header('Content-Disposition: attachment; filename='.$path_parts['basename']);

            header('Content-Transfer-Encoding: binary');

            header('Connection: close');

            readfile($file_name); // push it out

            exit();
        }
