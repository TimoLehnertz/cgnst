<?php
include_once "../users/userAPI.php";

// {
//     name: "Geisingen Kader3",
//     startDate: int,
//     startTime: int,
//     initiator: "userID",//ToDO
//     type: "training",
//     color: "pink"
// }

if(isset($_GET["submitEntry"])){
    $data = json_decode(file_get_contents('php://input'), true);
    processEntry($mysqli, $data);
}

function isDataValid($data){
    if(!isset($data["entryType"])){
        return false;
    } else if(!isset($data["title"])){
        return false;
    }else if(!isset($data["startDate"])){
        return false;
    }else if(!isset($data["endDate"])){
        return false;
    }else if(!isset($data["trainingsBlueprint"])){
        return false;
    }else if(!isset($data["groups"])){
        return false;
    }
    return true;
}

function processEntry($mysqli, $data){
    print_r($data);
    if(!isDataValid($data)){
        echo "Error: Wrong format :(";
        return;
    }
    $startDate = strtotime($data["startDate"]);
    $endDate = strtotime($data["endDate"]);
    switch($data["entryType"]){
        case "training": processTraining($mysqli, $data); break;
        case "wettkampf": processWettkampf($mysqli, $data); break;
        case "trainingslager": processTrainingslager($mysqli, $data); break;
        case "andere": processAndere($mysqli, $data); break;
    }
    // if ($jsDateTS !== false)
    //     echo date('Y-m-d-H:i', $jsDateTS);
    // else
    //     echo "error";
    // .. date format invalid
}

function processTraining($mysqli, $data){
    echo "training";
    // $idgroup = getGroupId($groupNameIn, $groups)
    $iduser = getUserId($mysqli, $_SESSION["username"]);
    // if(!isUserAdminInGroup($mysqli, , $idgroup))
}

function processWettkampf($mysqli, $data){

}

function processTrainingslager($mysqli, $data){

}

function processAndere($mysqli, $data){

}

function insertTraining($mysqli, $groups, $startDate, $endDate, $idVorlage){

}
