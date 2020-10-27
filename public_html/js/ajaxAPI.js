"use strict";

let userList = undefined;
let userListCallbacks = [];

let groupList = undefined;
let groupListCallbacks = [];

load();

function load(){
    $.ajax({
        url: "/users/userAPI.php?getUserList=1",
        success: function(result){
            userList = JSON.parse(result);
            for (const callback of userListCallbacks) {
                callback(userList);
            }
        }
    });
    $.ajax({
        url: "/users/userAPI.php?getGroupList=1",
        success: function(result){
            groupList = JSON.parse(result);
            for (const callback of groupListCallbacks) {
                callback(groupList);
            }
        }
    });
}

function getUserList(callback){
    if(userList != undefined){
        callback(userList);
    } else{
        userListCallbacks.push(callback);
    }
}

function getGroupList(callback){
    if(groupList != undefined){
        callback(groupList);
    } else{
        groupListCallbacks.push(callback);
    }
}

// group Helper

function isAdminInGroup(group){
    for (const user of group.users) {
        if(user.username == username && user.isAdmin){
            return true;
        }
    }
    return false;
}