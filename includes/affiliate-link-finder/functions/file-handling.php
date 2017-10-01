<?php

if (!function_exists('exo_extract_remote_zip')) {

function exo_extract_remote_zip($new_file_loc, $tmp_file_loc, $zip_url) {

    echo 'Copying Zip to local....<br>';

    // read the zip
    if ( $zip_str = exo_file_get_contents_curl( $zip_url ) ) {

      // write the zip to local
      if (  !file_put_contents( $tmp_file_loc, $zip_str ) ) {
        echo "failed to write the zip to: " . $zip_url."<br>";
        return FALSE;
      }

    } else {
      echo "failed to read the zip from: " . $zip_url."<br>";
      return FALSE;
    }

    //unzip
    $zip = new ZipArchive;
    $res = $zip->open($tmp_file_loc);

    if ($res === TRUE) {
        echo 'Extracting Zip....<br>';
        if(! $zip->extractTo($new_file_loc)){
            echo 'Couldnt extract!<br>';
        }
        $zip->close();
        echo 'Deleting local copy....<br>';
        unlink($tmp_file_loc);
        return 1;
        echo 'Extracted..<br>';


    } else {
        echo 'Failed to open tmp zip!<br>';
        return 0;
    }
}

}


if (!function_exists('exo_file_get_contents_curl')) {

function exo_file_get_contents_curl( $url ) {

  $ch = curl_init();

  curl_setopt( $ch, CURLOPT_AUTOREFERER, TRUE );
  curl_setopt( $ch, CURLOPT_HEADER, 0 );
  curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
  curl_setopt( $ch, CURLOPT_URL, $url );
  curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, TRUE );

  $data = curl_exec( $ch );
  if ( curl_errno( $ch ) <> FALSE ) {
    echo "ERROR at line " . __LINE__ . " in exo_file_get_contents_curl: error number: " . curl_errno( $ch ) . ' error : ' . curl_error( $ch ) . " url: $url";
    return FALSE;
  }

  curl_close( $ch );

  return $data;

}

}


if (!function_exists('exo_download_in_chunks')) {

function exo_download_in_chunks($srcName, $dstName, $chunkSize = 1, $returnbytes = true) {

    $chunksize = $chunkSize*(1024*1024); // How many bytes per chunk
    $data = '';
    $bytesCount = 0;
    $handle = fopen($srcName, 'rb');
    $fp = fopen($dstName, 'w');
    if ($handle === false) {
        return false;
    }
    while (!feof($handle)) {
        $data = fread($handle, $chunksize);
        fwrite($fp, $data, strlen($data));
        if ($returnbytes) {
            $bytesCount += strlen($data);
        }
    }
    $status = fclose($handle);
    fclose($fp);
    if ($returnbytes && $status) {
        return $bytesCount; // Return number of bytes delivered like readfile() does.
    }
    return $status;
}

}


if (!function_exists('exo_download_csv')) {

function exo_download_csv($new_file_loc, $url) {

    echo 'Downloading CSV....<br>';

    //copy file to local
    if (!copy($url, $new_file_loc)) {
        echo "Failed to copy CSV from ".$url."...<br>";
    }
}

}


if (!function_exists('exo_delete_files_from_dir')) {

function exo_delete_files_from_dir($dir) {

    if(!is_dir($dir)) {
        echo 'Tried to delete directory '.$dir.' but it doesnt exist<br>';
        echo 'Creating directory: '.$dir.'<br>';
        mkdir($dir, 0777, true);
    }

    $files = exo_get_files_from_dir($dir);

    if(empty($files)) {
        echo 'Directory was empty...<br>';
        return;
    }

    foreach($files as $file) {
        echo 'Deleting '.$file.'<br>';
        unlink($dir.$file);
    }
}

}


if (!function_exists('exo_get_files_from_dir')) {

function exo_get_files_from_dir($dir) {

    if(!is_dir($dir)) {
        echo 'Tried to get files from directory '.$dir.' but it doesnt exist<br>';
        return;
    }

    $files = array_diff(scandir($dir,1), array('..', '.'));
    return $files;
}

}

?>
