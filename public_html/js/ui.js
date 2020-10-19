"use strict";

/*
    Accordion
    Extend- and collabsale content with header

    usage: 
    <div class="accordion">
        <button type="button" class="accordionButton">Expand content</button>
        <div class="accordionContent">
            <p>Test Content</p>
        </div>
    </div>
*/
$(window).ready(()=>{
    setAccordionEvents();
    window.addEventListener('resize', resizeAccordions);
    functionInitAll();
});


function setAccordionEvents(){
    document.querySelectorAll(".accordionButton:not(.listener)").forEach(button =>{
        button.classList.add("listener");
        button.addEventListener('click', () => {
            const accordionContent = button.nextElementSibling;
            button.classList.toggle("accordionButtonActive");
            if(button.classList.contains("accordionButtonActive")){
                accordionContent.style.maxHeight = accordionContent.scrollHeight + 'px';
                //accordionContent.style.maxHeight = '100vh';
            } else{
                accordionContent.style.maxHeight = 0;
            }
            window.setTimeout(resizeAccordions, 200);
        });
    });
}

function resizeAccordions(){
    document.querySelectorAll(".accordionButtonActive").forEach(button =>{
        const accordionContent = button.nextElementSibling;
        accordionContent.style.maxHeight = 2 * accordionContent.scrollHeight + 'px';
    });
}

function functionInitAll(){
    setAccordionEvents();
    const location = window.location.pathname.substring(1, window.location.pathname.indexOf("/", 1) == -1 ? undefined : window.location.pathname.indexOf("/", 1));
    $(`a[href="${location.length == 0 ? "/index.php" : "/" + location}"]`).addClass("nav-now").prop("href", "#");
}