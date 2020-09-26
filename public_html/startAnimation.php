<?php
    if(isset($_COOKIE["firstLoad"])){
        setcookie("firstLoad", "1");
?>
<style>
    header a{
        overflow: hidden;
    }

    header a:nth-child(1){
        animation: startAnimation-blendInFromLeft 0.5s 1.5s backwards;
    }

    header a:nth-child(2){
        animation: startAnimation-blendInFromLeft 0.5s 1.6s backwards;
    }
    
    header a:nth-child(3){
        animation: startAnimation-blendInFromLeft 0.5s 1.7s backwards;
    }

    header a:nth-child(4){
        animation: startAnimation-blendInFromLeft 0.5s 1.8s backwards;
    }

    header #signIn-sign-Out-form{
        animation: startAnimation-blendInFromTop 0.5s 2s both;
    }

    header:before{
        content: "";
        left: 0;
        position: absolute;
        background-color: white;
        height: 4px;
        width: 200px;
        animation: startAnimation 1s 1.4s both;
    }

    #startAnimation-background{
        width: 100vw;
        height: 100vh;
        position: fixed;
        z-index: 100;
        background-color: white;
        animation: startAnimation-loadingBackground 1.5s both ease-in-out 0.5s;
    }

    #startAnimation-loading{
        left: 0;
        position: fixed;
        width: 100vw;
        height: 100vh;
        margin:0 auto;
        overflow:hidden;
    }

    #startAnimation-loading:after{
        content:'';
        position:absolute;
        bottom: 0;
        left: 0;
        border-top-left-radius:50%;
        border-top-right-radius:50%;
        width:100vw; 
        height:60vh;
        box-shadow: 0px 0px 0px 2000px #333;
        animation: startAnimation-loadingDiv 1.5s both ease-in-out 0.5s;
        z-index: 101;
    }

    @keyframes startAnimation{
        0%{
            transform: translateX(0) translateY(-5px);
        } 20%{
            transform: translateX(100px) translateY(0);
        }40%{
            transform: translateX(200px) translateY(0);
        }60%{
            transform: translateX(300px) translateY(0);
        }80%{
        }100%{
            transform: translateX(100vw) translateY(0);
        }
    }

    @keyframes startAnimation-blendInFromTop{
        from{
            transform: translateY(-10px) translateX(-1000px);
            opacity: 0;
         }
        to{
            transform: translateX(0) translateX(0);
            opacity: 1;
        }
    }

    @keyframes startAnimation-blendInFromLeft{
        from{
            background-color: #FFF;
            transform: translateX(-10px);
            height: 0;
         }
        to{
            background-color: #333;
            transform: translateX(0);
            height: 100%;
        }
    }

    @keyframes startAnimation-loadingBackground{
        0%{
        }
        50%{
            opacity: 1;
        }
        100%{
            opacity: 0;
        }
    }

    @keyframes startAnimation-loadingDiv{
        0%{
            height: 0;
            border-top-left-radius:50%;
            border-top-right-radius:50%;
            box-shadow: 0px 0px 0px 150vh #333;
        }
        90%{
            opacity: 1;
        }
        100%{
            height: calc(100vh - 50px);
            border-top-left-radius: 0;
            border-top-right-radius: 0;
            box-shadow: 0px 0px 0px 150vh #333;
            opacity: 0;
        }
    }
</style>

<div id="startAnimation-loading">
    <div id="startAnimation-background">
        <img width="1920px" height="1080px" src="/img/zielschritt.png" alt="Titelbild">
    </div>
</div>
<?php
    }
?>