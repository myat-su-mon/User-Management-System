<?php
    require_once('../config/logGen.php');
    session_start();

    // not login user
    if (!isset($_SESSION['loginauth'])) {
        header('location: ../index.php');
        exit();
    }

    if (isset($_GET['image'])) {
        $filename = $_GET['image'];
        $filedir  = "../img/user/$filename";

        if (file_exists($filedir)) {
            header('Content-Type: application/image');
            header('Content-Disposition: attachment; filename="'.basename($filedir).'";');
            readfile($filedir);
        } else {
            $name_arr = explode("_", $filename);
            $userid   = $name_arr[0];
            header("location: ./detail.php?searchuser=$userid");
        }
    } else {
        $_SESSION['error'] = " 検索が失敗しました。";
        header("location: ./detail.php?searchuser=$userid");
    }
?>

