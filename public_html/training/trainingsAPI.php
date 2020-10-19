<?php
// Trainings API
if(session_status() != PHP_SESSION_ACTIVE){
    session_start();
}

include_once "../includes/dbh.inc.php";
include_once "../users/userAPI.php";

if(isset($_GET["submitTrainingsBlueprint"])){
    $data = json_decode(file_get_contents('php://input'), true);
    insertTRainingsBlueprintByData($conn, $data);
}else if(isset($_GET["getAvailableExerciseGroups"])){
    echo json_encode(getAllAvailableExerciseGroupsJson($conn));
} else if(isset($_GET["getAvailableExercises"])){
    echo json_encode(getAllAvailableExercisesJson($conn));
} else if(isset($_GET["getAvailableTrainingsBlueprints"])){
    echo json_encode(getAllAvailableTrainingsBlueprints($conn));
} else if(isset($_GET["getTrainingsBlueprintJsonById"])){
    echo json_encode(getTrainingsBlueprintObjectById($_GET["getTrainingsBlueprintJsonById"], $conn));
}

function deleteTraining($mysqli, $idtraining){
    $success = true;
    $stmt = $mysqli->prepare("DELETE FROM training_has_group WHERE training_idtraining=?;");
    if($stmt->bind_param("i", $idtraining)){
        if($stmt->execute()){
            $stmt->close();
        } else{
            $success = false;
            printf("Error message: %s\n", $mysqli->error);
        }
    }
    $stmt = $mysqli->prepare("DELETE FROM training_has_trainer WHERE training_idtraining=?;");
    if($stmt->bind_param("i", $idtraining)){
        if($stmt->execute()){
            $stmt->close();
        } else{
            $success = false;
            printf("Error message: %s\n", $mysqli->error);
        }
    }
    $stmt = $mysqli->prepare("DELETE FROM training WHERE idtraining=?;");
    if($stmt->bind_param("i", $idtraining)){
        if($stmt->execute()){
            $stmt->close();
        } else{
            $success = false;
            printf("Error message: %s\n", $mysqli->error);
        }
    }
    return $success;
}

function insertTrainingGroupRelation($mysqli, $idtraining, $idgroup){
    $stmt = $mysqli->prepare("INSERT INTO training_has_group(training_idtraining, group_idgroup) VALUES(?,?);");
    if($stmt->bind_param("ii", $idtraining, $idgroup)){
        if($stmt->execute()){
            $stmt->close();
            return true;
        }
    }
    printf("Error message: %s\n", $mysqli->error);
    $stmt->close();
    return false;
}

function insertTraininTrainerRelation($mysqli, $idtraining, $iduser){
    $stmt = $mysqli->prepare("INSERT INTO training_has_trainer(training_idtraining, user_iduser) VALUES(?,?);");
    if($stmt->bind_param("ii", $idtraining, $iduser)){
        if($stmt->execute()){
            $stmt->close();
            return true;
        }
    }
    printf("Error message: %s\n", $mysqli->error);
    $stmt->close();
    return false;
}

function insertTraining($mysqli, $startDate, $endDate, $idtrainingsfacility, $idblueprint, $comment, $iduser, $name){
    $stmt = $mysqli->prepare("INSERT INTO training(startDate, endDate, comment, trainingsBlueprint_idtrainingsBlueprint1, trainingFacility_idtrainingFacility, uploadUser, name) VALUES(?,?,?,?,?,?,?);");
    if($stmt->bind_param("sssiiis", $startDate, $endDate, $comment, $idblueprint, $idtrainingsfacility, $iduser, $name)){
        if($stmt->execute()){
            $idtraining = $mysqli->insert_id;
            $stmt->close();
            return $idtraining;
        }
    }
    printf("Error message: %s\n", $mysqli->error);
    $stmt->close();
    return false;
}

function getTrainingsForGroupId($mysqli, $idgroup){
    $stmt = $mysqli->prepare("SELECT training.* FROM training_has_group
        JOIN training ON training.idtraining=training_has_group.training_idtraining
        WHERE training_has_group.group_idgroup=?;");
    if($stmt->bind_param("i", $idgroup)){
        if($stmt->execute()){
            $result = $stmt->get_result();
            $trainings = array();
            while($row = $result->fetch_array(MYSQLI_ASSOC)){
                $row["participating"] = getParticipatingUsersForTraining($mysqli, $row["idtraining"]);
                $trainings[] = $row;
            }
            $result->close();
            $stmt->close();
            return $trainings;
        }
    }
    printf("Error message: %s\n", $mysqli->error);
    $stmt->close();
    return false;
}

function getParticipatingUsersForTraining($mysqli, $idtraining){
    $stmt = $mysqli->prepare("SELECT user.username, user.iduser, training_has_athletes.participates FROM training_has_athletes
        JOIN user ON user.iduser=training_has_athletes.user_iduser
        WHERE training_has_athletes.training_idtraining=?;");
    if($stmt->bind_param("i", $idtraining)){
        if($stmt->execute()){
            $result = $stmt->get_result();
            $users = array();
            while($row = $result->fetch_array(MYSQLI_ASSOC)){
                $users[] = $row;
            }
            $result->close();
            $stmt->close();
            return $users;
        }
    }
    printf("Error message: %s\n", $mysqli->error);
    $stmt->close();
    return false;
}

/*
    0 => not participating
    1 => remembering
    2 => participating
*/
function setParticipatingForUserInTraining($mysqli, $iduser, $idtraining, $participate){
    $participatingUsers = getParticipatingUsersForTraining($mysqli, $idtraining);
    $state = 0;
    for ($i=0; $i < sizeof($participatingUsers); $i++) { 
        if($participatingUsers[$i]["iduser"] == $iduser){
            $state = 1;
            if($participatingUsers[$i]["participates"]){
                $state = 2;
            }
            break;
        }
    }
    $state1 = $participate == 2;
    if($state == 0 && $participate > 0){
        echo "inserting";
        $stmt = $mysqli->prepare("INSERT INTO training_has_athletes(training_idtraining, user_iduser, participates) VALUES(?,?,?);");
        if($stmt->bind_param("iii", $idtraining, $iduser, $state1)){
            if(!$stmt->execute()){
                printf("Error message: %s\n", $mysqli->error);
            }
        } else{
            printf("Error message: %s\n", $mysqli->error);
        }
        $stmt->close();
    } 
    if($state > 0 && $participate != $state && $participate > 0){
        echo "updating";
        $stmt = $mysqli->prepare("UPDATE training_has_athletes SET participates=?;");
        if($stmt->bind_param("i", $state1)){
            if(!$stmt->execute()){
                printf("Error message: %s\n", $mysqli->error);
            }
        } else{
            printf("Error message: %s\n", $mysqli->error);
        }
        $stmt->close();
    }
    if($state > 0 && $participate == 0){
        echo "DELETEing";
        $stmt = $mysqli->prepare("DELETE FROM training_has_athletes WHERE user_iduser=? AND training_idtraining=?;");
        if($stmt->bind_param("ii", $iduser, $idtraining)){
            if(!$stmt->execute()){
                printf("Error message: %s\n", $mysqli->error);
            }
        } else{
            printf("Error message: %s\n", $mysqli->error);
        }
        $stmt->close();
    }
}

function getGroupsForTrainingsId($mysqli, $idtraining){
    $stmt = $mysqli->prepare("SELECT `group`.* FROM training_has_group
        JOIN `group` ON `group`.idgroup=training_has_group.group_idgroup
        WHERE training_has_group.training_idtraining=?;");
    if($stmt->bind_param("i", $idtraining)){
        if($stmt->execute()){
            $result = $stmt->get_result();
            $groups = array();
            while($row = $result->fetch_array(MYSQLI_ASSOC)){
                $groups[] = $row;
            }
            $result->close();
            $stmt->close();
            return $groups;
        }
    }
    printf("Error message: %s\n", $mysqli->error);
    $stmt->close();
    return false;
}

function getTrainingsForId($mysqli, $idtraining){
    $stmt = $mysqli->prepare("SELECT * FROM training WHERE idtraining=?;");
    if($stmt->bind_param("i", $idtraining)){
        if($stmt->execute()){
            $result = $stmt->get_result();
            if($row = $result->fetch_array(MYSQLI_ASSOC)){
                $result->close();
                $stmt->close();
                $row["trainer"] = getTrainerToTrainingById($mysqli, $idtraining);
                return $row;
            }
            $result->close();
        }
    }
    printf("Error message: %s\n", $mysqli->error);
    $stmt->close();
    return false;
}

function doesTrainingExistById($mysqli, $idtraining){
    $stmt = $mysqli->prepare("SELECT * FROM training WHERE idtraining=?;");
    if($stmt->bind_param("i", $idtraining)){
        if($stmt->execute()){
            $result = $stmt->get_result();
            $count = $result->num_rows;
            $result->close();
            $stmt->close();
            return $count > 0;
        }
    }
    printf("Error message: %s\n", $mysqli->error);
    $stmt->close();
    return false;
}

function getTrainerToTrainingById($mysqli, $idtraining){
    $stmt = $mysqli->prepare("SELECT user.username, user.iduser FROM training_has_trainer
    JOIN user ON user.iduser=training_has_trainer.user_iduser
    WHERE training_has_trainer.training_idtraining=?;");
    if($stmt->bind_param("i", $idtraining)){
        if($stmt->execute()){
            $result = $stmt->get_result();
            $count = $result->num_rows;
            $trainer = array();
            while($row = $result->fetch_array(MYSQLI_ASSOC)){
                $trainer[] = $row;
            }
            $result->close();
            $stmt->close();
            return $trainer;
        }
    }
    printf("Error message: %s\n", $mysqli->error);
    $stmt->close();
    return false;
}

function isTrainingInDefaultGroup($mysqli, $idtraining){
    $groups = getGroupsForTrainingsId($mysqli, $idtraining);
    for ($i=0; $i < sizeof($groups); $i++) { 
        if($groups[$i]["name"] == getDefaultGroupName()){
            return true;
        }
    }
    return false;
}

function getArrayFromRowProp($rows, $prop){
    $ids = array();
    for ($i=0; $i < sizeof($rows); $i++) { 
        $ids[] = $rows[$i][$prop];
    }
    return $ids;
}

function hasThisUserWritePermissionOnTrainingsId($mysqli, $idtraining){
    if(isset($_SESSION["username"]) && doesTrainingExistById($mysqli, $idtraining)){
        $iduser = $_SESSION["userId"];
        $idgroups = getArrayFromRowProp(getGroupsForTrainingsId($mysqli, $idtraining), "idgroup");
        return isUserAdminInOneOfGroupIds($mysqli, $iduser, $idgroups);
    } else{
        return false;
    }
}

function hasThisUserReadPermissionOnTrainingsId($mysqli, $idtraining){
    if(isset($_SESSION["username"]) && doesTrainingExistById($mysqli, $idtraining)){
        $iduser = $_SESSION["userId"];
        $idgroups = getArrayFromRowProp(getGroupsForTrainingsId($mysqli, $idtraining), "idgroup");
        return isUserMemberInOneOfGroupIds($mysqli, $iduser, $idgroups);
    } else{
        return isTrainingInDefaultGroup($mysqli, $idtraining);
    }
}

function insertTRainingsBlueprintByData($conn, $data){
    if(isset($_SESSION["username"])){
        $trainingsBlueprintId = insertTrainingsBlueprint($data["name"], $_SESSION["userId"], $conn);

        for ($group = 0; $group < sizeof($data["groups"]); $group++) {
            $groupId = insertExerciseGroup($data["groups"][$group], $conn);

            insertTrainingsBlueprint_has_exerciseGroup($trainingsBlueprintId, $groupId, $conn);

            for ($exercise = 0; $exercise < sizeof($data["groups"][$group]["exercises"]); $exercise++) { 
            $exerciseId = insertExercise($data["groups"][$group]["exercises"][$exercise], $conn);
            insertExercise_has_exerciseGroup($groupId, $exerciseId, $conn);
            }
        }
    }
}


function insertExerciseGroup($group, $conn){
    $sql = "INSERT INTO exerciseGroup (name) VALUES (?);";
    $stmt = mysqli_stmt_init($conn);
    if(!mysqli_stmt_prepare($stmt, $sql)){
        echo "SQL error";
    } else{
        mysqli_stmt_bind_param($stmt, "s", $group["exerciseGroupName"]);
        mysqli_stmt_execute($stmt);
        return mysqli_insert_id($conn);
    }
}

function insertExercise_has_exerciseGroup($groupId, $exerciseId, $conn){
    $sql = "INSERT INTO exercise_has_exerciseGroup (exercise_idexercise, exerciseGroup_idexerciseGroup) VALUES (?,?);";
    $stmt = mysqli_stmt_init($conn);
    if(!mysqli_stmt_prepare($stmt, $sql)){
        echo "SQL error";
    } else{
        mysqli_stmt_bind_param($stmt, "ii", $exerciseId, $groupId);
        mysqli_stmt_execute($stmt);
    }
}

function insertExercise($exercise, $conn){
    $sql = "INSERT INTO exercise (time, pauseAfter, name, description, intensity, aim) VALUES (?,?,?,?,?,?);";
    $stmt = mysqli_stmt_init($conn);
    if(!mysqli_stmt_prepare($stmt, $sql)){
        echo "SQL error";
    } else{
        mysqli_stmt_bind_param($stmt, "iissis", $exercise["time"], $exercise["pauseAfter"], $exercise["name"], $exercise["description"], $exercise["intensity"], $exercise["aim"]);
        mysqli_stmt_execute($stmt);
        return mysqli_insert_id($conn);
    }
}

function insertTrainingsBlueprint($name, $creator, $conn){
    $sql = "INSERT INTO trainingsBlueprint (name, creator) VALUES (?,?);";
    $stmt = mysqli_stmt_init($conn);
    if(!mysqli_stmt_prepare($stmt, $sql)){
        echo "SQL error";
    } else{
        mysqli_stmt_bind_param($stmt, "si", $name, $creator);
        mysqli_stmt_execute($stmt);
        return mysqli_insert_id($conn);
    }
}

function insertTrainingsBlueprint_has_exerciseGroup($trainingsBlueprintId, $exerciseGroupId, $conn){
    $sql = "INSERT INTO trainingsBlueprint_has_exerciseGroup (trainingsBlueprint_idtrainingsBlueprint, exerciseGroup_idexerciseGroup) VALUES (?,?);";
    $stmt = mysqli_stmt_init($conn);
    if(!mysqli_stmt_prepare($stmt, $sql)){
        echo "SQL error";
    } else{
        mysqli_stmt_bind_param($stmt, "ii", $trainingsBlueprintId, $exerciseGroupId);
        mysqli_stmt_execute($stmt);
        echo "Affected rows: ".mysqli_affected_rows($conn);
        echo "Affected rows: ".mysqli_error($conn);
    }
}

function getAllAvailableExerciseGroupsJson($conn){
    $myArray = array();
    if ($result = $conn->query("SELECT name, idexerciseGroup FROM exerciseGroup;")) {
        while($row = $result->fetch_array(MYSQLI_ASSOC)) {
            $myArray[] = $row;
        }
        return $myArray;
    }
    return "sql Error";
}

function getAllAvailableExercisesJson($conn){
    $myArray = array();
    if ($result = $conn->query("SELECT name, idexercise, pauseAfter, description, intensity, aim FROM exercise;")) {
        while($row = $result->fetch_array(MYSQLI_ASSOC)) {
            $myArray[] = $row;
        }
        return $myArray;
    }
    return "sql Error";
}

function getAllAvailableTrainingsBlueprints($conn){
    $myArray = array();
    if ($result = $conn->query("SELECT * FROM trainingsBlueprint;")) {
        while($row = $result->fetch_array(MYSQLI_ASSOC)) {
            $myArray[] = $row;
        }
        return $myArray;
    }
    return "sql Error";
}

function getTrainingsBlueprintObjectById($id, $conn){
    $blueprint = json_decode("{'name' : '', 'groups' : []}", true);//workarround for stupidness
    $sql = "SELECT name, idtrainingsBlueprint FROM trainingsBlueprint WHERE idtrainingsBlueprint = ?;";
    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        echo "SQL Error";
    } else{
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        if($row = $stmt->get_result()->fetch_assoc()){
            $blueprint["name"] = $row["name"];
            $blueprint["groups"] = getGroupsByBlueprintId($id, $conn);
        }else{
            echo "bad request :(";
        }
    }
    return $blueprint;
}

function getGroupsByBlueprintId($id, $conn){
    $groups = array();

    $sql = "SELECT exerciseGroup.idexerciseGroup, exerciseGroup.name
    FROM trainingsBlueprint_has_exerciseGroup as tb_e
    LEFT JOIN trainingsBlueprint AS blueprint ON blueprint.idtrainingsBlueprint = tb_e.trainingsBlueprint_idtrainingsBlueprint
    LEFT JOIN exerciseGroup ON exerciseGroup.idexerciseGroup = tb_e.exerciseGroup_idexerciseGroup
    WHERE blueprint.idtrainingsBlueprint = ?;";
    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        echo "SQL Error";
    } else{
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        $result = $stmt->get_result();
        while($row = $result->fetch_assoc()){
            $group = json_decode("{'exerciseGroupName':'','exercises': []}", true);
            $group["exerciseGroupName"] = $row["name"];
            $group["exercises"] = getExercisesByGroupId($row["idexerciseGroup"], $conn);
            #print_r($group);
            $groups[] = $group;
        }
    }
    return $groups;
}

function getExercisesByGroupId($id, $conn){
    $exercises = array();

    $sql = "SELECT exercise.*, exerciseGroup.name as groupName
    FROM exercise_has_exerciseGroup as e_eg
    LEFT JOIN exerciseGroup ON exerciseGroup.idexerciseGroup = e_eg.exerciseGroup_idexerciseGroup
    LEFT JOIN exercise ON exercise.idexercise = e_eg.exercise_idexercise
    WHERE exerciseGroup.idexerciseGroup = ?;";
    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        echo "SQL Error";
    } else{
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        $result = $stmt->get_result();
        while($row = $result->fetch_assoc()){
            $exercise = json_decode("{'name':'','time':'',pauseAfter:'','description':'','intensity':'','aim':'','groupName':''}", true);
            print_r($exercise);
            $exercise["name"] = $row["name"];
            $exercise["time"] = $row["time"];
            $exercise["pauseAfter"] = $row["pauseAfter"];
            $exercise["description"] = $row["description"];
            $exercise["intensity"] = $row["intensity"];
            $exercise["aim"] = $row["aim"];
            $exercise["groupName"] = $row["groupName"];
            $exercises[] = $exercise;
        }
    }
    return $exercises;
}

function getTrainingsBLueprintById($mysqli, $idblueprint){
    $stmt = $mysqli->prepare("SELECT * FROM trainingsBlueprint WHERE idtrainingsBlueprint=?;");
    if($stmt->bind_param("i", $idblueprint)){
        if($stmt->execute()){
            $result = $stmt->get_result();
            if($row = $result->fetch_array(MYSQLI_ASSOC)){
                $row["exerciseGroups"] = getExerciseGroupsByBlueprintId($mysqli, $idblueprint);
                $result->close();
                $stmt->close();
                return $row;
            }
        }
    } else{
        printf("Error message: %s\n", $mysqli->error);
    }
}

function getExerciseGroupsByBlueprintId($mysqli, $idblueprint){
    $stmt = $mysqli->prepare("SELECT exerciseGroup.* FROM trainingsBlueprint_has_exerciseGroup
    JOIN exerciseGroup ON exerciseGroup.idexerciseGroup = trainingsBlueprint_has_exerciseGroup.exerciseGroup_idexerciseGroup
    WHERE trainingsBlueprint_idtrainingsBlueprint=?;");
    if($stmt->bind_param("i", $idblueprint)){
        if($stmt->execute()){
            $groups = array();
            $result = $stmt->get_result();
            while($row = $result->fetch_array(MYSQLI_ASSOC)){
                $row["exercises"] = getExerciseByGroupId($mysqli, $row["idexerciseGroup"]);
                $groups[] = $row; 
            }
            $result->close();
            $stmt->close();
            return $groups;
        }
    }
    printf("Error message: %s\n", $mysqli->error);
    $stmt->close();
    return false;
}

function getExerciseByGroupId($mysqli, $idgroup){
    $stmt = $mysqli->prepare("SELECT exercise.* FROM exercise_has_exerciseGroup
    JOIN exercise ON exercise.idexercise = exercise_has_exerciseGroup.exercise_idexercise
    WHERE exerciseGroup_idexerciseGroup=?;");
    if($stmt->bind_param("i", $idgroup)){
        if($stmt->execute()){
            $exercises = array();
            $result = $stmt->get_result();
            while($row = $result->fetch_array(MYSQLI_ASSOC)){
                $exercises[] = $row; 
            }
            $result->close();
            $stmt->close();
            return $exercises;
        }
    }
    printf("Error message: %s\n", $mysqli->error);
    $stmt->close();
    return false;
}

function doesBlueprintExistForTRainingsId($mysqli, $idtraining){
    $training = getTrainingsForId($mysqli, $idtraining);
    return isset($training["trainingsBlueprint_idtrainingsBlueprint1"]);
}

function getTrainingsBlueprintHtml($mysqli, $idtraining){
    $training = getTrainingsForId($mysqli, $idtraining);
    if(isset($training["trainingsBlueprint_idtrainingsBlueprint1"])){
        $blueprint = getTrainingsBLueprintById($mysqli, intval($training["trainingsBlueprint_idtrainingsBlueprint1"]));
        // print_r($blueprint);
        for ($i=0; $i < sizeof($blueprint["exerciseGroups"]); $i++) { 
            echo "<div><span class='header'>".($i + 1).": ".$blueprint["exerciseGroups"][$i]["name"]."</span>";
            for ($l=0; $l < sizeof($blueprint["exerciseGroups"][$i]["exercises"]); $l++) { 
                echo  "<div class='exercise'><span class='header'>".($l + 1).": ".$blueprint["exerciseGroups"][$i]["exercises"][$l]["name"]."</span>";
                echo "<p>".$blueprint["exerciseGroups"][$i]["exercises"][$l]["description"]."</p>";
                echo "<p><div class='name'>Dauer:</div> ".$blueprint["exerciseGroups"][$i]["exercises"][$l]["time"]."</p>";
                echo "<p><div class='name'>Anschließende Pause:</div> ".$blueprint["exerciseGroups"][$i]["exercises"][$l]["pauseAfter"]."</p>";
                echo "<p><div class='name'>Intensität:</div> Pause: ".$blueprint["exerciseGroups"][$i]["exercises"][$l]["intensity"]."</p>";
                echo "<p><div class='name'>Ziel:</div> Pause: ".$blueprint["exerciseGroups"][$i]["exercises"][$l]["aim"]."</p>";
                echo "</div>";
            }
            echo "</div>";
        }
    } else{
        return "<p>Es liegt noch kein Trainingsplan Vor</p>";
    }
}