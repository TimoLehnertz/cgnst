<?php

include "../includes/dbh.inc.php";

if(isset($_GET["getAvailableExerciseGroups"])){
    echo json_encode(getAllAvailableExerciseGroupsJson($conn));
} else if(isset($_GET["getAvailableExercises"])){
    echo json_encode(getAllAvailableExercisesJson($conn));
} else if(isset($_GET["getAvailableTrainingsBlueprints"])){
    echo json_encode(getAllAvailableTrainingsBlueprints($conn));
} else if(isset($_GET["getTrainingsBlueprintJsonById"])){
    echo json_encode(getTrainingsBlueprintObjectById($_GET["getTrainingsBlueprintJsonById"], $conn));
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
    if ($result = $conn->query("SELECT name, idtrainingsBlueprint, creator FROM trainingsBlueprint;")) {
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
            #print_r($exercises);
            $exercises[] = $exercise;
        }
    }
    return $exercises;
}
?>