<?php
    require_once('../config/logGen.php');
    session_start();

    // not login user
    if (!isset($_SESSION['loginauth'])) {
        header('location: ../index.php');
        exit();
    }

    // not administrator
    if($_SESSION['loginauth'] !== '1') {
        header('location: ../main.php');
        exit();
    }

    try {
        // store post data to session
        if (isset($_POST)) {
            $_SESSION['post_data'] = $_POST;
        }

        // if tokens do not match
        if (!isset($_SESSION['token']) || !isset($_POST['token']) || $_SESSION['token'] !== $_POST['token']) {
            throw new Exception("トークンが一致しません。");
        }

        // if image is not uploaded
        if (empty($_FILES['image'])) {
            throw new Exception("画像ファイルをアップロードしてください。");
        }

        // upload image
        $imgName     = $_FILES['image']['name'];
        $tmpName     = $_FILES['image']['tmp_name'];
        $imgExt      = pathinfo($imgName, PATHINFO_EXTENSION);
        $allowed     = array('jpg', 'jpeg', 'png');
        $isAllowed   = in_array(strtolower($imgExt), $allowed);
        $imageSize   = $_FILES['image']['size'];
        $maximumSize = 1000000;
        $uploadDir   = "../img/".$imgName;

        if (!$isAllowed) {
            throw new Exception("無効な画像形式になりました。");
        }
        if ($imageSize > $maximumSize) {
            throw new Exception("ファイル最大サイズは１MBです。");
        }
        if (!exec("move $tmpName $uploadDir")) {
            throw new Exception("画像のアップロードに失敗しました。");
        }
        $_SESSION['post_data']['image'] = $imgName;
    } catch(Exception $e) {
        $_SESSION['error'] = $e->getMessage();
        logerror(basename($_SERVER['SCRIPT_NAME']), "", $_SESSION['error']);
    }
    header('location: ./input.php');
?>
