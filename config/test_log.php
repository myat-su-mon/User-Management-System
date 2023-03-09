<?php
    date_default_timezone_set("Asia/Yangon");

    function writeLog($filename, $query, $error_message){
        $date = date("D F j h:i:s Y");
        $root = dirname(__DIR__, 1);
        $file = fopen("$root/logs/" . date("Ymd") . "log.txt", "a");

        fwrite($file, "$date \t" . str_repeat("=", 20) . $filename . str_repeat("=", 20) . "\n" .
                    "$date \t Query => \t\t $query \n" .
                    "$date \t Error => $error_message \n" .
                    "$date \t" . str_repeat("=", 55) . "\n\n"
                    );
        fclose($file);
    }
?>