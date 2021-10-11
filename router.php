<?php
include 'vendor/autoload.php';

//токен приложения
//$client = new Spatie\Dropbox\Client('');
$cnf['apikey'] = '12345';
$cnf['path'] = '/images';
$cnf['files'] = 'dropboxupload';
$request = $_REQUEST;
$files = $_FILES;


$modes = ['image_url', 'thumb_url', 'image_base64', 'thumb_base64', 'all_path_files', 'image_path', 'all_link_info', 'form_file_input'];
$actions = ['uploadDropbox', 'getDropbox'];
$links = ['dropboxlink', 'dropboxpath', 'image_path'];

if (!isset($request['apikey']) && $request['apikey'] != $cnf['apikey']) {
    $error = 'Invalid POST apikey';
    outErrors(E_USER_ERROR, $error);
    return false;
}


if (isset($request['action']) && false === (array_search($action = urldecode($request['action']), $actions))) {
    $error = 'Invalid POST action';
    outErrors(E_USER_ERROR, $error);
    return false;
}

if (isset($request['mode']) && false === (array_search($mode = urldecode($request['mode']), $modes))) {
    $error = 'Invalid POST mode';
    outErrors(E_USER_ERROR, $error);
    return false;
}


if (isset($request['path'])) {
    $imagePath = urldecode($request['path']);
} else {
    $imagePath = $cnf['path'];
}

foreach ($links as $v) {
    if (isset($request[$v])) {
        if ($v == 'dropboxlink' || $v == 'dropboxpath') {
            $dropboxUrl = urldecode($request[$v]);
        }
        if ($v == 'image_path') {
            $imageUrl = urldecode($request[$v]);
        }
    }

}

if ($mode == 'form_file_input') {
    $file = $files[$cnf['files']];
} else {
    $file = false;
}

if ($action == 'uploadDropbox' && (!$imageUrl && empty($file))) {
    $error = 'Url or file not provided';
    outErrors(E_USER_ERROR, $error);
    return false;
}
if ($action == 'getDropbox' && !$dropboxUrl) {
    $error = 'Url or file not provided';
    outErrors(E_USER_ERROR, $error);
    return false;
}

switch ($action) {
    case 'uploadDropbox':
        out(upload($imageUrl, $file, $imagePath));
        break;

    case 'getDropbox':
    {

        switch ($mode) {
            case $cnf['images']['img']:
            case $cnf['images']['img_base64']:
            case $cnf['images']['thumb']:
            case $cnf['images']['thumb_base64']:
                $output[] = $mode;
                out(getDropbox($dropboxUrl, $output));
                break;

            case  'all_link_info':
                $output = [$cnf['images']['img'], $cnf['images']['thumb']];
                out(getDropbox($dropboxUrl, $output));
                break;

            case  'all_path_files':
                out(getAll($dropboxUrl));
                break;
        }
        break;
    }

}



