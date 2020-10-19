userLists = [];

function getUserListElement(createUser){
    console.log("getUserListElement")
    const elem = $(`<div class="search-user">
        <input type="test" class="search-user__input" placeholder="Suchen..">
    </div>
    <div class="user-list"></div>`);
    userLists.push({elem: elem.next()[0], createUser: createUser});
    getUserList(userListCallback);
    elem.find(".search-user__input").on("input", function(e){
        let text = $(this).val();
        if(text.length == 0){
            elem.next().children().each(function(){
                $(this).show();
            });
        } else{
            elem.next().children().each(function(){
                if($(this).text().toLowerCase().includes(text.toLowerCase())){
                    $(this).show();
                } else{
                    $(this).hide();
                }
            });
        }
    });
    return elem;
}

function userListCallback(users){
    for (const userList of userLists) {
        for (const user of users) {
            const userElem = userList.createUser(user);
            $(userList.elem).append(userElem);
        }
    }
    userLists = [];
}