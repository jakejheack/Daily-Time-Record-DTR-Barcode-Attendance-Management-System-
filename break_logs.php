<?php
session_start();

include_once("connection/cons.php");

$con = conns();

date_default_timezone_set("Asia/Manila");

if(isset($_POST['barcode'])){
    
    // $current_time=time();
    // $DateTime=strftime("%d-%m-%y  %H:%M:%S",$current_time);
    // $DateTime;
    
    $dAte = date('l, F d, Y');
    $time = date('h:i A');
    $now = date('F d');
    $last_time = date('h:i:s A');

    $emp_id = trim($_POST['barcode']);

    // check ID number
    $check_id = "SELECT * FROM employees WHERE emp_id = '$emp_id'";
    $result = $con->query($check_id) or die ($con->error);
    $row = $result->fetch_assoc();
    $count = mysqli_num_rows($result);     
    
    if($count > 0)
    {
        $IDs = $row['ID'];
        $name = $row['lname'].', '.$row['fname'].' '.substr($row['mname'],0,1).' '.$row['extname'];
        $bday = $row['bdate'];
        $bday = date('F d', strtotime($bday));
        // $position = $row['position'];
        // $pictures = $row['pictures'];    

        // loggings in breaks
        $logs = "SELECT * FROM dtrs_breaks WHERE emp_id = '$IDs' && log_date = '$dAte'";
        $res_log = $con->query($logs) or die ($con->error);
        $row_log = $res_log->fetch_assoc();
        $count_logs = mysqli_num_rows($res_log);

        // loggings
        $l_in = "SELECT * FROM dtrs WHERE emp_id = '$IDs' && log_date = '$dAte'";
        $res_in = $con->query($l_in) or die ($con->error);
        $row_in = $res_in->fetch_assoc();
        $count_in_out = mysqli_num_rows($res_in);

        $log_no = 0;
        
        if($count_logs > 0)
        {
            $id_log = $row_log['ID'];
            $log_no = $row_log['log_no'];
            $time_last = strtotime($row_log['last_time']);
            $n_time = strtotime($last_time);

            $sec = abs($n_time - $time_last)/3600;
            $sec = $sec * 60;
            $sec = $sec * 60;

            $time_last = date('h:i:s A', $time_last);
            // bt-out 1
            if($log_no == '1')
            {
                $in = strtotime($row_log['in1']);
                $out = strtotime($time);

                $total_hrs = abs($out - $in)/3600;
                $total_hrs = $total_hrs * 60;
                $total_hrs = round($total_hrs,2);

                if($sec < 60){
                    // interval
                    $success = "NO";
                    $in = $row_log['in1'];
                    echo header("Location: breaktime.php?ID=".$IDs."&statuss=IN ALREADY (Interval 60 sec.)&time=".$time_last."");
                }else{

                    if($total_hrs > 30)
                    {
                        $late = $total_hrs - 30;
                        $remarks = $late." mins. late";
                        $sql = "UPDATE `dtrs_breaks` SET `log_no` = '2', `out1` = '$time', `remarks` = '$remarks', `total_hrs` = '$total_hrs', `last_time` = '$last_time', `lates` = '$late' WHERE `dtrs_breaks`.`ID` = '$id_log'";
                    }else{
                        $sql = "UPDATE `dtrs_breaks` SET `log_no` = '2', `out1` = '$time', `total_hrs` = '$total_hrs', `last_time` = '$last_time' WHERE `dtrs_breaks`.`ID` = '$id_log'";
                    }

                    $log_stat = "BT-OUT";
                }
            // bt-in 2
            }elseif($log_no == '2')
            {
                $prev = strtotime($row_log['out1']);
                $out1 = strtotime($out1);
                $pres = strtotime($time);

                $interval = abs($pres - $prev)/3600;
                $interval = $interval * 60;
                $interval = round($interval,2);

                if($sec < 60){
                    $success = "NO";
                    $out = $row_log['out1'];
                    echo header("Location: breaktime.php?ID=".$IDs."&statuss=OUT ALREADY (Interval 60 sec.)&time=".$time_last."");
                }else{
                    $sql = "UPDATE `dtrs_breaks` SET `log_no` = '3', `in2` = '$time', `last_time` = '$last_time' WHERE `dtrs_breaks`.`ID` = '$id_log'";
                    $log_stat = "BT-IN";
                    $rem_mins = $row_log['total_hrs'];
                }
            // bt-out 2
            }if($log_no == '3')
            {
                $hrs = $row_log['total_hrs'];
                $in = strtotime($row_log['in2']);
                $out = strtotime($time);

                $total_hrs = abs($out - $in)/3600;
                $total_hrs = $total_hrs * 60;
                $total_hrs = round($total_hrs,2);
                $interval = $total_hrs;
                $total_hrs = $total_hrs + $hrs;
                $late = $total_hrs - 30;

                if($sec < 60){
                    // interval
                    $success = "NO";
                    $in = $row_log['in2'];
                    echo header("Location: breaktime.php?ID=".$IDs."&statuss=IN ALREADY (Interval 60 sec.)&time=".$time_last."");
                }else{

                    if($total_hrs > 30)
                    {
                        $remarks = $late." mins. late";
                        $sql = "UPDATE `dtrs_breaks` SET `log_no` = '4', `out2` = '$time', `remarks` = '$remarks', `total_hrs` = '$total_hrs', `last_time` = '$last_time', `lates` = '$late' WHERE `dtrs_breaks`.`ID` = '$id_log'";
                    }else{
                        $sql = "UPDATE `dtrs_breaks` SET `log_no` = '4', `out2` = '$time', `total_hrs` = '$total_hrs', `last_time` = '$last_time' WHERE `dtrs_breaks`.`ID` = '$id_log'";
                    }
                    
                    $log_stat = "BT-OUT";
                }
            // bt-in 3
            }if($log_no == '4')
            {
                $prev = strtotime($row_log['out2']);
                $pres = strtotime($time);
                $in = strtotime($row_log['in2']);
                $hrs = $row_log['total_hrs'];

                $interval = abs($pres - $prev)/3600;
                $interval = $interval * 60;
                $interval = round($interval,2);

                if($sec < 60){
                    $success = "NO";
                    $out = $row_log['out2'];
                    echo header("Location: breaktime.php?ID=".$IDs."&statuss=OUT ALREADY (Interval 60 sec.)&time=".$time_last."");
                }else{
                    $sql = "UPDATE `dtrs_breaks` SET `log_no` = '5', `in3` = '$time', `last_time` = '$last_time' WHERE `dtrs_breaks`.`ID` = '$id_log'";
                    $log_stat = "BT-IN";
                    $rem_mins = $row_log['total_hrs'];
                }
            // bt-out 3
            }if($log_no == '5')
            {
                $hrs = $row_log['total_hrs'];
                $in = strtotime($row_log['in3']);
                $out = strtotime($time);

                $total_hrs = abs($out - $in)/3600;
                $total_hrs = $total_hrs * 60;
                $total_hrs = round($total_hrs,2);
                $interval = $total_hrs;
                $total_hrs = $total_hrs + $hrs;
                $late = $total_hrs - 30;

                if($sec < 60){
                    // interval
                    $success = "NO";
                    $in = $row_log['in3'];
                    echo header("Location: breaktime.php?ID=".$IDs."&statuss=IN ALREADY (Interval 60 sec.)&time=".$time_last."");
                }else{

                    if($total_hrs > 30)
                    {
                        $remarks = $late." mins. late";
                        $sql = "UPDATE `dtrs_breaks` SET `log_no` = '6', `out3` = '$time', `remarks` = '$remarks', `total_hrs` = '$total_hrs', `last_time` = '$last_time', `lates` = '$late' WHERE `dtrs_breaks`.`ID` = '$id_log'";
                    }else{
                    $sql = "UPDATE `dtrs_breaks` SET `log_no` = '6', `out3` = '$time', `total_hrs` = '$total_hrs', `last_time` = '$last_time' WHERE `dtrs_breaks`.`ID` = '$id_log'";
                    }
                    
                    $log_stat = "BT-OUT";
                }
            }if($log_no >= '6')
            {
                $_SESSION['Log_Error'] = "Log in Error!";
            }
        }else
        {
            $sql = "INSERT INTO `dtrs_breaks` (`ID`, `emp_id`, `log_date`, `log_no`, `in1`, `out1`, `in2`, `out2`, `in3`, `out3`, `remarks`, `last_time`) 
            VALUES (NULL, '$IDs', '$dAte', '1', '$time', '00:00', '00:00', '00:00', '00:00', '00:00', '', '$last_time')";

            $log_stat = "BT-IN";
            $rem_mins = 0;
        }


        if($log_no < 6){
                // logs record
                if($log_stat == "BT-IN"){
                    if($rem_mins < 0){
                        $mins = 0;
                    }else{
                        $mins = 30 - floatval($rem_mins);
                    }
                    $nTime = strtotime($time);
                    $endTime = date("h:i A", strtotime('+'.$mins.' minutes', $nTime));
                }else{
                    $endTime = "";
                }

                $sql_logs = "INSERT INTO `logs` (`ID`, `emp_id`, `fullname`, `statuss`, `log_time`, `log_date`, `inss`)
                            VALUES (NULL, '$IDs', '$name', '$log_stat', '$time', '$dAte', '$endTime')";
                
                $con->query($sql_logs) or die ($con->error);
            
            if($con->query($sql) or die ($con->error))
            {
                echo header("Location: breaktime.php?ID=".$IDs."&statuss=".$log_stat."&time=".$time."");
            }else{
                echo "Something went wrong";
            }
        }else{
            echo header("Location: breaktime.php?ID=".$IDs."&statuss=&time=");
        }

        if($bday == $now)
        {
            $_SESSION['Birthdate'] = $bday;
        }

    }else{
        $_SESSION['Log_Error'] = "Invalid ID!";
        echo header("Location: breaktime.php");
    }


}

?>