<?php
    require_once('../config/config.php');
    require_once('../config/logGen.php');

    header('Content-Type: application/json');
    $response_code = 200;
    $response_arr  = array();

    try {
        // ヘッダーチェック
        if (!isset($_SERVER["CONTENT_TYPE"]) || empty($_SERVER["CONTENT_TYPE"]) || !($_SERVER["CONTENT_TYPE"] == "application/json")) {
            $response_code = 403;
            throw new Exception("不正なアクセスです。");
        }

        // データベース接続チェック
        if (!$pdo) {
            $response_code = 400;
            throw new Exception("データベース接続に失敗しました。");
        }

        // 無効なリクエスト
        if (!(isset($_GET['userid']) && isset($_GET['age']) && isset($_GET['authority']) &&
                isset($_GET['gender']) && isset($_GET['mobile']) && isset($_GET['address']))) {
            $response_code = 500;
            throw new Exception("データの検索に失敗しました。");
        }

        if (strlen($_GET['gender']) != 5 || strlen($_GET['mobile']) != 7) {
            $response_code = 500;
            throw new Exception("データの検索に失敗しました。");
        }

        // get url parameters
        $userid    = $_GET['userid'];
        $age       = $_GET['age'];
        $authority = $_GET['authority'];
        $gender    = explode("|", $_GET['gender']);
        $mobile    = explode("|", $_GET['mobile']);
        $address   = $_GET['address'];

        $sql = <<<EOT
            SELECT
                USERID,
                AGE,
                CASE AUTHORITY
                    WHEN '1' THEN '管理者'
                    WHEN '2' THEN '一般'
                END AUTHORITY,
                CASE GENDER
                    WHEN 1 THEN '男性'
                    WHEN 2 THEN '女性'
                    WHEN 3 THEN 'その他'
                END GENDER,
                CASE MOBILE
                    WHEN 0 THEN 'なし'
                    WHEN 1 THEN 'iOS'
                    WHEN 2 THEN 'Android'
                    WHEN 3 THEN 'iOS/Android'
                END MOBILE,
                ADDRESS,
                LOCKSTATUS,
                USERSERIAL AS SERIAL
            FROM
                USERINFO_TABLE
EOT;

        $conditions = array();
        $sql .= " WHERE ";

        // where conditions
        if (!empty($userid)) {
            $conditions[] = "USERID = '$userid'";
        }

        if (!empty($age)) {
            // validation check
            if (!is_numeric($age)) {
                $response_code = 400;
                throw new Exception("年齢は数値ではありません。");
            }
            $conditions[] = "AGE = $age";
        }

        if (!empty($authority)) {
            $conditions[] = "AUTHORITY = '$authority'";
        }

        if (!empty($gender) && $_GET['gender'] != '0|0|0') {
            $gen = array();
            foreach ($gender as $key => $value) {
                if ($value == 1) {
                    $gen[] = $key + 1;
                }
            }
            $gen_str = implode(",", $gen);
            $conditions[] = "GENDER IN ($gen_str)";
        }

        if (!empty($mobile) && $_GET['mobile'] != '0|0|0|0') {
            $mob = array();
            if ($mobile[3] == 1) {
                $mob[] = 0;
            }
            for ($i = 0; $i < count($mobile) - 1; $i++) {
                if ($mobile[$i] == 1) {
                    $mob[] = $i + 1;
                }
            }
            $mob_str = implode(",", $mob);
            $conditions[] = "MOBILE IN ($mob_str)";
        }

        if (!empty($address)) {
            $conditions[] = "ADDRESS ILIKE '%$address%'";
        }

        $conditions[] = "ACTIVEFLAG = '1' ";
        $sql .= implode(" AND ",$conditions);

        $sql   .= " ORDER BY CREATEDATE ASC";
        $stmt   = $pdo->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($result) {
            $response = array(
                "status" => "OK",
                "message" => "",
                "data" => $result
            );
        } else {
            $response = array(
                "status" => "OK",
                "message" => "検索条件に対するユーザは０件です。",
                "data" => $result
            );
        }
    } catch (PDOException $e) {
        logerror(basename($_SERVER['SCRIPT_NAME']), $sql, $e->getMessage());
        $response_code = 400;
        $response = array(
            "status" => "NG",
            "message" => "ユーザ検索に失敗しました。"
        );
    } catch (Exception $e) {
        logerror(basename($_SERVER['SCRIPT_NAME']), "", $e->getMessage());
        $response = array(
            "status" => "NG",
            "message" => $e->getMessage()
        );
    }

    http_response_code($response_code);
    echo json_encode($response);
?>
