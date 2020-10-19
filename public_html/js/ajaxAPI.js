"use strict";

let userList = undefined;

loadUserList();

function loadUserList(){
    $.ajax({
        url: "/users/userAPI.php?getUserList=1",
        success: function(result){
            userList = JSON.parse(result);
            for (const callback of userListCallbacks) {
                callback(userList);
            }
        }
    });
}

let userListCallbacks = [];
function getUserList(callback){
    if(userList != undefined){
        callback(userList);
    } else{
        userListCallbacks.push(callback);
    }
}