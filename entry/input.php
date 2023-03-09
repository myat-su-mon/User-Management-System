<?php
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
    // login user and admin
    $_SESSION['token'] = $token = bin2hex(random_bytes(32));

    $inputUser = isset($_SESSION['post_data']['inputuser']) ? $_SESSION['post_data']['inputuser'] : '';
    $password  = isset($_SESSION['post_data']['password']) ? $_SESSION['post_data']['password'] : '';
    $age       = isset($_SESSION['post_data']['age']) ? $_SESSION['post_data']['age'] : '';
    $authority = isset($_SESSION['post_data']['authority']) ? $_SESSION['post_data']['authority'] : '';
    $gender    = isset($_SESSION['post_data']['gender']) ? $_SESSION['post_data']['gender'] : '';
    $mobile    = isset($_SESSION['post_data']['mobile']) ? $_SESSION['post_data']['mobile'] : '';
    $address   = isset($_SESSION['post_data']['address']) ? $_SESSION['post_data']['address'] : '';
    $image     = isset($_SESSION['post_data']['image']) ? $_SESSION['post_data']['image'] : '';
    $ios       = (!empty($mobile) && in_array('iOS', $mobile))? 'checked': '';
    $android   = (!empty($mobile) && in_array('Android', $mobile))? 'checked': '';
    $message   = isset($_SESSION['error']) ? $_SESSION['error']: '';
    
    unset($_SESSION['error']);
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
        <div class="row">
            <div class="col-6 offset-4">
                <p class='text-danger text-left'>
                    <?php echo $message; ?>
                </p>
            </div>
        </div>
        <form enctype="multipart/form-data">
            <div class="row">
                <input type="hidden" name="token" value="<?php echo $token; ?>">
                <div class="col-4 offset-1">
                    <div class="form-group">
                        <label for="inputuser" class="text-left mb-3 text-primary">ユーザID</label><br>
                        <input type="text" name="inputuser" id="inputuser" class="mb-3 w-100" value="<?php echo $inputUser; ?>">
                    </div>
                    <div class="form-group">
                        <label for="password" class="text-left mb-3 text-primary">パスワード</label><br>
                        <input type="password" name="password" id="password" class="mb-3 w-100" value="<?php echo $password; ?>">
                    </div>
                    <div class="form-group">
                        <label for="age" class="text-left mb-3 text-primary">年齢</label><br>
                        <input type="text" name="age" id="age" class="mb-3 w-100" value="<?php echo $age; ?>">
                    </div>
                </div>
                <div class="col-4">
                    <div class="form-group">
                        <label for="authority" class="text-left text-primary mb-1">権限</label><br>
                        <select class="mb-3 custom-select" name="authority" id="authority">
                            <option value="">権限してください</option>
                            <option value="1" <?php echo ($authority ==='1') ? 'selected': ''; ?>>管理者</option>
                            <option value="2" <?php echo ($authority ==='2') ? 'selected': ''; ?>>一般</option>
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label for="gender" class="text-left mb-3 text-primary">性別</label><br>
                        <input class="form-check-input" type="radio" name="gender" value="1" <?php echo ($gender ==='1') ? 'checked': ''; ?>><span>男性</span>
                        <input class="form-check-input" type="radio" name="gender" value="2" <?php echo ($gender ==='2') ? 'checked': ''; ?>><span>女性</span>
                        <input class="form-check-input" type="radio" name="gender" value="3" <?php echo ($gender ==='3') ? 'checked': ''; ?>><span>その他</span>
                    </div>
                    <div class="form-group">
                        <label for="mobile" class="text-left mb-3 text-primary">携帯電話</label><br>
                        <input class="form-check-input" type="checkbox" value="iOS" name="mobile[]" <?php echo $ios; ?>><span>iOS</span>
                        <input class="form-check-input" type="checkbox" value="Android" name="mobile[]" <?php echo $android; ?>><span>Android</span>
                    </div>
                </div>
                <div class="col-8 offset-1">
                    <div class="form-group">
                        <label for="address" class="text-left mb-3 text-primary">住所</label><br>
                        <input type="text" name="address" id="address" class="mb-3 w-100" value="<?php echo $address; ?>">
                    </div>
                    <div class="form-group">
                        <label for="image" class="text-left mb-3 text-primary">画像</label><br>
                        <div class="d-flex justify-content-between">
                            <input type="file" name="image" id="image" class="mb-3" id="input-file" value="<?php echo $image; ?>">
                            <button type="submit" class="btn btn-primary custom-btn2 mb-3" formmethod="post" formaction="./upload.php">送信</button>
                        </div>
                    </div>
                    <?php if ($image) { ?>
                        <div class="row">
                            <div class="col-8 offset-2">
                                <div class="img-container text-center">
                                    <div class="d-flex justify-content-around align-items-center">
                                        <label for="picture" class="img-label text-primary"><?php echo $image; ?></label>
                                        <img src="../img/<?php echo $image; ?>" class="img-pic" id="picture" width=150 height=120>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                    <div class="form-group text-center">
                        <button type="reset" class="btn btn-primary custom-btn2 btn-w-180 mx-2" formmethod="get" onclick="location.href='../main.php'">戻る</button>
                        <button type="submit" class="btn btn-primary custom-btn2 btn-w-180 mx-2" formmethod="post" formaction="./confirm.php">確認</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</body>
</html>
