<?php

if(session_status() != PHP_SESSION_ACTIVE){
    session_start();
}

include_once "../includes/dbh.inc.php";
include_once "../users/userAPI.php";
include_once "../kalender/kalenderAPI.php";

if(!isset($_GET["id"])){
    header("location: /index.php?error=Keine Id");
    exit();
}
if(!isset($_SESSION["username"])){
    header("location: /index.php?Error=Du musst dich einloggen");
    exit();
}
$idgroup = intval($_GET["id"]);
$iduser = $_SESSION["userId"];
if(!doesGroupExistById($mysqli, $idgroup)){
    header("location: /index.php?error=Nogroupforid");
    exit();
}
if(!isUserMemberInGroup($mysqli, $iduser, $idgroup)){
    header("location: /index.php?error=NotAUserOfGroup");
    exit();
}
$groups = getGroupList($mysqli);
$group = getGroupById($idgroup, $groups);
$groupName = $group["name"];
$members = $group["users"];
$entries = getEntriesForGroupId($mysqli, $idgroup, $groups);
$myState = isUserAdminInGroup($mysqli, $_SESSION["userId"], $idgroup) ? "Gruppenadmin" : "Mitglied";
$admin = isUserAdminInGroup($mysqli, $_SESSION["userId"], $idgroup);
$dependency = ["titleimg", "group"];
include_once "../header.php";
?>
<div class="layout basic">
    <main class="main">
        <section class="section">
            <div class="headline">
                <span class="font size big margin right"><i class="fas fa-users color secondary margin right"></i><?=$groupName?></span>
                <span class="color secondary mergin left">Gruppe</span>
                <div>
                    <p><i class="far fa-comment-alt color secondary margin right"><span></i>Beschreibung:</span></p>
                    <p class="margin left"><?php echo $group["description"]?></p>
                </div>
                <div>
                    <?php
                        // print_r($entries);
                    ?>
                </div>
            </div>
            <div class="content">
                <div class="members">
                    <h3>Mitglieder</h3>
                    <?php
                        for ($i=0; $i < sizeof($members); $i++) {
                            if($members[$i]["isAdmin"]){
                                echo "<div class='flex row start'>
                                    <div style='width: 10em;'>".($members[$i]["iduser"] == $_SESSION["userId"] ? "Du" : $members[$i]["username"])."</div><div>".($members[$i]["isAdmin"] ? "Gruppenadmin" : "Mitglied")."</div>
                                </div>";
                            } else{
                                echo "<div class='flex row start'>
                                    <div style='width: 10em;'>".($members[$i]["iduser"] == $_SESSION["userId"] ? "Du" : $members[$i]["username"])."</div><div>".($members[$i]["isAdmin"] ? "Gruppenadmin" : "Mitglied")."</div>
                                </div>";
                            }
                        }
                    ?>
                </div>
            </div>
        </section>
    </main>
    <aside class="aside">
        <div class="content">
            <?php
                if($admin){
                    echo "Du kannst diese daten bearbeiten!";
                }
            ?>
        </div>
    </aside>
</div>
<?php
    include_once "../footer.php";
?>