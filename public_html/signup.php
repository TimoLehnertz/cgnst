<?php
    require "header.php";
?>
    <main class="main">
        <form action="includes/signup.inc.php" method="POST">
            <h2>Signup</h2>
            <?php
                if(isset($_GET['error'])){
                    if($_GET['error'] == "emptyFields"){
                        echo "<p class='signupError'>Please fill in all fields</p>";
                    } else if($_GET['error'] == "userTaken"){
                        echo "<p class='signupError'>Username is taken :(</p>";
                    }else if($_GET['error'] == "invalidEmail"){
                        echo "<p class='signupError'>Please enter a valid E-mail</p>";
                    }else if($_GET['error'] == "passwordCheck"){
                        echo "<p class='signupError'>Your passwords do not match</p>";
                    }else if($_GET['error'] == "invalidUsername"){
                        echo "<p class='signupError'>username invalid please only use a-Z, 0-9</p>";
                    }
                } else if(isset($_GET['signup'])){
                    if($_GET['signup'] == 'success'){
                        echo "<p class='signupSuccess'>Signup successfull!</p>";
                    }
                }
            ?>
            <input type="text" name="username" placeholder="Username">
            <input type="text" name="email" placeholder="E-mail">
            <input type="password" name="password" placeholder="Password">
            <input type="password" name="passwordRepeat" placeholder="Repeat password">
            <button type="submit" name="signup-submit">Signup</button>
        </form>
    </main>
<?php
    require "footer.php";
?>