<?php

    // Get image string posted from Android App
    $base=$_REQUEST['image'];
    // Get file name posted from Android App
    $filename = $_REQUEST['filename'];
    // Decode Image
    $binary=base64_decode($base);
    header('Content-Type: bitmap; charset=utf-8');


	if (! $file = fopen('img_users/'.$filename.'.JPEG', 'wb')) {
             echo "Cannot open file ($filename)";
             exit;
        }
	// Create File
    fwrite($file, $binary);
    fclose($file);

    echo 'Imagen Actualizada!';


?>
