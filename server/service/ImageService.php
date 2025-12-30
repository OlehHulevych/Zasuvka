<?php

class ImageService{

    function compressImage ($source,$destination, $quality, $maxWidth){
        $imgInfo = getimagesize($source);
        $mime = $imgInfo['mime'];
        $width= $imgInfo[0];
        $height = $imgInfo[1];

        if ($width > $maxWidth) {
            $ratio = $maxWidth / $width;
            $newWidth = $maxWidth;
            $newHeight = $height * $ratio;
        } else {
            $newWidth = $width;
            $newHeight = $height;
        }

        switch ($mime) {
            case 'image/jpeg': $image = imagecreatefromjpeg($source); break;
            case 'image/png':  $image = imagecreatefrompng($source); break;
            default: return false;
        }

        $newImage = imagecreatetruecolor($newWidth,$newHeight);
        imagecopyresampled($newImage, $image, 0, 0, 0, 0, $newWidth, $newHeight,$width, $height);
        $result = false;
        switch ($mime) {
            case 'image/jpeg':
                $result = imagejpeg($newImage, $destination, $quality);
                break;
            case 'image/png':
                $pngQuality = floor((100 - $quality) / 10);
                $result = imagepng($newImage, $destination, $pngQuality);
                break;
        }
        imagedestroy($image);
        imagedestroy($newImage);
        return $result;

    }
}
?>
