<?php
include_once "../includes/dbh.inc.php";
include_once "../includes/utils.inc.php";

if(session_status() != PHP_SESSION_ACTIVE){
    session_start();
}

if(isset($_GET["insertData"])){
    $data = json_decode(file_get_contents('php://input'), true);
    // var_dump($data, $mysqli);
    insertWmData($data, $mysqli);
    
}

function insertWmData($data, $mysqli){
    if(sizeof($data) == 0){
        return false;
    }
    $types = "";
    $placeholders = "";
    $array = array();
    for ($i=0; $i < sizeof($data); $i++) {
        $year = intval($data[$i]["year"]);
        $location = $data[$i]["location"];
        $category = $data[$i]["category"];
        $sex = $data[$i]["sex"];
        $discipline = $data[$i]["discipline"];
        $place = intval($data[$i]["place"]);
        $seconds = doubleval($data[$i]["seconds"]);
        $surename = $data[$i]["surename"];
        $country = $data[$i]["country"];
        $name = $data[$i]["name"];
        $types.= "issssidsss";
        if(strlen($placeholders) > 0){
            $placeholders.= ",(?,?,?,?,?,?,?,?,?,?)";
        } else{
            $placeholders.= "(?,?,?,?,?,?,?,?,?,?)";
        }
        push_array_in_array($array, array($year, $location, $category, $sex, $discipline, $place, $seconds, $surename, $country, $name));
    }
    if($stmt = $mysqli->prepare("INSERT INTO wm (year, location, category, sex, discipline, place, seconds, surename, country, name) VALUES $placeholders")){
        $stmt->bind_param($types, ...$array);
        if(!$stmt->execute()){
            echo "error: ".$mysqli->error;
        }
    }
    echo "succsess";
    return true;
}