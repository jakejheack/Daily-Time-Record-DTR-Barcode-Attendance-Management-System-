<?php
session_start();

include_once("connection/cons.php");

$con = conns();

if(empty($_SESSION['Login']) && empty($_SESSION['Usernames'])){
    echo header ("Location: index.php");
}

date_default_timezone_set("Asia/Manila");

if(isset($_POST['barcode'])){
    
    // $current_time=time();
    // $DateTime=strftime("%d-%m-%y  %H:%M:%S",$current_time);
    // $DateTime;

    $dAte = date('l, F d, Y');
    $time = date('h:i A');
    $last_time = date('h:i:s A');
    // $time = '11:45 AM';
    $now = date('F d');
    $day_now = date('D');

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

        // loggings
        $logs = "SELECT * FROM dtrs WHERE emp_id = '$IDs' && log_date = '$dAte'";
        $res_log = $con->query($logs) or die ($con->error);
        $row_log = $res_log->fetch_assoc();
        $count_logs = mysqli_num_rows($res_log);

        $log_no = 0;

        // schedules
        $sched = "SELECT * FROM schedules WHERE emp_id = '$IDs'";
        $res_sched = $con->query($sched) or die ($con->error);
        $row_sched = $res_sched->fetch_assoc();
        $count_sched = mysqli_num_rows($res_sched);

            if($count_sched > 0)
            {
                if($day_now == 'Mon'){
                    $shift = $row_sched['mon'];
                    $in1 = $row_sched['mon_in1'];
                    $out1 = $row_sched['mon_out1'];
                    $in2 = $row_sched['mon_in2'];
                    $out2 = $row_sched['mon_out2'];
                }elseif($day_now == 'Tue'){
                    $shift = $row_sched['tue'];
                    $in1 = $row_sched['tue_in1'];
                    $out1 = $row_sched['tue_out1'];
                    $in2 = $row_sched['tue_in2'];
                    $out2 = $row_sched['tue_out2'];
                }elseif($day_now == 'Wed'){
                    $shift = $row_sched['wed'];
                    $in1 = $row_sched['wed_in1'];
                    $out1 = $row_sched['wed_out1'];
                    $in2 = $row_sched['wed_in2'];
                    $out2 = $row_sched['wed_out2'];
                }elseif($day_now == 'Thu'){
                    $shift = $row_sched['thu'];
                    $in1 = $row_sched['thu_in1'];
                    $out1 = $row_sched['thu_out1'];
                    $in2 = $row_sched['thu_in2'];
                    $out2 = $row_sched['thu_out2'];
                }elseif($day_now == 'Fri'){
                    $shift = $row_sched['fri'];
                    $in1 = $row_sched['fri_in1'];
                    $out1 = $row_sched['fri_out1'];
                    $in2 = $row_sched['fri_in2'];
                    $out2 = $row_sched['fri_out2'];
                }elseif($day_now == 'Sat'){
                    $shift = $row_sched['sat'];
                    $in1 = $row_sched['sat_in1'];
                    $out1 = $row_sched['sat_out1'];
                    $in2 = $row_sched['sat_in2'];
                    $out2 = $row_sched['sat_out2'];
                }elseif($day_now == 'Sun'){
                    $shift = $row_sched['sun'];
                    $in1 = $row_sched['sun_in1'];
                    $out1 = $row_sched['sun_out1'];
                    $in2 = $row_sched['sun_in2'];
                    $out2 = $row_sched['sun_out2'];
                }
            }

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
            // OUT 1
            if($log_no == '1')
            {
                $in = strtotime($row_log['in1']);
                $out = strtotime($time);

                $total_hrs = abs($out - $in)/3600;
                $total_hrs = round($total_hrs,2);

                if($sec < 60){
                    $success = "NO";
                    $in = $row_log['in1'];
                    echo header("Location: in_out.php?ID=".$IDs."&statuss=IN ALREADY (Interval 60 sec.)&time=".$time_last."");
                }else{

                    if($count_sched > 0){
                        if($shift == 'OFF'){
                            if($total_hrs > 8.25)
                            {
                                $total_hrs = '8.25';
                            }

                            $sql = "UPDATE `dtrs` SET `log_no` = '2', `out1` = '$time', `total_hrs` = '$total_hrs', `last_time` = '$last_time' WHERE `dtrs`.`ID` = '$id_log'";
                        }elseif($shift == 'ON'){
                            $in1 = strtotime($in1);
                            $out1 = strtotime($out1);
                            $in2 = strtotime($in2);
                            $out2 = strtotime($out2);
                            $tt = strtotime($time);

                            // undertime
                            $timeLate = abs($tt - $out1)/3600;
                            $timeLate = $timeLate * 60;

                            // early in
                            $adv = abs($in - $in1)/3600;
                            $adv = $adv * 60;

                            // greater than 15 minutes early in schedule
                            if($adv > 15 && $in < $in1){
                                $in = $in1 - (15 * 60);
                            }

                            if($timeLate > 0 && $out < $out1)
                            {
                                // ut
                                $timeLate = round($timeLate);
                                $total_hrs = abs($out - $in)/3600;
                                $total_hrs = round($total_hrs,2);
                            }elseif($out >= $out1 && $out <= $in2){
                                // out w/ in 1 hour break
                                $timeLate = '';
                                $total_hrs = abs($out1 - $in)/3600;
                                $total_hrs = round($total_hrs,2);
                            }elseif($out >= $out2){
                                // without lunch break but not u.t.
                                $timeLate = '';
                                $total_1 = abs($out2 - $in2)/3600;
                                $total_2 = abs($out1 - $in)/3600;
                                $total_hrs = floatval($total_1) + floatval($total_2);
                                $total_hrs = round($total_hrs,2);
                            }elseif($out < $out2 && $out > $in2){
                                // undertime
                                $timeLate = abs($tt - $out2)/3600;
                                $timeLate = $timeLate * 60;

                                // w/out lunch break and w/ ut
                                $timeLate = round($timeLate);
                                $total_1 = abs($out - $in2)/3600;
                                $total_2 = abs($out1 - $in)/3600;
                                $total_hrs = floatval($total_1) + floatval($total_2);
                                $total_hrs = round($total_hrs,2);
                            }

                            if($total_hrs > 8.25)
                            {
                                $total_hrs = '8.25';
                            }

                            $sql = "UPDATE `dtrs` SET `log_no` = '2', `out1` = '$time', `total_hrs` = '$total_hrs', `ut_times` = '$timeLate', `last_time` = '$last_time' WHERE `dtrs`.`ID` = '$id_log'";
                        }
                    }else{
                        if($total_hrs > 8.25)
                        {
                            $total_hrs = '8.25';
                        }

                        $sql = "UPDATE `dtrs` SET `log_no` = '2', `out1` = '$time', `total_hrs` = '$total_hrs', `last_time` = '$last_time' WHERE `dtrs`.`ID` = '$id_log'";
                    }
        
                    $log_stat = "OUT";
                    $success = "YES";
                }

            // in 2
            }elseif($log_no == '2')
            {
                $prev = strtotime($row_log['out1']);
                $out1 = strtotime($out1);
                $pres = strtotime($time);

                $interval = abs($pres - $prev)/3600;
                $interval = $interval * 60;

                if($sec < 60){
                    $success = "NO";
                    $out = $row_log['out1'];
                    echo header("Location: in_out.php?ID=".$IDs."&statuss=OUT ALREADY (Interval 60 sec.)&time=".$time_last."");
                }else{
                    if($count_sched > 0){
                        if($shift == 'OFF'){
                            $sql = "UPDATE `dtrs` SET `log_no` = '3', `in2` = '$time', `last_time` = '$last_time' WHERE `dtrs`.`ID` = '$id_log'";
                            $log_stat = "IN";
                            $success = "YES";        
                        }elseif($shift == 'ON'){
                            $in2 = strtotime($in2);
                            $tt = strtotime($time);

                            if($tt < $in2 && $tt < $out1){
                                $sql = "UPDATE `dtrs` SET `log_no` = '1', `out1` = '00:00', `total_hrs` = '0', `ut_times` = '0', `last_time` = '$last_time' WHERE `dtrs`.`ID` = '$id_log'";
                                $log_stat = "UPDATED BASED ON YOUR SCHEDULE... ";
                                $success = "YES";
                            }else{
                                if($tt > $in2){
                                    $timeLate = abs($in2 - $tt)/3600;
                                    $timeLate = $timeLate * 60;
                                }else{
                                    $timeLate = 0;
                                }

                                $in_late = $row_log['in_lates'];
                                $in_late = floatval($in_late);
            
                                if($timeLate >= 1)
                                {
                                    $timeLate = round($timeLate);
                                    $timeLate = abs($timeLate + $in_late);
                                }else{
                                    $timeLate = $in_late;
                                }

                                if($timeLate == 0){
                                    $timeLate = '';
                                }
            
                                $sql = "UPDATE `dtrs` SET `log_no` = '3', `in2` = '$time', `in_lates` = '$timeLate', `last_time` = '$last_time' WHERE `dtrs`.`ID` = '$id_log'";
                                $log_stat = "IN";
                                $success = "YES";            
                            }
                        }
                    }else{
                        $sql = "UPDATE `dtrs` SET `log_no` = '3', `in2` = '$time', `last_time` = '$last_time' WHERE `dtrs`.`ID` = '$id_log'";
                        $log_stat = "IN";
                        $success = "YES";
                    }
                }

            // OUT 2
            }if($log_no == '3')
            {
                $hrs = $row_log['total_hrs'];
                $in = strtotime($row_log['in2']);
                $out = strtotime($time);

                $total_hrs = abs($out - $in)/3600;
                $total_hrs = round($total_hrs,2);

                if($sec < 60){
                    $success = "NO";
                    $in = $row_log['in2'];
                    echo header("Location: in_out.php?ID=".$IDs."&statuss=IN ALREADY (Interval 60 sec.)&time=".$time_last."");
                }else{
                    $hrs = floatval($hrs);

                    if($count_sched > 0){
                        if($shift == 'OFF'){
                            $sql = "UPDATE `dtrs` SET `log_no` = '4', `out2` = '$time', `total_hrs` = '$total_hrs', `last_time` = '$last_time' WHERE `dtrs`.`ID` = '$id_log'";
                        }elseif($shift == 'ON'){
                            $in2 = strtotime($in2);
                            $out2 = strtotime($out2);
                            $tt = strtotime($time);

                            // ut
                            $timeLate = abs($tt - $out2)/3600;
                            $timeLate = $timeLate * 60;
                            $ut_time = $row_log['ut_times'];

                            // greater than 15 minutes early in schedule
                            if($in < $in2){
                                $in = $in2;
                            }

                            if($timeLate > 0 && $out < $out2)
                            {
                                // ut
                                $timeLate = floatval($timeLate) + floatval($ut_time);
                                $timeLate = round($timeLate);
                                $total_hrs = abs($out - $in)/3600;
                                $total_hrs = round($total_hrs,2);
                                $total_hrs = $total_hrs + $hrs;
                            }elseif($out >= $out2){
                                $timeLate = $ut_time;
                                $total_hrs = abs($out2 - $in)/3600;
                                $total_hrs = round($total_hrs,2);
                                $total_hrs = $total_hrs + $hrs;
                            }elseif($out >= $out2){
                                $timeLate = $ut_time;
                                $total_hrs = abs($out2 - $in)/3600;
                                $total_hrs = round($total_hrs,2);
                                $total_hrs = $total_hrs + $hrs;
                            }elseif($out < $out2 && $out > $in2){
                                $timeLate = floatval($timeLate) + floatval($ut_time);
                                $timeLate = round($timeLate);
                                $total_hrs = abs($out - $in)/3600;
                                $total_hrs = round($total_hrs,2);
                                $total_hrs = $total_hrs + $hrs;
                            }

                            if($total_hrs > 8.25)
                            {
                                $total_hrs = '8.25';
                            }

                            $sql = "UPDATE `dtrs` SET `log_no` = '4', `out2` = '$time', `total_hrs` = '$total_hrs', `ut_times` = '$timeLate', `last_time` = '$last_time' WHERE `dtrs`.`ID` = '$id_log'";
                        }
                    }else{
                        $total_hrs = $total_hrs + $hrs;
                        
                        $sql = "UPDATE `dtrs` SET `log_no` = '4', `out2` = '$time', `total_hrs` = '$total_hrs', `last_time` = '$last_time' WHERE `dtrs`.`ID` = '$id_log'";
                    }

                    $log_stat = "OUT";
                    $success = "YES";

                }

            // IN 3
            }if($log_no == '4')
            {
                $prev = strtotime($row_log['out2']);
                $pres = strtotime($time);
                $in = strtotime($row_log['in2']);
                $hrs = $row_log['total_hrs'];

                $interval = abs($pres - $prev)/3600;
                $interval = $interval * 60;

                if($sec < 60){
                    $success = "NO";
                    $out = $row_log['out2'];
                    echo header("Location: in_out.php?ID=".$IDs."&statuss=OUT ALREADY (Interval 60 sec.)&time=".$time_last."");
                }else{
                    $out = $row_log['out2'];
                    $out = strtotime($out);

                    $in2 = strtotime($in2);
                    $out2 = strtotime($out2);
                    $tt = strtotime($time);
                    
                    if($tt >= $in2 && $tt <= $out2){
                        // ut
                        $timeLate = abs($out2 - $out)/3600;
                        $timeLate = $timeLate * 60;
                        $ut_time = floatval($row_log['ut_times']);

                        if($ut_time >= 1){
                            $timeLate = $ut_time - $timeLate;
                        }else{
                            $timeLate = 0;
                        }

                        // greater than 15 minutes early in schedule
                        if($in < $in2){
                            $in = $in2;
                        }

                        // hrs
                        $hrs_ded = abs($out - $in)/3600;
                        $hrs_ded = round($hrs_ded,2);

                        if($hrs >= 1 && $hrs >= $hrs_ded){
                            $hrs = $hrs - $hrs_ded;
                        }

                        $sql = "UPDATE `dtrs` SET `log_no` = '3', `out2` = '00:00', `total_hrs` = '$hrs', `ut_times` = '$timeLate', `last_time` = '$last_time' WHERE `dtrs`.`ID` = '$id_log'";

                        $log_stat = "UPDATED BASED ON YOUR SCHEDULE... ";
                        $success = "YES";

                    }else{
                        $sql = "UPDATE `dtrs` SET `log_no` = '5', `in4` = '$time', `last_time` = '$last_time' WHERE `dtrs`.`ID` = '$id_log'";
                        $log_stat = "IN";
                        $success = "YES";
                    }

                }

            // OUT 3 Overtime
            }if($log_no == '5')
            {
                $in = strtotime($row_log['in4']);
                $out = strtotime($time);

                $total_hrs = abs($out - $in)/3600;
                $total_hrs = round($total_hrs,2);

                if($sec < 60){
                    $success = "NO";
                    $in = $row_log['in4'];
                    echo header("Location: in_out.php?ID=".$IDs."&statuss=IN ALREADY (Interval 60 sec.)&time=".$time_last."");
                }else{
                    $sql = "UPDATE `dtrs` SET `log_no` = '6', `out4` = '$time', `total_ot` = '$total_hrs', `last_time` = '$last_time' WHERE `dtrs`.`ID` = '$id_log'";
                    $log_stat = "OUT";
                    $success = "YES";
                }

            }if($log_no >= '6')
            {
                $_SESSION['Log_Error'] = "Log in Error!";
            }
        }else{
            if($count_sched > 0){

                if($shift == 'OFF'){
                    $sql = "INSERT INTO `dtrs` (`ID`, `emp_id`, `log_date`, `log_no`, `in1`, `out1`, `in2`, `out2`, `in3`, `out3`, `remarks`, `last_time`) 
                    VALUES (NULL, '$IDs', '$dAte', '1', '$time', '00:00', '00:00', '00:00', '00:00', '00:00', 'DAY-OFF', '$last_time')";
                }elseif($shift == 'ON'){
                    $in1 = strtotime($in1);
                    $tt = strtotime($time); 

                    $in2 = strtotime($in2);
                    $out1 = strtotime($out1);

                    if($tt > $in2){
                        $timeLate = abs($in2 - $tt)/3600;
                        $timeLate = $timeLate * 60;
                    }elseif($tt < $in2 && $tt < $out1 && $tt > $in1){
                        $timeLate = abs($in1 - $tt)/3600;
                        $timeLate = $timeLate * 60;
                    }elseif($tt < $in2 && $tt > $out1 || $in1 < $tt){
                        $timeLate = "";
                    }

                    if($timeLate >= 1)
                    {
                        $timeLate = round($timeLate);
                    }else{
                        $timeLate = '';
                    }

                    if($tt > $out1){
                        $sql = "INSERT INTO `dtrs` (`ID`, `emp_id`, `log_date`, `log_no`, `in1`, `out1`, `in2`, `out2`, `in3`, `out3`, `remarks`, `in_lates`, `last_time`) 
                        VALUES (NULL, '$IDs', '$dAte', '3', '00:00', '00:00', '$time', '00:00', '00:00', '00:00', '', '$timeLate', '$last_time')";                        
                    }else{
                        $sql = "INSERT INTO `dtrs` (`ID`, `emp_id`, `log_date`, `log_no`, `in1`, `out1`, `in2`, `out2`, `in3`, `out3`, `remarks`, `in_lates`, `last_time`) 
                        VALUES (NULL, '$IDs', '$dAte', '1', '$time', '00:00', '00:00', '00:00', '00:00', '00:00', '', '$timeLate', '$last_time')";
                    }
                }

            }elseif($count_sched == 0){

                $sql = "INSERT INTO `dtrs` (`ID`, `emp_id`, `log_date`, `log_no`, `in1`, `out1`, `in2`, `out2`, `in3`, `out3`, `remarks`, `last_time`) 
                VALUES (NULL, '$IDs', '$dAte', '1', '$time', '00:00', '00:00', '00:00', '00:00', '00:00', '', '$last_time')";
            }

            $log_stat = "IN";
            $success = "YES";
        }

        if($log_no < 6 && $success == 'YES'){ 
                // logs record
                $sql_logs = "INSERT INTO `logs` (`ID`, `emp_id`, `fullname`, `statuss`, `log_time`, `log_date`)
                            VALUES (NULL, '$IDs', '$name', '$log_stat', '$time', '$dAte')";
                
                $con->query($sql_logs) or die ($con->error);
            
            if($con->query($sql) or die ($con->error))
            {
                echo header("Location: in_out.php?ID=".$IDs."&statuss=".$log_stat."&time=".$time."");
            }else{
                echo "Something went wrong";
            }
        }elseif($log_no >= 6){
            echo header("Location: in_out.php?ID=".$IDs."&statuss=&time=");
        }

        if($bday == $now)
        {
            $_SESSION['Birthdate'] = $bday;
        }

    }else{
        $_SESSION['Log_Error'] = "Invalid ID!";
        echo header("Location: in_out.php");
    }

}
?>