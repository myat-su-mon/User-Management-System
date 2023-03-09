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
        // check database connection
        if (!$pdo) {
            throw new Exception("データベース接続に失敗しました。");
        }

        $detailedUser = $_GET['searchuser'] ?? '';
        $image        = '../img/noimage.jpg';
        $message      = $_SESSION['error'] ?? '';

        $sql          = <<<EOT
                            SELECT
                                *
                            FROM
                                USERINFO_TABLE
                            WHERE
                                USERID = '$detailedUser';
EOT;
        $stmt   = $pdo->query($sql);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if (empty($result)) {
            throw new Exception("ユーザーIDが存在しません。");
        } else {
            $userid  = $result['userid'];
            $age     = $result['age'];
            $address = $result['address'];
            $image   = $result['filename'] ? "../img/user/".$result['filename'] : "../img/noimage.jpg";

            if ($result['authority'] === '1') {
                $authority = "管理者";
            } else {
                $authority = "一般";
            }

            if ($result['gender'] === '1') {
                $gender = "男性";
            } elseif ($result['gender'] === '2') {
                $gender = "女性";
            } else {
                $gender = "その他";
            }

            if ($result['mobile'] === 0) {
                $mobile = "なし";
            } elseif ($result['mobile'] === 1) {
                $mobile = "iOS";
            } elseif ($result['mobile'] === 2) {
                $mobile = "Android";
            } elseif ($result['mobile'] === 3) {
                $mobile = "iOS/Android";
            }
        }
    } catch (PDOException $e) {
        $message = "詳細についてはシステム管理者に確認してください。";
        logerror(basename($_SERVER['SCRIPT_NAME']), $sql, $message);
    } catch (Exception $e) {
        $message = $e->getMessage();
        logerror(basename($_SERVER['SCRIPT_NAME']), "", $message);
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Manager</title>
    <!-- CSS only -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-0evHe/X+R7YkIZDRvuzKMRqM+OrBnVFBL6DOitfPri4tjfHxaWutUpFmBp4vmVor" crossorigin="anonymous">
    <!-- JavaScript Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/js/bootstrap.bundle.min.js" integrity="sha384-pprn3073KE6tl6bjs2QrFaJGz5/SUsLqktiwsUTF55Jfv3qYSDhgCecCxMW52nD2" crossorigin="anonymous"></script>
    <link href="../css/style.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <?php if (isset($message)) { ?>
            <div class="box">
                <p class='text-danger text-center fw-bold'>
                    <?php echo $message; ?>
                </p>
            </div>
        <?php } ?>
        <div class="row">
            <div class="col">
                <div class="img-container text-end me-0">
                    <img src="<?php echo (isset($image)) ? $image : '../img/noimage.jpg' ?>" class="img-pic" id="picture" width=200 height=200><br>
                        <label for="picture" class="img-label text-primary">
                            <?php if (isset($image) && strcmp($image, '../img/noimage.jpg')) { ?>
                                <a href="./download.php?image=<?php echo $result['filename']; ?>" class="text-decoration-none">
                                    <?php echo $result['filename']; ?>
                                </a>
                            <?php } ?>
                        </label>
                </div>
            </div>
            <div class="col">
                <div class="form-group">
                    <div class="row">
                        <div class="col-3"><label for="userid" class="text-left text-primary">ユーザID</label><br>
                            <p class="fw-bold"><?php echo $userid ?? ''; ?></p>
                        </div>
                        <div class="col-3"><label for="authority" class="text-left text-primary">権限</label><br>
                            <p class="fw-bold"><?php echo $authority ?? ''; ?></p>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="age" class="text-left text-primary">年齢</label><br>
                    <p class="fw-bold"><?php echo $age ?? ''; ?></p>
                </div>
                <div class="form-group">
                    <label for="gender" class="text-left text-primary">性別</label><br>
                    <p class="fw-bold"><?php echo $gender ?? ''; ?></p>
                </div>
                <div class="form-group">
                    <label for="mobile" class="text-left text-primary">携帯電話</label><br>
                    <p class="fw-bold"><?php echo $mobile ?? ''; ?></p>
                </div>
                <div class="form-group">
                    <label for="address" class="text-left text-primary">住所</label><br>
                    <p class="fw-bold"><?php echo $address ?? ''; ?></p>
                </div>
            </div>
        </div>
        <div class="d-flex justify-content-center">
            <button type="reset" class="btn btn-primary custom-btn2 btn-w-180 mx-3" formmethod="get" onclick="location.href='./search.php'">戻る</button>
            <?php 
                if (isset($userid) && $_SESSION['loginauth'] !== '2' && $_SESSION['userid'] !== $detailedUser) {
                    echo "<button type='submit' class='btn btn-primary custom-btn2 btn-w-180 mx-3' formmethod='get' onclick=document.location.href='./delete.php?userid=$userid'>削除</button>";
                }
            ?>
        </div>
    </div>
</body>
</html>
