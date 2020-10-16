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

    addEntries(calcEntriesOffset(getEntrys()));

    crateEntryInfoElement();

    $(".kalender__enter.kalender__enter--minimized").click((e)=>{
        maximizeEnter();
        e.stopPropagation();
    });
    $(".kalender__enter__close-btn").click((e)=>{minimizeEnter(); e.stopPropagation();});
    $(".kalender").click(()=>{hideInfoElement(); minimizeEnter();hideDateSelector();hideTimeSelector()});
    $(window).keyup(keyUpHandler);
    $(window).resize(kalenderResize);
    loadGroups();
    loadUsername();
    loadTrainingsBlueprints();
    reloadPage();
});

let username;
let groups;
let trainingsBlueprints;

let userLoggedIn = false;

const interval = window.setInterval(function(){
    if(username != undefined && groups != undefined && trainingsBlueprints != undefined){
        ajaxDataLoaded();
        clearInterval(interval);
    }
})

function loadUsername(){
    $.ajax({
        url: "/users/userAPI.php?getUsername=1",
        success: function(result){
            if(result.includes("NOT LOGGED IN YET")){
                console.log(result);
                username = "";
                userLoggedIn = false;
            } else{
                username = result;
                userLoggedIn = true;
            }
        }
    });
}

function loadGroups(){
    $.ajax({
        url: "/users/userAPI.php?getGroupList=1",
        success: function(result){
            if(result.includes("Error message")){
                console.log(result);
            } else{
                groups = JSON.parse(result)
            }
        }
    });
}

function loadTrainingsBlueprints(){
    $.ajax({
        url: "/training/getTrainingsBlueprints.php?getAvailableTrainingsBlueprints=1",
        success: function(result){
            if(result.includes("Error message")){
                console.log(result);
                trainingsBlueprints = [];
            } else{
                trainingsBlueprints = JSON.parse(result);
            }
        }
    });
}

let hasBeenMaximized = false;

function maximizeEnter(){
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

function minimizeEnter(){
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

function getEntrys(){
    return [{
        name: "Geisingen Kader1",
        startDate: new Date("10/3/2020"),
        endDate: new Date("11/2/2020"),
        initiator: "CST1",//ToDO
        type: "training",
        color: "red"
    },{
        name: "Geisingen Kader2",
        startDate: new Date("10/25/2020"),
        endDate: new Date("10/23/2020"),
        initiator: "CST",//ToDO
        type: "training"
    },{
        name: "Geisingen Kader3",
        startDate: new Date("10/12/2020"),
        endDate: new Date("10/12/2020"),
        initiator: "CST",//ToDO
        type: "training",
        color: "pink"
    },{
        name: "Geisingen Kader4",
        startDate: new Date("10/2/2020"),
        endDate: new Date("10/11/2020"),
        initiator: "CST",//ToDO
        type: "training",
        color: "green"
    }];
}

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
        insertEntry(entry);
    }
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
    const entryElement = $(`<div class="kalender__entry${startCap ? " kalender__entry--start-cap" : ""}${endCap ? " kalender__entry--end-cap" : ""}" style="background-color: ${entry.color == undefined ? "#333" : entry.color};">
        ${ showName ? `<div class="kalender__entry__name">${entry.name}</div>` : ""}
    </div>`);
    entryElement.click((e)=>{blendInfoElementIn(entry, entryElement); e.stopPropagation()});
    return entryElement;
}

function crateEntryInfoElement(){
    $("main").append($(`<div class="kalender__entry-info">
        <div class="kalender__entry-info__header">
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
            <div class="kalender__entry-info__content">
                <div class="kalender__entry-info__initiator"></div>
            </div>
        </div>
    </div>`));
    $(".kalender__entry-info__close-button").click((e)=>{hideInfoElement(); e.stopPropagation();});
    $(".kalender__entry-info").click((e)=>{e.stopPropagation();});
}

function blendInfoElementIn(entry, entryElement){
    infoElementEntry = entry;
    infoElementEntryElement = entryElement;
    $(".kalender__entry-info").stop(true, false);
    const width = 400;
    var rect = entryElement.offset();
    let right = window.innerWidth - (rect.left);
    const fromRight = rect.left + (entryElement.width() / 2) < window.innerWidth / 2;
    if(fromRight){
        right = right - entryElement.width() - 70 - width;
    } if($(".kalender__view-dropdown").val() == "day"){
        right = (window.innerWidth / 2) - width / 2;
    }
    let top = Math.min(rect.top, window.innerHeight - $(".kalender__entry-info").height() - 60);
    $(".kalender__entry-info").css("display", "block");
    $(".kalender__entry-info__name").text(entry.name);
    $(".kalender__entry-info__date").text(getStartEndString(entry.startDate, entry.endDate));
    $(".kalender__entry-info__initiator").text(entry.initiator);
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
        out = from.getDate() + ". - " + to.getDate() + " " + monthsLong[from.getMonth()]
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
    addEntries(calcEntriesOffset(getEntrys()));
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
    addEntries(calcEntriesOffset(getEntrys()));
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
        case "day": return getDayElement(date, true)
        case "week": return getWeekElement(date, true)
        case "month": return getMonthElement(date, true)
    }
}

function kalender__header_Html(){
    const html = `<div class="kalender__header">
        <div class="kalender__header__left">
            <label class="kalender__burger-label kalender_interactive-shadow" for="kalender__burger-input">
                <span class="kalender__burger-line"></span>
                <span class="kalender__burger-line"></span>
                <span class="kalender__burger-line"></span>
            </label>
            <input type="checkbox" id="kalender__burger-input">
            <h3 class="kalender__header__headline">Kalender</h3>
            <div class="kalender__header__month">
                <button type="button" class="kalender__header__month__backwards kalender_interactive-shadow"><</button>
                <button type="button" class="kalender__header__month__forewards kalender_interactive-shadow">></button>
                <span class="kalender__header__current-month">Januar 2020</span>
            </div>
        </div>
        <div class="kalender__header__right">
            <div class="kalender__search">
            <input type="text" class="kalender__search-input kalender_interactive-shadow" id="kalender__search-input" placeholder="Suchen...">
                <img class="kalender__search-button kalender_interactive-shadow" src="/img/search-icon.svg" width="30px" alt="Suchen">
            </div>
            <select name="kalender-view" class="kalender__view-dropdown kalender_interactive-shadow">
                <option value="day">Tag</option>
                <option value="week">Woche</option>
                <option value="month" selected>Monat</option>
            </select>
        </div>
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
                <div class="kalender__enter-choice" color="green"><span class="kalender__enter-choice__name">Training</span><i class="fas fa-dumbbell"></i></i></div>
                <div class="kalender__enter-choice" color="red"><span class="kalender__enter-choice__name">Wettkampf</span><i class="fas fa-flag-checkered"></i></i></i></div>
                <div class="kalender__enter-choice" color="gray"><span class="kalender__enter-choice__name">Trainingslager</span><i class="fas fa-campground"></i></div>
                <div class="kalender__enter-choice" color="orange"><span class="kalender__enter-choice__name">Andere</span><i class="fas fa-bookmark"></i></div>
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
        case "Andere": changeEnterContent(enterAndereElement,toRight); break;
    }
}

function changeEnterContent(newElement, toRight){
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
}

function initGroupSelectors(){
    $(".group-select__groups").each(function(){
        if($(this).children().length > 0){return;}
        const initialSelect = $(this).parent().attr("initialSelect") == "true";
        const onlyAdmin = $(this).parent().attr("onlyAdmin");
        const groupNames = getGroupNames(onlyAdmin);
        for (const grpName of groupNames) {
            $(this).prepend(getGroupsElement(grpName, initialSelect));
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

function getGroupsElement(grpName, selected){
    const ranId = Math.random();
    const elem = $(`<div class="group-select__group kalender_interactive-shadow">
        <input id="${ranId}" type="checkbox" class="group-select__group-check" ${selected ? "checked" : ""}>
        <label for="${ranId}" class="group-select__group-name">${grpName}</label>
    </div>`);
    elem.find(".group-select__group-check").change(function(){
        if($(this).prop("checked")){
            elem.addClass("group-select__group--checked")
        } else{
            elem.removeClass("group-select__group--checked")
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

function getPropertyJson(propertieElem){   
    const entryType = propertieElem.attr("entryType");
    const title = propertieElem.find(".kalender__termin__name__input").val();
    const startDate = new Date(parseInt($(propertieElem.find(".kalender__date-selector")[0]).attr("date")));
    let endDate = new Date(parseInt($(propertieElem.find(".kalender__date-selector")[1]).attr("date")));
    const startTime = new Date(parseInt($(propertieElem.find(".kalender__time-selector")[0]).attr("time")))
    const endTime = new Date(parseInt($(propertieElem.find(".kalender__time-selector")[1]).attr("time")))
    const trainingsBlueprint = propertieElem.find(".blueprint--checked .trainings-blueprint__name").text();
    const groups = [];

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
        trainingsBlueprint: trainingsBlueprint,
        groups: groups
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
    console.log("submitting:");
    console.log(entryProperties);
    $.ajax({
        type: "POST",
        url: '/kalender/kalenderAPI.php?submitEntry=1',
        dataType: 'text',
        data: JSON.stringify(entryProperties),
        success: function (response) {
            console.log(response)
        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.log(jqXHR.status);
            console.log(textStatus);
            console.log(errorThrown);
        }
    });
}

const enterTrainingslagerElement = $(`<div class="kalender__enter__properties kalender__enter__trainings-lager" entryType="trainingslager"></div`);
enterTrainingslagerElement.append(getTerminTitelElement)
enterTrainingslagerElement.append(getDateSelector("Von"));
enterTrainingslagerElement.append(getTimeSelector("Von"));
enterTrainingslagerElement.append(getDateSelector("Bis"));
enterTrainingslagerElement.append(getTimeSelector("Bis"));
enterTrainingslagerElement.append(getEnterEnterSection);

const enterWettkampfElement = $(`<div class="kalender__enter__properties kalender__enter__wettkampf" entryType="wettkampf"></div`);
enterWettkampfElement.append(getTerminTitelElement)
enterWettkampfElement.append(getDateSelector("Von"));
enterWettkampfElement.append(getTimeSelector("Von"));
enterWettkampfElement.append(getDateSelector("Bis"));
enterWettkampfElement.append(getTimeSelector("Bis"));
enterWettkampfElement.append(getEnterEnterSection);

const enterTrainingElement = $(`<div class="kalender__enter__properties kalender__enter__training" entryType="training"></div`);
enterTrainingElement.append(getTerminTitelElement)
enterTrainingElement.append(getDateSelector());
enterTrainingElement.append(getTimeSelector("Von"));
enterTrainingElement.append(getTimeSelector("Bis"));
enterTrainingElement.append(getTrainingsBlueprintSelectElem(false));
enterTrainingElement.append(getGroupSelectElem(true, false, false, "In Gruppen teilen"));
enterTrainingElement.append(getEnterEnterSection);

const enterAndereElement = $(`<div class="kalender__enter__properties kalender__enter__andere" entryType="andere"></div`);
enterAndereElement.append(getTerminTitelElement)
enterAndereElement.append(getDateSelector("Von"));
enterAndereElement.append(getTimeSelector("Von"));
enterAndereElement.append(getTimeSelector("Bis"));
enterAndereElement.append(getDateSelector("Bis"));
enterAndereElement.append(getGroupSelectElem(true, false, false, "In Gruppen teilen"));
enterAndereElement.append(getEnterEnterSection);

function getEnterEnterSection(){
    const elem = $(`<div>
        <button class="enter__enter-btn">Eintragen</button>
    </div`);
    elem.find("button").click(()=>{
        if(validateNewEntry(getPropertyJson(elem.parent()))){
            submitEntry(getPropertyJson(elem.parent()));
        }
    })
    return elem;
}

function getGroupSelectElem(onlyAdminGroups, selected, expanded, name){
    const elem = $(`<div class="group-select" initialSelect="${selected}">
            <div class="kalender_interactive-shadow group-select__header">
                <i class="fas fa-users"></i>
                <span classs="group-select__title">${name}</span>
                <i class="far fa-caret-square-down"></i>
            </div>
            <div class="group-select__groups" onlyAdmin="${onlyAdminGroups ? "true" : "fasle"}" ${expanded ? "" : 'expanded="true"'}></div>
        </div>`);
        const content = elem.find(".group-select__groups");
        elem.find(".group-select__header").click(()=>{
            updateGroupSelect(content);
            elem.find(".fa-caret-square-down").toggleClass("rotate-reverse");
        });
        updateGroupSelect(content)
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
        updateGroupSelect(content)
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

function getGroupNames(onlyAdminGroups){
    const names = [];
    if(groups != undefined){
        for (const grpName in groups) {
            if (groups.hasOwnProperty(grpName)) {
                const group = groups[grpName];
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
    return names;
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
            selector.find(".kalender__date-selector__date").text(getDateStringAllDay(date));
            selector.attr("date", date.getTime());
        });
    });
    return selector;
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
    elem.append(getGroupSelectElem(false, true, true, "Gruppen anzeigen"));
    return elem;
}

function getDayElement(date, month, register){
    if(month == undefined){
        month = date.getMonth();
    }
    const day = $(`<div class="kalender__day${date.getMonth() == month ? "" : " kalender__day--out-of-month"}${compareDatesDayly(new Date(), date) ? " kalender__today" : ""}">
        <div class="day__name">${date.getDay() == 0 ? "<b>" : ""}${daysShort[date.getDay()]}${date.getDay() == 0 ? "<b>" : ""}</div>
        <div class="day__number">${date.getDate()}</div>
    </div>`);
    if(register){
        currentElements.push({date: new Date(date), element: day});
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