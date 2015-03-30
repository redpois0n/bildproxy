<?php

$target_dir = "uploads/";
$target_file = $target_dir . basename($_FILES["i"]["name"]);
$uploadOk = 1;
$imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);

if(isset($_POST["submit"])) {
    $check = getimagesize($_FILES["i"]["tmp_name"]);
    if($check !== false) {
        $uploadOk = 1;
    } else {
        echo "Filen är ingen bild.";
        $uploadOk = 0;
    }
}


if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
&& $imageFileType != "gif" ) {
    echo "Bara bilder tack!";
    $uploadOk = 0;
}

if ($uploadOk == 0) {
    echo "Något gick fel..";

} else {

    if (move_uploaded_file($_FILES["i"]["tmp_name"], $target_file)) {

        //döper om filen för jag litar inte på dig
        rename($target_file, "uploads/0.jpg"); 

        //ta bort alla EXIF på alla filer i uploads-mappen. Dessutom vill vi inte ha en kopia.
        shell_exec('exiftool -all="" -overwrite_original_in_place "' . $target_dir .'" *');

        //ladda upp bilden till img.bi över Tor, ta också bort Proxychains's output
        $img = "proxychains imgbi-client -i '" . $target_dir . "'/0.jpg | sed 's/ProxyChains.*//g'";

        echo '<pre>';
        passthru($img);
        echo '</pre>';

        //Skriv över allting i uploads-mappen, just in case
        shell_exec('find "' . $target_dir . '" -type f -name "*" -exec srm -ll -z {} \;');

        
    } else {
        echo "error";
    }
}
?>

