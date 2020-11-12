<?php
    $dependency = ["titleimg"];
    include_once "../header.php";
    include_once "trainingsAPI.php";
?>
    <main class="main layout-basic">
        <h1 class="h1">Training</h1>
        <section class="section">
            <?php
                $trainings = getTrainingsForThisUser($mysqli);
                for ($i=0; $i < sizeof($trainings); $i++) { 
                    echo "<div class='training'><a href='x.php?id=".$trainings[$i]["idtraining"]."'>".$trainings[$i]["name"]."</div>";
                }
            ?>
        </section>
    </main>
<?php
    include "../footer.php";
?>