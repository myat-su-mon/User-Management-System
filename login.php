<?php
    require_once('config/config.php');
    require_once('config/logGen.php');
    session_start();

    $_SESSION['userid'] = isset($_POST['userid'])? $_POST['userid'] : '';

    $userid   = isset($_POST['userid'])? $_POST['userid'] : '';
    $password = isset($_POST['password'])? $_POST['password'] : '';

    try {
        // check userid and password length
        if (!(strlen($userid) >= 8 && strlen($userid) <= 16 && strlen($password) >= 8 && strlen($password) <= 16)) {
            throw new Exception("ユーザID、またはパスワードが違います。");
        }

        // check database connection
        if (!$pdo) {
            throw new Exception("データベース接続に失敗しました。");
        }

        // retrieve user from database
        $sql = <<<EOT
                SELECT
                    *
                FROM
                    USERINFO_TABLE
                WHERE
                    USERID = '$userid' AND
                ACTIVEFLAG='1' AND
                LOCKSTATUS=FALSE
EOT;
        $stmt   = $pdo->query($sql);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        // user does not exist
        if (empty($result)) {
            throw new Exception("ユーザID、またはパスワードが違います。");
        }

        // user exists but incorrect password
        if ($password !== $result['password']) {
            $falsecount  = $result['falsecount']++;
            $sql = <<<EOT
                    UPDATE
                        USERINFO_TABLE
                    SET
                        FALSECOUNT = $falsecount
                        LOCKSTATUS =
                        CASE
                            WHEN $falsecount = 5 THEN TRUE
                            ELSE FALSE
                        END
                    WHERE
                        USERID = '$userid'
EOT;
            $pdo->beginTransaction();
            $stmt  = $pdo->query($sql);
            $count = $stmt->rowCount();

            if ($count == 1) {
                $pdo->commit();
            } else {
                $pdo->rollback();
            }
            throw new Exception("ユーザID、またはパスワードが違います。");
        }

        // correct userid and password
        $sql = <<<EOT
                UPDATE
                    USERINFO_TABLE
                SET
                    FALSECOUNT = 0
                WHERE
                    USERID = '$userid'
EOT;
        $pdo->beginTransaction();
        $stmt  = $pdo->query($sql);
        $count = $stmt->rowCount();

        if ($count == 1) {
            $pdo->commit();
            $_SESSION['loginauth'] = $result['authority'];
        } else {
            $pdo->rollback();
            throw new Exception("ユーザID、またはパスワードが違います。");
        }

    } catch (PDOException $e) {
        $_SESSION['error'] = "詳細についてはシステム管理者に確認してください。";
        logerror(basename($_SERVER['SCRIPT_NAME']), $sql, $_SESSION['error']);
    } catch (Exception $e) {
        $_SESSION['error'] = $e->getMessage();
        logerror(basename($_SERVER['SCRIPT_NAME']), $sql, $_SESSION['error']);
        header('location: index.php');
    }

    if (isset($_SESSION['error'])) {
        header('location: index.php');
    }else {
        header('location: main.php');
    }
?>
