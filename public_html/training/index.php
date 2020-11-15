<?php
    $dependency = ["titleimg"];
    include_once "../header.php";
    include_once "trainingsAPI.php";
?>
    <div class="layout simple">
        <main class="main">
            <section class="section">
                <h1 class="headline">Training</h1>
                <div class="content">
                    <?php
                        $trainings = getTrainingsForThisUser($mysqli);
                        for ($i=0; $i < sizeof($trainings); $i++) { 
                            echo "<div class='training'><a href='x.php?id=".$trainings[$i]["idtraining"]."'>".$trainings[$i]["name"]."</div>";
                        }
                    ?>
                </div>
            </section>
        </main>
    </div>
<?php
    include "../footer.php";
?>