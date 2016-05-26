<?

function initDir() {
    global $thumbnail_dir;
    if (!file_exists($thumbnail_dir)) {
        mkdir($thumbnail_dir, 0777, true);
    }
    
    $files = glob($thumbnail_dir.'/*'); // get all file names
    foreach($files as $file){ // iterate files
        if(is_file($file))
            unlink($file); // delete file
    }
}

function saveAndReturnImg($imgUrl) {
    global $thumbnail_dir;

    $filenameIn = $imgUrl;
    $baseName = round(microtime(true) * 1000);
    $filenameOut = $thumbnail_dir.'/'.$baseName;

    file_put_contents($filenameOut, file_get_contents($filenameIn));

    crop($filenameOut);

    return $filenameOut;
}

function crop($imgUrl) {
    $ini_filename = $imgUrl;
    $im = imagecreatefromjpeg($ini_filename);
    imagealphablending($im, false);
    imagesavealpha($im, true);

    $ini_x_size = getimagesize($ini_filename)[0];
    $ini_y_size = getimagesize($ini_filename)[1];

    $crop_measure = max($ini_x_size, $ini_y_size);

    $newImg = imagecreatetruecolor($crop_measure, $crop_measure);
    imagealphablending($newImg, false);
    imagesavealpha($newImg, true);
    $transparent = imagecolorallocatealpha($newImg, 255, 255, 255, 127);
    imagefilledrectangle($newImg, 0, 0, $crop_measure, $crop_measure, $transparent);
    imagecopyresampled($newImg, $im,
        ($crop_measure - $ini_x_size) / 2, ($crop_measure - $ini_y_size) / 2,
        0, 0,
        $ini_x_size, $ini_y_size,
        $ini_x_size, $ini_y_size);

    imagepng($newImg, $imgUrl);
}

?>
