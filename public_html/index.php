<?php
    $dependency = ["titleimg"];
    include_once "header.php";
    include_once "users/userAPI.php";
?>
    <div class="layout basic">
        <main class="main">
            <section class="section">
                <h2 class="headline">Section</h2>
                <div class="content">
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
                </div>
            </section>
            <section class="section">
                <h2 class="headline">Section2</h2>
                <div class="content">
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
                </div>
            </section>
        </main>
        <aside class="aside">
            <div class="content">
                <h2>Aside</h2>
            </div>
        </aside>
    </div>
<?php
    include "footer.php";
?>