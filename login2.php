<?php
    session_start();
    include("connect.php");
    $sql = mysqli_query($conn,"select * from tbluser where username='".$_POST['username']."' && password='".$_POST['password']."'") or die(mysqli_error());
?>
<!DOCTYPE html>
<html>
<head>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert2/11.10.5/sweetalert2.all.min.js"></script>
</head>
<body>
<?php
    //If correct ang username ug passwdord ug naay account mao ni mahitabo
    if($result1=mysqli_fetch_array($sql)){
        $name = $result1["name"];
        $_SESSION["name"] = $name;
        $_SESSION["username"] = $result1["username"];
        $_SESSION["admin"] = true;
        ?>
        <script type="text/javascript">
            Swal.fire({
                icon: "success",
                title: "Welcome!",
                text: "Records Matched",
                confirmButtonColor: "#6b1522",
                timer: 1500,
                showConfirmButton: false
            }).then(() => {
                window.location = "admin/index.php";
            });
        </script>
        <?php
    //Kong gapataka raka mao ni mahitabo
    }else{
        ?>
        <script type="text/javascript">
            Swal.fire({
                icon: "error",
                title: "Login Failed",
                text: "Invalid login details",
                confirmButtonColor: "#6b1522"
            }).then(() => {
                window.location = "login.php";
            });
        </script>
        <?php
    }
?>
</body>
</html>