let kalender;
    let kalender__header;
    let kalender__main;
        let kalender__body;
        let kalender__aside;


$(function(){
    kalender = $(".kalender");
    
    kalender.append(kalender__header_Html());
    kalender__header = $(".kalender__header");
    
    kalender.append('<div class="kalender__main"></div>');
    kalender__main = $(".kalender__main");

    kalender__main.append(kalender__aside_html());
    kalender__aside = $(".kalender__aside");

    kalender__main.append('<div class="kalender__body"></div>');
    kalender__aside = $(".kalender__aside");
});

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
            <div class="kalender__month">
                <button type="button" class="kalender__month__backwards kalender_interactive-shadow"><</button>
                <button type="button" class="kalender__month__forewards kalender_interactive-shadow">></button>
                <span class="kalender__current-month">Januar 2020</span>
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

function kalender__aside_html(){
    return `<div class="kalender__aside">
        <span>ToDo</span>
    </div>`;
}