let dragElement;
let drag = false;

$(document).ready(()=>{
    $.ajax({
        url: "/users/userAPI.php?getUserList=1",
        success: function(result){
            console.log(result);
            if(result.includes("Error message")){
                $(".error-message").text(result);
            }
            addUsersToList(JSON.parse(result));
        }
    });
    $.ajax({
        url: "/users/userAPI.php?getGroupList=1",
        success: function(result){
            console.log(result);
            if(result.includes("Error message")){
                $(".error-message").text(result);
            }
            addGroupsToList(JSON.parse(result));
        }
    });
    $(".submit-button").click(submit);
    $(".add-group-button").click(()=>{
        addGroupUi()
    });
    $(".add-group-input").on("keydown", (e)=>{
        if(e.keyCode == 13){
            addGroupUi();
        }
    });
    $(".undo-button").click(undo);
    $(".redo-button").click(redo);
});

function undo(){
    $.ajax({
        url: "/users/userAPI.php?undoGroups=1",
        success: function(r){
            console.log(r);
            if(!r.includes("Error")){
                location.reload();
            } else{
                $(".error-message").html(r);
            }
        }
    });
}

function redo(){
    $.ajax({
        url: "/users/userAPI.php?redoGroups=1",
        success: function(r){
            console.log(r);
            if(!r.includes("Error")){
                location.reload();
            } else{
                $(".error-message").html(r);
            }
        }
    });
}

function addGroupUi(){
    const groupName = $(".add-group-input").val();
    if(groupNameValid(groupName)){
        let obj = {};
        obj[groupName] = {users:[]};
        addGroupsToList(obj);
    }
}

function groupNameValid(groupName){
    if(groupName.length == 0){
        return false;
    } else{
        return !groupNameExists(groupName);
    }
}

function groupNameExists(groupName){
    const struct = getStruct();
    for (const key in struct) {
        if (struct.hasOwnProperty(key)) {
            if(key == groupName){
                return true;
            }
        }
    }
    return false;
}

function submit(){
    console.log(JSON.stringify(getStruct()));
    $.ajax({
        type: "POST",
        url: '/users/userAPI.php?setGroups=1',
        dataType: 'text',
        async: true,
        data: JSON.stringify(getStruct()),
        success: function (response) {
            console.log(response);
            $(".error-message").html(response);
            $(".loading").remove();
        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.log(jqXHR.status);
            console.log(textStatus);
            console.log(errorThrown);
        }
    });
    $(".submit-button").after(`<div class="loading">Loading pls wait...</div>`);
}

function getStruct(){
    let groups = {};
    $(".group-list__group").each((i, e)=>{
        let group = {users:[]};
        $(e).find(".group-user-dragndrop__user").each((i, userElement)=>{
            let user = {
                username: $(userElement).find(".group-user-dragndrop__user__name").text(),
                isAdmin: $(userElement).find(".is-admin-checkbox").is(':checked')
            }
            group.users.push(user);
        })
        groups[$(e).find(".group-name").text()] = group;
    })
    return groups;
}

function addUsersToList(users){
    for (const user of users) {
        $(".group-user-dragndrop__list").append(getUserElement(user));
    }
}

function getUserElement(user){
    const element = $(`
    <div class="group-user-dragndrop__user" draggable="true">
        <span class="group-user-dragndrop__user__name">${user.username}</span>
    </div>`);
    element.on("dragstart", ()=>{dragStart(element)});
    return element;
}

function dragStart(element){
    drag = true;
    dragElement = element;
}

function addGroupsToList(groups){
    for (const key in groups) {
        if (groups.hasOwnProperty(key)) {
            $(".add-group-section").prepend(getGroupElement(groups[key], key));
        }
    }
    // for (const group of groups) {
    //     $(".add-group-section").prepend(getGroupElement(group));
    // }
}

function getDeleteButton(element){
    if(element.find(".delete-btn").length == 0){
        const btn = $('<button type="button" class="delete-btn">X</button>');
        btn.click(()=>{
            element.remove();
        });
        return btn;
    }
}

function getAdminDiv(element, isAdmin){
    let ranId = Math.random();
    if(element.find(".admin-div").length == 0){
        const div = $(`<div class='admin-div' style="display: inline; float: right;"></div>`);
        const label = $(`<label for="${ranId}">admin</label>`);
        const checkBox = $(`<input class="is-admin-checkbox" type="checkbox" id="${ranId}">`);
        if(isAdmin != undefined){
            if(isAdmin){
                checkBox.prop("checked", true);
            }
        }
        div.append(label);
        div.append(checkBox);
        return div;
    }
}

function getGroupElement(group, groupName){
    const element = $(`
    <div class="group-list__group">
        <div class="group-list__group__header"><span class="group-name">${groupName}</span></div>
    </div>`);
    element.find(".group-list__group__header").append(getDeleteButton(element));
    let content = $(`<div class="group-list__group__content"></div>`);
    element.append(content);
    for (const user of group.users) {
        let userElement = getUserElement(user)
        userElement.append(getDeleteButton(userElement));
        userElement.append(getAdminDiv(userElement, user.isAdmin));
        userElement.addClass("group-user");
        content.append(userElement);
    }
    element.on("dragover", (e)=>{dragOver(e, element)});
    element.on("dragleave", (e)=>{dragLeave(e, element)});
    element.on("drop", (e)=>{drop(e, element)});
    return element;
}

function dragOver(e, element){
    if(!groupElementHasUser(element, getDragElementName())){
        e.preventDefault();
        e.stopPropagation();
        $(element).addClass('dragging');
    }
}

function dragLeave(e, element){
    e.preventDefault();
    e.stopPropagation();
    $(element).removeClass('dragging');
}

function drop(e, element){
    if(!groupElementHasUser(element, getDragElementName())){
        e.preventDefault();
        e.stopPropagation();
        let cpyDrag = dragElement;
        if(!dragElement.hasClass("group-user")){
            cpyDrag = dragElement.clone();
        }
        cpyDrag.addClass("group-user");
        cpyDrag.append(getDeleteButton(cpyDrag));
        cpyDrag.append(getAdminDiv(cpyDrag));
        cpyDrag.on("dragstart", ()=>{dragStart(cpyDrag)});
        element.find(".group-list__group__content").append(cpyDrag);
        $(element).removeClass('dragging');
    }
}

function groupElementHasUser(groupElement, username){
    success = false;
    groupElement.find(".group-user-dragndrop__user__name").each((i,e) => {
        if($(e).text() == username){
            success = true;
        }
    });
    return success;
}

function getDragElementName(){
    if(dragElement != undefined){
        return dragElement.find(".group-user-dragndrop__user__name").text();
    }
}