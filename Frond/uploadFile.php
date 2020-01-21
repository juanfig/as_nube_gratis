<?php

var_dump($_POST['id']);

if(isset($_FILES["file_img_product_online"]["type"]))
{
    $validextensions = array("jpeg", "jpg", "png");
    $temporary = explode(".", $_FILES["file_img_product_online"]["name"]);
    $file_extension = end($temporary);
    if ((($_FILES["file_img_product_online"]["type"] == "image/png") || ($_FILES["file_img_product_online"]["type"] == "image/jpg") || ($_FILES["file_img_product_online"]["type"] == "image/jpeg")
        ) 
        && in_array($file_extension, $validextensions)) {
        if ($_FILES["file_img_product_online"]["error"] > 0)
        {
            echo "Return Code: " . $_FILES["file_img_product_online"]["error"] . "<br/><br/>";
        }
        else
        {
            if (file_exists("upload/" . $_FILES["file_img_product_online"]["name"])) {
                echo $_FILES["file_img_product_online"]["name"] . " <span id='invalid'><b>already exists.</b></span> ";
            }
            else
            {
                $sourcePath = $_FILES['file_img_product_online']['tmp_name']; 

                move_uploaded_file($sourcePath,"images/".$_POST['folder']."/".$_POST['id'].$_POST['name']) ; 
                echo "<span id='success'>Image Uploaded Successfully...!!</span><br/>";
                echo "<br/><b>File Name:</b> " . $_FILES["file_img_product_online"]["name"] . "<br>";
                echo "<b>Type:</b> " . $_FILES["file_img_product_online"]["type"] . "<br>";
                echo "<b>Size:</b> " . ($_FILES["file_img_product_online"]["size"] / 1024) . " kB<br>";
                echo "<b>Temp file:</b> " . $_FILES["file_img_product_online"]["tmp_name"] . "<br>";
            }
        }
    }
    else
    {
        echo "<span id='invalid'>***Invalid file Size or Type***<span>";
    }
}

if(isset($_FILES["file_img"]["type"]))
{
    $validextensions = array("jpeg", "jpg", "png");
    $temporary = explode(".", $_FILES["file_img"]["name"]);
    $file_extension = end($temporary);
    if ((($_FILES["file_img"]["type"] == "image/png") || ($_FILES["file_img"]["type"] == "image/jpg") || ($_FILES["file_img"]["type"] == "image/jpeg")
        ) 
        && in_array($file_extension, $validextensions)) {
        if ($_FILES["file_img"]["error"] > 0)
        {
            echo "Return Code: " . $_FILES["file_img"]["error"] . "<br/><br/>";
        }
        else
        {
            if (file_exists("upload/" . $_FILES["file_img"]["name"])) {
                echo $_FILES["file_img"]["name"] . " <span id='invalid'><b>already exists.</b></span> ";
            }
            else
            {
                $sourcePath = $_FILES['file_img']['tmp_name']; 

                move_uploaded_file($sourcePath,"images/".$_POST['folder']."/".$_POST['id'].".png") ; 
                echo "<span id='success'>Image Uploaded Successfully...!!</span><br/>";
                echo "<br/><b>File Name:</b> " . $_FILES["file_img"]["name"] . "<br>";
                echo "<b>Type:</b> " . $_FILES["file_img"]["type"] . "<br>";
                echo "<b>Size:</b> " . ($_FILES["file_img"]["size"] / 1024) . " kB<br>";
                echo "<b>Temp file:</b> " . $_FILES["file_img"]["tmp_name"] . "<br>";
            }
        }
    }
    else
    {
        echo "<span id='invalid'>***Invalid file Size or Type***<span>";
    }
}

if(isset($_FILES["file_img"]["type"]))
{
    $validextensions = array("jpeg", "jpg", "png");
    $temporary = explode(".", $_FILES["file_img"]["name"]);
    $file_extension = end($temporary);
    if ((($_FILES["file_img"]["type"] == "image/png") || ($_FILES["file_img"]["type"] == "image/jpg") || ($_FILES["file_img"]["type"] == "image/jpeg")
        ) && ($_FILES["file_img"]["size"] < 5000000)
        && in_array($file_extension, $validextensions)) {
        if ($_FILES["file_img"]["error"] > 0)
        {
            echo "Return Code: " . $_FILES["file_img"]["error"] . "<br/><br/>";
        }
        else
        {
            if (file_exists("upload/" . $_FILES["file_img"]["name"])) {
                echo $_FILES["file_img"]["name"] . " <span id='invalid'><b>already exists.</b></span> ";
            }
            else
            {
                $sourcePath = $_FILES['file_img']['tmp_name']; 
                $targetPath = "upload/".$_FILES['file_img']['name']; 
                move_uploaded_file($sourcePath,"images/".$_POST['folder']."/".$_POST['id'].".png");
                echo "<span id='success'>Image Uploaded Successfully...!!</span><br/>";
                echo "<br/><b>File Name:</b> " . $_FILES["file_img"]["name"] . "<br>";
                echo "<b>Type:</b> " . $_FILES["file_img"]["type"] . "<br>";
                echo "<b>Size:</b> " . ($_FILES["file_img"]["size"] / 1024) . " kB<br>";
                echo "<b>Temp file:</b> " . $_FILES["file_img"]["tmp_name"] . "<br>";
            }
        }
    }
    else
    {
        echo "<span id='invalid'>***Invalid file Size or Type***<span>";
    }
}
?>
