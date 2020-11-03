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
    <main>
        <section>
            <h2>Erstelle eine neue Gruppe!</h2>
            <form action="#" methode="POST">
                <p>
                    <label for="name">Name: </label>
                    <input type="text" id="name">
                </p>
            </form>
        </section>
    </main>
<?php
    include_once "../footer.php";
?>