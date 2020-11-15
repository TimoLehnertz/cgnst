<?php
include_once "../includes/permissions.inc.php";

include_once "../includes/dbh.inc.php";
include_once "../includes/utils.inc.php";
include_once "../includes/utils.inc.php";

if(isset($_GET["insertData"])){
    requirePermission("permission_wmdata");
    $data = json_decode(file_get_contents('php://input'), true);
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

function getAvailableWmColumnValues($mysqli, $column){
    $values = array();
    $columns = getTableColumns($mysqli, "wm");
    if(in_array($column, $columns)){
        $stmt = $mysqli->prepare("SELECT $column FROM wm GROUP BY $column ORDER BY $column");
        if($res = $stmt->execute()){
            $result = $stmt->get_result();
            while($row = $result->fetch_assoc()){
                $values[] = $row[$column];
            }
            $result->close();
        }
        $stmt->close();
    }
    return $values;
}

function getTableColumns($mysqli, $tableName){
    $sql = "SHOW COLUMNS FROM $tableName;";
    $res = $mysqli->query($sql);
    $columns = array();
    while($row = $res->fetch_assoc()){
        $columns[] = $row['Field'];
    }
    $res->close();
    return $columns;
}

function getResultFromFilter($mysqli, $filter){
    $values = array();
    push_assoc_array_in_array($values, $filter);
    if(isset($values["search"])){
        unset($values["search"]);
    }
    foreach ($values as $key => $value) {
        if(strlen($value) == 0){
            $value = "%";
        }
    }
    $columns = getTableColumns($mysqli, "wm");
    $sql = "SELECT ";
    appendColumnStringExept($columns, "id", $sql);
    $sql .= " FROM wm WHERE ";
    $comma = " ";
    $insertTypes = "";
    $insertValues = array();
    foreach ($columns as $key => $value) {
        if(isset($filter[$value])){
            if(strlen($filter[$value]) == 0){
                $filter[$value] = "%";
            }
            $sql .= $comma.$value." LIKE ?";
            $comma = " AND ";
            $insertTypes.="s";
            $insertValues[] = &$filter[$value];
        }
    }
    $sql .= ";";
    $resultArray = array();
    if($stmt = $mysqli->prepare($sql)){
        if(call_user_func_array(array($stmt, "bind_param"), array_merge(array($insertTypes), $insertValues))){
            if($stmt->execute()){
                $result = $stmt->get_result();
                while($row = $result->fetch_assoc()){
                    $resultArray[] = $row;
                }
                $result->close();
            }else{
                printf("Error message: %s\n", $mysqli->error);
            }
        }
    } else{
        printf("Error message: %s\n", $mysqli->error);
    }
    return $resultArray;
}

function echoTableFromArray($array){
    if(sizeof($array) == 0){
        return;
    }
    echo "<table class='table'><tr><td>Id</td>";
    foreach ($array[0] as $key => $value) {
        echo "<td>$key</td>";
    }
    echo "</tr>";
    foreach ($array as $i => $value) {
        if($i % 2 == 0){
            echo "<tr class='zebra'><td>$i</td>";
        } else{
            echo "<tr><td>$i</td>";
        }
        foreach ($value as $key => $value) {
            if(is_numeric($value)){
                if($value == 0){
                    echo "<td style='text-align: center;'>-</td>";
                    continue;
                }
            } else if(strlen($value) ==0){
                echo "<td style='text-align: center;'>-</td>";
                continue;
            }
            echo "<td>$value</td>";
        }
        echo "</tr>";
    }
    echo "</table>";
}

function appendColumnStringExept($columns, $exept, &$sql){
    $comma = " ";
    foreach ($columns as $key => $value) {
        if($value != $exept){
            $sql .= $comma.$value;
            $comma = ", ";
        }
    }
}