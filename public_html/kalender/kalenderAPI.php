<?php
include_once "../users/userAPI.php";

// Example entry
// $entry = [
//     "refId" => $trainings[$i]["idtraining"],
//     "name" => $trainings[$i]["name"],
//     "startDate" => strtotime($trainings[$i]["startDate"]),
//     "endDate" => strtotime($trainings[$i]["endDate"]),
//     "initiator" => getUserNameFromId($mysqli, $trainings[$i]["uploadUser"]),
//     "type" => "training",
//     "groupName" => getGroupById($idgroup, $groups)["name"]
// ];

if(isset($_GET["submitEntry"])){
    $data = json_decode(file_get_contents('php://input'), true);
    processEntry($mysqli, $data);
} else if(isset($_GET["getEntries"])){
    echo json_encode(getEntries($mysqli), true);
} else if(isset($_GET["deleteEntry"])){
    $data = json_decode(file_get_contents('php://input'), true);
    deleteEntry($mysqli, $data);
}

function deleteEntry($mysqli, $entry){
    $id = intval(explode(":", $entry["refId"])[1]);
    switch($entry["type"]){
        case "training": deleteTraining($mysqli, $id);
        case "note": deleteNoteEntry($mysqli, $id);
    }
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

function deleteNoteEntry($mysqli, $idkalenderNote){
    $success = true;
    $stmt = $mysqli->prepare("DELETE FROM kalenderNote_has_group WHERE kalenderNote_idkalenderNote=?;");
    if($stmt->bind_param("i", $idkalenderNote)){
        if($stmt->execute()){
            $stmt->close();
        } else{
            $success = false;
            printf("Error message: %s\n", $mysqli->error);
        }
    }
    $stmt = $mysqli->prepare("DELETE FROM kalenderNote WHERE idkalenderNote=?;");
    if($stmt->bind_param("i", $idkalenderNote)){
        if($stmt->execute()){
            $stmt->close();
        } else{
            $success = false;
            printf("Error message: %s\n", $mysqli->error);
        }
    }
    return $success;
}

function getEntries($mysqli){
    if(!isset($_SESSION["username"])){
        $groups = array(getDefaultGroup($mysqli));
        return getEntriesForGroupId($mysqli, getDefaultGroup($mysqli)["idgroup"], $groups);
    } else{
        $entries = array();
        $groups = getGroupListForUserId($mysqli, $_SESSION["userId"]);
        foreach ($groups as $groupName => $group) {
            push_array_in_array($entries, getEntriesForGroupId($mysqli, $group["idgroup"], $groups));
        }
        return filterRedundantEntries($entries);
    }
}

function getEntriesForGroupId($mysqli, $idgroup, $groups){
    $entries = array();
    push_array_in_array($entries, getTrainingsEntriesForGroupId($mysqli, $idgroup, $groups));
    push_array_in_array($entries, getKalenderNoteEntriesForGroupId($mysqli, $idgroup, $groups));
//  etc..
    return $entries;
}

function push_array_in_array(&$arr1, $arr2){
    for ($i=0; $i < sizeof($arr2); $i++) {
        $arr1[] = $arr2[$i];
    }
}

function getTrainingsEntriesForGroupId($mysqli, $idgroup, $groups){
    $entries = array();
    $trainings = getTrainingsForGroupId($mysqli, $idgroup);
    for ($i=0; $i < sizeof($trainings); $i++) { 
        $entry = [
            "refId" => "idtraining:".$trainings[$i]["idtraining"],
            "name" => $trainings[$i]["name"],
            "startDate" => strtotime($trainings[$i]["startDate"]),
            "endDate" => strtotime($trainings[$i]["endDate"]),
            "initiator" => getUserNameFromId($mysqli, $trainings[$i]["uploadUser"]),
            "type" => "training",
            "groups" => [0 => getGroupById($idgroup, $groups)["name"]],
            "comment" => $trainings[$i]["comment"]
        ];
        $entries[] = $entry;
    }
    return $entries;
}

function getKalenderNoteEntriesForGroupId($mysqli, $idgroup, $groups){
    $entries = array();
    $notes = getKalenderNotesForGroupId($mysqli, $idgroup);
    for ($i=0; $i < sizeof($notes); $i++) { 
        $entry = [
            "refId" => "idkalenderNote:".$notes[$i]["idkalenderNote"],
            "name" => $notes[$i]["name"],
            "startDate" => strtotime($notes[$i]["startDate"]),
            "endDate" => strtotime($notes[$i]["endDate"]),
            "initiator" => getUserNameFromId($mysqli, $notes[$i]["uploadUser"]),
            "type" => "note",
            "groups" => [0 => getGroupById($idgroup, $groups)["name"]],
            "comment" => $notes[$i]["comment"]
        ];
        $entries[] = $entry;
    }
    return $entries;
}

function filterRedundantEntries($entries){
    $refIds = array();
    for ($i=0; $i < sizeof($entries); $i++) {
        if(!in_array($entries[$i]["refId"], $refIds)){
            $refIds[] = $entries[$i]["refId"];
        } else{
            for ($j=0; $j < $i; $j++) { 
                if($entries[$j] != null){
                    if($entries[$j]["refId"] == $entries[$i]["refId"]){
                        $entries[$j]["groups"][] = $entries[$i]["groups"][0];
                    }
                }
            }
            $entries[$i] = null;
        }
    }
    $newEntries = [];
    for ($i=0; $i < sizeof($entries); $i++) {
        if($entries[$i] != null){
            $newEntries[] = $entries[$i];
        }
    }
    return array_values($newEntries);
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

function getKalenderNotesForGroupId($mysqli, $idnote){
    $stmt = $mysqli->prepare("SELECT kalenderNote.* FROM kalenderNote_has_group
        JOIN kalenderNote ON kalenderNote.idkalenderNote=kalenderNote_has_group.kalenderNote_idkalenderNote
        WHERE kalenderNote_has_group.group_idgroup=?;");
    if($stmt->bind_param("i", $idnote)){
        if($stmt->execute()){
            $result = $stmt->get_result();
            $notes = array();
            while($row = $result->fetch_array(MYSQLI_ASSOC)){
                $notes[] = $row;
            }
            $result->close();
            $stmt->close();
            return $notes;
        }
    }
    printf("Error message: %s\n", $mysqli->error);
    $stmt->close();
    return false;
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
    }else if(!isset($data["idTrainingsBlueprint"])){
        return false;
    }else if(!isset($data["groups"])){
        return false;
    }else if(!isset($data["idtrainingFacility"])){
        return false;
    }else if(!isset($data["comment"])){
        return false;
    }
    return true;
}

function processEntry($mysqli, $data){
    // print_r($data);
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
        case "andere": processNotes($mysqli, $data); break;
    }
    // if ($jsDateTS !== false)
    //     echo date('Y-m-d-H:i', $jsDateTS);
    // else
    //     echo "error";
    // .. date format invalid
}

function processTraining($mysqli, $data){
    echo "training";
    $iduser = getUserId($mysqli, $_SESSION["username"]);
    $startDate = date('Y-m-d H:i:s', strtotime($data["startDate"]));
    $endDate = date('Y-m-d H:i:s', strtotime($data["endDate"]));
    $idblueprint = $data["idTrainingsBlueprint"] == "-" ? NULL :  $data["idTrainingsBlueprint"];
    $idtrainingsfacility = NULL;
    $comment = $data["comment"];
    $groupNames = $data["groups"];
    $name = $data["title"];
    if(isUserAdminInAllGroups($mysqli, $iduser, $groupNames)){
        $idtraining = insertTraining($mysqli, $startDate, $endDate, $idtrainingsfacility, $idblueprint, $comment, $iduser, $name);
        $groups = getGroupList($mysqli);
        for ($i=0; $i < sizeof($groupNames); $i++) { 
            insertTrainingGroupRelation($mysqli, $idtraining, getGroupId($groupNames[$i], $groups));
        }
    } else{
        echo "Error: Ungenügende Berechtigungen :(";
    }
}

function processWettkampf($mysqli, $data){

}

function processTrainingslager($mysqli, $data){

}

function processNotes($mysqli, $data){
    echo "Andere";
    $iduser = getUserId($mysqli, $_SESSION["username"]);
    $startDate = date('Y-m-d H:i:s', strtotime($data["startDate"]));
    $endDate = date('Y-m-d H:i:s', strtotime($data["endDate"]));
    $comment = $data["comment"];
    $groupNames = $data["groups"];
    $name = $data["title"];
    if(isUserAdminInAllGroups($mysqli, $iduser, $groupNames)){
        $idkalenderNote = insertNote($mysqli, $startDate, $endDate, $comment, $iduser, $name);
        $groups = getGroupList($mysqli);
        for ($i=0; $i < sizeof($groupNames); $i++) {
            insertNoteGroupRelation($mysqli, $idkalenderNote, getGroupId($groupNames[$i], $groups));
        }
    } else{
        echo "Error: Ungenügende Berechtigungen :(";
    }
}

function insertNote($mysqli, $startDate, $endDate, $comment, $iduser, $name){
    $stmt = $mysqli->prepare("INSERT INTO kalenderNote(startDate, endDate, comment, uploadUser, name) VALUES(?,?,?,?,?);");
    if($stmt->bind_param("sssis", $startDate, $endDate, $comment, $iduser, $name)){
        if($stmt->execute()){
            $idkalenderNote = $mysqli->insert_id;
            $stmt->close();
            return $idkalenderNote;
        }
    }
    printf("Error message: %s\n", $mysqli->error);
    $stmt->close();
    return false;
}

function insertNoteGroupRelation($mysqli, $idkalenderNote, $idgroup){
    $stmt = $mysqli->prepare("INSERT INTO kalenderNote_has_group(kalenderNote_idkalenderNote, group_idgroup) VALUES(?,?);");
    if($stmt->bind_param("ii", $idkalenderNote, $idgroup)){
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