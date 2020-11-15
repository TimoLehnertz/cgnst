"use strict";
let kalender;
    let kalender__header;
    let kalender__main;
        let kalender__body;
        let kalender__aside;

let currentDate = new Date();

let daysLong = ['Sonntag', 'Montag', 'Dienstag', 'Mittwoch', 'Donnerstag', 'Freitag', 'Samstag'];
let daysShort = ['So', 'Mo', 'Di', 'Mi', 'Do', 'FR', 'SA'];

let monthsLong = ['Januar', 'Februar', 'März', 'April', 'Mai', 'Juni', 'Juli', 'August', 'September', 'Oktober', 'November', 'Dezember'];

let currentElements = [];

let infoElementVisible = false;

let infoElementEntry;
let infoElementEntryElement;

let entries = [];

$(function(){
    kalender = $(".kalender");
    kalender.append(getDateSelectorElement());
    kalender.append(getTimeSelectorElement());

    kalender.append(kalender__header_Html());
    kalender__header = $(".kalender__header");
    
    kalender.append('<div class="kalender__main"></div>');
    kalender__main = $(".kalender__main");

    kalender__main.prepend(getEnterElement());

    kalender__main.append(kalender__aside_html());
    kalender__aside = $(".kalender__aside");

    kalender__main.append('<div class="kalender__body"></div>');
    kalender__body = $(".kalender__body");


    $(".kalender__header__month__forewards").click(function(){turnPage(true)});
    $(".kalender__header__month__backwards").click(function(){turnPage(false)});

    $(".kalender__view-dropdown").change(()=>{reloadPage(); $(".kalender__view-dropdown").blur()});

    $(".kalender__burger-label").click(()=>{
        $(".kalender__aside").toggleClass("kalender__aside--hidden");
        $(".kalender__enter").toggleClass("kalender__enter--bigger")
    });
    kalender__body.append(getMonthElement(currentDate), true);

    crateEntryInfoElement();
    crateInfoMoreOptionsElement();

    $(".kalender__enter.kalender__enter--minimized").click((e)=>{
        maximizeEnter();
        e.stopPropagation();
    });
    $(".kalender__enter__close-btn").click((e)=>{minimizeEnter(); e.stopPropagation();});
    $(".kalender").click(()=>{hideInfoElement(); minimizeEnter();hideDateSelector();hideTimeSelector();hideMoreOptionsElement();});
    $(window).keyup(keyUpHandler);
    $(window).resize(kalenderResize);
    loadEntries();
    loadGroups();
    hideMoreOptionsElement();
    loadTrainingsBlueprints();
    reloadPage();
});
// Colors

const colorTraining = "green";
const colorWettkampf = "red";
const colorNote = "orange";
const colorTrainingslager = "gray";

let enableddGroups = [];

let groups;
let trainingsBlueprints;

const interval = window.setInterval(function(){
    if(groups != undefined && trainingsBlueprints != undefined){
        ajaxDataLoaded();
        clearInterval(interval);
    }
}, 100);

function loadEntries(callback){
    $.ajax({
        url: "/kalender/kalenderAPI.php?getEntries=1",
        success: function(result){
            entries = prepareEntries(JSON.parse(result));
            if(callback){
                callback();
            }
        }
    });
}

function prepareEntries(entries){
    for (const entry of entries) {
        entry.startDate = new Date(entry.startDate * 1000);
        entry.endDate = new Date(entry.endDate * 1000);
    }
    calcEntriesOffset(entries);
    return entries;
}

function loadGroups(){
    $.ajax({
        url: "/users/userAPI.php?getGroupList=1",
        success: function(result){
            if(result.includes("Error message")){
            } else{
                groups = JSON.parse(result)
            }
        }
    });
}

function loadTrainingsBlueprints(){
    $.ajax({
        url: "/training/trainingsAPI.php?getAvailableTrainingsBlueprints=1",
        success: function(result){
            if(result.includes("Error message")){
                trainingsBlueprints = [];
            } else{
                trainingsBlueprints = JSON.parse(result);
            }
        }
    });
}

function getColorOfEntryType(type){
    switch(type){
        case "training": return colorTraining;
        case "wettkampf": return colorWettkampf;
        case "trainingslager": return colorTrainingslager;
        case "note": return colorNote;
        default: return "black";
    }
}

let hasBeenMaximized = false;

let enterMaximized = false;

function maximizeEnter(date){
    if(date != undefined){
        setEnterDate(date);
    }
    enterMaximized = true;
    hideMoreOptionsElement();
    hideInfoElement();
    if(userLoggedIn){
        $(".kalender__enter").removeClass("kalender__enter--minimized");
        $(".kalender__enter").addClass("kalender__enter--maximized");
        if(!hasBeenMaximized){
            window.setTimeout(()=>{
                loadEnterChoice($(".kalender__enter-choice")[0]);
            },200);
        }
        hasBeenMaximized = true;
    } else{
        alert("Loggen sie sich ein, um Termine einzutragen");
    }
}

function setEnterDate(date){
    setDateForDateSelectorElement($(".kalender__date-selector__date"), date)
}

function minimizeEnter(){
    enterMaximized = false;
    hideDateSelector();
    $(".kalender__enter").addClass("kalender__enter--minimized");
    $(".kalender__enter").removeClass("kalender__enter--maximized");
    $(".kalender__enter").scrollTop(0);
}

function keyUpHandler(e){
    if(e.keyCode == 39){//arrow Right
        turnPage(true);
    } else if(e.keyCode == 37){
        turnPage(false);
    }
}

// function getEntrys(){
//     return [{
//         name: "Geisingen Kader1",
//         startDate: new Date("10/3/2020"),
//         endDate: new Date("11/2/2020"),
//         initiator: "CST1",//ToDO
//         type: "training",
//         color: "red"
//     },{
//         name: "Geisingen Kader2",
//         startDate: new Date("10/25/2020"),
//         endDate: new Date("10/23/2020"),
//         initiator: "CST",//ToDO
//         type: "training"
//     },{
//         name: "Geisingen Kader3",
//         startDate: new Date("10/12/2020"),
//         endDate: new Date("10/12/2020"),
//         initiator: "CST",//ToDO
//         type: "training",
//         color: "pink"
//     },{
//         name: "Geisingen Kader4",
//         startDate: new Date("10/2/2020"),
//         endDate: new Date("10/11/2020"),
//         initiator: "CST",//ToDO
//         type: "training",
//         color: "green"
//     }];
// }

function kalenderResize(){
    if(infoElementEntry != undefined && infoElementEntryElement != undefined && infoElementVisible){
        blendInfoElementIn(infoElementEntry, infoElementEntryElement);
    }
}

function calcEntriesOffset(entries){
    entries.sort((a, b) => {return (getLastDate(b.endDate, b.startDate) - getFirstDate(b.endDate, b.startDate)) - (getLastDate(a.endDate, a.startDate) - getFirstDate(a.endDate, a.startDate))});
    for (const entry of entries) {
        let date = new Date(getFirstDate(entry.startDate, entry.endDate));
        let offset = 0;
        let encountered = [];
        for (let i = 0; i < Math.abs(getDayDifference(entry.startDate, entry.endDate)); i++) {
            for (const i of entries) {
                if(!encountered.includes(i) && dateInRange(date, i.startDate, i.endDate) && entries.indexOf(i) < entries.indexOf(entry)){
                    encountered.push(i);
                    offset++;
                }
            }
            date.setDate(date.getDate() + 1);
        }
        entry["offset"] = offset;
    }
    return entries;
}

function dateInRange(check, from, to){
    let tmp = from;
    from = getFirstDate(from, to);
    to = getLastDate(tmp, to);
    return check >= from && check <= to;
}

function addEntries(entries){
    for (const entry of entries) {
        if(isEntryEnabledByGroup(entry)){
            insertEntry(entry);
        }
    }
}

function removeAllEntries(){
    $("kalender__entry").remove();
}

function isEntryEnabledByGroup(entry){
    for (const group of entry.groups) {
        if(enableddGroups.indexOf(group) != -1){
            return true;
        }
    }
    return false;
}

function insertEntry(entry){
    const difference = getDayDifference(entry.startDate, entry.endDate);
    let date = new Date(getFirstDate(entry.startDate, entry.endDate));
    for (let i = 0; i < Math.abs(difference) + 1; i++) {
        const element = getElementToDate(date);
        if(element != undefined){
            const entryElement = getEntryElement(entry, date, $(".kalender__view-dropdown").val())
            if(entry.offset != undefined){
                let existing = element.find(".kalender__entry").length;
                for (let index = existing; index < entry.offset; index++) {
                    element.append(`<div class="kalender__entry kalender__entry--placeholder"></div>`);
                }
            }
            element.append(entryElement);
        }
        date.setDate(date.getDate() + 1);
        if(compareDatesDayly(date, getIncrementedDate(getLastDate(entry.startDate, entry.endDate)))){
            break;
        }
    }
}

function getIncrementedDate(date){
    let cpy = new Date(date);
    cpy.setDate(cpy.getDate() + 1);
    return cpy;
}

function getFirstDate(dateA, dateB){
    return dateA > dateB ? dateB : dateA;
}

function getLastDate(dateA, dateB){
    return dateA < dateB ? dateB : dateA;
}

function getDayDifference(dateB, dateA){
    let difference = dateA.getTime() - dateB.getTime();
    return Math.ceil(difference / (1000 * 3600 * 24));
}

function getEntryElement(entry, date, kalenderView){
    const showName = kalenderView == "day" || compareDatesDayly(date, getFirstDate(entry.startDate, entry.endDate)) || date.getDay() == 1;
    const startCap = kalenderView == "day" || compareDatesDayly(date, getFirstDate(entry.startDate, entry.endDate));
    const endCap = kalenderView == "day" || compareDatesDayly(date, getLastDate(entry.startDate, entry.endDate));
    let color = getColorOfEntryType(entry.type);
    const entryElement = $(`<div class="kalender__entry${startCap ? " kalender__entry--start-cap" : ""}${endCap ? " kalender__entry--end-cap" : ""}" style="background-color: ${color};">
        ${ showName ? `<div class="kalender__entry__name">${entry.name}</div>` : ""}
    </div>`);
    entryElement.click((e)=>{blendInfoElementIn(entry, entryElement); e.stopPropagation()});
    return entryElement;
}

function crateEntryInfoElement(){
    $("main").append($(`<div class="kalender__entry-info">
        <div class="kalender__entry-info__header">
            <div class="participate">
                <button class="remember-btn">Merken</button>
                <button class="participate-btn">Teilnehmen</button>
                <button class="no-participate-btn">Nichtmehr teilnehmen</button>
            </div>
            <div class="kalender__more-options kalender_interactive-shadow">
                <div class="kalender__more-optios__dot"></div>
                <div class="kalender__more-optios__dot"></div>
                <div class="kalender__more-optios__dot"></div>
            </div>
            <button class="kalender__entry-info__close-button kalender_interactive-shadow">X</button>
        </div>
        <div class="kalender__entry__body">
            <div class="kalender__entry-info__color"></div>
            <div class="kalender__entry-info__name"></div>
            <div class="kalender__entry-info__date"></div>
            <button class="entry-expand-btn kalender_interactive-shadow"><i class="fas fa-chevron-down"></i></button>
            <div class="kalender__entry-info__content">
                <div><i class="fas fa-user-alt"></i><div class="initiator">Initiator: <span class="kalender__entry-info__initiator"></span></div></div>
                <div><i class="fas fa-users"></i></i><div class="groups"></div></div>
                <div><i class="far fa-comment"></i><div class="comment"></div></div>
            </div>
        </div>
    </div>`));
    $(".no-participate-btn").click(function(){
        $(this).empty();
        $(this).append(getLoadingCircle());
        setParticipating(0);
    });
    $(".remember-btn").click(function(){
        $(this).empty();
        $(this).append(getLoadingCircle());
        setParticipating(1);
    });
    $(".participate-btn").click(function(){
        $(this).empty();
        $(this).append(getLoadingCircle());
        setParticipating(2);
    });
    $(".kalender__entry-info__close-button").click((e)=>{hideInfoElement(); hideMoreOptionsElement(); e.stopPropagation();});
    $(".kalender__entry-info").click((e)=>{e.stopPropagation();hideMoreOptionsElement();});
    $(".entry-expand-btn").click(function(){
        toggleMaximizeEntryinfo()
    });
    $(".kalender__more-options").click((e)=>{
        const rect = $(".kalender__more-options").offset();
        moreOptionsElemeAt(rect.left, rect.top + 25);
        e.stopPropagation();
    })
}
// 0 not
// 1 remember
// 2 participating
function setParticipating(state){
    $.ajax({
        type: "GET",
        url: '/kalender/kalenderAPI.php?setParticipating=' + state + "&idtraining=" + infoElementEntry.refId.split(":")[1],
        dataType: 'text',
        success: function (response) {
            if(response.includes("Error")){
                console.log(response);
            } else{
                if(state == 0){$(".no-participate-btn").empty().append(`<i class="fas fa-check"></i>`);}
                if(state == 2){$(".participate-btn").empty().append(`<i class="fas fa-check"></i>`);}
                if(state == 1){$(".remember-btn").empty().append(`<i class="fas fa-check"></i>`);}
                window.setTimeout(()=>{setParticipatingState(state)}, 500);
            }
            loadEntries();
        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.log(jqXHR.status);
            console.log(textStatus);
            console.log(errorThrown);
        }
    });
}



function toggleMaximizeEntryinfo(){
    if($(".entry-expand-btn").hasClass("entry-expand-btn--expanded")){
        minimizeEntryInfo();
    } else{
        maximizeEntryInfo();
    }
}

function maximizeEntryInfo(){
    $(".entry-expand-btn").addClass("entry-expand-btn--expanded");
    $(".kalender__entry-info__content").addClass("kalender__entry-info__content--expanded");
    $(".kalender__entry-info").animate({
        top: Math.min(lastEntryElement.offset().top, window.innerHeight - $(".kalender__entry-info").height() - 260)
    }, 200);
}

function minimizeEntryInfo(){
    $(".entry-expand-btn").removeClass("entry-expand-btn--expanded");
    $(".kalender__entry-info__content").removeClass("kalender__entry-info__content--expanded");
    $(".kalender__entry-info").animate({
        top: Math.min(lastEntryElement.offset().top, window.innerHeight - 200)
    }, 200);
}

function crateInfoMoreOptionsElement(){
    $("main").append($(`<div class="more-options kalender_interactive-shadow">
        <div class="more-options__delete option" style="color: firebrick;">Löschen</div>
    </div>`));
    $(".more-options__delete").click((e)=>{
        if(lastClickedEntry != undefined){
            deleteEntry(lastClickedEntry);
        }
    });
}

function deleteEntry(entry){
    $(".more-options__delete").html("");
    $(".more-options__delete").append(getLoadingCircle());
    $.ajax({
        type: "POST",
        url: '/kalender/kalenderAPI.php?deleteEntry=1',
        dataType: 'text',
        data: JSON.stringify(entry),
        success: function (response) {
            $(".more-options__delete").html('<i class="fas fa-check"></i>');
            window.setTimeout(()=>{
                $(".more-options__delete").html('Löschen');
            },1000);
            window.setTimeout(()=>{
                hideMoreOptionsElement();
                hideInfoElement();
            },300);
            loadEntries(reloadPage)
        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.log(jqXHR.status);
            console.log(textStatus);
            console.log(errorThrown);
            $(".more-options__delete").html('Löschen');
        }
    });
}

function hideMoreOptionsElement(){
    $(".more-options").animate({
        left: "+=100",
        opacity: 0
    }, 100, function(){
        $(this).css("display", "none");
    });
}

function moreOptionsElemeAt(xPos, yPos){
    $(".more-options").css("display", "block");
    $(".more-options").css("opacity", 0);
    $(".more-options").css("left", xPos - 30);
    $(".more-options").css("top", yPos);
    $(".more-options").animate({
        left: xPos,
        opacity: 1
    }, 60, "swing");
}

function getParticipatingState(entry){
    if(entry.type == "training"){
        for (const i of entry.participating) {
            if(i.username == username){
                return i.participates + 1;
            }
        }
    }
    return 0;
}

let lastClickedEntry;

let lastEntryElement;

function setParticipatingState(state){
    if(state == 0){
        $(".no-participate-btn").hide();
        $(".remember-btn").show();
        $(".participate-btn").show();
        $(".participate-btn").html("Teilnehmen");
        $(".remember-btn").html("Merken");
    } else if(state == 1){
        $(".no-participate-btn").show();
        $(".participate-btn").show();
        $(".remember-btn").hide();
        $(".participate-btn").html("Teilnehmen");
        $(".no-participate-btn").html("Nicht mehr merken");
    }
    else if(state == 2){
        $(".no-participate-btn").show();
        $(".participate-btn").hide();
        $(".remember-btn").hide();
        $(".no-participate-btn").html("Nicht mehr Teilnehmen");
    }
}

function hideParticipating(){
    $(".no-participate-btn").hide();
    $(".participate-btn").hide();
    $(".remember-btn").hide();
}

function dateInPast(date){
    return date < new Date() && !compareDatesDayly(date, new Date());
}

function blendInfoElementIn(entry, entryElement){
    if(enterMaximized){
        return;
    }
    lastEntryElement = entryElement;
    minimizeEntryInfo();
    lastClickedEntry = entry;
    infoElementEntry = entry;
    infoElementEntryElement = entryElement;
    const participatingState = getParticipatingState(entry);
    setParticipatingState(participatingState)
    if(dateInPast(entry.startDate)){
        hideParticipating();
    }
    $(".kalender__entry-info").stop(true, false);
    $(".kalender__entry-info").find(".participate").hide();
    if(entry.type == "training"){
        $(".kalender__entry-info").find(".participate").show();
    }
    const width = 400;
    var rect = entryElement.offset();
    let right = window.innerWidth - (rect.left);
    const fromRight = rect.left + (entryElement.width() / 2) < window.innerWidth / 2;
    if(fromRight){
        right = right - entryElement.width() - 70 - width;
    } if($(".kalender__view-dropdown").val() == "day"){
        right = (window.innerWidth / 2) - width / 2;
    }
    let top = Math.min(rect.top, window.innerHeight - 200);
    $(".kalender__entry-info").css("display", "block");
    $(".kalender__entry-info__name").text(entry.name);
    $(".kalender__entry-info__date").text(getStartEndString(entry.startDate, entry.endDate));
    $(".kalender__entry-info__initiator").text(entry.initiator);
    $(".comment").text(entry.comment);
    $(".groups").text(entry.groups.toString());
    $(".kalender__entry-info__color").css("background-color", $(entryElement).css("background-color"));
    let duration = 300;
    if(!infoElementVisible){
        $(".kalender__entry-info").css("right", right + (fromRight ? 20 : -20));
        $(".kalender__entry-info").css("top", top);
        duration = 200;
    }
    $(".kalender__entry-info").animate({
        opacity: 1,
        right: right,
        top: top
      }, duration);
      infoElementVisible = true;
}

function hideInfoElement(){
    infoElementVisible = false;
    $(".kalender__entry-info").animate({
        opacity: 0
    }, 100, function(){
        $(".kalender__entry-info").css("display", "none");
    });
}

function getStartEndString(from, to){
    let tmp = new Date(from);
    from = getFirstDate(from, to);
    to = getLastDate(tmp, to);
    let out = "";
    if(from.getMonth() == to.getMonth()){
        if(compareDatesDayly(from, to)){
            out = to.getDate() + " " + monthsLong[from.getMonth()];
        } else{
            out = from.getDate() + ". - " + to.getDate() + " " + monthsLong[from.getMonth()]
        }
    }else{
        out = from.getDate() + ". " + monthsLong[from.getMonth()] + " - " + to.getDate() + ". " + monthsLong[to.getMonth()];
    }
    return out + " " + (to.getYear() + 1900);
}

function getElementToDate(date){
    for (const element of currentElements) {
        if(compareDatesDayly(element.date, date)){
            return element.element;
        }
    }
}

function getDateSelectorDateToElement(dayElement){
    for (const element of dateRegisterSelectorField) {
        if($(dayElement).is($(element.element))){
            return element.date;
        }
    }
}

function compareDatesDayly(dateA, dateB){
    const success = dateA.getDate() == dateB.getDate() && dateA.getMonth() == dateB.getMonth() && dateA.getYear() == dateB.getYear();
    return success;
}

function reloadPage(){
    currentElements = [];
    $(".kalender__body > div").animate({
        left: '100px',
        opacity: 0,
    }, 100, "swing", function() {
        $(this).remove();
    });
    setCurrentDate(currentDate)
    const nextPage = getPage(currentDate);
    $(".kalender__body").append(nextPage).show("fast");
    removeAllEntries();
    addEntries(calcEntriesOffset(entries));
}

function turnPage(forewards){
    currentElements = [];
    $(".kalender__body > div").animate({
        left: (forewards ? "-80%" : "80%"), // animate slideUp
        opacity: 0
    }, 200, function() {
        $(this).remove();
    });
    turnCurrentDate(forewards);
    const nextPage = getPage(currentDate);
    $(".kalender__body").append(nextPage);
    nextPage.css("left" , (forewards ?  "80%" : "-80%"));
    nextPage.css("opacity" , 0);
    nextPage.animate({
        left: 0,
        opacity: 1
    }, 200);
    addEntries(calcEntriesOffset(entries));
}

function turnCurrentDate(forewards){
    switch($(".kalender__view-dropdown").val()){
        case "day": currentDate.setDate(currentDate.getDate() + (forewards ? 1 : -1)); break;
        case "week": currentDate.setDate(currentDate.getDate() + (forewards ? 7 : -7)); break;
        case "month": currentDate.setMonth(currentDate.getMonth() + (forewards ? 1 : -1)); break;
    }
    setCurrentDate(currentDate);
}

function setCurrentDate(date){
    switch($(".kalender__view-dropdown").val()){
        case "day": $(".kalender__header__current-month").text(date.getDate() + "." + monthsLong[date.getMonth()] + " " + (date.getYear() + 1900));break;
        case "week": $(".kalender__header__current-month").text(monthsLong[date.getMonth()] + " " + (date.getYear() + 1900));break;
        case "month": $(".kalender__header__current-month").text(monthsLong[date.getMonth()] + " " + (date.getYear() + 1900));break;
    }
}

function getDateStringAll(date){
    return date.getDate() + "." + monthsLong[date.getMonth()] + " " + (date.getYear() + 1900);
}

function getDateStringAllDay(date){
    return daysLong[date.getDay()] + ", " + date.getDate() + "." + monthsLong[date.getMonth()] + " " + (date.getYear() + 1900);
}

function getTimeString(date){
    return date.getHours() + ":" + (date.getMinutes() < 10 ? "0" + date.getMinutes() : date.getMinutes());
}

function getDateStringMonth(date){
    return monthsLong[date.getMonth()] + " " + (date.getYear() + 1900);
}

function getPage(date){
    switch($(".kalender__view-dropdown").val()){
        case "day": return getDayElement(date, null, true);
        case "week": return getWeekElement(date, null, true);
        case "month": return getMonthElement(date, true);
    }
}

function kalender__header_Html(){
    const html = `<div class="kalender__header">
        <label class="kalender__burger-label" for="kalender__burger-input">
            <span class="kalender__burger-line"></span>
            <span class="kalender__burger-line"></span>
            <span class="kalender__burger-line"></span>
        </label>
        <input type="checkbox" id="kalender__burger-input">
        <div class="kalender__header__month">
            <button type="button" class="kalender__header__month__backwards kalender_interactive-shadow"><</button>
            <button type="button" class="kalender__header__month__forewards kalender_interactive-shadow">></button>
            <span class="kalender__header__current-month">Januar 2020</span>
        </div>
        <select name="kalender-view" class="kalender__view-dropdown kalender_interactive-shadow">
            <option value="day">Tag</option>
            <option value="week">Woche</option>
            <option value="month" selected>Monat</option>
        </select>
    </div>`;
    return html;
}

function getEnterElement(){
    const enterElement = $(`
    <div class="kalender__enter kalender__enter--bigger kalender__enter--minimized">
        <img class="kalender__enter__img" alt="Eintragen" src="/img/plus.svg">
        <span class="kalender__enter__text">Eintragen</span>
        <button class="kalender__enter__close-btn kalender_interactive-shadow">X</button>
        <div class="kalender__enter__header">
            <div class="kalender__enter__choices">
                <div class="kalender__enter-choice" color="${colorTraining}"><span class="kalender__enter-choice__name">Training</span><i class="fas fa-dumbbell"></i></i></div>
                <div class="kalender__enter-choice" color="${colorWettkampf}"><span class="kalender__enter-choice__name">Wettkampf</span><i class="fas fa-flag-checkered"></i></i></i></div>
                <div class="kalender__enter-choice" color="${colorTrainingslager}"><span class="kalender__enter-choice__name">Trainingslager</span><i class="fas fa-campground"></i></div>
                <div class="kalender__enter-choice" color="${colorNote}"><span class="kalender__enter-choice__name">Hinweis</span><i class="fas fa-bookmark"></i></div>
            </div>
        </div>
        <div class="kalender__enter__content"></div>
    </div>`);
    enterElement.find(".kalender__enter__choices").append(`<div class="kalender__enter-chosen"></div>`);
    enterElement.find(".kalender__enter-choice").click(function(){
        loadEnterChoice(this);
    });
    enterElement.find(".kalender__enter-choice").each((i, e)=>{
        $(e).css("color", $(e).attr("color"));
    })
    enterElement.click(()=>{hideDateSelector(); hideTimeSelector()});
    return enterElement;
}

function loadEnterChoice(element){
    if($(".kalender__enter-chosen").position().left == $(element).position().left){
        return;
    }
    const toRight = $(".kalender__enter-chosen").position().left > $(element).position().left;
    $(".kalender__enter-chosen").animate({
        left: $(element).position().left,
        width: $(element).width() + ($(element).css("padding-left").split("px")[0] * 2)
    }, 100, "swing");
    $(window).resize(()=>{
        window.setTimeout(()=>{
            $(".kalender__enter-chosen").css("left", $(element).position().left);
            $(".kalender__enter-chosen").css("width", $(element).width() + ($(element).css("padding-left").split("px")[0] * 2));
        }, 400);
    });
    $(".kalender__enter-choice").each((i, e)=>{$(e).css("color", $(e).attr("color"));})//resetting
    $(element).css("color", "white");
    $(".kalender__enter-chosen").css("background-color", $(element).attr("color"));
    $(".kalender__enter__choices").css("border-color", $(element).attr("color"));
    switch($(element).find(".kalender__enter-choice__name").text()){
        case "Training": changeEnterContent(enterTrainingElement, toRight); break;
        case "Wettkampf": changeEnterContent(enterWettkampfElement, toRight); break;
        case "Trainingslager": changeEnterContent(enterTrainingslagerElement, toRight); break;
        case "Hinweis": changeEnterContent(enterAndereElement,toRight); break;
    }
}

function changeEnterContent(newElement, toRight){
    toRight = true;
    if(!(newElement.attr("visible") == "true")){
        $('.kalender__enter__content > div[visible="true"]').animate({
            left: toRight ? "50%" : "-50%",
            opacity: 0
        }, 200, "swing", function(){
            $(this).css("display", "none");
            $(this).attr("visible", false);
        });
        $(".kalender__enter__content").append(newElement);
        newElement.attr("visible", true);
        newElement.css("left", toRight ? "-50%" : "50%")
        newElement.css("opacity", 0)
        newElement.css("display", "block")
        newElement.animate({
            left: 0,
            opacity: 1
        }, 200, "swing");
        newElement.find(".kalender__termin__name__input").focus();
        initGroupSelectors();
        initTrainingsBlueprintSelectors();
    }
}

function ajaxDataLoaded(){
    initGroupSelectors();
    initTrainingsBlueprintSelectors();
    enableddGroups = getGroupNames(false, false);
    reloadPage();
}

function initGroupSelectors(){
    $(".group-select__groups").each(function(){
        if($(this).children().length > 0){return;}
        const initialSelect = $(this).parent().attr("initialSelect") == "true";
        const onlyAdmin = $(this).attr("onlyAdmin") == "true";
        const noDefault = $(this).attr("nodefault") == "true";
        const groupNames = getGroupNames(onlyAdmin, noDefault);
        let callback = undefined;
        for (const groupSelectCallback of groupSelectCallbacks) {
            if($(groupSelectCallback.elem).is($(this))){
                callback = groupSelectCallback.callback;
                break;
            }
        }
        for (const grpName of groupNames) {
            $(this).prepend(getGroupsElement(grpName, initialSelect, callback));
        }
    });
}

function initTrainingsBlueprintSelectors(){
    $(".trainings-blueprint__blueprints").each(function(){
        if($(this).find(".trainings-blueprint").length > 0){return;}
        const blueprintNames = getBlueprintName();
        for (const blueprintName of blueprintNames) {
            $(this).prepend(getBlueprintElement(blueprintName));
        }
    });
}

function getGroupsElement(grpName, selected, callback){
    const ranId = Math.random();
    const elem = $(`<div class="group-select__group kalender_interactive-shadow">
        <input id="${ranId}" type="checkbox" class="group-select__group-check" ${selected ? "checked" : ""}>
        <label for="${ranId}" class="group-select__group-name">${grpName}</label>
    </div>`);
    elem.find(".group-select__group-check").change(function(){
        const name = $(this).parent().find(".group-select__group-name").text();
        if($(this).prop("checked")){
            elem.addClass("group-select__group--checked");
            if(callback != undefined){
                callback(name, true);
            }
        } else{
            elem.removeClass("group-select__group--checked")
            if(callback != undefined){
                callback(name, false);
            }
        }
    });
    if(elem.find(".group-select__group-check").prop("checked")){
        elem.addClass("group-select__group--checked")
    } else{
        elem.removeClass("group-select__group--checked")
    }
    return elem;
}

function getBlueprintElement(blueprintName){
    const elem = $(`<div class="trainings-blueprint kalender_interactive-shadow">
        <div class="trainings-blueprint__name">${blueprintName}</div>
    </div>`);
    elem.click(function(){
        const hadClass = $(this).hasClass("blueprint--checked");
        $(".trainings-blueprint").removeClass("blueprint--checked");
        if(hadClass){
            elem.removeClass("blueprint--checked");
        }else{
            elem.addClass("blueprint--checked");
        }
    });
    return elem;
}

function getIdBlueprint(name){
    if(trainingsBlueprints != undefined){
        for (const blueprint of trainingsBlueprints) {
            if(blueprint.name == name){
                return blueprint.idtrainingsBlueprint;
            }
        }
    }
    return "";
}

function getPropertyJson(propertieElem){   
    const entryType = propertieElem.attr("entryType");
    const title = propertieElem.find(".kalender__termin__name__input").val();
    const startDate = new Date(parseInt($(propertieElem.find(".kalender__date-selector")[0]).attr("date")));
    let endDate = new Date(parseInt($(propertieElem.find(".kalender__date-selector")[1]).attr("date")));
    const startTime = new Date(parseInt($(propertieElem.find(".kalender__time-selector")[0]).attr("time")))
    const endTime = new Date(parseInt($(propertieElem.find(".kalender__time-selector")[1]).attr("time")))
    let idTrainingsBlueprint = getIdBlueprint(propertieElem.find(".blueprint--checked .trainings-blueprint__name").text());
    const comment = propertieElem.find(".comment-input").val();
    const groups = [];


    const trainer = [];

    let trainerAttr = [];
    if(entryType == "training"){
        trainerAttr = propertieElem.find(".trainer").attr("trainer").split(",");
    }

    for (const attr of trainerAttr) {
        trainer.push(parseInt(attr));
    }

    if(idTrainingsBlueprint.length == 0){
        idTrainingsBlueprint = "-";
    };

    startDate.setHours(startTime.getHours());
    startDate.setMinutes(startTime.getMinutes());

    if(isNaN(endDate.getTime())){
        endDate = new Date(startDate);
        endDate.setHours(endTime.getHours());
        endDate.setMinutes(endTime.getMinutes());
    } else{
        endDate.setHours(endTime.getHours());
        endDate.setMinutes(endTime.getMinutes());
    }

    propertieElem.find(".group-select__group--checked").each(function(){
        groups.push($(this).find(".group-select__group-name").text());
    });

    const json = {
        entryType: entryType,
        title: title,
        startDate: startDate,
        endDate: endDate,
        idTrainingsBlueprint: idTrainingsBlueprint,
        groups: groups,
        comment: comment,
        idtrainingFacility: "-",
        trainer: trainer
    }
    return json;
}

function validateNewEntry(properties){
    $(".kalender__enter__properties *").removeClass("errorClass");
    let success = true;
    if(properties.title.length == 0){
        success = false;
        $(".kalender__termin__name__input").addClass("errorClass");
    }
    if(properties.entryType == "training" || properties.entryType == "andere"){
        if(properties.groups.length == 0){
            success = false;
            $(".kalender__enter__properties .group-select").addClass("errorClass");
        }
    }
    return success;
}

function submitEntry(entryProperties){
    $.ajax({
        type: "POST",
        url: '/kalender/kalenderAPI.php?submitEntry=1',
        dataType: 'text',
        data: JSON.stringify(entryProperties),
        success: function (response) {
            if(response.includes("Error")){
                $(".kalender__enter__properties .error-message").html(response);
                $(".enter__enter-btn").html("Eintragen");
            } else{
                $(".kalender__enter__properties .error-message").html("");
                $(".enter__enter-btn").html('<i class="fas fa-check"></i>');
                window.setTimeout(()=>{minimizeEnter();}, 400);
                window.setTimeout(()=>{reloadEnterContent()}, 600);
                loadEntries();
                reloadPage();
            }
        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.log(jqXHR.status);
            console.log(textStatus);
            console.log(errorThrown);
            $(".enter__enter-btn").html("Eintragen");
            $(".kalender__enter__properties .error-message").html("Der Server konnte <b>nicht</b> erreicht werden :(");
        }
    });
}

function enterTrainingslagerContent(){
    const arr = [];
    arr.push(getTerminTitelElement());
    arr.push(getDateSelector("Von"));
    arr.push(getTimeSelector("Von"));
    arr.push(getDateSelector("Bis"));
    arr.push(getTimeSelector("Bis"));
    arr.push(getCommentElem());
    arr.push(getEnterEnterSection());
    return arr;
}

const enterTrainingslagerElement = $(`<div class="kalender__enter__properties kalender__enter__trainings-lager" entryType="trainingslager"></div`);
enterTrainingslagerElement.append(enterWettkampfContent());

function enterWettkampfContent(){
    const arr = [];
    arr.push(getTerminTitelElement());
    arr.push(getDateSelector("Von"));
    arr.push(getTimeSelector("Von"));
    arr.push(getDateSelector("Bis"));
    arr.push(getTimeSelector("Bis"));
    arr.push(getCommentElem());
    arr.push(getEnterEnterSection());
    return arr;
}

const enterWettkampfElement = $(`<div class="kalender__enter__properties kalender__enter__wettkampf" entryType="wettkampf"></div`);
enterWettkampfElement.append(enterWettkampfContent());

function enterTrainingContent(){
    const arr = [];
    arr.push(getTerminTitelElement());
    arr.push(getDateSelector());
    arr.push(getTimeSelector("Von"));
    arr.push(getTimeSelector("Bis"));
    arr.push(getTrainingsBlueprintSelectElem(false));
    arr.push(getGroupSelectElem(true, false, false, "In Gruppen teilen", undefined, false));
    arr.push(getTrainerSelector());
    arr.push(getCommentElem());
    arr.push(getEnterEnterSection());
    return arr;
}

const enterTrainingElement = $(`<div class="kalender__enter__properties kalender__enter__training" entryType="training"></div`);
enterTrainingElement.append(enterTrainingContent());

function enterAndereContent(){
    const arr = [];
    arr.push(getTerminTitelElement());
    arr.push(getDateSelector("Von"));
    arr.push(getTimeSelector("Von"));
    arr.push(getTimeSelector("Bis"));
    arr.push(getDateSelector("Bis"));
    arr.push(getGroupSelectElem(true, false, false, "In Gruppen teilen", undefined, false));
    arr.push(getCommentElem());
    arr.push(getEnterEnterSection());
    return arr;
}

const enterAndereElement = $(`<div class="kalender__enter__properties kalender__enter__andere" entryType="andere"></div`);
enterAndereElement.append(enterAndereContent());

function reloadEnterContent(type){
    switch(type){
        case "training":
            enterTrainingElement.empty();
            enterTrainingElement.append(enterTrainingContent());
            break;
        case "wettkampf":
            enterWettkampfElement.empty();
            enterWettkampfElement.append(enterWettkampfContent());
            break;
        case "trainingslager":
            enterTrainingslagerElement.empty();
            enterTrainingslagerElement.append(enterWettkampfContent());
            break;
        case "andere":
            enterAndereElement.empty();
            enterAndereElement.append(enterAndereContent());
            break;
        default:
            enterTrainingslagerElement.empty();
            enterWettkampfElement.empty();
            enterTrainingElement.empty();
            enterAndereElement.empty();
        
            enterTrainingslagerElement.append(enterWettkampfContent());
            enterWettkampfElement.append(enterWettkampfContent());
            enterTrainingElement.append(enterTrainingContent());
            enterAndereElement.append(enterAndereContent());;
    }
    initGroupSelectors();
    initTrainingsBlueprintSelectors();
}

function getTrainerSelector(){
    const elem = $(`<div class="trainer kalender_interactive-shadow" trainer="">
        <div class="trainer__header"><i class="fas fa-id-card-alt"></i>Trainer hinzufügen<span class="trainer__delimiter"></span><span class="trainer__selected"></span>
    </div>
    <div class="trainer__content"></div>`);
    elem.find(".trainer__header").click(()=>{
        elem.find(".trainer__content").toggleClass("trainer__content--visible");
    })
    let trainer = [];
    elem.find(".trainer__content").append(getUserListElement(
        function(user){
            const userelem = $(`<div class="user-list__user kalender_interactive-shadow">${user.username}</div>`);
            userelem.on("click", function(){
                name = $(this).text();
                if(usernameInUsers(name, trainer)){
                    trainer.splice(indexOfusernameInUsers(name, trainer), 1);
                } else{
                    trainer.push(cloneUser(user));
                }
                updateTrainer(trainer, elem);
            })
            return userelem;
        }));
    return elem;
}

function updateTrainer(trainer, elem){
    let attrString = "";
    for (const tr of trainer) {
        attrString += tr.iduser + ",";
    }
    attrString = attrString.substring(0, attrString.length - 1);

    let nameString = "";
    for (const tr of trainer) {
        nameString += tr.username + ", ";
    }
    nameString = nameString.substring(0, nameString.length - 2);
    if(nameString.length == 0){
        elem.find(".trainer__delimiter").text("");
    } else{
        elem.find(".trainer__delimiter").text(":  ");
    }
    elem.attr("trainer", attrString);
    elem.find(".trainer__selected").text(nameString);
}

function cloneUser(user){
    return {username: user.username, iduser: user.iduser};
}

function usernameInUsers(username, users){
    for (const user of users) {
        if(username == user.username){
            return true;
        }
    }
    return false;
}

function indexOfusernameInUsers(username, users){
    let counter = 0;
    for (const user of users) {
        if(username == user.username){
            return counter;
        }
        counter++;
    }
    return -1;
}

function getCommentElem(){
    const elem = $(`<div class="comment kalender_interactive-shadow">
        </i><textarea class="comment-input" rows="4" placeholder="Kommentar"></textarea>
    </div`);
    return elem;
}

function getEnterEnterSection(){
    const elem = $(`<div>
        <div class="error-message"></div>
        <button class="enter__enter-btn">Eintragen</button>
        <button class="enter__reset-btn">Zurücksetzen</button>
    </div`);
    elem.find(".enter__reset-btn").click(function(){
        let activeEnterSection = $(this).parent().parent().attr("entrytype");
        reloadEnterContent(activeEnterSection);
    });
    elem.find(".enter__enter-btn").click(()=>{
        if(validateNewEntry(getPropertyJson(elem.parent()))){
            elem.find("button").html("");
            elem.find("button").append(getLoadingCircle());
            submitEntry(getPropertyJson(elem.parent()));
        }
    })
    return elem;
}

function getLoadingCircle(){
    const elem = $(`<div class="loading-circle"></div>`);
    return elem;
}

let groupSelectCallbacks = [];

function getGroupSelectElem(onlyAdminGroups, selected, expanded, name, callback, noDefault){
    const elem = $(`<div class="group-select" initialSelect="${selected}">
            <div class="kalender_interactive-shadow group-select__header">
                <i class="fas fa-users"></i>
                <span classs="group-select__title">${name}</span>
                <i class="far fa-caret-square-down"></i>
            </div>
            <div class="group-select__groups" onlyAdmin="${onlyAdminGroups ? "true" : "false"}" nodefault="${noDefault ? "true" : "false"}" ${expanded ? "" : 'expanded="true"'}></div>
        </div>`);
        const content = elem.find(".group-select__groups");
        elem.find(".group-select__header").click(()=>{
            updateGroupSelect(content);
            elem.find(".fa-caret-square-down").toggleClass("rotate-reverse");
        });
        updateGroupSelect(content);
    if(callback != undefined){
        groupSelectCallbacks.push({elem: elem.find(".group-select__groups"), callback: callback});
    }
    return elem;
}

function getTrainingsBlueprintSelectElem(expanded){
    const elem = $(`<div class="trainings-blueprint-select">
            <div class="kalender_interactive-shadow trainings-blueprint-select__header">
                <i class="far fa-clone"></i>
                <span classs="trainings-blueprint-select__title">Trainings Vorlage hinzufügen</span>
                <i class="far fa-caret-square-down"></i>
            </div>
            <div class="trainings-blueprint__blueprints" ${expanded ? "" : 'expanded="true"'}>
                <div><a href="/training/createTraingsblueprint.php">Neu</a></div>
            </div>
        </div>`);
        const content = elem.find(".trainings-blueprint__blueprints");
        elem.find(".trainings-blueprint-select__header").click(()=>{
            updateGroupSelect(content);
            elem.find(".fa-caret-square-down").toggleClass("rotate-reverse");
        });
        updateGroupSelect(content);
    return elem;
}


function updateGroupSelect(content){
    let expanded;
    if(content.attr("expanded")){
        content.removeAttr("expanded");
        expanded = false;
    } else{
        content.attr("expanded", "true");
        expanded = true;
    }
    if(expanded){
        content.show();
    }
    content.animate({
        height: expanded ? "100%" : "0px"
    }, 100, "swing", function(){
        if(!expanded){
            $(this).hide();
        }
    });
}

function getBlueprintName(){
    const names = [];
    if(trainingsBlueprints != undefined){
        for (const blueprint of trainingsBlueprints) {
            names.push(blueprint.name);
        }
    }
    return names;
}

function getGroupNames(onlyAdminGroups, nodefault){
    if(nodefault == undefined){
        nodefault = false;
    }
    const names = [];
    if(groups != undefined){
        for (const grpName in groups) {
            if (groups.hasOwnProperty(grpName)) {
                const group = groups[grpName];
                if(group.isDefaultGroup && nodefault){
                    continue;
                }
                if(userInGroup(username, group) || group.isDefaultGroup){
                    if(onlyAdminGroups){
                        if(userAdminInGroup(username, group)){
                            names.push(grpName);
                        }
                    }else{
                        names.push(grpName);
                    }
                }
            }
        }
    }
    return names;
}

function userInGroup(username, group){
    for (const user of group.users) {
        if(user.username == username){
            return true;
        }
    }
    return false;
}

function userAdminInGroup(username, group){
    for (const user of group.users) {
        if(user.username == username && user.isAdmin){
            return true;
        }
    }
    return false;
}

function getDateSelector(name){
    const selector = $(`<div class="kalender__date-selector kalender_interactive-shadow kalender__date-selector-${name}" date="${new Date().getTime()}">
        <span><i class="far fa-calendar-alt"></i></i>${name != undefined ? name + " " : ""} </span>
        <span class="kalender__date-selector__date">${getDateStringAllDay(new Date())}</span
    </div>`);
    selector.on("click", function(e){
        e.stopPropagation();
        const rect = selector.offset();
        dateSelectorFieldAt(rect.left, rect.top + selector.height() + 30, (date)=>{
            setDateForDateSelectorElement(selector, date);
        });
    });
    return selector;
}

function setDateForDateSelectorElement(elem, date){
    $(elem).text(getDateStringAllDay(date));
    $(elem).attr("date", date.getTime());
}

function getTerminTitelElement(){
    const elem = $(`<div class="kalender__termin__name kalender_interactive-shadow"><input class="kalender__termin__name__input" type="text" placeholder="Titel hinzufügen"></div>`)
    return elem;
}

function getTimeSelector(name){
    const selector = $(`<div class="kalender__time-selector kalender__time-selector${name != undefined ? name : ""} kalender_interactive-shadow" time="${new Date().getTime()}">
        <i class="far fa-clock"></i></i>
        <span>${name != undefined ? name + " " : ""}</span><span class="kalender__time-selector__time">${getTimeString(new Date())}</span>
        <input type="checkbox" class="kalender__time-selector__use" checked="true">
    </div>`);
    selector.on("click", function(e){
        if(selector.hasClass("kalender__time-selector--grayed-out")){
            return;
        }
        e.stopPropagation();
        const rect = selector.offset();
        timeSelectorFieldAt(rect.left, rect.top + selector.height() + 30, (date)=>{
            selector.find(".kalender__time-selector__time").text(getTimeString(date));
            selector.attr("time", date.getTime());
        }, selector.attr("time"));
    });
    selector.find('input[type="checkbox"]').on("click", function(e){
        e.stopPropagation();
        if($(this).prop("checked")){
            selector.removeClass("kalender__time-selector--grayed-out");
        } else{
            selector.addClass("kalender__time-selector--grayed-out");
        }
    })
    return selector;
}

let dateRegisterSelectorField = [];
let dateSelectorCallback;

function dateSelectorFieldAt(xPos, yPos, callback){
    hideTimeSelector();
    dateSelectorCallback = callback;
    dateRegisterSelectorField = [];
    changeDateSelectorMonth(new Date())
    $(".kalender__date-selector-field").css("top", yPos);
    $(".kalender__date-selector-field").css("left", xPos - 50);
    $(".kalender__date-selector-field").css("display", "block");
    $(".kalender__date-selector-field").animate({
        left: xPos,
        opacity: 1
    },200, "swing");
    $(".kalender__date-selector-field .kalender__day").click(function(){
        hideDateSelector();
        dateSelectorCallback(getDateSelectorDateToElement(this));
    });
}

let timeSelectorCallback;

function timeSelectorFieldAt(xPos, yPos, callback, time){
    hideDateSelector();
    const date = new Date(parseInt(time));
    $(".kalender__time-selector-field__hours").val(date.getHours());
    $(".kalender__time-selector-field__minutes").val(date.getMinutes());
    timeSelectorCallback = callback;
    $(".kalender__time-selector-field").css("top", yPos);
    $(".kalender__time-selector-field").css("left", xPos - 50);
    $(".kalender__time-selector-field").css("display", "block");
    $(".kalender__time-selector-field").animate({
        left: xPos,
        opacity: 1
    },200, "swing");
    $(".kalender__time-selector-field__check").click(function(){
        hideTimeSelector();
    });
    $(".kalender__time-selector-field__hours, .kalender__time-selector-field__minutes").change(function(){
        if($(this).hasClass("kalender__time-selector-field__minutes")){
            if($(this).val() == 60){
                $(this).val(0);
                $(".kalender__time-selector-field__hours").val((parseInt($(".kalender__time-selector-field__hours").val()) + 1) % 24);
            } else if($(this).val() == -1){
                $(this).val(59);
                $(".kalender__time-selector-field__hours").val((parseInt($(".kalender__time-selector-field__hours").val()) - 1) % 24);
            }
        } else{
            if($(this).val() == 24){
                $(this).val(0);
            } else if($(this).val() == -1){
                $(this).val(23);
            }
        }
        if(timeSelectorCallback != undefined){
            timeSelectorCallback(getTimeSelectorDate());
        }
    });
}

function getTimeSelectorDate(){
    const date = new Date();
    date.setHours($(".kalender__time-selector-field__hours").val());
    date.setMinutes($(".kalender__time-selector-field__minutes").val());
    return date;
}

function hideDateSelector(){
    $(".kalender__date-selector-field").animate({
        left: "+=50px",
        opacity: 0
    },100, ()=>{
        $(".kalender__date-selector-field").css("display", "none");
    });
}

function hideTimeSelector(){
    $(".kalender__time-selector-field").animate({
        left: "+=50px",
        opacity: 0
    },100, ()=>{
        $(".kalender__time-selector-field").css("display", "none");
    });
}

let dateSelectorCurrentMonth = new Date();

function getDateSelectorElement(){
    const element = $(`<div class="kalender__date-selector-field">
        <div class="kalender__date-selector-field__header">
            <button class="kalender__date-selector-field__back-btn kalender_interactive-shadow"><</button>
            <span class="kalender__date-selector-field__date"></span>
            <button class="kalender__date-selector-field__forewards-btn kalender_interactive-shadow">></button>
        </div>
    </div>`);
    element.find(".kalender__date-selector-field__back-btn").click(()=>{
        element.find(".kalender__month").remove();
        dateSelectorCurrentMonth.setMonth(dateSelectorCurrentMonth.getMonth() - 1);
        changeDateSelectorMonth(dateSelectorCurrentMonth);
    })
    element.find(".kalender__date-selector-field__forewards-btn").click(()=>{
        element.find(".kalender__month").remove();
        dateSelectorCurrentMonth.setMonth(dateSelectorCurrentMonth.getMonth() + 1);
        changeDateSelectorMonth(dateSelectorCurrentMonth);
    });
    element.append(getMonthElement(dateSelectorCurrentMonth, false));
    element.click((e)=>{e.stopPropagation()})
    return element;
}

function getTimeSelectorElement(){
    const element = $(`<form class="kalender__time-selector-field">
        <input class="kalender__time-selector-field__hours" size="2" type="number" min="-1" max="24"><span>:</span>
        <input class="kalender__time-selector-field__minutes" size="2" type="number" min="-1" max="60"><span class="kalender__time-selector-field__check"><i class="fas fa-check"></i></span>
    </form>`);
    element.find(".kalender__time-selector-field__hours").val(new Date().getHours());
    element.find(".kalender__time-selector-field__minutes").val(new Date().getMinutes());
    element.click((e)=>{e.stopPropagation()})
    return element;
}

function changeDateSelectorMonth(date){
    dateRegisterSelectorField = [];
    const element = $(".kalender__date-selector-field");
    element.find(".kalender__month").remove();
    dateSelectorCurrentMonth = date;
    element.append(getMonthElement(date));
    element.find(".kalender__date-selector-field__date").text(getDateStringMonth(date));
    $(".kalender__date-selector-field .kalender__day").click(function(){
        hideDateSelector();
        dateSelectorCallback(getDateSelectorDateToElement(this));
    });
}

function kalender__aside_html(){
    const elem = $(`<div class="kalender__aside">
        <h4>Gruppen</h4>
        <hr>
    </div>`);
    elem.append(getGroupSelectElem(false, true, true, "Gruppen anzeigen", groupSelectChanged, true));
    return elem;
}

function groupSelectChanged(name, value){
    const index = enableddGroups.indexOf(name);
    if(value){
        if(index == -1){
            enableddGroups.push(name);
        }
    } else{
        if(index > -1){
            enableddGroups.splice(index, 1);
        }
    }
    reloadPage();
}

function getDayElement(date, month, register){
    if(month == undefined){
        month = date.getMonth();
    }
    const datecpy = new Date(date);
    const day = $(`<div class="kalender__day${date.getMonth() == month ? "" : " kalender__day--out-of-month"}${compareDatesDayly(new Date(), date) ? " kalender__today" : ""}">
        <div class="day__name">${date.getDay() == 0 ? "<b>" : ""}${daysShort[date.getDay()]}${date.getDay() == 0 ? "<b>" : ""}</div>
        <div class="day__number">${date.getDate()}</div>
    </div>`);
    if(register){
        currentElements.push({date: new Date(date), element: day});
        day.on("click", function(e){
            maximizeEnter(datecpy);
            e.stopPropagation();
        });
    } else{
        dateRegisterSelectorField.push({date: new Date(date), element: day});
    }
    return day;
}

function getWeekElement(date, month, register){
    if(month == undefined){
        month = date.getMonth();
    }
    let dateCpy = new Date(date);
    dateCpy.setDate(dateCpy.getDate() - (dateCpy.getDay() == 0 ? 6 : dateCpy.getDay() - 1));//resetting to last monday
    const week = $(`<div class="kalender__week"></div>`);
    for (let day = 0; day < 7; day++){
        week.append(getDayElement(dateCpy, month, register));
        dateCpy.setDate(dateCpy.getDate() + 1);
    }
    return week;
}

function getMonthElement(date1, register){
    let date = new Date(date1);
    date.setDate(1);//resetting to first day of month
    const month = date.getMonth();
    const week = $(`<div class="kalender__month"></div>`);
    for (let i = 0; i < 6; i++){
        week.append(getWeekElement(date, month, register));
        date.setDate(date.getDate() + 7);
    }
    return week;
}
