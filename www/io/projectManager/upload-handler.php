<?php
namespace Mediaio;

class projectPictureManager
{
  private static function checkIfFolderExists()
  {
    try {
      if (!file_exists("./projectManager/pictures")) {
        mkdir("./projectManager/pictures", 0777, true);
      }
    } catch (\Exception $e) {
      return 500;
    }
    return 200;
  }

  static function uploadImage($taskId, $file)
  {
    // Check if folder exists
    if (self::checkIfFolderExists() == 500) {
      //return 500;
    }

    $imageFileType = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));
    $target_dir = "./projectManager/pictures/";
    $target_file = $target_dir . $taskId . "." . $imageFileType;
    $uploadOk = 1;
    // Check if image file is a actual image or fake image
    if ($imageFileType != "heic" && $imageFileType != "heif") {
      $check = getimagesize($file["tmp_name"]);
      if ($check !== false) {
        $uploadOk = 1;
      } else {
        return 400;
      }
    }

    // Check file size
    if ($file["size"] > 5 * 1024 * 1024) { // 5MB
      return 500;
    }
    // Allow certain file formats
    if (
      $imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
      && $imageFileType != "gif" && $imageFileType != "heic" && $imageFileType != "heif"
    ) {
      return 400;
    }
    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {
      return 500;
      // if everything is ok, try to upload file
    } else {
      // Check if file format is heic or heif and convert it to jpg
      try {
        if ($imageFileType == "heic" || $imageFileType == "heif") {
          $image = new \Imagick($file["tmp_name"]);
          $image->setImageFormat('jpg');
          $target_file = $target_dir . $taskId . ".jpg";
          $image->writeImage($target_file);
        } else {
          move_uploaded_file($file["tmp_name"], $target_file);
        }
        return 200;
      } catch (\Exception $e) {
        return 500;
      }
    }
  }

  static function deleteImage($taskId)
  {
    if ($_POST['mode'] == "deleteImage") {
      $target_dir = "./pictures/";
    } else {
      $target_dir = "./projectManager/pictures/";
    }
    $target_file = $target_dir . $taskId;
    // Get an array of file paths that match the pattern

    $files = glob($target_file . ".*");

    if (!empty($files)) {
      foreach ($files as $file) {
        if (file_exists($file)) {
          unlink($file);
        }
      }
      return 200;
    } else {
      return 404;
    }
  }
}


if (isset($_POST['mode'])) {
  if ($_POST['mode'] == "uploadImage") {
    echo projectPictureManager::uploadImage($_POST['taskId'], $_FILES['fileToUpload']);
    exit();
  }
  if ($_POST['mode'] == "deleteImage") {
    echo projectPictureManager::deleteImage($_POST['taskId']);
    exit();
  }
}
