<?
  
  foreach ($_POST['data'] as $key => $value) {
    
    if(isset($_POST['id'])){
      $file = $_POST['route'].$_POST['id'].$value;
    }else{
      $file = $_POST['route'].$value;
    }

    if(file_exists($file)){
      unlink($file);
    }
  }

?>