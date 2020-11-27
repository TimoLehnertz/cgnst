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
if(isset($_GET["insert500mData"])){
    requirePermission("permission_wmdata");
    $data = json_decode(file_get_contents('php://input'), true);
    insert500mData($data, $mysqli);
}
if(isset($_GET["get500mData"])){
    echo json_encode(get500mData($mysqli));
}

function echoSelectorFor($mysqli, $column, $label){
    echoSelectorForName($mysqli, $column, $label, $column);
}

function getWinnerTimes($mysqli){

}

function getBestTimes($mysqli){
    $data = array();
    if($stmt = $mysqli->prepare("CALL sp_best_times();")){
        if($stmt->execute()){
            if($result = $stmt->get_result()){
                while($row = $result->fetch_assoc()){
                    $data[] = $row;
                }
                $result->close();
            } else{
                printf("Error message: %s\n", $mysqli->error);
            }
            $stmt->close();
        } else{
            printf("Error message: %s\n", $mysqli->error);
        }
    } else{
        printf("Error message: %s\n", $mysqli->error);
    }
    return $data;
}

function getMedalYearCountries($mysqli, $disciplines){
    $rlike = "^(";
    $delimiter = "";
    foreach ($disciplines as $i => $discipline) {
        if($discipline == "500"){
            $rlike .= $delimiter . $discipline;
        } else{
            $rlike .= $delimiter . $discipline . "$";
        }
        $delimiter = "|";
    }
    $rlike .= ")";
    $data = array();
    if($stmt = $mysqli->prepare("CALL sp_medal_years_country('$rlike');")){
        if($stmt->execute()){
            if($result = $stmt->get_result()){
                while($row = $result->fetch_assoc()){
                    $country = $row["country"];
                    $scores = array();
                    $startYear = 2007;
                    $endYear = 2019;
                    for ($year=$startYear; $year <= $endYear; $year++) { 
                        $scores[] = $row[$year.""];
                    }
                    $data[] = ["country" => $country, "startYear" => $startYear, "scores" => $scores];
                }
                $result->close();
        } else{
            printf("Error message: %s\n", $mysqli->error);
        }
        } else{
            printf("Error message: %s\n", $mysqli->error);
        }
        $stmt->close();
    } else{
        printf("Error message: %s\n", $mysqli->error);
    }
    return $data;
}

function echoSelectorForName($mysqli, $column, $label, $name){
    $values = getAvailableWmColumnValues($mysqli, $column);
    $ranId = random_int(0, 100000);
    $selected = isset($_GET[$column]);
    echo "<p><label style='width: 100px' for='$ranId'>$label:</label></p><p>";
    echo "<select name='$name' id='$ranId'>";
    if(!$selected){
        echo "<option selected value=''>Auswählen</option>";
    } else{
        echo "<option value=''>Auswählen</option>";
    }
    foreach ($values as $i => $value) {
        if(is_numeric($value)){
            if($value == 0){
                continue;
            }
        } else if(strlen($value) == 0){
            continue;
        }
        if($selected){
            if($_GET[$column] == $value){
                echo "<option value='$value' selected>$value</option>";
                continue;
            }
        }
        echo "<option value='$value'>$value</option>";
    }
    echo "</select></p>";
}

function get500mData($mysqli){
    $data = array();
    $res = $mysqli->query("SELECT * FROM `500m`;");
    if($res){
        while($row = $res->fetch_assoc()){
            $data[] = $row;
        }
    } else{
        printf("Error message: %s\n", $mysqli->error);
    }
    return $data;
}

function toNum($data) {
    $val = ord(strtoupper($data)) - ord('A') + 1;
    if($val < 0){
        return -1;
    } else{
        return $val;
    }
}

function insert500mData($data, $mysqli){
    var_dump($data);
    if(sizeof($data) == 0){
        return false;
    }
    $types = "";
    $placeholders = "";
    $array = array();
    for ($i=0; $i < sizeof($data); $i++) {
        $year = intval($data[$i]["year"]);
        $competition = $data[$i]["competition"];
        $category = $data[$i]["category"];
        $sex = $data[$i]["sex"];
        $link = $data[$i]["link"];

        $afterStart1 = toNum($data[$i]["afterStart1"]);
        $afterStart2 = toNum($data[$i]["afterStart2"]);
        $afterStart3 = toNum($data[$i]["afterStart3"]);
        $afterStart4 = toNum($data[$i]["afterStart4"]);

        $beforeFinish1 = toNum($data[$i]["beforeFinish1"]);
        $beforeFinish2 = toNum($data[$i]["beforeFinish2"]);
        $beforeFinish3 = toNum($data[$i]["beforeFinish3"]);
        $beforeFinish4 = toNum($data[$i]["beforeFinish4"]);

        $finish1 = toNum($data[$i]["finish1"]);
        $finish2 = toNum($data[$i]["finish2"]);
        $finish3 = toNum($data[$i]["finish3"]);
        $finish4 = toNum($data[$i]["finish4"]);

        $types.= "issssiiiiiiiiiiii";
 
        if(strlen($placeholders) > 0){
            $placeholders.= ",(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
        } else{
            $placeholders.= "(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
        }
        push_array_in_array($array, array($year, $competition, $category, $link, $sex, $afterStart1, $afterStart2, $afterStart3, $afterStart4, $beforeFinish1, $beforeFinish2, $beforeFinish3, $beforeFinish4, $finish1, $finish2, $finish3, $finish4));
    }
    if($stmt = $mysqli->prepare("INSERT INTO `500m` ( `year`,  `competition`,  category, link, sex,  afterStart1,  afterStart2,  afterStart3,  afterStart4,  beforeFinish1,  beforeFinish2,  beforeFinish3,  beforeFinish4,  finish1,  finish2,  finish3,  finish4) VALUES  $placeholders;")){
        $stmt->bind_param($types, ...$array);
        if(!$stmt->execute()){
            echo "error: ".$mysqli->error;
        }
    } else{
        printf("Error message: %s\n", $mysqli->error);
    }
    echo "succsess";
    return true;
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

function query_medals_compare($mysqli, $filter){
    $rlike = "^(";
    $delimiter = "";
    if(!isset($filter["init"])){
        header("location: /wm/evaluation.php");
        exit();
    } else if(strlen($filter["init"]) == 0){
        header("location: /wm/evaluation.php");
        exit();
    }
    if(isset($filter["vgl1"])){
        if(strlen($filter["vgl1"]) > 0){
            $rlike .= $delimiter . $filter["vgl1"] . "$";
            $delimiter = "|";
        }
    }
    if(isset($filter["vgl2"])){
        if(strlen($filter["vgl2"]) > 0){
            $rlike .= $delimiter . $filter["vgl2"] . "$";
            $delimiter = "|";
        }
    }
    if(isset($filter["vgl3"])){
        if(strlen($filter["vgl3"]) > 0){
            $rlike .= $delimiter . $filter["vgl3"] . "$";
            $delimiter = "|";
        }
    }
    $rlike .= ")";
    if(strlen($rlike) == 3){
        header("location: /wm/evaluation.php");
        exit();
    }
    $data = array();
    $avg = 0;
    $size = 0;
    if($result = $mysqli->query("CALL sp_medals_compare('".$filter["init"]."','$rlike');")){
        $data = array();
        while($row = $result->fetch_assoc()){
            $data[] = $row;
            if(is_numeric($row["avg place in reference"])){
                $avg += $row["avg place in reference"];
                $size += 1;
            }
        }
    } else{
        printf("Error message: %s\n", $mysqli->error);
    }
    $avg = $avg / $size;
    echo "<h3 class='headline'>Durchschnittliche Platzierung in referenz Disziplinen: $avg</h3>";
    return $data;
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
    echo "<table class='table'><tr>";
    foreach ($array[0] as $key => $value) {
        echo "<td>$key</td>";
    }
    echo "</tr>";
    foreach ($array as $i => $value) {
        if($i % 2 == 0){
            echo "<tr class='zebra'>";
        } else{
            echo "<tr>";
        }
        foreach ($value as $key => $value) {
            if(is_numeric($value)){
                if($value == 0){
                    echo "<td style='text-align: center;'>-</td>";
                    continue;
                } else{
                    echo "<td>".round(doubleval($value), 2)."</td>";
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