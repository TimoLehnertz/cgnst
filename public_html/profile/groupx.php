<?php

if(session_status() != PHP_SESSION_ACTIVE){
    session_start();
}

include_once "../includes/dbh.inc.php";
include_once "../users/userAPI.php";
include_once "../kalender/kalenderAPI.php";

if(!isset($_GET["id"])){
    header("location: /index.php");
    exit();
}
if(!isset($_SESSION["username"])){
    header("location: /index.php?Error=Du musst dich einloggen");
    exit();
}
$idgroup = intval($_GET["id"]);
$iduser = $_SESSION["userId"];
if(!doesGroupExistById($mysqli, $idgroup)){
    header("location: /index.php");
    exit();
}
if(!isUserMemberInGroup($mysqli, $iduser, $idgroup)){
    header("location: /index.php");
    exit();
}
$groups = getGroupList($mysqli);
$group = getGroupById($idgroup, $groups);
$groupName = $group["name"];
$members = $group["users"];
$entries = getEntriesForGroupId($mysqli, $idgroup, $groups);
$myState = isUserAdminInGroup($mysqli, $_SESSION["userId"], $idgroup) ? "Gruppenadmin" : "Mitglied";
$dependency = ["titleimg", "group"];
include_once "../header.php";
?>
<main class="main">
    <section>
        <div class="headline">
            <h2><?=$groupName?></h2>
            <p class="description">Gruppe</p>
            <p class="my-state">Du bist <?=$myState?></p>
        </div>
        <div class="entries">
            <?php
                // print_r($entries);
            ?>
        </div>
        <div class="members">
            <h3>Mitglieder</h3>
            <?php
                for ($i=0; $i < sizeof($members); $i++) {
                    if($members[$i]["isAdmin"]){
                        echo "<div class='member-row'>
                            <div>".($members[$i]["iduser"] == $_SESSION["userId"] ? "Du" : $members[$i]["username"])."</div><div>".($members[$i]["isAdmin"] ? "Gruppenadmin" : "Mitglied")."</div>
                        </div>";
                    }
                }
                for ($i=0; $i < sizeof($members); $i++) {
                    if(!$members[$i]["isAdmin"]){
                        echo "<div class='member-row'>
                            <div>".($members[$i]["iduser"] == $_SESSION["userId"] ? "Du" : $members[$i]["username"])."</div><div>".($members[$i]["isAdmin"] ? "Gruppenadmin" : "Mitglied")."</div>
                        </div>";
                    }
                }
            ?>
        </div>
    </section>
</main>
<?php
    include_once "../footer.php";
?>