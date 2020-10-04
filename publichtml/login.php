<?php
    if(!isset($_POST["login-submit"])){
        header("location: /index.php");
        exit(0);
    }
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="Example of a meta description this will often show up in search results">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Cst - Login</title>
    </head>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Kumbh+Sans:wght@300;400;700&display=swap');
        body{
            padding-top: 20vh;
            font-family: 'Kumbh Sans', sans-serif;
            font-weight: 300;
        }

        h1, h2, h3{
            text-align: center;
            font-weight: 400;
            line-height: 40px;
        }

        form{
            display: block;
            margin: 0 auto;
            border: 1px solid #CCC;
            width: 400px;
            height: 500px;
            border-radius: 8px;
            overflow: hidden;
            -webkit-box-shadow: 0px 0px 218px 17px rgba(230,230,230,1);
            -moz-box-shadow: 0px 0px 218px 17px rgba(230,230,230,1);
            box-shadow: 0px 0px 218px 17px rgba(230,230,230,1);
        }

        .formWrapper{
            transition: left 0.3s;
            position: relative;
            width: 900px;
            height: 100%;
            left: 0;
        }

        .formWrapper1{
            left: -400px;
        }

        .formStep{
            background-color: #F0F0F0;
            display: inline-block;
            float: left;
            width: 400px;
            height: 100%;
            margin: 0;
        }

        form div *{
            display: block;
        }

        a{
            color: #333;
            text-decoration: none;
        }

        a:hover{
            color: #555;
        }

        form div input{
            padding: 15px;
            border: 1px solid #CCC;
            border-radius: 4px;
            width: 300px;
            margin: 0 auto;
            font-family: 'Kumbh Sans', sans-serif;
            font-weight: 400;
        }

        form input:-webkit-autofill {
            background-color: white !important;
        }

        form .innerLabel{
            -webkit-box-shadow: 0px 0px 55px 72px rgba(255,255,255,1);
            -moz-box-shadow: 0px 0px 55px 72px rgba(255,255,255,1);
            box-shadow: 0px 0px 55px 72px rgba(255,255,255,1);
        }

        input {
            filter: none;
        }

        form .innerLabel + div{
            top: -40px;
            left: 50px;
            padding: 5px;
            height: 15px;
            line-height: 20px;
            display: inline-block;
            position: relative;
            background-color: white;
            color: gray;
            font-family: 'Kumbh Sans', sans-serif;
            font-weight: 400;
            transition: top 0.2s, left 0.2s, font-size 0.2s, height 0.2s;
            font-size: 12pt;
        }

        form .innerLabel + .moved, form .innerLabel:focus + div{
            font-size: 10pt;
            top: -60px;
            left: 40px;
            height: 10px;
        }

        form .innerLabel:focus{
            border: 1px solid #1a73e8;
        }

        form .innerLabel + div{
            pointer-events: none;
        }

        .submitDiv{
            height: 50px;
            padding: 40px;
        }

        form div div a{
            float: left;
            color: #1a73e8;
        }

        form div div button{
            float: right;
            background-color: #1a73e8;
            color: white;
            border-radius: 5px;
            padding: 10px 15px;
            border: none;
            transition: background-color 0.2s, box-shadow 0.4s;
        }

        #back{
            float: left;
            background-color: white;
            color: #1a73e8;
            border: none;
            transition: background-color 0.2s, box-shadow 0.4s;
        }

        form div div button:hover{
            background-color: #1768e5;
            box-shadow:0 6px 10px 0 rgba(0,0,0,0.14),0 1px 18px 0 rgba(0,0,0,0.12),0 3px 5px -1px rgba(0,0,0,0.2);
        }

        .hint{
            color: #9e4536;
            height: 20px;
            line-height: 20px;
            padding-left: 40px;
        }

        #password{
            display: none;
        }

        /*@media (max-width: 600px) {
            body{
                padding: 0;
            }
            form{
                border: none;
                width: 100%;
                height: 100vh;
            }
            .formStep{
                width: 100vw;
            }
            .formWrapper{
                width: 200vw;
            }
            .formWrapper1{
                left: -100vw;
            }
            form .innerLabel + div{
                margin-left: 100px;
            }
            input{
                width: 100px;
            }
        }*/
    </style>
    <body>
        <form action='includes/login.inc.php' method='POST' autocomplete="off" onsubmit="console.log(passCorrect); return passCorrect;">
            <div class="formWrapper">
                <div class="formStep">
                    <h2>CST</h2>
                    <h3>Anmeldung</h3>
                    <p style="text-align: center;">Mit Cst Account anmelden</p>
                    <input class="innerLabel" id="mailUsername" type='text' name='mailUsername' required>
                    <div>E-Mail, oder Benutzername</div>
                    <div class="hint"></div>
                    <div class="submitDiv">
                        <a href="signup.php">Accout erstellen</a>
                        <button type="button" onclick="checkEmailUsername()">Weiter</button>
                    </div>
                </div>
                <div class="formStep">
                    <h2>CST</h2>
                    <h3 id="welcomeMsg">Willkommen</h3>
                    <p style="text-align: center;">Mit Cst Account anmelden</p>
                    <input class="innerLabel" id="password" type='password' name='password' required>
                    <div>Password</div>
                    <div class="hint"></div>
                    <div class="submitDiv">
                        <button id="back" type="button" onclick="backToUsername();">zurück</a>
                        <button type='submit' name='login-submit'>Anmelden</button>
                    </div>
                </div>
            </div>
        </form>
    </body>
    <script>
        let username = "";
        let email = "";
        let passCorrect = false;

        window.addEventListener('keydown',function(e){if(e.keyIdentifier=='U+000A'||e.keyIdentifier=='Enter'||e.keyCode==13){if(e.target.nodeName=='INPUT'&&e.target.type=='text'){e.preventDefault(); checkEmailUsername(); return false;}}},true);

        document.getElementById("password").onchange = checkPassword;
        document.getElementById("password").onkeyup = checkPassword;

        setTimeout(function(){
            document.getElementById("mailUsername").focus();
        }, 400);

        document.getElementById("mailUsername").onkeyup = function(e){
            if(event.keyCode == 13){
                document.activeElement.blur();
                checkEmailUsername();
                e.preventDefault();
                return false;
            }
        }

        document.getElementById("password").onkeyup = function(){
            alert(document.getElementById("password").style.display);
            if(event.keyCode == 13 && document.getElementById("password").style.display != "none"){
                checkPassword();
            }
        }

        let elems = document.getElementsByClassName("innerLabel");
        for (let index = 0; index < elems.length; index++) {
            elems[index].onkeyup = function (){
                elems[index].nextElementSibling.classList.toggle("moved", elems[index].value.length > 0);
            };
        }

        function backToUsername(){
            let elems = document.getElementsByClassName("innerLabel");
            for (let index = 0; index < elems.length; index++) {
                elems[index].onkeyup = function (){
                    elems[index].nextElementSibling.classList.toggle("moved", true);
                };
            }
            document.getElementsByClassName('formWrapper')[0].classList.remove('formWrapper1');
            document.getElementsByClassName('formStep')[0].scrollIntoView();
            document.getElementById("password").style.disply = "block";
        }

        backToUsername();

        function weiter(){
            document.getElementById('password').style.display = 'block';
            setTimeout(function(){
                    document.getElementById("password").focus();
                    checkPassword();
                }, 400);
            hideHint();
            document.getElementById("welcomeMsg").innerText = "Wilkommen " + username + "!";
            document.getElementsByClassName("formWrapper")[0].classList.add("formWrapper1");
        }

        function displayHint(hint){
            let elems = document.getElementsByClassName("hint");
            for (let index = 0; index < elems.length; index++) {
                elems[index].innerText = hint;
            }
        }

        function hideHint(){
            let elems = document.getElementsByClassName("hint");
            for (let index = 0; index < elems.length; index++) {
                elems[index].innerText = "";
            }
        }

        function checkEmailUsername(){
            console.log("checkEmailUsername")
            if(!document.getElementById("mailUsername").value.length > 0){
                displayHint("Bitte ausfüllen");
            } else{
                let http = new XMLHttpRequest();
                http.onload = function(){
                    let response = this.responseText;
                    if(response == "noUser"){
                        displayHint("Erneut versuchen");
                    } else if(response.search("error") == -1 && response.length > 2){
                        let split = response.split(',');
                        username = split[0];
                        email = split[1];
                        displayHint(response);
                        weiter();
                    }
                }
                http.open("POST", "/includes/checkUser.inc.php?user=" + document.getElementById("mailUsername").value, true);
                http.send();
            }
        }

        function checkPassword(){
            console.log("s");
            if(!document.getElementById("password").value.length > 0){
                displayHint("Bitte ausfüllen");
                passCorrect = false;
            } else{
                var request = new XMLHttpRequest();
                request.open('POST', "/includes/checkPassword.inc.php", false);
                request.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
                request.send("user=" + username + "&password=" + document.getElementById("password").value);
                console.log("response: " + request.responseText);
                if(request.status == 200) {
                    passCorrect = request.responseText == "true";
                    if(!passCorrect){
                        displayHint("Password inkorrekt!");
                    } else{
                        hideHint();
                    }
                } else{
                    displayHint("Server error");
                }
            }
            return passCorrect;
        }
    </script>
</html>