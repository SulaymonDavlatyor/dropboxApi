<?php


class Client
{
  private $auth_code;

  public function __construct($auth_code){
      $this->auth_code = $auth_code;
  }

  public function getList($path){
      $URL = 'https://api.dropboxapi.com/2/files/list_folder';
      $header = [
          "Authorization: Bearer " . $this->auth_code,
          "Content-type: application/json; charset=utf-8",
          "Accept: text/plain",
      ];
      $post = json_encode([
          'path' => $path
      ]);

      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $URL);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
      curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
      $result = curl_exec($ch);

      return json_decode($result,1);
  }


    public function getMetadata($path){
        $URL = 'https://api.dropboxapi.com/2/files/get_metadata';
        $header = [
            "Authorization: Bearer " . $this->auth_code,
            "Content-type: application/json; charset=utf-8",
            "Accept: text/plain",
        ];
        $post = json_encode([
            'path' => $path
        ]);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $URL);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        $result = curl_exec($ch);

        return json_decode($result,1);
    }


    public function download($path,$pathLocal = '/images/'){
        $URL = 'https://content.dropboxapi.com/2/files/download';
        $header = [
            "Authorization: Bearer ". $this->auth_code,
            "Content-type: text/plain; charset=utf-8",
            "Accept: text/plain",
            "Dropbox-API-Arg: {\"path\": \"" . $path ."\"}"
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $URL);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        $result = curl_exec($ch);



        return $result;
    }


    public function getThumbnail($path){
        $URL = 'https://content.dropboxapi.com/2/files/get_thumbnail_v2';
        $header = [
            "Authorization: Bearer " . $this->auth_code,
            "Content-type: application/json; charset=utf-8",
            "Accept: text/plain",
        ];
        $post = json_encode([
            'path' => $path
        ]);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $URL);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        $result = curl_exec($ch);

        return $result;
    }



    public function listSharedLinks($path){
        $URL = 'https://api.dropboxapi.com/2/sharing/list_shared_links';
        $header = [
            "Authorization: Bearer " . $this->auth_code,
            "Content-type: application/json; charset=utf-8",
            "Accept: text/plain",
        ];
        $post = json_encode([
            'path' => $path
        ]);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $URL);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        $result = curl_exec($ch);

        return json_decode($result,1);
    }



    public function getSharedLinks($path){
        $URL = 'https://api.dropboxapi.com/2/sharing/get_shared_links';
        $header = [
            "Authorization: Bearer " . $this->auth_code,
            "Content-type: application/json; charset=utf-8",
            "Accept: text/plain",
        ];
        $post = json_encode([
            'path' => $path
        ]);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $URL);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        $result = curl_exec($ch);

        return json_decode($result,1);
    }

    public function createSharedLink($path){
        $URL = 'https://api.dropboxapi.com/2/sharing/create_shared_link';
        $header = [
            "Authorization: Bearer " . $this->auth_code,
            "Content-type: application/json; charset=utf-8",
            "Accept: text/plain",
        ];
        $post = json_encode([
            'path' => $path
        ]);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $URL);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        $result = curl_exec($ch);

        return json_decode($result,1);
    }



    public function search($name){
        $URL = 'https://api.dropboxapi.com/2/files/search_v2';
        $header = [
            "Authorization: Bearer ". $this->auth_code ,
            "Content-type: application/json; charset=utf-8",
            "Accept: text/plain",
        ];
        $post = json_encode([
            'query' => $name,
        ]);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $URL);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        $result = curl_exec($ch);

        return json_decode($result,1);

    }


    public function upload($file,$path = '/images'){
      $size = strlen($file);

      if($size > 146800640){
       $result = $this->uploadBig($file,$path);
      }else{
       $result =  $this->uploadStandart($file,$path);
      }
      return $result;
    }

    public function uploadStandart($file ,$path = '/images'){
        $URL = 'https://content.dropboxapi.com/2/files/upload';
        $header = [
            "Authorization: Bearer " . $this->auth_code,
            "Dropbox-API-Arg: {\"path\": \"" . $path ."\",\"mode\": \"add\",\"autorename\": true,\"mute\": false,\"strict_conflict\": false}",
            "Content-type: application/octet-stream",
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $URL);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $file);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        $result = curl_exec($ch);

        return json_decode($result,1);
    }


    public function fsplit($file,$buffer=1024){

        $file_handle = fopen($file,'r');

        $file_size = filesize($file);

        $parts = $file_size / $buffer;


        $file_parts = array();

        $store_path = "splits/";


        $file_name = basename($file);

        for($i=0;$i<$parts;$i++){

            $file_part = fread($file_handle, $buffer);

            $file_part_path = $store_path.$file_name.".part$i";

            $file_new = fopen($file_part_path,'w+');

            fwrite($file_new, $file_part);

            array_push($file_parts, $file_part_path);

            fclose($file_new);
        }


        fclose($file_handle);
        return $file_parts;
    }

    function uploadBig($file , $path = '/images'){
        $fileDivided = $this->fsplit('C:\OpenServer\domains\localhost\dropbox\deepspeech.scorer',146800640);

        foreach ($fileDivided as $k => $file){
            $offset = 146800640;
            if($k != 0){
                if($k != array_key_last($fileDivided)) {
                    $part = file_get_contents($file);
                    $this->sessionAppend($part, $id,$offset * $k);

                }else{

                    $part = file_get_contents($file);
                    $result = $this->sessionFinish($part,$id,$offset*$k,$path);

                }
            }else{
                $part = file_get_contents($file);
                $id = $this->sessionStart($part)['session_id'];

            }
        }


        return $result;


    }
    function sessionStart($file){
        $URL = 'https://content.dropboxapi.com/2/files/upload_session/start';
        $header = [
            "Authorization: Bearer 3slMEIuPI9gAAAAAAAAAAVsAdvqLBuCtNr3WcXVi1eqx5OoHvHDSOLMe7mLzD1g5 ",
            "Dropbox-API-Arg: {\"close\": false}",
            "Content-type: application/octet-stream",
        ];


        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $URL);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $file);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        $result = curl_exec($ch);


        return json_decode($result,1);
    }

    function sessionAppend($file,$sessionId,$offset){


        $URL = 'https://content.dropboxapi.com/2/files/upload_session/append_v2';
        $header = [
            "Authorization: Bearer 3slMEIuPI9gAAAAAAAAAAVsAdvqLBuCtNr3WcXVi1eqx5OoHvHDSOLMe7mLzD1g5",
            "Dropbox-API-Arg: {\"cursor\": {\"session_id\": \"$sessionId\",\"offset\": $offset},\"close\": false}",
            "Content-type: application/octet-stream",
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $URL);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $file);

        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        $result = curl_exec($ch);

        return json_decode($result,1);

    }

    function sessionFinish($file,$sessionId,$offset){

        $URL = 'https://content.dropboxapi.com/2/files/upload_session/finish';
        $header = [
            "Authorization: Bearer 3slMEIuPI9gAAAAAAAAAAVsAdvqLBuCtNr3WcXVi1eqx5OoHvHDSOLMe7mLzD1g5 ",
            "Dropbox-API-Arg: {\"cursor\": {\"session_id\": \"" . $sessionId ."\",\"offset\": $offset },\"commit\": {\"path\": \"/images/big3.scorer\",\"mode\": \"add\",\"autorename\": true,\"mute\": false,\"strict_conflict\": false}}",
            "Content-type: application/octet-stream",
        ];


        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $URL);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $file);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        $result = curl_exec($ch);

        return json_decode($result,1);

    }





}