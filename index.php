<?php
        session_start();
        $userid     = isset($_SESSION['userid'])? $_SESSION['userid'] : '';
        $inputError = isset($_SESSION['error'])? $_SESSION['error'] : '';
        unset($_SESSION['error']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Index</title>
    <!-- CSS only -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-0evHe/X+R7YkIZDRvuzKMRqM+OrBnVFBL6DOitfPri4tjfHxaWutUpFmBp4vmVor" crossorigin="anonymous">
    <!-- JavaScript Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/js/bootstrap.bundle.min.js" integrity="sha384-pprn3073KE6tl6bjs2QrFaJGz5/SUsLqktiwsUTF55Jfv3qYSDhgCecCxMW52nD2" crossorigin="anonymous"></script>
    <link href="css/style.css" rel="stylesheet">
</head>
<body>
    
    <div class="container w-75">
        <div class="row">
            <div class="col-4 offset-4">
                <form action="login.php" method="POST">
                    <div class="form-group text-center">
                        <img src="person-circle.svg" class="img-fluid" width=120 height=120>
                    </div>
                    <div class="form-group mt-5">
                        <label for="userid" class="text-left mb-3 text-primary">ユーザID</label><br>
                        <input type="text" name="userid" id="userid" class="mb-3 w-100" value="<?php echo $userid; ?>">
                    </div>
                    <div class="form-group">
                        <label for="password" class="text-left mb-3 text-primary">パスワード</label><br>
                        <input type="password" name="password" id="password" class="mb-3 w-100">
                    </div>
                    <div class="form-group text-center">
                        <p class="text text-danger m-3"><?php echo $inputError; ?></p>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary w-100 cus-btn">ログイン</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
