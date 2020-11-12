<?php
    $dependency = ["titleimg"];
    include_once "header.php";
    include_once "users/userAPI.php";
?>
    <div class="layout basic">
        <main class="main">
            <section class="section">
                <?php
                    if(isset($_SESSION["error"])){
                        echo '<div style="color: coral; padding: 20px;">'.$_SESSION["error"].'</div>';
                        unset($_SESSION["error"]);
                    }

                    if (isset($_SESSION["username"])) {
                        echo "<p>Welcome ".$_SESSION["username"]."! You are logged in!</p>";
                        print_r($_SESSION["permissions"]);
                    } else{
                        echo "<p>You are logged out!</p>";
                    }
                ?>
            </section>
        </main>
        <aside class="aside">
                <h2>Aside</h2>
        </aside>
    </div>
<?php
    include "footer.php";
?>