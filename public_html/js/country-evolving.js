$(()=>{
    createDiagram();
});

let canvas;
let mouseDown = false;

function createDiagram(){
    $(`.country-evolving`).append(`<canvas width="900" height="400"></canvas>`);
    canvas = document.querySelector(".country-evolving canvas");
    $("select[name=search]").change(select);
    $(canvas).on("mousedown",()=>{mouseDown = true; updateDiagram()});
    $(canvas).on("mouseup",()=>{mouseDown = false; updateDiagram()});
    window.onscroll=function(){ reOffset(); }
    window.onresize=function(){ reOffset(); }
    $(canvas).mousemove(function(e){handleMouseMove(e);});
    $(canvas).mouseenter(function(){mouseInside=true;});
    $(canvas).mouseleave(function(){mouseInside=false;updateDiagram();});
    $(".reset-scale").click(()=>{maxScore = getMaxScore() * 1.2; updateDiagram()});
    $(".view-all").click(reset);
    reOffset();
    updateDiagram();
}

function reset(){
    maxScore = getMaxScore() * 1.2;
    hoverCountry = null;
    searchCountry = undefined;
    updateDiagram();
}

function select(){
    const countryName = $(this).val();
    for (const country of countryScores) {
        if(country.country == countryName){
            searchCountry = country;
            maxScore = getMaxScoreFromCountry(country) * 1.3;
            updateDiagram();
            return;
        }
    }
    searchCountry = undefined;
    updateDiagram();
}

let offsetX,offsetY;

function reOffset(){
    if(canvas != null){
        canvas.width = document.querySelector("main").offsetWidth - 50;
        var bb=canvas.getBoundingClientRect();
        offsetX=bb.left;
        offsetY=bb.top;
        updateDiagram();
    }
}

const background = "#333";
const circleRaduis = 5;
const lineWidth = 3;

let searchCountry;

let maxScore = getMaxScore() + 10;

function updateDiagram(){
    const ctx = canvas.getContext('2d');
    ctx.font = "1.2rem Montserrat";
    const yearAmount = countryScores[0].scores.length;
    const height = canvas.height;
    const width = canvas.width;
    ctx.fillStyle = background;
    ctx.fillRect(0,0,width, height);
    
    let hoverX;
    let hoverY;
    let hoverDistance = 50;
    let hoverCountry;
    let hoverScore;
    for (const country of countryScores) {
        if(searchCountry){
            if(country != searchCountry){
                continue;
            }
            hoverCountry = searchCountry;
        }
        let year = 0;
        for (const score of country.scores) {
            
            const x = (year / yearAmount) * width;
            const y = height - ((score / maxScore) * height);
            if(mouseInside){
                if(mouseDistanceFrom(x, y) < hoverDistance){
                    hoverDistance = mouseDistanceFrom(x, y);
                    hoverCountry = country;
                    hoverX = x;
                    hoverY = y;
                    hoverScore = score;
                }
            }
            year++;
        }
    }

    for (let year = 2007; year < 2020; year++) {
        const x = ((year - 2007) / yearAmount) * width;
        ctx.fillStyle = "#FFF";
        ctx.fillText(year + "", x - 20, 20);
        ctx.fillStyle = "#AAA";
        ctx.fillRect(x, 0, 1, height);
    }

    for (const country of countryScores) {
        if(searchCountry){
            if(country != searchCountry){
                continue;
            }
        }
        ctx.filter = "opacity(1)";
        if(hoverCountry === country){
            ctx.fillStyle = "white";
            ctx.strokeStyle = "white";
        } else if(hoverCountry){
            ctx.filter = "opacity(0.3)";
            ctx.fillStyle = countryToColor(country);
            ctx.strokeStyle = countryToColor(country);
        } else{
            ctx.fillStyle = countryToColor(country);
            ctx.strokeStyle = countryToColor(country);
        }
        if(searchCountry === country){
            ctx.filter = "opacity(1)";
            ctx.fillStyle = "red";
            ctx.strokeStyle = "red";
        }
        
        ctx.beginPath();
        let year = 0;
        ctx.moveTo(0, height);
        for (const score of country.scores) {
            const x = (year / yearAmount) * width;
            const y = height - ((score / maxScore) * height);
            ctx.lineTo(x,y);
            ctx.arc(x,y, circleRaduis, 0, 2 * Math.PI, false);
            year++;
        }
        ctx.lineWidth = lineWidth;
        ctx.stroke();
    }
    ctx.filter = "opacity(1)";
    ctx.fillStyle = "white";
    if(hoverCountry != null){
        if(mouseDown){
            searchCountry = hoverCountry;
            maxScore = getMaxScoreFromCountry(hoverCountry) * 1.3;
            mouseDown = false;
            updateDiagram();
        }
        ctx.fillText(hoverCountry.country + ": " + hoverScore, Math.min(width - 150, hoverX + 10), hoverY - 5);
    }
}

function mouseDistanceFrom(x1,y1){
    let x = mouseX - x1;
    let y = mouseY - y1;
    return Math.sqrt(x * x + y * y)
}

const colors =  new Map();
function countryToColor(country){
    if(colors.has(country)){
        return colors.get(country);
    } else{
        let color = getRandomColor();
        colors.set(country, color);
        return color;
    }
}

function getMaxScore(){
    let max = 0;
    for (const country of countryScores) {
        for (const score of country.scores) {
            if(parseInt(score) > max){
                max = parseInt(score);
            }
        }
    }
    return max;
}

function getMaxScoreFromCountry(country){
    let max = 0;
    for (const score of country.scores) {
        if(parseInt(score) > max){
            max = parseInt(score);
        }
    }
    return max;
}

function getRandomColor() {
    var letters = '0123456789ABCDEF';
    var color = '#';
    for (var i = 0; i < 6; i++) {
      color += letters[Math.floor(Math.random() * 16)];
    }
    return color;
  }

let mouseX = -1000;
let mouseY = -1000;
let mouseInside = false;
function handleMouseMove(e){
    mouseX=parseInt(e.clientX-offsetX);
    mouseY=parseInt(e.clientY-offsetY);
    updateDiagram();
}