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
        if (!isset($_SESSION['token']) || !isset($_POST['token']) || $_SESSION['token'] !== $_POST['token']) {
            throw new Exception("トークン情報が一致しませんでした。");
        } else {
            $confirmToken = $_SESSION['confirm_token'] = bin2hex(random_bytes(32));
        }

        // check input userid
        if (empty($_POST['inputuser'])) {
            throw new Exception("『ユーザーID』は必要です。");
        } else {
            $inputuser = $_SESSION['post_data']['inputuser'] = $_POST['inputuser'];
        }

        // check input password
        if (empty($_POST['password'])) {
            throw new Exception("『パスワード』は必要です。");
        } else {
            $password = $_SESSION['post_data']['password'] = $_POST['password'];
            $hiddenPass = preg_replace("|.|", "*", $password);
        }

        // check input age
        if (empty($_POST['age'])) {
            throw new Exception("『年齢』は必要です。");
        } else {
            $age = $_SESSION['post_data']['age'] = $_POST['age'];
        }

        // check input authority
        if (empty($_POST['authority'])) {
            throw new Exception("『権限』は必要です。");
        } else {
            $authority = $_SESSION['post_data']['authority'] = $_POST['authority'];
        }

        // check input gender
        if (empty($_POST['gender'])) {
            throw new Exception("『性別』は必要です。");
        } else {
            $gender = $_SESSION['post_data']['gender'] = $_POST['gender'];
        }

        // check input mobile
        if (!empty($_POST['mobile'])) {
            $mobile = $_SESSION['post_data']['mobile'] = $_POST['mobile'];
        }

        // check input address
        if (empty($_POST['address'])) {
            throw new Exception("『住所』は必要です。");
        } else {
            $address = $_SESSION['post_data']['address'] = $_POST['address'];
        }

        // validate the length of userid
        if (!(strlen($inputuser) >= 8 && strlen($inputuser) <= 16)) {
            throw new Exception("ユーザIDまたはパスワードの桁数は8から16の間です。");
        }

        // validate the lenght of password
        if (!(strlen($password) >= 8 && strlen($password) <= 16)) {
            throw new Exception("ユーザIDまたはパスワードの桁数は8から16の間です。");
        }

        // validate the age
        if (!($age == (int) $age)) {
            throw new Exception("年齢値が無効です。");
        }

        // check database connection
        if (!$pdo) {
            throw new Exception("データベース接続に失敗しました。");
        }

        $sql = <<<EOT
                SELECT
                    *
                FROM
                    USERINFO_TABLE
                WHERE
                    USERID = '$inputuser' AND
                    ACTIVEFLAG='1';
EOT;
        $stmt   = $pdo->query($sql);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result && $result['userid'] === $inputuser) {
            throw new Exception("ユーザーはすでに存在します。");
        }

        if ($authority === '1') {
            $authority = "管理者";
        } else {
            $authority = "一般";
        }

        if ($gender === '1') {
            $gender = "男性";
        } elseif ($gender === '2') {
            $gender = "女性";
        } else {
            $gender = "その他";
        }

        if (isset($mobile)) {
            if (in_array('iOS', $mobile) && in_array('Android', $mobile)) {
                $mobile = "iOS/Android";
            } elseif (in_array('iOS', $mobile)) {
                $mobile = "iOS";
            } elseif (in_array('Android', $mobile)) {
                $mobile = "Android";
            }
        } else {
            $mobile = "なし";
        }

        $image = isset($_SESSION['post_data']['image']) ? $_SESSION['post_data']['image'] : '';
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
    <title>Input</title>
    <!-- CSS only -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-0evHe/X+R7YkIZDRvuzKMRqM+OrBnVFBL6DOitfPri4tjfHxaWutUpFmBp4vmVor" crossorigin="anonymous">
    <!-- JavaScript Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/js/bootstrap.bundle.min.js" integrity="sha384-pprn3073KE6tl6bjs2QrFaJGz5/SUsLqktiwsUTF55Jfv3qYSDhgCecCxMW52nD2" crossorigin="anonymous"></script>
    <link href="../css/style.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <form enctype="multipart/form-data">
            <div class="row">
                <input type="hidden" name="confirmToken" value="<?php echo $confirmToken; ?>">
                <div class="col-4 offset-1">
                    <div class="form-group">
                        <label for="userid" class="text-left text-primary">ユーザID</label><br>
                        <p><?php echo $inputuser; ?></p>
                    </div>
                    <div class="form-group">
                        <label for="password" class="text-left text-primary">パスワード</label><br>
                        <p><?php echo $hiddenPass; ?></p>
                    </div>
                    <div class="form-group">
                        <label for="age" class="text-left text-primary">年齢</label><br>
                        <p><?php echo $age; ?></p>
                    </div>
                </div>
                <div class="col-4">
                    <div class="form-group">
                        <label for="authority" class="text-left text-primary mb-1">権限</label><br>
                        <p class="txt-bolder"><?php echo $authority; ?></p>
                    </div>
                    <div class="form-group">
                        <label for="gender" class="text-left text-primary">性別</label><br>
                        <p class="txt-bolder"><?php echo $gender; ?></p>
                    </div>
                    <div class="form-group">
                        <label for="mobile" class="text-left text-primary">携帯電話</label><br>
                        <p class="txt-bolder"><?php echo $mobile; ?></p>
                    </div>
                </div>
                <div class="col-8 offset-1">
                    <div class="form-group">
                        <label for="address" class="text-left text-primary">住所</label><br>
                        <p><?php echo $address; ?></p>
                    </div>
                </div>
            </div>
            <?php if ($image) { ?>
                <div class="col-8">
                    <div class="img-container text-center">
                        <div class="d-flex justify-content-around align-items-center">
                            <label for="picture" class="img-label text-primary"><?php echo $image; ?></label>
                            <img src="../img/<?php echo $image; ?>" class="img-pic" id="picture" width=150 height=120>
                        </div>
                    </div>
                </div>
            <?php } ?>
            <div class="col-8">
                <div class="form-group text-center">
                    <button type="reset" class="btn btn-primary custom-btn2 btn-w-180 mx-2" formmethod="get" onclick="location.href='./input.php'">戻る</button>
                    <button type="submit" class="btn btn-primary custom-btn2 btn-w-180 mx-2" formmethod="post" formaction="./register.php">登録</button>
                </div>
            </div>
        </form>
    </div>
</body>
</html>
