<?php
    if(session_status() != PHP_SESSION_ACTIVE){
        session_start();
    }
    if (!isset($_SESSION["username"])) {
        header("location: /index.php");
        exit();
    }
    $username = $_SESSION["username"];
    $dependency = ["titleimg", "lists", "profile"];
    include_once "../header.php"
?>
    <div class="layout simple">
        <main class="main">
            <section class="section">
                <h2 class="headline">Erstelle eine neue Gruppe!</h2>
                <div class="content">
                    <form action="#" methode="POST">
                        <p>
                            <label for="name">Gruppenname: </label>
                            <input type="text" id="name">
                        </p>
                    </form>
                </div>
            </section>
        </main>
    </div>
<?php
    include_once "../footer.php";
?>