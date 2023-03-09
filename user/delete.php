<?php
    require_once('../config/config.php');
    require_once('../config/logGen.php');
    session_start();

    // not login user
    if (!isset($_SESSION['loginauth'])) {
        header('location: ../index.php');
        exit();
    } 

    try {
        // if general user
        if($_SESSION['loginauth'] === '2' || empty($_GET)) {
            header('location: ../main.php');
            exit();
        }

        // check database connection
        if (!$pdo) {
            throw new Exception("ユーザ削除が失敗しました。");
        }

        $userid = $_GET['userid'];
        $sql = <<<EOT
                    UPDATE
                        USERINFO_TABLE
                    SET
                        ACTIVEFLAG = '0'
                    WHERE
                        USERID = '$userid';
EOT;
        $pdo->beginTransaction();
        $stmt  = $pdo->query($sql);
        $count = $stmt->rowCount();

        if ($count == 1) {
            $pdo->commit();
            $successMessage = "ユーザー削除が完了しました。";
        } else {
            $pdo->rollback();
            throw new Exception("ユーザ削除が失敗しました。");
        }
    } catch (PDOException $e) {
        $errorMessage = "ユーザ削除が失敗しました。";
        logerror(basename($_SERVER['SCRIPT_NAME']), $sql, $errorMessage);
    } catch (Exception $e) {
        $errorMessage = $e->getMessage();
        logerror(basename($_SERVER['SCRIPT_NAME']), "", $errorMessage);
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <!-- CSS only -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-0evHe/X+R7YkIZDRvuzKMRqM+OrBnVFBL6DOitfPri4tjfHxaWutUpFmBp4vmVor" crossorigin="anonymous">
    <!-- JavaScript Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/js/bootstrap.bundle.min.js" integrity="sha384-pprn3073KE6tl6bjs2QrFaJGz5/SUsLqktiwsUTF55Jfv3qYSDhgCecCxMW52nD2" crossorigin="anonymous"></script>
    <link href="../css/style.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <div class="row">
            <div class="col-4 offset-4">
                <div class="box">
                    <?php if (isset($errorMessage)) { 
                            echo "<p class='text-danger text-center fw-bold'>$errorMessage</p>";
                        } else {
                            echo "<p class='text-center fw-bold'>$successMessage</p>";
                        }
                    ?>
                    <a href="./search.php" class="btn btn-primary cus-btn mt-5">一覧ページへ戻る</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
