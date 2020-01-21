<?php
/*  if(isset($_FILES['file'])){
    //The error validation could be done on the javascript client side.
    $errors= array();
    $file_name = $_FILES['file']['name'];
    $file_size =$_FILES['file']['size'];
    $file_tmp =$_FILES['file']['tmp_name'];
    $file_type=$_FILES['file']['type'];
    $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
    $extensions = array("jpeg","jpg","png");
    if(in_array($file_ext,$extensions )=== false){
     $errors[]="image extension not allowed, please choose a JPEG or PNG file.";
    }
    if($file_size > 2097152){
    $errors[]='File size cannot exceed 2 MB';
    }
    if(empty($errors)==true){
        move_uploaded_file($file_tmp,"images/MyAgency.png");
        echo  " Logo Actualizado: " . "images/" . $file_name;
    }else{
        print_r($errors);
    }
}*/

//var_dump($_FILES);
// if(isset($_FILES["file"]["type"]))
// {
// $validextensions = array("jpeg", "jpg", "png");
// $temporary = explode(".", $_FILES["file"]["name"]);
// $file_extension = end($temporary);
// if ((($_FILES["file"]["type"] == "image/png") || ($_FILES["file"]["type"] == "image/jpg") || ($_FILES["file"]["type"] == "image/jpeg")
// ) 
// && in_array($file_extension, $validextensions)) {
// if ($_FILES["file"]["error"] > 0)
// {
// echo "Return Code: " . $_FILES["file"]["error"] . "<br/><br/>";
// }
// else
// {
//     if (file_exists("upload/" . $_FILES["file"]["name"])) {
//         echo $_FILES["file"]["name"] . " <span id='invalid'><b>already exists.</b></span> ";
//     }else{
//         $sourcePath = $_FILES['file']['tmp_name']; // Storing source path of the file in a variable
//         //$targetPath = "upload/".$_FILES['file']['name']; // Target path where file is to be stored
//         move_uploaded_file($sourcePath,"images/MyAgency.png") ; // Moving Uploaded file
//         echo "<span id='success'>Image Uploaded Successfully...!!</span><br/>";
//         echo "<br/><b>File Name:</b> " . $_FILES["file"]["name"] . "<br>";
//         echo "<b>Type:</b> " . $_FILES["file"]["type"] . "<br>";
//         echo "<b>Size:</b> " . ($_FILES["file"]["size"] / 1024) . " kB<br>";
//         echo "<b>Temp file:</b> " . $_FILES["file"]["tmp_name"] . "<br>";
//     }
// }
// }
// else
// {
// echo "<span id='invalid'>***Invalid file Size or Type***<span>";
// }
// }

// if(isset($_FILES["file"]["type"]))
// {
// $validextensions = array("jpeg", "jpg", "png");
// $temporary = explode(".", $_FILES["file"]["name"]);
// $file_extension = end($temporary);
// if ((($_FILES["file"]["type"] == "image/png") || ($_FILES["file"]["type"] == "image/jpg") || ($_FILES["file"]["type"] == "image/jpeg")
// ) && ($_FILES["file"]["size"] < 5000000)//Approx. 100kb files can be uploaded.
// && in_array($file_extension, $validextensions)) {
// if ($_FILES["file"]["error"] > 0)
// {
// echo "Return Code: " . $_FILES["file"]["error"] . "<br/><br/>";
// }
// else
// {
// if (file_exists("upload/" . $_FILES["file"]["name"])) {
// echo $_FILES["file"]["name"] . " <span id='invalid'><b>already exists.</b></span> ";
// }
// else
// {
// $sourcePath = $_FILES['file']['tmp_name']; // Storing source path of the file in a variable
// $targetPath = "upload/".$_FILES['file']['name']; // Target path where file is to be stored
//  move_uploaded_file($sourcePath,"images/MyAgency.png");
// echo "<span id='success'>Image Uploaded Successfully...!!</span><br/>";
// echo "<br/><b>File Name:</b> " . $_FILES["file"]["name"] . "<br>";
// echo "<b>Type:</b> " . $_FILES["file"]["type"] . "<br>";
// echo "<b>Size:</b> " . ($_FILES["file"]["size"] / 1024) . " kB<br>";
// echo "<b>Temp file:</b> " . $_FILES["file"]["tmp_name"] . "<br>";
// }
// }
// }
// else
// {
// echo "<span id='invalid'>***Invalid file Size or Type***<span>";
// }
// }



if (isset($_FILES["file_image_advertising"]["type"])) {
    $validextensions = array("jpeg", "jpg", "png","JPEG","PNG","JPG");
    $temporary = explode(".", $_FILES["file_image_advertising"]["name"]);
    $file_extension = end($temporary);
    if ( ( ($_FILES["file_image_advertising"]["type"] == "image/png") || ($_FILES["file_image_advertising"]["type"] == "image/jpg") || ($_FILES["file_image_advertising"]["type"] == "image/jpeg"))
        && in_array($file_extension, $validextensions)) {
        if ($_FILES["file_image_advertising"]["error"] > 0) {
            echo "Return Code: " . $_FILES["file_image_advertising"]["error"] . "<br/><br/>";
        } else {

            if (file_exists("upload/" . $_FILES["file_image_advertising"]["name"])) {
                echo $_FILES["file_image_advertising"]["name"] . " <span id='invalid'><b>already exists.</b></span> ";
            } else {
                $sourcePath = $_FILES['file_image_advertising']['tmp_name']; // Storing source path of the file in a variable

                if ($_FILES['file_image_advertising']) {

                    if (move_uploaded_file($sourcePath, "../images/Advertising/" . $_POST['idAdvertising'] . ".png")) {
                        $arrayName = array('response' => 'success');
                        echo json_encode($arrayName);
                    } else {
                        $arrayName = array('response' => 'error');
                        echo json_encode($arrayName);
                    }
                }

            }
        }
    } else {
        echo "<span id='invalid'>***Invalid file Size or Type***<span>";
    }
} else {


}


 ?>
