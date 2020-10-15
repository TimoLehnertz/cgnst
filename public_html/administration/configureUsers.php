<?php
    $dependency = ["user-list"];
    include "../header.php";
    if(!isset($_SESSION["permission_administration"])){
        header("location: /index.php");
        exit();
    }
?>
    <main>
        <h1>Administration - Configure Users</h1>
        <hr>
        <?php include "../users/userList.html"?>
    </main>
<?php
    #include "../footer.php";
?>