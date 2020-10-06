<?php
    #SELECT * from exercise_has_exerciseGroup
    #join exerciseGroup ON exerciseGroup.idexerciseGroup = exercise_has_exerciseGroup.exerciseGroup_idexerciseGroup
    #join exercise ON exercise.idexercise = exercise_has_exerciseGroup.exercise_idexercise;
    include "../includes/dbh.inc.php";
    $data = json_decode(file_get_contents('php://input'), true);
    #print_r($data);
    
    for ($group = 0; $group < sizeof($data["groups"]); $group++) {
        $groupId = insertGroup($data["groups"][$group], $conn);
        for ($exercise = 0; $exercise < sizeof($data["groups"][$group]["exercises"]); $exercise++) { 
           $exerciseId = insertExercise($data["groups"][$group]["exercises"][$exercise], $conn);
           insertRelation($groupId, $exerciseId, $conn);
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

    function insertRelation($groupId, $exerciseId, $conn){
        $sql = "INSERT INTO exercise_has_exerciseGroup (exercise_idexercise, exerciseGroup_idexerciseGroup) VALUES (?,?);";
        $stmt = mysqli_stmt_init($conn);
        if(!mysqli_stmt_prepare($stmt, $sql)){
            echo "SQL error";
        } else{
            echo "inserting relation: exerciseId: $exerciseId, groupId: $groupId\n";
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
            #echo "inserting exercise:\n";
            #print_r($exercise);
            mysqli_stmt_bind_param($stmt, "iissis", $exercise["time"], $exercise["pauseAfter"], $exercise["name"], $exercise["description"], $exercise["intensity"], $exercise["aim"]);
            mysqli_stmt_execute($stmt);
            echo "Affected Rows: ".mysqli_affected_rows($conn)."\n";
            echo "Error: ".mysqli_error($conn)."\n";
            return mysqli_insert_id($conn);
        }
    }
?>