<?php
session_start();

include_once("connection/cons.php");

$con = conns();

if(empty($_SESSION['Login']) && empty($_SESSION['Usernames'])){
    echo header ("Location: index.php");
}

date_default_timezone_set("Asia/Manila");

if(isset($_GET['ID']))
{
    $id = $_GET['ID'];
    $log_stat = $_GET['statuss'];
    $time_in_out = $_GET['time'];

    // check ID
    $check_id = "SELECT * FROM employees WHERE ID = '$id'";
    $result = $con->query($check_id) or die ($con->error);
    $row = $result->fetch_assoc();
    $count = mysqli_num_rows($result);


    if($count > 0){
        $employee_id = $row['emp_id'];
        $name = $row['lname'].', '.$row['fname'].' '.substr($row['mname'],0,1).' '.$row['extname'];
        $position = $row['position'];
        $pictures = $row['pictures'];
    }else{
        $employee_id = '---';
        $name = '---';
        $position = '---';
        $pictures = '---';
        $id = '---';
    }
}else
{
    $employee_id = '---';
    $name = '---';
    $position = '---';
    $pictures = '---';
    $id = '---';
}

if(isset($_GET['page1']))
{
    $page1 = $_GET['page1'];
}else{
    $page1 = 1;
}

$limit1 = $page1 * 5;
$start1 = $limit1 - 5;

if(isset($_GET['page2']))
{
    $page2 = $_GET['page2'];
}else{
    $page2 = 1;
}

$limit2 = $page2 * 7;
$start2 = $limit2 - 7;



// header
include('includes/header.php');

?>

    <body onload="document.bcode.barcode.focus();" background="img/bluegreen.jpg">

            <div class="top_menu">
                <h2><img src="img/COHLogo3.png" alt=""><span> Employees Attendance Recording System ver. 2.0</span> </h2>
                <h3><a href="breaktime.php?
                    ID=<?php echo $id ?>&statuss=<?php
                        if(empty($log_stat)){
                            echo '---';
                        }else{
                            echo $log_stat;
                        }?>&time=<?php 
                        if(empty($time_in_out)){
                            echo '---';
                        }else{
                            echo $time_in_out;
                        }?>" accesskey="1 b">
                     <img src="img/hand-cursor-48.png" alt=""> Breaktime In / Out</a></h3>
            </div>

        <div class="container">            

            <div class="left_side">

                <h2 style="color: blue;"><img src="img/clock.gif" alt="" width="43px">
                    Time In / Out
                    <img src="img/clock.gif" alt="" width="43px">
                </h2>
            
                <div class="info">
                    <h3>Employee's Information</h3>
                        <?php
                            if($pictures == '---')
                            {
                                ?>
                                <img src="img/cross.gif" alt="" width="">
                            <?php
                            }elseif(empty($pictures))
                            {
                                ?>
                                    <img src="img/blank.jpg" alt="" width="">
                            <?php
                            }elseif(!empty($pictures) && $pictures != '---')
                            {
                                ?>
                                    <img src="data:image/jpg;charset=utf8;base64,<?php echo base64_encode($pictures); ?>"/>
                            <?php
                            }
                        ?>
                        <div class="infos">
                            <span>Employee ID:</span>
                            <br>
                            <b><?php echo $employee_id?></b>
                            <br>
                            <span>Name:</span>
                            <br>
                            <b><?php echo $name?></b>
                            <br>
                            <span>Position:</span>
                            <br>
                            <b><?php echo $position?></b>
                        </div>

                        <div class="bday">
                            <?php
                                if(isset($_SESSION['Birthdate']))
                                {
                            ?>
                                <img src="img/birthday.gif" alt="">
                            <?php
                                unset($_SESSION['Birthdate']);
                                }
                            ?>
                        </div>

                </div>

                <div class="stat">
                    <?php
                        if(isset($_SESSION['Log_Error']))
                        {
                            ?>
                                <b><img src="img/cross0.png" alt="" width="30px">
                                    <?php echo $_SESSION['Log_Error']?></b>
                            <?php
                            unset($_SESSION['Log_Error']);
                        }elseif($employee_id == '---'){
                            ?>
                            <b><img src="img/cross0.png" alt="" width="30px"></b>
                            <?php
                        }elseif(!empty($log_stat)){
                            ?>
                                
                                <b><img src="img/check.png" alt="" width="30px">
                                    <?php echo "TIME ".$log_stat." / ".$time_in_out?></b>
                            <?php
                        }
                    ?>
                </div>

                <form class="bcode" name="bcode" action="loggings.php" method="post">
                    <input type="text" name="barcode" class="form-control" placeholder="Employee ID no." required>
                </form>

                <div class="dtr">
                    <h3>Daily Time Record Month of <?php echo date('F Y')?></h3>
                    <table>
                        <thead>
                            <tr>
                                <th>Day</th>
                                <th>In-1</th>
                                <th>Out-1</th>
                                <th>In-2</th>
                                <th>Out-2</th>
                                <th>In-3</th>
                                <th>Out-3</th>
                                <th>Min. Late</th>
                                <th>Remarks</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                // loggings
                                $logs = "SELECT * FROM dtrs WHERE emp_id = '$id' ORDER BY ID DESC";
                                $res_log = $con->query($logs) or die ($con->error);
                                $row_log = $res_log->fetch_assoc();
                                $count_logs = mysqli_num_rows($res_log);

                                $no1 = 1;

                                if($count_logs > 0){
                                    do{
                                        $dates = $row_log['log_date'];
                                        $mmmm = date('F Y',strtotime($dates));
                                        $day = date('d',strtotime($dates));

                                        if($mmmm == date('F Y')){
                                            if($no1 > $start1 && $no1 <= $limit1){
                                                ?>
                                                    <tr>
                                                        <td><?php echo $day?></td>
                                                        <td><?php echo $row_log['in1']?></td>
                                                        <td><?php echo $row_log['out1']?></td>
                                                        <td><?php echo $row_log['in2']?></td>
                                                        <td><?php echo $row_log['out2']?></td>
                                                        <td><?php echo $row_log['in4']?></td>
                                                        <td><?php echo $row_log['out4']?></td>
                                                        <td><?php echo $row_log['in_lates']?></td>
                                                        <td><?php echo $row_log['remarks']?></td>
                                                    </tr>
                                                <?php
                                            }
                                            $no1++;
                                        }else{
                                            $count_logs--;
                                        }
                                    }while($row_log = $res_log->fetch_assoc());
                                }else{
                                    ?>
                                        <tr>
                                            <td colspan="9">No Records Found</td>
                                        </tr>
                                    <?php
                                }
                            ?>
                        </tbody>
                        <?php 
                            $pp1 = ceil($count_logs / 5);

                            $prev1 = $page1 - 1;
                            $next1 = $page1 + 1;
                        ?>
                        <tfoot>
                            <tr>
                                <td colspan="9">
                                <?php
                                if($page1 > 1)
                                {
                                ?>
                                    <a href="in_out.php?ID=<?php echo $id ?>&statuss=<?php echo $log_stat?>&time=<?php echo $time_in_out?>
                                            &page1=1"><img src="img/backward.png" alt=""></a>
                                    <a href="in_out.php?ID=<?php echo $id ?>&statuss=<?php echo $log_stat?>&time=<?php echo $time_in_out?>
                                            &page1=<?php echo $prev1?>"><img src="img/previous.png" alt=""></a>
                                    <?php } ?>
                                    <?php echo 'Page '.$page1.' of '.$pp1;
                                    if($page1 < $pp1)
                                    {
                                    ?>
                                    <a href="in_out.php?ID=<?php echo $id ?>&statuss=<?php echo $log_stat?>&time=<?php echo $time_in_out?>
                                    &page1=<?php echo $next1?>"><img src="img/next.png" alt=""></a>
                                    <a href="in_out.php?ID=<?php echo $id ?>&statuss=<?php echo $log_stat?>&time=<?php echo $time_in_out?>
                                    &page1=<?php echo $pp1?>"><img src="img/forward.png" alt=""></a>
                                <?php } ?>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

            </div>

            <div class="right_side">
                <!-- <img src="img/COHLogo3.png" alt=""> -->

                <div id="time">
                    <h3><?php echo date('l, F d, Y')?></h3>
                    <h1><?php echo date('h:i:s A')?></h1>
                </div>

                <div class="in_out">
                <!-- <div id="app"></div>
    <script src="src/index.js"></script> -->
                    <?php
                        // include("table1.php");
                    ?>

                    <table>
                        <thead>
                            <tr>
                                <th></th>
                                <th>Name</th>
                                <th>Status</th>
                                <th>Log Time</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                $date_now = date('l, F d, Y');
                                // in out
                                $log_stats = "SELECT * FROM logs WHERE log_date = '$date_now' && statuss = 'IN' || log_date = '$date_now' && statuss = 'OUT' ORDER BY ID DESC";
                                $stat_log = $con->query($log_stats) or die ($con->error);
                                $row_stat = $stat_log->fetch_assoc();
                                $count_log_stat = mysqli_num_rows($stat_log);

                                $no2 = 1;

                                if($count_log_stat > 0)
                                {
                                    do{
                                            $emp_id = $row_stat['emp_id'];

                                            // check ID
                                            $check_ids = "SELECT * FROM employees WHERE ID = '$emp_id'";
                                            $results = $con->query($check_ids) or die ($con->error);
                                            $rows = $results->fetch_assoc();
                                            $counts = mysqli_num_rows($results);

                                            if($counts < 1){
                                                $no2--;
                                                $count_log_stat = $count_log_stat - 1;
                                            }else{
                                                $idS = $rows['ID'];
                                                $pix = $rows['pictures'];

                                                if($no2 > $start2 && $no2 <= $limit2){
                                                    ?>
                                                        <tr>
                                                            <td><a href="in_out.php?ID=<?php echo $idS ?>&statuss=<?php echo $row_stat['statuss']?>&time=<?php echo $row_stat['log_time']?>">
                                                                <!-- <img src="img/eye.png" alt=""></a> -->
                                                                    <?php
                                                                        if(empty($pix))
                                                                            {
                                                                                ?>
                                                                                    <img src="img/blank.jpg" alt="" width="">
                                                                            <?php
                                                                            }elseif(!empty($pix) && $pix != '---')
                                                                            {
                                                                                ?>
                                                                                    <img src="data:image/jpg;charset=utf8;base64,<?php echo base64_encode($pix); ?>"/>
                                                                            <?php
                                                                            }
                                                                    ?>
                                                                </a>
                                                            </td>
                                                            <td><?php echo $row_stat['fullname']?></td>
                                                            <td><?php echo $row_stat['statuss']?></td>
                                                            <td><?php echo $row_stat['log_time']?></td>
                                                        </tr>
                                                    <?php
                                                }
                                            }
                                        $no2++;
                                    }while($row_stat = $stat_log->fetch_assoc());
                                }else
                                {
                                    ?>
                                        <tr>
                                            <td colspan="4">No Records Found</td>
                                        </tr>
                                    <?php
                                }
                            ?>
                        </tbody>

                        <?php 
                            $pp2 = ceil($count_log_stat / 7);

                            $prev2 = $page2 - 1;
                            $next2 = $page2 + 1;
                        ?>
                        <tfoot>
                            <tr>
                                <td colspan="4">
                                <?php
                                if($count_log_stat < 1){
                                    $page2 = 0;
                                }
                                if($page2 > 1)
                                {
                                ?>
                                    <a href="in_out.php?ID=<?php echo $id ?>&statuss=<?php echo $log_stat?>&time=<?php echo $time_in_out?>
                                            &page2=1"><img src="img/backward.png" alt=""></a>
                                    <a href="in_out.php?ID=<?php echo $id ?>&statuss=<?php echo $log_stat?>&time=<?php echo $time_in_out?>
                                            &page2=<?php echo $prev2?>"><img src="img/previous.png" alt=""></a>
                                    <?php } ?>
                                    <?php echo 'Page '.$page2.' of '.$pp2;
                                    if($page2 < $pp2)
                                    {
                                    ?>
                                    <a href="in_out.php?ID=<?php echo $id ?>&statuss=<?php echo $log_stat?>&time=<?php echo $time_in_out?>
                                    &page2=<?php echo $next2?>"><img src="img/next.png" alt=""></a>
                                    <a href="in_out.php?ID=<?php echo $id ?>&statuss=<?php echo $log_stat?>&time=<?php echo $time_in_out?>
                                    &page2=<?php echo $pp2?>"><img src="img/forward.png" alt=""></a>
                                <?php } ?>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                        <div class="btn-group-toggle" data-toggle="buttons" style="float: right;">
                            <img src="images/f11.png" alt="" style="width: 20px; height: 20px; margin: 0; padding: 0; margin-right: 5px"><span>Fullscreen  </span>
                            <img src="images/f5.png" alt="" style="width: 20px; height: 20px; margin: 0; padding: 0; margin-right: 5px; margin-left: 10px; padding-top: 5px"><span>Refresh</span>
                        </div>
                </div>
            </div>
        </div>
    </body>
    <?php 
        include("includes/footer.php");
    ?>
</html>


<script>
    $(document).ready(function() {
        setInterval(function() {
            $('#time').load('time.php')
        }, 1000);
    });

    $(document).keypress(function (e) {
    if (e.which==45) {
        window.location.replace("breaktime.php?ID=<?php echo $id ?>&statuss=<?php
                        if(empty($log_stat)){
                            echo '---';
                        }else{
                            echo $log_stat;
                        }?>&time=<?php 
                        if(empty($time_in_out)){
                            echo '---';
                        }else{
                            echo $time_in_out;
                        }?>"
        )
        }
    });


    $(document).keypress(function (e) {
    if (e.which==43 || e.which==48 || e.which==49 || e.which==50 || e.which==51 || e.which==52 || e.which==53 || e.which==54 || e.which==55 || e.which==56 || e.which==57) {
        window.location.replace("in_out.php?ID=<?php echo $id ?>&statuss=<?php
                        if(empty($log_stat)){
                            echo '---';
                        }else{
                            echo $log_stat;
                        }?>&time=<?php 
                        if(empty($time_in_out)){
                            echo '---';
                        }else{
                            echo $time_in_out;
                        }?>"
        )}
    });
</script>