<!-- Head -->
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="Cologne Speed Team" content="width=device-width, initial-scale=1">
        <title>Cologne Speed Team</title>
        <link rel="icon" type="image/gif" href="/img/rolle2.gif">
        <link rel="stylesheet" href="https://www.cst-skate.de/css/main.css">
        <link href="https://fonts.googleapis.com/css2?family=Carter+One&display=swap" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,300;0,400;0,500;0,600;1,300;1,400;1,500&display=swap" rel="stylesheet">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <script src="https://kit.fontawesome.com/bb5d468397.js" crossorigin="anonymous"></script>
    </head>
    <body id="body">
        <header class="header">
            <div class="burger">
                <div class="line1"></div>
                <div class="line2"></div>
                <div class="line3"></div>
            </div>
            <nav class="nav">
                <ul class="nav-links">
                    <li><a href="#">Home</a></li>
                </ul>
            </nav>
        </header>
        <main class="layout simple">
            <section class="section">
                <h1 class="headline">PHP</h1>
                <h2>Dateihandling</h2>
                <h3>Textdateien</h3>
                <?php
                    $handle = fopen("files/bsp.txt", "r");
                    if($handle){
                        echo "Alles klar, folgende resource steht zur verfügung: ";
                        var_dump($hande);
                    }
                ?> 
            </section>
        </main>
    <footer class="footer">
        © copyright by 3.14159
    </footer>
    </body>
    <script src="https://www.cst-skate.de/js/ui.js"></script>
</html>