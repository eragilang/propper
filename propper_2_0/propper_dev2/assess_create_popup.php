<?php
    session_start();

    $hak = $_SESSION['aCCeSSRiGHT'];
    $employeeid = $_SESSION["uSeRiD"];

    if (isset($_REQUEST["btnSubmit"])){
        $rnum = rand(111, 99999999);
        $idrand = strtoupper(substr(md5($_SESSION["uSeRiD"]."SQM".$rnum."main.php"),0,10)) ;
        $_SESSION["prevpage"] = "main.php";
        $_SESSION["rnum"] = $rnum;

        echo "<script language=\"javascript\">window.open('assessment_pre_start.php?rnum=$rnum&idrand=$idrand','pnpassessment','height='+(screen.height)+',width='+(screen.width)+',center=yes,toolbar=no,scrollbars=yes,status=no,menubar=no,location=no');</script>";
        echo "<script language=\"javascript\">location.href=\"main.php\"</script>";
    }
    else{
        echo "<script language=\"javascript\">location.href=\"main.php?err=461003\"</script>";
    }
?>