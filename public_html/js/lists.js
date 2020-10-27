function search(elem, text){
    if(text.length == 0){
        elem.children().each(function(){
            $(this).show();
        });
    } else{
        elem.children().each(function(){
            if($(this).text().toLowerCase().includes(text.toLowerCase())){
                $(this).show();
            } else{
                $(this).hide();
            }
        });
    }
}

// User list

userLists = [];
function getUserListElement(createUser){
    const elem = $(`<div class="user-list">
        <input type="test" class="search" placeholder="Suchen..">
        <div class="user-list"></div>
    </div>`);
    userLists.push({elem: elem.find(".user-list"), createUser: createUser});
    getUserList(userListCallback);
    elem.find(".search").on("input", function(e){
        search(elem.find(".user-list"), $(this).val())
    });
    return elem;
}

function userListCallback(users){
    for (const userList of userLists) {
        for (const user of users) {
            const userElem = userList.createUser(user);
            if(userElem != undefined){
                $(userList.elem).append(userElem);
            }
        }
    }
    userLists = [];
}

// Group List

groupLists = [];
function getGroupListElement(createGroup){
    const elem = $(`<div class="group-list">
        <input type="test" class="search" placeholder="Suchen..">
        <div class="group-list"></div>
    </div>`);
    groupLists.push({elem: elem.find(".group-list"), createGroup: createGroup});
    getGroupList(groupListCallback);
    elem.find(".search").on("input", function(e){
        search(elem.find(".group-list"), $(this).val())
    });
    return elem;
}

function groupListCallback(groups){
    for (const groupList of groupLists) {
        for (const groupName in groups) {
            if(groups.hasOwnProperty(groupName)){
                const group = groups[groupName];
                const groupElem = groupList.createGroup(group);
                if(groupElem != undefined){
                    $(groupList.elem).append(groupElem);
                }
            }
        }
    }
    groupLists = [];
}