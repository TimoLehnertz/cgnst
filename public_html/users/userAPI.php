<?php

session_start();

include "../includes/dbh.inc.php";

if(isset($_GET["getUserList"])){
    echo json_encode(getUserList($mysqli));
} else if(isset($_GET["getGroupList"])){
    echo json_encode(getGroupList($mysqli));
} else if(isset($_GET["setGroups"])){
    $data = json_decode(file_get_contents('php://input'), true);
    updateGroup($conn, $mysqli, $data, true);
} else if(isset($_GET["undoGroups"])){
    undoGroups($conn, $mysqli);
} else if(isset($_GET["redoGroups"])){
    redoGroups($conn, $mysqli);
}


function undoGroups($conn, $mysqli){
    if(isset($_SESSION["groupconfigurationUndo"])){
        if($_SESSION["undoStep"] > 0){
            echo "size: ".sizeof($_SESSION["groupconfigurationUndo"])."\n";
            echo "undo to: ".($_SESSION["undoStep"] - 1)."\n";
            $_SESSION["undoStep"] = $_SESSION["undoStep"] - 1;
            updateGroup($conn, $mysqli, $_SESSION["groupconfigurationUndo"][$_SESSION["undoStep"]], false);
        } else{
            echo "Error: No more undos left";
        }
    } else{
        echo "Error: nothing to undo in this session";
    }
}

function redoGroups($conn, $mysqli){
    if(isset($_SESSION["groupconfigurationUndo"])){
        if($_SESSION["undoStep"] < sizeof($_SESSION["groupconfigurationUndo"]) - 1){
            $_SESSION["undoStep"] = $_SESSION["undoStep"] + 1;
            updateGroup($conn, $mysqli, $_SESSION["groupconfigurationUndo"][$_SESSION["undoStep"]], false);
        } else{
            echo "Error: Already on latest state";
        }
    } else{
        echo "Error: nothing to redo in this session";
    }
}

function updateGroup($conn, $mysqli, $data, $trackUndo){
    $groupNames = array();
    $groups = getGroupList($mysqli);
    if($trackUndo){
        if(!isset($_SESSION["groupconfigurationUndo"])){
            $_SESSION["groupconfigurationUndo"] = array();
            $_SESSION["groupconfigurationUndo"][] = $groups;
        }
        $_SESSION["groupconfigurationUndo"][] = $data;
        $_SESSION["undoStep"] = sizeof($_SESSION["groupconfigurationUndo"]) - 1;
    }
    foreach ($data as $groupName => $group) {
        
        $groupNames[] = $groupName;
        if(!groupNameInGroups($groupName, $groups)){
            addGroup($mysqli, $groupName);
            $groups = getGroupList($mysqli);
        }
        $idgroup = getGroupId($groupName, $groups);

        // Adding users group relations
        for ($i=0; $i < sizeof($group["users"]); $i++) {
            $iduser = getUserId($mysqli, $group["users"][$i]["username"]);
            if($iduser > -1){
                addUserGroupRelation($mysqli, $iduser, $idgroup);
                setUserAdminInGroup($mysqli, $iduser, $idgroup, $group["users"][$i]["isAdmin"]);
            } else{
                echo "didnt find user";
            }
        }
        // Removing users group relations
        $existingUsers = getUsersFromGroupId($mysqli, $idgroup);
        for ($i=0; $i < sizeof($existingUsers); $i++) {
            if(!usernameInusers($existingUsers[$i]["username"], $group["users"])){
                $userId = $existingUsers[$i]["iduser"];
                deleteUserGroupRelation($mysqli, $userId, $idgroup);
            }
        }
    }
    // removing groups
    $existingGroups = getGroupList($mysqli);
    foreach($existingGroups as $groupName => $group){
        if(!in_array($groupName, $groupNames)){
            $idgroup = $group["idgroup"];
            deleteGroup($mysqli, $idgroup);
        }
    }
}

function usernameInusers($username, $users){
    for ($i=0; $i < sizeof($users); $i++) { 
        if($users[$i]["username"] == $username){
            return true;
        }
    }
    return false;
}

function getUserId($mysqli, $name){
    if($stmt = $mysqli->prepare("SELECT iduser FROM user WHERE username like ?;")){
        $stmt->bind_param("s", $name);
        if($stmt->execute()){
            if($row = $stmt->get_result()->fetch_array(MYSQLI_ASSOC)){
                return $row["iduser"];
            } else{
                return -1;
            }
        }
        $stmt->close();
    }
}

function groupNameInGroups($groupNameIn, $groups){
    foreach($groups as $groupName => $group){
        if($groupName == $groupNameIn){
            return true;
        }
    }
    return false;
}

function getGroupId($groupNameIn, $groups){
    foreach($groups as $groupName => $group){
        if($groupName == $groupNameIn){
            return $group["idgroup"];
        }
    }
    return -1;
}

function addUserGroupRelation($mysqli, $userId, $groupId){
    if(userGroupRelationExists($mysqli, $userId, $groupId)){
        return true;
    } else{
        $stmt = $mysqli->prepare("INSERT INTO group_has_user (group_idgroup, user_iduser) VALUES(?, ?);");
        if($stmt->bind_param("ii", $groupId, $userId)){
            if($stmt->execute()){
                $stmt->close();
                return true;
            } else{
                printf("Error message: %s\n", $mysqli->error);
            }
        }
        $stmt->close();
        return false;
    }
}

function userGroupRelationExists($mysqli, $userId, $groupId){
    $stmt = $mysqli->prepare("SELECT * FROM group_has_user WHERE group_idgroup=? AND user_iduser=?;");
    if($stmt->bind_param("ii", $groupId, $userId)){
        if($stmt->execute()){
            $out = $stmt->get_result()->num_rows > 0;
            $stmt->close();
            return $out;
        } else{
            printf("Error message: %s\n", $mysqli->error);
        }
    }
    $stmt->close();
    return false;
}

function doesGroupExist($mysqli, $idgroup){
    $stmt = $mysqli->prepare("SELECT * FROM `group` WHERE idgroup=?;");
    if($stmt->bind_param("i", $idgroup)){
        if($stmt->execute()){
            $out = $stmt->get_result()->num_rows > 0;
            $stmt->close();
            return $out;
        } else{
            printf("Error message: %s\n", $mysqli->error);
        }
    }
    return false;
}

function deleteGroup($mysqli, $idgroup){
    if(doesGroupExist($mysqli, $idgroup)){
        deleteAllGroupConstraints($mysqli, $idgroup);
        $stmt = $mysqli->prepare("DELETE FROM `group` WHERE idgroup=?;");
        if($stmt->bind_param("i", $idgroup)){
            if($stmt->execute()){
                $stmt->close();
                return true;
            } else{
                printf("Error message: %s\n", $mysqli->error);
                return false;
            }
        }
    }
    return true;
}

function deleteAllGroupConstraints($mysqli, $idgroup){
    $success = true;
    $stmt = $mysqli->prepare("DELETE FROM group_has_user WHERE group_idgroup=?;");
    if($stmt->bind_param("i", $idgroup)){
        if($stmt->execute()){
            $stmt->close();
        } else{
            $success = false;
            printf("Error message: %s\n", $mysqli->error);
        }
    }
    $stmt = $mysqli->prepare("DELETE FROM group_has_admin WHERE group_idgroup=?;");
    if($stmt->bind_param("i", $idgroup)){
        if($stmt->execute()){
            $stmt->close();
        } else{
            $success = false;
            printf("Error message: %s\n", $mysqli->error);
        }
    }
    return $success;
}

function deleteUserGroupRelation($mysqli, $userId, $groupId){
    $stmt = $mysqli->prepare("DELETE FROM group_has_user WHERE group_has_user.group_idgroup=? AND group_has_user.user_iduser=?;");
    if($stmt->bind_param("ii", $groupId, $userId)){
        if($stmt->execute()){
            $stmt->close();
            return true;
        } else{
            printf("Error message: %s\n", $mysqli->error);
        }
        $stmt->close();
        return false;
    }
    $stmt->close();
}

function addGroup($mysqli, $name){
    $stmt = $mysqli->prepare("INSERT INTO `group`(name) VALUES(?);");
    if($stmt->bind_param("s", $name)){
        if($stmt->execute()){
            $stmt->close();
            return true;
        } else{
            printf("Error message: %s\n", $mysqli->error);
        }
        $stmt->close();
        return false;
    }
    $stmt->close();
}

function getUserList($mysqli){
    $users = array();
    if ($result = $mysqli->query("SELECT username, iduser FROM user;")) {
        while($row = $result->fetch_array(MYSQLI_ASSOC)){
            $users[] = $row;
        }
        $result->close();
    } else{
        printf("Error message: %s\n", $mysqli->error);
    }
    return $users;
}

function getGroupList($mysqli){
    $groups = array();
    if($result = $mysqli->query("SELECT * FROM `group`;")) {
        while($row = $result->fetch_array(MYSQLI_ASSOC)){
            $groupName = $row["name"];
            $group = json_decode('{"idgroup":"'.$row["idgroup"].'", "users":[]}', true);
            $group["users"] = getUsersFromGroupId($mysqli, $row["idgroup"]);
            $groups[$groupName] = $group;
        }
        $result->close();
    } else{
        printf("Error message: %s\n", $mysqli->error);
    }
    // print_r($groups);
    return $groups;
}

function getUsersFromGroupId($mysqli, $idgroup){
    $users = array();
    $stmt = $mysqli->prepare("SELECT user.username, user.iduser FROM group_has_user JOIN user ON user.iduser = group_has_user.user_iduser WHERE group_has_user.group_idgroup=?;");
    if($stmt->bind_param("i", $idgroup)){
        if($stmt->execute()) {
            $result = $stmt->get_result();
            while($row = $result->fetch_array(MYSQLI_ASSOC)){
                $iduser = $row["iduser"];
                $isAdmin = isUserAdminInGroup($mysqli, $iduser, $idgroup);
                $user = json_decode('{"username": "'.$row["username"].'", "iduser":"'.$iduser.'", "isAdmin":'.($isAdmin ? "true" : "false").'}', true);
                $users[] = $user;
            }
            $result->close();
        } else{
            printf("Error message: %s\n", $mysqli->error);
        }
    }
    $stmt->close();
    return $users;
}

function isUserAdminInGroup($mysqli, $iduser, $idgroup){
    $stmt = $mysqli->prepare("SELECT * FROM group_has_admin WHERE group_idgroup=? AND user_iduser=?;");
    if($stmt->bind_param("ii", $idgroup, $iduser)){
        if($stmt->execute()){
            return $stmt->get_result()->num_rows > 0;
        } else{
            printf("Error message: %s\n", $mysqli->error);
        }
    }
    $stmt->close();
    return false;
}

function setUserAdminInGroup($mysqli, $iduser, $idgroup, $isAdmin){
    $wasAdmin = isUserAdminInGroup($mysqli, $iduser, $idgroup);
    if(!$wasAdmin && $isAdmin){
        $sql = "INSERT INTO group_has_admin(group_idgroup, user_iduser) VALUES(?, ?);";
    } else if($wasAdmin && !$isAdmin){
        $sql = "DELETE FROM group_has_admin WHERE group_idgroup=? AND user_iduser=?;";
    } else{
        return true;
    }
    $stmt = $mysqli->prepare($sql);
    if($stmt->bind_param("ii", $idgroup, $iduser)){
        if($stmt->execute()){
            $stmt->close();
            return true;
        }else{
            printf("Error message: %s\n", $mysqli->error);
        }
    }
    $stmt->close();
    return false;
}