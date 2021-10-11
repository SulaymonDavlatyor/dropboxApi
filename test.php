<?php


include 'Client.php';
//токен приложения
$client = new Client('3slMEIuPI9gAAAAAAAAAAVsAdvqLBuCtNr3WcXVi1eqx5OoHvHDSOLMe7mLzD1g5');

$cnf['httpPath'] = 'https://dropbox.com/home/';
$cnf['preview_formats'] = ['img', 'png', 'jpg', '3fr', 'ai', 'arw', 'bmp', 'cr2', 'crw', 'dcr', 'dcs', 'dng', 'dwg', 'erf', 'gif', 'heic', 'jpeg', 'kdc', 'mef', 'mos', 'mrw', 'nef', 'nrw', 'orf', 'pef', 'ppm', 'psd', 'r3d', 'raf', 'rw2', 'rwl', 'sr2', 'svg', 'svgz', 'tif', 'tiff', 'wbmp', 'webp', 'x3f',];
$cnf['return_errors'] = false;
$cnf['images'] = [
    'img' => 'image_url',
    'img_base64' => 'image_base64',
    'thumb' => 'thumb_url',
    'thumb_base64' => 'thumb_base64'
];
$cnf['thumb']['size'] = 'w64h64';
$cnf['thumb']['format'] = 'jpeg';
set_error_handler('outErrors', E_ALL);
set_exception_handler('outException');
//загрузка

function upload($url = false, $file = false, $path = '/images')
{
    global $client;
    global $cnf;

    if (!empty($file)) {
        $url = $file['tmp_name'];
        $fileName = $file['name'];
    } else {
        preg_match('/([^\/]*?\.[a-z0-9]{1,5})[^\/]*$/', $url, $matches);

        if ($matches[0]) {
            $fileName = $matches[0];
        } else {
            $error = "Preg_match didnt work on " . $url;
            outErrors(E_USER_ERROR, $error);
            return false;
        }
    }

    preg_match('/[a-z0-9]{1,5}/', $fileName, $matches);

    if (empty($matches)) {
        $error = 'Pregmatch didnt find format in ' . $fileName;
        outErrors(E_USER_ERROR, $error);
        return false;
    } else {
        $format = end($matches);
    }
    try {
        $file = file_get_contents($url);
    } catch (Exception $e) {
        $error = 'file path is wrong';
        outErrors(E_USER_ERROR, $error);
        return false;
    }

    try {
        $last =  substr("$path", -1);

        if($last != '/') {
            $response = $client->upload($file,$path . '/' . $fileName, );
        }else{
            $response = $client->upload($file,$path . $fileName, );
        }

    } catch (Exception $e) {
        $error = 'Couldnt upload ' . $fileName;
        outErrors(E_USER_ERROR, $error);
        return false;
    }


    try {
        $sharedLink = getShareLink($response['path_display']);
    } catch (Exception $e) {
        $error = 'Couldnt get shareLink ' . $fileName;
        outErrors(E_USER_ERROR, $error);
        return false;
    }

    $result['status'] = 'success';

    $result['data'] = [
        'size' => $response['size'],
        'path' => $response['path_display'],
        'httpPath' => $cnf['httpPath'] . $response['path_display'],
        'format' => $format,
        'sharedLink' => $sharedLink,
        'is_downloadable' => $response['is_downloadable'],
        'name' => $response['name'],
        'client_modified' => $response['client_modified']
    ];

    return $result;
}

//по ссылке вытаскиваем название осуществляем поиск и получаем картинку
function getDropbox($url, $output, $pathLocal = 'images/')
{
    global $client;
    global $cnf;

    preg_match('/([^\/]*?\.[a-z0-9]{1,5})[^\/]*$/', $url, $matches);

    if (isset($matches[1])) {
        $fileName = $matches[1];
    } else {
        $error = "Preg_match didnt work on " . $url;
        outErrors(E_USER_ERROR, $error);
        return false;
    }

    preg_match('/[a-z0-9]{1,5}/', $fileName, $matches);

    if (empty($matches)) {
        $error = 'Pregmatch didnt find format in ' . $fileName;
        outErrors(E_USER_ERROR, $error);
        return false;
    } else {
        $format = end($matches);
    }
    if (array_search($format, $cnf['preview_formats'])) {
        $hasPreview = true;
    } else {
        $output = [$cnf['images']['img']];
        $hasPreview = false;
    }


    try {
        $path = $client->search($fileName)['matches'][0]['metadata']['metadata']['path_display'];

    } catch (Exception $e) {
        $error = 'Couldnt search ' . $fileName;
        outErrors(E_USER_ERROR, $error);
        return false;
    }
    if (!$path) {
        $error = "Search didnt work on " . $fileName;
        outErrors(E_USER_ERROR, $error);
        return false;

    }

    try {
        $data = $client->getMetadata($path);
    } catch (Exception $e) {
        $error = 'Couldnt get metadata on ' . $path;
        outErrors(E_USER_ERROR, $error);
        return false;
    }
    if (!$data) {
        $error = "getMetadata didnt work on " . $path;
        outErrors(E_USER_ERROR, $error);
        return false;
    }

    foreach ($output as $v) {
        if ($v == $cnf['images']['img']) {
            $img = $client->download($path);
            $images['file_url'] = $img;
        }
        if ($v == $cnf['images']['img_base64']) {
            $img = $client->download($path);
            $img = base64_encode(stream_get_contents($img));
            $images[$v] = $img;
        }
        if ($v == $cnf['images']['thumb']) {
            $img = $client->getThumbnail($path, $cnf['thumb']['format'], $cnf['thumb']['size']);
            $images[$v] = $img;
        }
        if ($v == $cnf['images']['thumb_base64']) {
            $img = $client->getThumbnail($path, $cnf['thumb']['format'], $cnf['thumb']['size']);
            $img = base64_encode($img);
            $images[$v] = $img;
        }
    }

    if (!empty($images)) {
        foreach ($images as $k => $v) {
            if ($k == $cnf['images']['thumb_base64'] || $k == $cnf['images']['img_base64']) {
                $res[$k]['base64'] = $v;
            } else {

                $tmpRes = file_put_contents($pathLocal . $k . ' - ' . $fileName, $v);
                if ($tmpRes == false) {
                    $error = "file_put_contents didnt work";
                    outErrors(E_USER_ERROR, $error);
                    return false;
                }
                $res[$k]['size'] = $tmpRes;
            }
        }
    } else {
        $error = "img was not found on path " . $path;
        outErrors(E_USER_ERROR, $error);
        return false;
    }

    $result['status'] = 'success';

    foreach ($res as $k => $v) {

        $result['data'][$k] = [
            'hasPreview' => $hasPreview,
            'fileName' => $fileName,
            'path' => $path,
            'content_type' => $k,
            'client_modified' => $data['client_modified'],
            'sizeDrop' => $data['size'],
        ];
        if ($k == $cnf['images']['thumb_base64'] || $k == $cnf['images']['img_base64']) {
            $result['data'][$k]['format'] = 'base64';
            $result['data'][$k]['base64'] = $res[$k]['base64'];
        } else {
            $result['data'][$k]['sizeGot'] = $res[$k]['size'];
            $result['data'][$k]['format'] = $format;
            $result['data'][$k]['httpPath'] = $cnf['httpPath'] . $pathLocal . $k . ' - ' . $fileName;
            $result['data'][$k]['pathLocal'] = $pathLocal . $k . ' - ' . $fileName;
        }
    }
    return $result;
}


function getList($path)
{
    global $client;
    global $cnf;

    try {

        $list = $client->getList($path);

    } catch (Exception $e) {
        $error = "ListFolder did not work on " . $path;
        outErrors(E_USER_ERROR, $error);
        return false;
    }

    $result['status'] = 'success';


    foreach ($list['entries'] as $k => $v) {

        $result['data'][$k]['tag'] = $v['.tag'];
        $result['data'][$k]['name'] = $v['name'];
        $result['data'][$k]['path'] = $v['path_display'];
        $result['data'][$k]['httpPath'] = $cnf['httpPath'] . $v['path_display'];

        if ($v['.tag'] != 'folder') $result['data'][$k]['size'] = $v['size'];
        if ($v['.tag'] != 'folder') $result['data'][$k]['client_modified'] = $v['client_modified'];

    }

    return $result;
}

function getAll($path)
{
    global $cnf;
    global $client;

    if ($path != '/') {
        try {
            $data = $client->getMetaData($path);
        } catch (Exception $e) {
            $error = 'Couldnt get metadata on ' . $path;
            outErrors(E_USER_ERROR, $error);
            return false;
        }

        if ($data) {
            if ($data['.tag'] == 'folder') {
                $allPath = $path;
            } else {
                $allPath = str_replace($data['name'], '', $path);
            }
        } else {
            $error = 'Path ' . $path . 'was not found';
            outErrors(E_USER_ERROR, $error);
            return false;
        }
    } else {
        $allPath = '';
    }

    $list = getList($allPath);

    if ($list['status'] == 'success') {
        foreach ($list['data'] as $k => $v) {
            if ($v['tag'] != 'folder') {
                try {

                    $result['data'][$k] = getDropbox($v['httpPath'], [$cnf['images']['img'], $cnf['images']['thumb']])['data'];

                } catch (Exception $e) {
                    $error = 'getAll didnt work on getDropbox' . $v['httpPath'];
                    outErrors(E_USER_ERROR, $error);
                    return false;
                }
            }
        }
    }
    $result['status'] = 'success';

    return $result;
}

function getShareLink($path)
{
    global $client;

    try {

        $response = $client->getSharedLinks($path);

    } catch (Exception $e) {
        $error = "Couldnt find share links list " . $path;
        outErrors(E_USER_ERROR, $error);
        return false;
    }

    if (!empty($response['links'])) {

        $sharedLink = $response['links'][0]['url'];

    } else {
        try {
            $sharedLink = $client->createSharedLink($path)['url'];
        } catch (Exception $e) {
            $error = "Did not work on createSharedLink " . $path;
            outErrors(E_USER_ERROR, $error);
            return false;
        }
    }
    return $sharedLink;
}

function out($arr)
{
    print(json_encode($arr, JSON_UNESCAPED_SLASHES));
}

function outErrors($type, $error, $file = null, $line = null)
{
    global $cnf;
    $result['status'] = 'error';
    $result['errors'][] = $error;
    if ($cnf['return_errors']) {
        return $result;
    }
    print(json_encode($result, JSON_UNESCAPED_SLASHES));
    exit();
}

function outException($exception)
{
    $msg = 'Caught exception ' . $exception->getMessage() . PHP_EOL;
    print(json_encode($msg));
    // outErrors(E_USER_ERROR,$msg);
}
