<?php
    $dependency = ["titleimg"];
    include_once "../header.php";
    include_once "trainingsAPI.php";
?>
    <main>
        <h1>Training</h1>
        <section style="padding: 50px;">
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