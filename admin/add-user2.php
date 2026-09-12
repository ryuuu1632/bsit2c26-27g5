<?php
     session_start();

     include("../connect.php");

   
    $sfname =$_POST['sfname'];
    $smname =$_POST['smname'];
    $slname =$_POST['slname'];
    $saddress =$_POST['saddress'];
    $sgender =$_POST['sgender'];
    $sbday =$_POST['sbday'];
    $scontact =$_POST['scontact'];
    $smstatus =$_POST['smstatus'];



    if($sfname=="" || $slname==""){
    echo '<script type="text/javascript">';
    echo 'alert("Bawal Blank ang Name, Utro Please....");';
    echo 'window.location="add-user.php";';
    echo '</script>';

   }else{
    $sql = mysqli_query($conn,"insert into tblstudent (sfname,smname,slname,saddress,sgender,scontact,sbday,smstatus) values ('$sfname','$smname','$slname','$saddress','$sgender','$scontact','$sbday','$smstatus') ") or die(mysqli_error());

    echo '<script type="text/javascript">';
    echo 'alert("One (1) Student is added to the database");';
    echo 'window.location="add-user.php";';
    echo '</script>';

  }
     
?>