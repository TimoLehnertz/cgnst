<?php
    session_start();
    echo "Userid: ".$_SESSION["userId"]."  ";
    #SELECT * from exercise_has_exerciseGroup
    #join exerciseGroup ON exerciseGroup.idexerciseGroup = exercise_has_exerciseGroup.exerciseGroup_idexerciseGroup
    #join exercise ON exercise.idexercise = exercise_has_exerciseGroup.exercise_idexercise;
    include "../includes/dbh.inc.php";
    $data = json_decode(file_get_contents('php://input'), true);
    #print_r($data);
    
    $trainingsBlueprintId = insertTrainingsBlueprint($data["name"], $_SESSION["userId"], $conn);
    echo $data["name"];

    for ($group = 0; $group < sizeof($data["groups"]); $group++) {
        $groupId = insertGroup($data["groups"][$group], $conn);

        insertTrainingsBlueprint_has_exerciseGroup($trainingsBlueprintId, $groupId, $conn);

        for ($exercise = 0; $exercise < sizeof($data["groups"][$group]["exercises"]); $exercise++) { 
           $exerciseId = insertExercise($data["groups"][$group]["exercises"][$exercise], $conn);
           insertExercise_has_exerciseGroup($groupId, $exerciseId, $conn);
        }
    }



    function insertGroup($group, $conn){
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
?>