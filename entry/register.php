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
        if($_SESSION['loginauth'] === '2' || empty($_POST)) {
            header('location: ../main.php');
            exit();
        }

        // tokens do not match
        if (!isset($_SESSION['confirm_token']) || !isset($_POST['confirmToken']) || $_SESSION['confirm_token'] !== $_POST['confirmToken']) {
            throw new Exception("トークン情報が一致しませんでした。");
        }

        $userid    = isset($_SESSION['post_data']['inputuser']) ? $_SESSION['post_data']['inputuser'] : '';
        $password  = isset($_SESSION['post_data']['password']) ? $_SESSION['post_data']['password'] : '';
        $age       = isset($_SESSION['post_data']['age']) ? $_SESSION['post_data']['age'] : '';
        $authority = isset($_SESSION['post_data']['authority']) ? $_SESSION['post_data']['authority'] : '';
        $gender    = isset($_SESSION['post_data']['gender']) ? $_SESSION['post_data']['gender'] : '';
        $mobile    = isset($_SESSION['post_data']['mobile']) ? $_SESSION['post_data']['mobile'] : '';
        $address   = isset($_SESSION['post_data']['address']) ? $_SESSION['post_data']['address'] : '';

        if (!empty($mobile)) {
            $mobile = implode("/", $mobile);
        } else {
            $mobile = "なし";
        }

        if (isset($_SESSION['post_data']['image'])) {
            $oldName = $_SESSION['post_data']['image'];
            $oldDir  = "../img/".$_SESSION['post_data']['image'];
            $newName = trim($_SESSION['post_data']['inputuser'])."_".$_SESSION['post_data']['image'];
            $newDir  = "../img/user/".$newName;

            if (file_exists($newDir) || !exec("move $oldDir $newDir")) {
                throw new Exception("画像の移動に失敗しました。");
            }
        }

        $oldName = isset($oldName) ? $oldName : '';
        $newName = isset($newName) ? $newName : '';
        $message = isset($_SESSION['error']) ? $_SESSION['error']: '';

        // check database connection
        if (!$pdo) {
            throw new Exception("データベース接続に失敗しました");
        }

        $sql = <<<EOT
        SELECT
            *
        FROM
            USERINFO_TABLE
        WHERE
            USERID = '$userid' AND
            ACTIVEFLAG='1';
EOT;

        $stmt   = $pdo->query($sql);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result && $result['userid'] === $userid) {
            throw new Exception("ユーザーはすでに存在します");
        }

        $sql  =<<<EOT
                INSERT INTO
                    USERINFO_TABLE
                    (USERID,
                    PASSWORD,
                    AUTHORITY,
                    AGE,
                    GENDER,
                    ADDRESS,
                    MOBILE,
                    ORIGINALFILENAME,
                    FILENAME)
                    VALUES ('$userid',
                        '$password',
                        '$authority',
                        $age,
                        $gender,
                        '$address',
                        CASE '$mobile'
                            WHEN 'iOS' THEN 1
                            WHEN 'Android' THEN 2
                            WHEN 'iOS/Android' THEN 3
                            ELSE 0
                        END,
                        NULLIF('$oldName', ''),
                        NULLIF('$newName', ''));
EOT;

        $pdo->beginTransaction();
        $stmt  = $pdo->query($sql);
        $count = $stmt->rowCount();

        if ($count == 1) {
            $pdo->commit();
        } else {
            $pdo->rollback();
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = "詳細についてはシステム管理者に確認してください。";
        logerror(basename($_SERVER['SCRIPT_NAME']), $sql, $_SESSION['error']);
        header('location: ./input.php');
    } catch (Exception $e) {
        $_SESSION['error'] = $e->getMessage();
        logerror(basename($_SERVER['SCRIPT_NAME']), "", $_SESSION['error']);
        header('location: ./input.php');
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
        <?php if(!empty($message)) { ?>
            <div class="row">
                <div class="col-6 offset-4">
                    <p class='text-danger text-left'>
                        <?php echo $message; ?>
                    </p>
                </div>
            </div>
        <?php } ?>
        <div class="row">
            <div class="col-4 offset-4">
                <div class="box">
                    <p class="text-center fw-bold">ユーザー登録が完了しました</h6>
                    <a href="../main.php" class="btn btn-primary cus-btn mt-5">トップページへ戻る</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
