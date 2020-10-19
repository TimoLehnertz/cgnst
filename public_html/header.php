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
        <title>Cologne Speet team</title>
        <link rel="icon" type="image/gif" href="/img/rolle2.gif">
        <link rel="stylesheet" href="/css/style.css">
        <link rel="stylesheet" href="/css/ui.css">
        <link rel="stylesheet" href="/css/normalize.css">
        <script src="/js/jquery-3.5.1.js"></script>
        <script src="/js/ui.js"></script>
        <script src="/js/ajaxAPI.js"></script>
        <script src="https://kit.fontawesome.com/bb5d468397.js" crossorigin="anonymous"></script>
        <?php
            $title = "";
            $loadedDependencies = array();
            if(isset($dependency)){
                if(in_array("kalender", $dependency)){
                    loadRawDependency("user-list", $loadedDependencies, $title);
                    loadRawDependency("kalender", $loadedDependencies, $title);
                }
                if(in_array("group-user-dragndrop", $dependency)){
                    loadRawDependency("group-user-dragndrop", $loadedDependencies, $title);
                }
                if(in_array("user-list", $dependency)){
                    loadRawDependency("user-list", $loadedDependencies, $title);
                }
                if(in_array("training", $dependency)){
                    loadRawDependency("training", $loadedDependencies, $title);
                }
                if(in_array("titleimg", $dependency)){
                    loadRawDependency("titleimg", $loadedDependencies, $title);
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
                    case "user-list":
                        if(!in_array("user-list", $dependencies)){
                            $dependencies[] = "user-list";
                            echo "<link rel='stylesheet' href='/css/user-list.css'>";
                            echo "<script src='/js/user-list.js'></script>"; 
                        }
                        break;
                    case "training":
                        if(!in_array("training", $dependencies)){
                            $dependencies[] = "training";
                            echo "<link rel='stylesheet' href='/css/training.css'>";
                            echo "<script src='/js/training.js'></script>"; 
                        }
                        break;
                    case "titleimg":
                        if(!in_array("titleimg", $dependencies)){
                            $dependencies[] = "titleimg";
                            echo "<link rel='stylesheet' href='/css/title-img.css'>";
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
        ?>
    </head>
    <body id="body">
        <?php
            include "startAnimation.php";
        ?>
        <header>
            <nav>
                <a href="/index.php">Home</a>
                <a href="/kalender">Kalender</a>
                <?php if(isset($_SESSION["username"])){echo '<a href="/profile">Profil</a>';}?>
                <a href="/training">Training</a>
                <a href="/timing">Timing</a>
                <a href="/kontakt">Kontakt</a>
                <!-- <a href="/index.php">About us</a> -->
                <?php if(hasPermission("permission_administration")){echo '<a href="/administration">Administration</a>';}?>
            </nav>
            <div>
            <?php if (isset($_SESSION["username"])) {//Signed in?>
                <?php echo $_SESSION["username"];?>
                <form id='signIn-sign-Out-form' action='/includes/logout.inc.php' method='POST'>
                    <button class='rectShadow' type='submit' name='login-submit'>Logout</button>
                </form>
            <?php } else{//not signed in?>
                <form id='signIn-sign-Out-form' action='/login.php' method='POST'>
                    <button class='rectShadow' type='submit' name='login-submit'>Login</button>
                </form>
            <?php }?>
            </div>
        </header>
        <?=$title?>