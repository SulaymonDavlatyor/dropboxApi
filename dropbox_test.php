<?php
require_once 'test3.php';
$path = 'https://localhost/dropbox/dropboxapi.php';
$urls = [
       $path . '?action=getDropbox&dropboxlink=/&mode=all_path_files&apikey=12345',
       $path . '?action=getDropbox&dropboxlink=https://www.dropbox.com/s/2680kdupx9dffq7/71scGIRRdjS._AC_SL1500_.jpg?dl=0&mode=image_base64&apikey=12345',
       $path . '?action=getDropbox&dropboxlink=https://www.dropbox.com/s/2680kdupx9dffq7/71scGIRRdjS._AC_SL1500_.jpg?dl=0&mode=image_url&apikey=12345',
       $path . '?action=getDropbox&dropboxlink=https://www.dropbox.com/s/2680kdupx9dffq7/71scGIRRdjS._AC_SL1500_.jpg?dl=0&mode=thumb_url&apikey=12345',
       $path . '?action=getDropbox&dropboxlink=https://www.dropbox.com/s/2680kdupx9dffq7/71scGIRRdjS._AC_SL1500_.jpg?dl=0&mode=thumb_base64&apikey=12345',
       $path . '?action=getDropbox&dropboxlink=https://www.dropbox.com/s/2680kdupx9dffq7/71scGIRRdjS._AC_SL1500_.jpg?dl=0&mode=all_link_info&apikey=12345',
       $path . '?action=uploadDropbox&image_path=https://i.stack.imgur.com/iX1SO.png&mode=image_url&path=/&apikey=12345',
       $path . '?action=uploadDropbox&image_path=https://i.stack.imgur.com/iX1SO.png&mode=image_url&apikey=12345',
       $path . '?action=uploadDropbox&image_path=deepspeech.scorer&mode=image_url&apikey=12345',

];
out(getArrUrls($urls));

function getArrUrls($urls)
{
    $arrContextOptions=array(
            "ssl"=>array(
                    "verify_peer"=>false,
                    "verify_peer_name"=>false,
            ),
    );
    foreach ($urls as $url) {
        try {
            $resp = file_get_contents($url, false, stream_context_create($arrContextOptions));
        } catch (Exception $e) {
            $error = "Error on file_get_contents " . $url;
            outErrors($error);
        }
        $response[] = $resp;
    }
    return $response;
}

 ?>