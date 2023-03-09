<?php
    session_start();

    // not yet login
    if (!isset($_SESSION['loginauth'])) {
        header('location: index.php');
        exit();
    }

    // session unset for input data
    if (isset($_SESSION['post_data'])) {
        unset($_SESSION['post_data']);
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Main</title>
    <!-- CSS only -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-0evHe/X+R7YkIZDRvuzKMRqM+OrBnVFBL6DOitfPri4tjfHxaWutUpFmBp4vmVor" crossorigin="anonymous">
    <!-- JavaScript Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/js/bootstrap.bundle.min.js" integrity="sha384-pprn3073KE6tl6bjs2QrFaJGz5/SUsLqktiwsUTF55Jfv3qYSDhgCecCxMW52nD2" crossorigin="anonymous"></script>
    <link href="css/style.css" rel="stylesheet">
</head>
<body> 
    <div class="container">
        <div class="row">
            <div class="col-4 offset-4">
                <form action="">
                    <button type="button" class="btn btn-primary cus-btn" formmethod="get" onclick=document.location.href="./user/search.php">ユーザー検索</button>
                    <?php
                        if ($_SESSION['loginauth'] === '1') {
                            echo "<button type='button' class='btn btn-primary cus-btn' onclick=document.location.href='entry/input.php' formmethod='get'>新規登録</button>";
                        }
                    ?>
                    <button type="button" class="btn btn-primary cus-btn" formmethod="get" onclick=document.location.href="logout.php">ログアウト</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
