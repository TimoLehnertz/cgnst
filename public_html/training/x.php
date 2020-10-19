<?php
    include_once "../includes/dbh.inc.php";

    $training;
    $trainers = array();
    $idtraining;
    if(!isset($_GET["id"])){
        header("location: /training/");
        exit();
    } else {
        include_once "trainingsAPI.php";
        $idtraining = intval($_GET["id"]);
        if(!doesTrainingExistById($mysqli, $idtraining)){
            header("location: /training/");
            exit();
        } else{
            if(!hasThisUserWritePermissionOnTrainingsId($mysqli, $idtraining)){
                header("location: /training/");
                exit();
            }
            $training = getTrainingsForId($mysqli, $idtraining);
            for ($i=0; $i < sizeof($training["trainer"]); $i++) { 
                $trainers[] = $training["trainer"][$i]["username"];
            }
            $dependency = ["training", "titleimg"];
            include_once "../header.php";
        }
    }
?>
    <main>
        <h1>Training</h1>
        <section>
            <aside>
                <?php if(hasThisUserWritePermissionOnTrainingsId($mysqli, $idtraining)){ //Write permissions?>
                    <p>Du kannst diese Daten bearbeiten</p>
                <?php } else if(hasThisUserReadPermissionOnTrainingsId($mysqli, $idtraining)){ //Read permissions?>
                    
                <?php }?>
            </aside>
            <div class="training-content">
                <h2 class="title"><?=$training["name"]?></h2>
                <p class="date"><i class="far fa-calendar-alt"></i><?=date("D, d. M, Y H:i",strtotime($training["startDate"]))?></p>
                <?php
                    if(sizeof($trainers) > 0){?>
                <p class="trainer"><i class="fas fa-id-card-alt"></i>Trainer: <?php echo implode(", " , $trainers)?></p>
                <?php }?>
                <div class="comment">
                    <div class="comment__header"><i class="far fa-comment"></i>Comment</div>
                    <p class="komment__body"><?=$training["comment"]?></p>
                </div>
                <div class="participants">
                    <div class="participants__title"><i class="far fa-hand-peace"></i>Nehmen Teil</div>
                    <ul class="participants__list">
                        <?php
                            $participants = getParticipatingUsersForTraining($mysqli, $idtraining);
                            for ($i=0; $i < sizeof($participants); $i++) { 
                                if($participants[$i]["participates"]){
                                    echo '<li>'.$participants[$i]["username"].'</li>';
                                }
                            }
                        ?>
                    </ul>
                    <div class="participants__title"><i class="far fa-bookmark"></i>Haben sich gemerkt</div>
                    <ul class="participants__list">
                        <?php
                            $participants = getParticipatingUsersForTraining($mysqli, $idtraining);
                            for ($i=0; $i < sizeof($participants); $i++) {
                                if(!$participants[$i]["participates"]){
                                    echo '<li>'.$participants[$i]["username"].'</li>';
                                }
                            }
                        ?>
                    </ul>
                </div>
                <p><i class="fas fa-list"></i>Trainingsplan:</p>
                <div class="trainings-blueprint">
                    <?php echo getTrainingsBlueprintHtml($mysqli, $idtraining)?>
                </div>
            </div>
        </section>
    </main>
<?php
    include "../footer.php";
?>