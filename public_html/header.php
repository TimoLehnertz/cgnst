<?php
    if(session_status() != PHP_SESSION_ACTIVE){
        session_start();
    }

    function hasPermission($permission){
        if(isset($_SESSION[$permission])){
            return $_SESSION[$permission] == TRUE;
        } else{
            return FALSE;
        }
    }

    function requirePermission($permission){
        if(!hasPermission($permission)){
            if(isset($_SESSION["username"])){
                $_SESSION["error"] = "Du hast keine berechtigung für diesen bereich :(";
            } 
            header("location: /index.php");
            exit(); 
        }
    }
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="Cologne Speed Team" content="width=device-width, initial-scale=1">
        <title>Cologne Speed Team</title>
        <link rel="icon" type="image/gif" href="/img/rolle2.gif">
        <link rel="stylesheet" href="/css/main.css">
        <link href="https://fonts.googleapis.com/css2?family=Carter+One&display=swap" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,300;0,400;0,500;0,600;1,300;1,400;1,500&display=swap" rel="stylesheet">
        <script src="/js/jquery-3.5.1.js"></script>
        <script src="/js/ui.js"></script>
        <script src="/js/ajaxAPI.js"></script>
        <script src="https://kit.fontawesome.com/bb5d468397.js" crossorigin="anonymous"></script>
        <?php
            $title = "";
            $loadedDependencies = array();
            if(isset($dependency)){
                if(in_array("kalender", $dependency)){
                    loadRawDependency("lists", $loadedDependencies, $title);
                    loadRawDependency("kalender", $loadedDependencies, $title);
                }
                if(in_array("group-user-dragndrop", $dependency)){
                    loadRawDependency("group-user-dragndrop", $loadedDependencies, $title);
                }
                if(in_array("lists", $dependency)){
                    loadRawDependency("lists", $loadedDependencies, $title);
                }
                if(in_array("training", $dependency)){
                    loadRawDependency("training", $loadedDependencies, $title);
                }
                if(in_array("titleimg", $dependency)){
                    loadRawDependency("titleimg", $loadedDependencies, $title);
                }
                if(in_array("profile", $dependency)){
                    loadRawDependency("profile", $loadedDependencies, $title);
                }
                if(in_array("group", $dependency)){
                    loadRawDependency("group", $loadedDependencies, $title);
                }
            }

            function loadRawDependency($dependency, $dependencies, &$title){
                switch($dependency){
                    case "kalender":
                        if(!in_array("kalender", $dependencies)){
                            $dependencies[] = "kalender";
                            echo "<link rel='stylesheet' href='/css/kalender.css'>";
                            echo "<script src='/js/kalender.js'></script>";
                        }
                        break;
                    case "group-user-dragndrop":
                        if(!in_array("group-user-dragndrop", $dependencies)){
                            $dependencies[] = "group-user-dragndro";
                            echo "<link rel='stylesheet' href='/css/group-user-dragndrop.css'>";
                            echo "<script src='/js/group-user-dragndrop.js'></script>"; 
                        }
                        break;
                    case "lists":
                        if(!in_array("lists", $dependencies)){
                            $dependencies[] = "lists";
                            echo "<script src='/js/lists.js'></script>"; 
                        }
                        break;
                    case "training":
                        if(!in_array("training", $dependencies)){
                            $dependencies[] = "training";
                            echo "<link rel='stylesheet' href='/css/training.css'>";
                            echo "<script src='/js/training.js'></script>"; 
                        }
                        break;
                    case "profile":
                        if(!in_array("profile", $dependencies)){
                            $dependencies[] = "training";
                            echo "<link rel='stylesheet' href='/css/profile.css'>";
                            echo "<script src='/js/profile.js'></script>"; 
                        }
                    break;
                    case "group":
                        if(!in_array("group", $dependencies)){
                            $dependencies[] = "training";
                            echo "<link rel='stylesheet' href='/css/group.css'>";
                            echo "<script src='/js/group.js'></script>"; 
                        }
                        break;
                    case "titleimg":
                        if(!in_array("titleimg", $dependencies)){
                            $dependencies[] = "titleimg";
                            echo "<script src='/js/title.js'></script>";
                            $path = new SplFileInfo(__FILE__);
                            $path = $path->getRealPath();
                            $directory = substr($path, 0, strlen($path) - 10)."/img/random";
                            $filenames = scandir($directory);
                            if($filenames){
                                $filenames2 = array();
                                for ($i=0; $i < sizeof($filenames); $i++) { 
                                    if(strpos($filenames[$i], ".jpg") > -1){
                                        $filenames2[] = $filenames[$i];
                                    }
                                }
                                $ranimg = $filenames2[random_int(0, sizeof($filenames2) - 1)];
                            }
                            $title = "<div class='title-img'
                            style='background-image: linear-gradient(to bottom, rgba(245, 246, 252, 0.52),#EEE),
                            url(/img/random/$ranimg);'></div>";
                        }
                        break;
                }
            }
            if(isset($_SESSION["username"])){
                $username = $_SESSION["username"];
                $userId = $_SESSION["userId"];
                echo "<script> const username = '$username'; const userId = '$userId'; const userLoggedIn = true;</script>";
            } else{
                echo "<script> const username = undefined; const userId = undefined; const userLoggedIn = false;</script>";
            }
        ?>
    </head>
    <body id="body">
        <?php
            include "startAnimation.php";
        ?>
        <header class="header">
            <div class="burger">
                <div class="line1"></div>
                <div class="line2"></div>
                <div class="line3"></div>
            </div>
            <nav class="nav">
                <ul class="nav-links">
                    <li><a href="/index.php">Home</a></li>
                    <li><a href="/kalender">Kalender</a></li>
                    <?php if(isset($_SESSION["username"])){echo '<li><a href="/profile">Profil</a></li>';}?>
                    <li><a href="/training">Training</a></li>
                    <li><a href="/timing">Timing</a></li>
                    <li><a href="/kontakt">Kontakt</a></li>
                    <li><a href="/wm">Wm Db</a></li>
                    <?php if(hasPermission("permission_administration")){echo '<li><a href="/administration">Administration</a></li>';}?>
                </ul>
            </nav>
            <h1>
                CST
            </h1>
            <?php
            if(isset($_SESSION["username"])){
            ?>
            <div class="profile">
                <div class="profile__header">
                    <?php echo $_SESSION["username"].'<i class="far fa-user-circle"></i>';?>
                </div>
                    <div class="profile__content">
                        <a href="/profile">Zu deinem Profil   </a><i class="fas fa-angle-right"></i>
                        <form action='/includes/logout.inc.php' method='POST'>
                            <button class='logout btn slide vertical' type='submit' name='logout-submit'>Logout<i class="fas fa-sign-out-alt"></i></button>
                        </form>
                        <?php print_r($_SESSION["permissions"]);?>
                    </div>
            </div>
                <?php } else{?>
                    <form class="login-form" action='/login.php' method='POST'>
                        <button class='login btn slide vertical' type='submit' name='login-submit'>Login</button>
                    </form>
                <?php }?>
        </header>
        <?=$title?>