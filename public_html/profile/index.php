<?php
    if(session_status() != PHP_SESSION_ACTIVE){
        session_start();
    }
    if (!isset($_SESSION["username"])) {
        header("location: /index.php");
        exit();
    }
    $username = $_SESSION["username"];
    $dependency = ["titleimg", "lists", "profile"];
    include_once "../header.php"
?>
    <div class="layout basic">
        <main class="main">
            <section class="section">
                <h2 class="headline">Willkommen <?=$username?></h3>
                <div class="content">
                    <h2>Gruppen</h2>
                    <p>Hier siest du deine Gruppen</p>
                    <script>
                        const groupList1 = getGroupListElement((group)=>{
                            if(!group.isDefaultGroup) {
                                return $(`<a href="groupx.php?id=${group.idgroup}" class="group-row">
                                    <div>${group.name}</div>${isAdminInGroup(group) ? "<div class='admin'>Gruppenadmin" : "<div>"}</div>
                                </a>`);
                            }
                        });
                        $(".group-section").append(groupList1);
                    </script>
                    <p><a href="create-group.php">Erstelle eine neue Gruppe</a></p>
                </div>
            </section>
        </main>
        <aside class="aside">
            <h2>Freunde</h2>
        </aside>
    </div>
<?php
    include_once "../footer.php";
?>