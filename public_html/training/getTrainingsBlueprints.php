<?php
if(isset($_GET["getAvailableExerciseGroups"])){
    echo getAvailableExerciseGroups();
}

function getAvailableExerciseGroups(){
    $sql = "SELECT name, idtrainingsBlueprint, creator FROM exerciseGroup";
    $stmt = mysqli_stmt_init($conn);
    if(!mysqli_stmt_prepare($stmt, $sql)){
        echo "SQL error";
    } else{
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
    }
}
?>