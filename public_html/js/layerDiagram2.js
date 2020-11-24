"use strict";

let data;

let subColors = true;
$(()=> {
    console.log("init")
    get500mData((res)=>{
        data = parseData(res);
        createDiagram();
    });
    $(".switch-direction").click(()=>{
        forewards = !forewards;
        updateDiagram();
    })
    $(".switch-subColors").click(function(){
        subColors =!subColors;
        updateDiagram();
        if(subColors){
            $(this).css("background-color", "#493")
        } else{
            $(this).css("background-color", "gray")
        }
    })
    $(".switch-subColors").css("background-color", "#493")
});

let canvas;

function createDiagram(){
    $(`.layerDiagram`).append(`<canvas width="900" height="400"></canvas>`);
    canvas = document.querySelector(".layerDiagram canvas");
    reOffset();
    window.onscroll=function(e){ reOffset(); }
    window.onresize=function(e){ reOffset(); }
    $(canvas).mousemove(function(e){handleMouseMove(e);});
    $(canvas).mouseenter(function(){mouseInside=true;});
    $(canvas).mouseleave(function(){mouseInside=false;});
    updateDiagram();
}

let offsetX,offsetY;

function reOffset(){
    if(canvas != null){
        var bb=canvas.getBoundingClientRect();
        offsetX=bb.left;
        offsetY=bb.top;
    }
}

/**
 * Diagram setup
 */

const layerWidth = 50;
const padding = 20;
const lineWidth = 2;
const positionColors = ["MediumSeaGreen", "DodgerBlue", "Orange", "Tomato"];//path
const layerColors = ["MediumSeaGreen", "CadetBlue", "DarkSlateGray", "Crimson"];//box

let forewards = true;
let focusedPlace = 0;

function updateDiagram(){
    const max = getMaxFromData();
    const canvas = document.querySelector(".layerDiagram canvas");
    const ctx = canvas.getContext('2d');
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    ctx.lineWidth = 10;
    
    const width = canvas.width - padding * 2;
    const height = canvas.height - padding * 2;
    
    if(mouseInside){
        focusedPlace = parseInt((mouseY) * 4 / height);
    } else{
        focusedPlace = -1;
    }
    
    
    /**
     * metadata
     */
    const lStartY = padding;
    const lStartX = [];
    for (let i = 0; i < 4; i++) {
        lStartX[i] = padding + (width) / 3 * i * ((width - layerWidth) / width);
    }

    const pHeight = height / 4;
    const pStartY = [];
    for (let i = 0; i < 4; i++) {
        pStartY[i] =  height / 4 * i;
    }

    let usedSpaceAfter = [[0,0,0,0],[0,0,0,0],[0,0,0,0],[0,0,0,0]];

    for (let layer = 0; layer < 4; layer++) {
        const posConsistsOf = data [layer].posConsistsOf;

        let position;
        if(mouseInside){
            position = (focusedPlace) % 4;
        } else{
            position = 0;
        }
        
        let counter = 0;
        while (counter < 4) {
            if(position == focusedPlace){
                ctx.fillStyle = "red";
            } else{
                ctx.fillStyle = positionColors[position];
            }
            if(!subColors){//simple
                ctx.fillRect(lStartX[layer], pStartY[position], layerWidth, pHeight);
            } else if(layer == 0){
                ctx.fillRect(lStartX[layer], pStartY[position], layerWidth, pHeight);
            }
            if(layer > 0){// all layers except of first one as has no ones in before
                const positions = posConsistsOf[position];
                let positionBefore = 0;
                let usedSpaceInFront = 0;
                for (const positionElem of positions) {
                    const percentage = positionElem.positions / max;
                    if(positionBefore == focusedPlace){
                        ctx.fillStyle = "red";
                    } else{
                        ctx.fillStyle = positionColors[forewards ? positionBefore : position];
                    }
                    if(subColors){
                        ctx.fillRect(lStartX[layer], pStartY[position] + usedSpaceInFront, layerWidth, pHeight * percentage);
                    }
                    
                    
                    ctx.beginPath();

                    ctx.moveTo(lStartX[layer], pStartY[position] + usedSpaceInFront);//start top left of position

                    ctx.lineTo(lStartX[layer], pStartY[position] + usedSpaceInFront + pHeight * percentage);

                    ctx.lineTo(lStartX[layer - 1] + layerWidth, pStartY[positionBefore] + usedSpaceAfter[layer - 1][positionBefore] + pHeight * percentage);

                    ctx.lineTo(lStartX[layer - 1] + layerWidth, pStartY[positionBefore] + usedSpaceAfter[layer - 1][positionBefore]);

                    ctx.fill();
                    usedSpaceAfter[layer - 1][positionBefore] += pHeight * percentage;
                    usedSpaceInFront += pHeight * percentage;
                    positionBefore++;
                }
            }
            position = (position + 1) % 4;
            counter++;
        }
    }
}

let mouseX = 0;
let mouseY = 0;
let mouseInside = false;
function handleMouseMove(e){
    mouseX=parseInt(e.clientX-offsetX);
    mouseY=parseInt(e.clientY-offsetY - padding);
    updateDiagram();
}

class Position{
    constructor(){
        this.infos = [];
        this.positions = 0;
    }
}

function parseData(rawData){
     const data = [[],[],[],[]];
     for (const row of rawData) {
        //Track each sportler throoughout the race(1,2,3,4)
        for (let startPosition = 0; startPosition < 4; startPosition++) {
            //track layers(start, afterStart, beforeFinish, finish)
            let lastPos = startPosition;//                  last Pos
            for (let layer = 1; layer < 4; layer++) {
                let position = undefined;
                for (let i = 1; i < 5; i++) {
                    if(row [layerToName(layer) + i] == startPosition + 1){
                        position = i - 1;//                 Position
                    }
                }
                if(position != undefined && position >= 0){



                    data[startPosition] [lastPos] [lastPos].positions++;



                    lastPos = position;
                }
            }
        }
     }
     //filling first layer with dummies(1)
     let max = 0;
     for (const position of data[3].posConsistsOf[3]) {
         max += position.positions;
     }
        for (let l = 0; l < 4; l++) {
            for (let i = 0; i < 4; i++) {
                data[0] ["posConsistsOf"] [l] [i] = 1
            }
        }
     console.log(data);
     return data;
}

function getMaxFromData(){
    let max = 0;
    for (const position of data[3].posConsistsOf[3]) {
        max += position.positions;
    }
    return max;
}

function nrFromName(name){
    return parseInt(name.charAt(name.length - 1));
}

function nextLayerName(name){
    const nr = nrFromName(name);
    return layerToName(nr + 1);
}

function nameToLayer(name){
    if(name.includes("start")){
        return 0;
    }
    if(name.includes("afterStart")){
        return 1;
    }
    if(name.includes("beforeFinish")){
        return 2;
    }
    if(name.includes("Finish")){
        return 3;
    }
    return -1;
}

function layerToName(layer){
    switch(layer){
        case 0: return "start";
        case 1: return "afterStart";
        case 2: return "beforeFinish";
        case 3: return "finish";
    }
    
}

function convertName(name){
    if(name.toLowerCase().includes("afterstart")){
        return "Nach Start";
    }
    if(name.toLowerCase().includes("beforefinish")){
        return "Eingang Zielgrade";
    }
    if(name.toLowerCase().includes("finish")){
        return "Zieleinlauf";
    }
    return "--";
}