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
    kalender__body.append(getMonthElement(currentDate));

    entries = getEntrys();
    addEntries(calcEntriesOffset(getEntrys()));

    crateEntryInfoElement();

    $(".kalender__enter.kalender__enter--minimized").click((e)=>{
        maximizeEnter();
        e.stopPropagation();
    });
    $(".kalender__enter__close-btn").click((e)=>{minimizeEnter(); e.stopPropagation();});
    $(".kalender").click(()=>{hideInfoElement(); minimizeEnter();});
    $(window).keyup(keyUpHandler);
    $(window).resize(kalenderResize);

    reloadPage();
});

function maximizeEnter(){
    $(".kalender__enter").removeClass("kalender__enter--minimized");
    $(".kalender__enter").addClass("kalender__enter--maximized");
}

function minimizeEnter(){
    $(".kalender__enter").addClass("kalender__enter--minimized");
    $(".kalender__enter").removeClass("kalender__enter--maximized");
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
    right = window.innerWidth - (rect.left);
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

function getPage(date){
    switch($(".kalender__view-dropdown").val()){
        case "day": return getDayElement(date)
        case "week": return getWeekElement(date)
        case "month": return getMonthElement(date)
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
                <div class="kalender__enter-choice" color="green">Training</div>
                <div class="kalender__enter-choice" color="red">Wettkampf</div>
                <div class="kalender__enter-choice" color="gray">Trainingslager</div>
                <div class="kalender__enter-choice" color="orange">Andere</div>
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
    return enterElement;
}

function loadEnterChoice(element){
    if($(".kalender__enter-chosen").position().left == $(element).position().left){
        return;
    }
    const toRight = $(".kalender__enter-chosen").position().left > $(element).position().left;
    const parentWIdth = $(".kalender__enter__content").width();
    console.log(($(element).position().left / parentWIdth * 90) + "%");
    $(".kalender__enter-chosen").animate({
        left: ($(element).position().left / parentWIdth * 110) + "%",
        width: $(element).width() + ($(element).css("padding-left").split("px")[0] * 2)
    }, 100);
    $(".kalender__enter-choice").each((i, e)=>{$(e).css("color", $(e).attr("color"));})//resetting
    $(element).css("color", "white");
    $(".kalender__enter-chosen").css("background-color", $(element).attr("color"));
    $(".kalender__enter__choices").css("border-color", $(element).attr("color"));
    switch($(element).text()){
        case "Training": changeEnterContent(enterTrainingslagerElement, toRight); break;
        case "Wettkampf": changeEnterContent(enterWettkampfElement, toRight); break;
        case "Trainingslager": changeEnterContent(enterTrainingslagerElement, toRight); break;
        case "Andere": changeEnterContent(enterAndereElement,toRight); break;
    }
}

function changeEnterContent(newElement, toRight){
    $(".kalender__enter__content > div").animate({
        left: toRight ? "50%" : "-50%",
        opacity: 0
    }, 200, "swing", function(){
        $(this).remove();
    });
    $(".kalender__enter__content").append(newElement);
    newElement.css("left", toRight ? "-50%" : "50%")
    newElement.css("opacity", 0)
    newElement.animate({
        left: 0,
        opacity: 1
    }, 200, "swing");
}

const enterTrainingslagerElement = $(`<div class="kalender__enter__properties kalender__enter__trainings-lager">
</div`);
enterTrainingslagerElement.append(getDateSelector(true));

const enterWettkampfElement = $(`<div class="kalender__enter__properties kalender__enter__wettkampf"></div`);
enterWettkampfElement.append(getDateSelector(true));

const enterTrainingElement = $(`<div class="kalender__enter__properties kalender__enter__training"></div`);
enterTrainingElement.append(getDateSelector(false));

const enterAndereElement = $(`<div class="kalender__enter__properties kalender__enter__andere"></div`);
enterAndereElement.append(getDateSelector(true));

function getDateSelector(twoDates){
    const selector = $(`<div class="kalender__date-selector">
    
    </div>`);
    return selector;
}

function kalender__aside_html(){
    return `<div class="kalender__aside">
        <span>ToDo: Aside</span>
    </div>`;
}

function getDayElement(date, month){
    if(month == undefined){
        month = date.getMonth();
    }
    const day = $(`<div class="kalender__day${date.getMonth() == month ? "" : " kalender__day--out-of-month"}${compareDatesDayly(new Date(), date) ? " kalender__today" : ""}">
        <div class="day__name">${daysShort[date.getDay()]}</div>
        <div class="day__number">${date.getDate()}</div>
    </div>`);
    currentElements.push({date: new Date(date), element: day});
    return day;
}

function getWeekElement(date, month){
    if(month == undefined){
        month = date.getMonth();
    }
    let dateCpy = new Date(date);
    dateCpy.setDate(dateCpy.getDate() - (dateCpy.getDay() == 0 ? 6 : dateCpy.getDay() - 1));//resetting to last monday
    const week = $(`<div class="kalender__week"></div>`);
    for (let day = 0; day < 7; day++){
        week.append(getDayElement(dateCpy, month));
        dateCpy.setDate(dateCpy.getDate() + 1)
    }
    return week;
}

function getMonthElement(date1){
    let date = new Date(date1);
    date.setDate(1);//resetting to first day of month
    const month = date.getMonth();
    const week = $(`<div class="kalender__month"></div>`);
    for (let i = 0; i < 6; i++){
        week.append(getWeekElement(date, month));
        date.setDate(date.getDate() + 7);
    }
    return week;
}