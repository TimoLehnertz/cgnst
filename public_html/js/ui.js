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
}