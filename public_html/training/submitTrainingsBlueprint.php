<?php
    include_once "../includes/dbh.inc.php";
    $data = json_decode(file_get_contents('php://input'), true);
    print_r($data);
    
    for ($group=0; $group < sizeof($data["groups"]); $group++) { 
        echo "group: ".$data["groups"][$group]["exerciseGroupName"]."\n";
        
        for ($exercise=0; $exercise < sizeof($data["groups"][$group]["exercises"]); $exercise++) { 
            echo "  ->  ".$data["groups"][$group]["exercises"][$exercise]["name"];
            echo "ID: ".insertExercise($data["groups"][$group]["exercises"][$exercise])."\n";
        }
    }

    function insertExercise($exercise){
        /*
        $sql = "INSERT INTO exercise (time, pauseAfter, name, description, intensity, aim) VALUES (?,?,?,?,?,?);";
        $stmt = mysqli_stmt_init($conn);
        if(!mysqli_stmt_prepare($stmt, $sql)){
            echo "error";
            return false;
        } else{
            mysqli_stmt_bind_param($stmt, "iissis", $exercise["time"], $exercise["pauseAfter"], $exercise["name"], $exercise["description"], $exercise["intensity"], $exercise["aim"]);
            mysqli_stmt_execute($stmt);
            return mysqli_insert_id();
        }*/
        $sql = "INSERT INTO exercise (time, pauseAfter, name, description, intensity, aim) VALUES (?,?,?,?,?,?);";
        $stmt = mysqli_stmt_init($conn);
        if(!mysqli_stmt_prepare($stmt, $sql)){
            echo "error";
            return false;
        } else{
            mysqli_stmt_bind_param($stmt, "iissis", $exercise["time"], $exercise["pauseAfter"], $exercise["name"], $exercise["description"], $exercise["intensity"], $exercise["aim"]);
            mysqli_stmt_execute($stmt);
        }
    }
?>