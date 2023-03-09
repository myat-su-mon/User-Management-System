<?php
    function logerror($fname, $sql, $message) {
        $file = realpath($_SERVER["DOCUMENT_ROOT"]).'/msm/logs/'.date('Ymd').'log.log';
        $file = fopen($file, 'a');

        $date_time = date('D'). " " .date('M'). " " .date('d'). " " .date('H:i:s'). " " .date('Y');

        $fname   = $date_time . "   ====================$fname====================\n";
        $query   = $date_time . "   Query => $sql\n";
        $message = $date_time . "   Error => $message\n";
        $end     = $date_time . "   =======================================================\n";
        $text    = $fname.$query.$message.$end;

        fwrite ($file, $text);
        fclose($file);
    }
?>