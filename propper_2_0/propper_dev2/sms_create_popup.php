<?php
    session_start();

    $hak = $_SESSION['aCCeSSRiGHT'];
    $employeeid = $_SESSION["uSeRiD"];

    if (isset($_REQUEST["btnRequestSoal"])){
        $rnum = rand(111, 99999999);
        $idrand = strtoupper(substr(md5($_SESSION["uSeRiD"]."SQM".$rnum."main.php"),0,10)) ;
        $_SESSION["prevpage"] = "main.php";
        $_SESSION["rnum"] = $rnum;

        echo "<script language=\"javascript\">window.open('home_sms_quiz.php?rnum=$rnum&idrand=$idrand','requestsoal','height=600,width=1000,top=30,left=150,center=yes,toolbar=no,scrollbars=yes,status=no,menubar=no,location=no');</script>";
        echo "<script language=\"javascript\">location.href=\"main.php\"</script>";
    }
    else{
        echo "<script language=\"javascript\">location.href=\"main.php?err=461003\"</script>";
    }
?>