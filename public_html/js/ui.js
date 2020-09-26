"use strict";
//tips
const tipElements = document.getElementsByClassName("tip");
for (let i = 0; i < tipElements.length; i++) {
    tipElements[i].innerHTML += "<button class='closeTipButton' onclick='hideTip(" + i + ")'>X</button>";
}

function hideTip(index){
    tipElements[index].classList.add("shrinkToTop");
    console.log("fade out " + index);
}