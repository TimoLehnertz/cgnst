<?php
    include "../header.php";
    if(!isset($_SESSION["username"])){
        header("location: /youNeedToLogin.php");
        exit();
    }
?>
    <main>
        <h3>Trainingsvorlage erstellen</h3>
        <div class="accordion">
            <div class="accordionButton">
                Tip:
            </div>
            <div class="accordionContent">
                <ul>
                    <li>Eine trainingsvorlage besteht aus einer oder mehreren Aufgabengruppen</li>
                    <li>Eine Aufgaben gruppe besteht aus einer oder mehreren Aufgaben</li>
                </ul>
                <details name="Beispiel">
                    <summary>
                        Beispiel
                    </summary>
                    <ul>
                        <li>Warmup(Aufgabengruppe)
                            <ul>
                                <li>5 Min Skaten, 5 min pause(Aufgabe)</li>
                                <li>3 Steigerungen, 10 min pause(Aufgabe)</li>
                            </ul>
                        </li>
                        <li>Training(Aufgabengruppe)
                            <ul>
                                <li>8 * doubinsprint, 5 min pause(Aufgabe)</li>
                            </ul>
                        </li>
                        <li>cooldown(Aufgabengruppe)
                            <ul>
                                <li>5 Min Skaten, 0 min pause(Aufgabe)</li>
                            </ul>
                        </li>
                    </ul>
                </details>
            </div>
        </div>
        <div id="createTrainingsBlueprint">
            <label for="selectBlueprint">Vorlage laden</label>
            <select name="selectBlueprint" id="selectBlueprint">
                <?php
                    include "getTrainingsBlueprints.php";
                    $blueprints = getAllAvailableTrainingsBlueprints($conn);
                    for ($i=0; $i < sizeof($blueprints); $i++) {
                        echo "<option value='".$blueprints[$i]["idtrainingsBlueprint"]."'>".$blueprints[$i]["name"]."</option>";
                    }
                ?>
            </select>
            <button type="button" onclick="loadBlueprint()">Laden</button>
            <br>
            <label for="blueprintName">Vorlagen Name: </label>
            <input id="blueprintName" type="text" placeholder="Vorlagen Name..." value="Vorlage">
            <p>Aufgabengruppen:</p>
            <div id="exerciseGroups" class="accordion">
                <div id="addExerciseGroup">
                    <input id="addExerciseGroupInput" type="text" placeholder="Hinzufügen">
                    <button class="addButton" id="addExerciseGroupButton" onclick="addExerciseButtonClicked();">+</button>
                </div>
            </div>
            <div id="exersice">
                <div><!--for jQuery to call empty--></div>
            </div>
            <div id="sendArea">
                <button type="button" onclick="submit()">Erstellen</button>
                <button type="button" onclick="clearAll()">Neu</button>
            </div>
        </div>
        <script>
            let activeExercise = {};
            const exerciseGroups = document.getElementById("exerciseGroups");
            const addExerciseGroupDiv = document.getElementById("addExerciseGroup");
            const addExerciseGroupInput = document.getElementById("addExerciseGroupInput");
            const addExerciseGroupButton = document.getElementById("addExerciseGroupButton");
            addExerciseGroupInput.onkeydown = () =>{if(event.keyCode == 13/*(Enter)*/){addExerciseGroupButton.click()}};

            let structure = [];
            let trainingsBlueprint = {
                name : 'Vorlage',
                groups : structure
            };
            // let preset = {"name":"Vorlage","groups":[{"exerciseGroupName":"warmup","exercises":[{"name":"10 min skating","time":"600","pauseAfter":30,"description":"Exercise: 10 min skating testtesttest","intensity":0,"aim":"aim","groupName":"warmup"}]},{"exerciseGroupName":"training","exercises":[{"name":"accelerations","time":60,"pauseAfter":30,"description":"Exercise: accelerations","intensity":0,"aim":"aim","groupName":"training"},{"name":"relay","time":60,"pauseAfter":30,"description":"Exercise: relay","intensity":0,"aim":"aim","groupName":"training"},{"name":"doubin sprint","time":60,"pauseAfter":30,"description":"Exercise: doubin sprint","intensity":0,"aim":"aim","groupName":"training"}]},{"exerciseGroupName":"cooldown","exercises":[{"name":"wrong direction","time":"130","pauseAfter":30,"description":"Exercise: wrong direction","intensity":0,"aim":"aim","groupName":"cooldown"}]}]}
            // let preset = {"name":"Taining 1","groups":[{"exerciseGroupName":"warmup","exercises":[{"name":"10 min skating","time":600,"pauseAfter":30,"description":"Exercise: 10 min skating testtesttest","intensity":0,"aim":"aim","groupName":"warmup"}]},{"exerciseGroupName":"training","exercises":[{"name":"accelerations","time":60,"pauseAfter":30,"description":"Exercise: accelerations","intensity":0,"aim":"aim","groupName":"training"},{"name":"relay","time":60,"pauseAfter":30,"description":"Exercise: relay","intensity":0,"aim":"aim","groupName":"training"},{"name":"doubin sprint","time":60,"pauseAfter":30,"description":"Exercise: doubin sprint","intensity":0,"aim":"aim","groupName":"training"}]},{"exerciseGroupName":"cooldown","exercises":[{"name":"wrong direction","time":130,"pauseAfter":30,"description":"Exercise: wrong direction","intensity":0,"aim":"aim","groupName":"cooldown"}]}]}

            // $(document).ready(function(){loadPreset(preset);});

            function loadBlueprint(){
                let blueprintId =  $("#selectBlueprint").val();
                $.ajax({
                    type: "GET",
                    url: 'getTrainingsBlueprints.php?getTrainingsBlueprintJsonById=' + blueprintId,
                    dataType: 'text',
                    async: true,
                    success: function (response) {
                        // console.log(response);
                        loadPreset(JSON.parse(response));
                        $("#selectBlueprint").removeClass("errorClass");
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        alert("failed to load blueprint!");
                        $("#selectBlueprint").addClass("errorClass");
                        console.log(jqXHR.status);
                        console.log(textStatus);
                        console.log(errorThrown);
                    }
                });
            }

            function submit(){
                // console.log("submitting");
                trainingsBlueprint.name = $("#blueprintName").val();
                // console.log("val: " + trainingsBlueprint.name);
                if(trainingsBlueprint.name.length > 0){
                    $("#blueprintName").removeClass("errorClass");
                    $.ajax({
                        type: "POST",
                        url: 'submitTrainingsBlueprint.php',
                        dataType: 'text',
                        async: true,
                        data: JSON.stringify(trainingsBlueprint),
                        success: function (response) {
                            // console.log(response);
                        },
                        error: function(jqXHR, textStatus, errorThrown) {
                            console.log(jqXHR.status);
                            console.log(textStatus);
                            console.log(errorThrown);
                        }
                    });
                    // console.log(JSON.stringify(trainingsBlueprint));
                } else{
                    $("#blueprintName").addClass("errorClass");
                }
            }

            function loadPreset(preset){
                clearAll();
                trainingsBlueprint.name = preset.name;
                $("#blueprintName").val(preset.name);
                for (let i = 0; i < preset.groups.length; i++) {
                    addExerciseGroup(preset.groups[i].exerciseGroupName);
                    for (let j = 0; j < preset.groups[i].exercises.length; j++) {
                        addExerciseFromObject(preset.groups[i].exercises[j]);
                    }
                }
            }

            function clearAll(){
                structure.splice(0, structure.length);
                $(".exerciseGroup").hide(0, function(element){
                    $(".exerciseGroup").remove();
                });
                $("#exersice").children().hide(0, function(){
                    $("#exersice").empty();
                    $("#exersice").append(document.createElement("div"));
                });
            }

            function addExerciseButtonClicked(){
                if(addExerciseGroupInput.value.length > 0){
                    addExerciseGroup(addExerciseGroupInput.value);
                    addExerciseGroupInput.classList.remove("errorClass");
                    addExerciseGroupInput.value = "";
                } else{
                    addExerciseGroupInput.classList.add("errorClass");
                }
            }

            function addExerciseGroup(name){
                // console.log(name);
                if (!doesGroupNameExist(name) && name.length > 0){
                    structure.push({
                        'exerciseGroupName' : name,
                        'exercises' : []
                    });
                    exerciseGroups.insertBefore(getNewExerciseGroupElement(name), addExerciseGroupDiv);
                    setAccordionEvents();
                    resizeAccordions();
                } else{
                    // console.log("Bitte ausfüllen");
                }
            }

            function addExerciseFromObject(exerciseObject){
                const exersizeGroupElement = getExerciseGroupElement(exerciseObject.groupName);
                addExercise(exerciseObject.name, exersizeGroupElement, null, exerciseObject);
            }

            function getExerciseGroupElement(name){
                const groups = $(".exerciseGroup");
                for (const key in groups) {
                    if (groups.hasOwnProperty(key)) {
                        const element = groups[key];
                        if(element.querySelector(".exerciseGroupName").innerText == name){
                            return element;
                        }
                    }
                }
            }

            function getExerciseElement(exerciseName, groupName){
                // console.log(getExerciseGroupElement(groupName));
                const exerciseElements = getExerciseGroupElement(groupName).querySelectorAll(".exerciseItem");
                // console.log("trying to find groupname: " + groupName + ", exerciseName: " + exerciseName + ". found Goups");
                // console.log(exerciseElements)
                for (const exerciseElement of exerciseElements) {
                    if(getExerciseName(exerciseElement) == exerciseName){
                        // console.log("found: ")
                        // console.log(exerciseElement);
                        return exerciseElement;
                    }
                }
                return null;
            }

            function addExercise(name, exersizeGroupElement, inputElement, exerciseObject){
                if(name.length > 0 && !doesNameExistInExerciseGroup(structure[getIndexOfGroupName(getGroupName(exersizeGroupElement))], name)){
                    const exercise = document.createElement("div");
                    exercise.classList.add("exerciseItem");
                    exercise.innerHTML = "<span class='exerciseName'>" + name + "</span>";
                    const upButton = document.createElement("button");
                    upButton.classList.add("upButton", "buttonRight");
                    upButton.innerText = "Up";
                    upButton.type = "button";
                    upButton.onclick = () => {
                        event.stopPropagation();
                        moveExersize(exercise, exersizeGroupElement, true);
                    }
                    const downButton = document.createElement("button");
                    downButton.classList.add("downButton", "buttonRight");
                    downButton.innerText = "Down";
                    downButton.type = "button";
                    downButton.onclick = () => {
                        event.stopPropagation();
                        moveExersize(exercise, exersizeGroupElement, false);
                    }
                    const removeButton = document.createElement("button");
                    removeButton.classList.add("removeButton", "buttonRight");
                    removeButton.type = "button";
                    removeButton.innerText = "-";
                    removeButton.onclick = () => {
                        removeExersize(exercise, exersizeGroupElement);
                    };
                    exercise.appendChild(removeButton);
                    exercise.appendChild(downButton);
                    exercise.appendChild(upButton);
                    $(exercise).click(function(){
                        $("#exersice").children().hide(100, function(){
                            $("#exersice").empty();
                            $("#exersice").append(getExerciseElementFromObject(getExerciseObj(exercise)));
                            activeExercise = getExerciseObj(exercise);
                        });
                    });
                    exersizeGroupElement.querySelector(".accordionContent").insertBefore(exercise, exersizeGroupElement.querySelector("input"));
                    resizeAccordions();
                    //setting data
                    const groupName = getGroupName(exersizeGroupElement);
                    if(exerciseObject != null){
                        structure[getIndexOfGroupName(groupName)]['exercises'].push(exerciseObject);
                    } else{
                        structure[getIndexOfGroupName(groupName)]['exercises'].push({
                            'name' : name,
                            'time' : 60,
                            'pauseAfter' : 30,
                            'description' : "Exercise: " + name,
                            'intensity' : 0,
                            'aim' : "aim",
                            'groupName' : groupName
                        });
                    }
                    // console.log(structure);
                    if(inputElement != null){
                        inputElement.classList.remove("errorClass");
                    }
                } else if(inputElement != null){
                    inputElement.classList.add("errorClass");
                }
            }

            function getExerciseObj(exerciseElement){
                const groupName = getGroupName(exerciseElement.parentNode.parentNode);
                return structure[getIndexOfGroupName(groupName)].exercises[getIndexOfExerciseName(groupName, getExerciseName(exerciseElement))];
            }

            function doesGroupNameExist(groupName){
                for (const iterator of structure) {
                    if(iterator.hasOwnProperty("exerciseGroupName")){
                        if(iterator['exerciseGroupName'] == groupName){
                            return true;
                        }
                    }
                }
                return false;
            }

            function getGroupName(exersizeGroupElement){
                return exersizeGroupElement.querySelector(".exerciseGroupName").innerText;
            }

            function getExerciseName(exerciseElement){
                return exerciseElement.querySelector(".exerciseName").innerText;
            }

            function removeExersizeGroup(exersizeGroupElement){
                $(exersizeGroupElement).hide(100);
                let index = getIndexOfGroupName(getGroupName(exersizeGroupElement));
                if(index >= 0){
                    structure.splice(index, 1);
                }
                // console.log(structure);
            }

            function moveExersizeGroup(exersizeGroupElement, up){
                const index = getIndexOfGroupName(getGroupName(exersizeGroupElement));
                // console.log("name: " + getGroupName(exersizeGroupElement));
                let tmp = structure[index];
                // console.log("move " + up + ", index: " + index)
                if(up && index > 0){
                    structure[index] = structure[index - 1];
                    structure[index - 1] = tmp;
                    exersizeGroupElement.parentNode.insertBefore(exersizeGroupElement, exersizeGroupElement.previousSibling);
                } else if(!up && index + 1 < structure.length){
                    structure[index] = structure[index + 1];
                    structure[index + 1] = tmp;
                    exersizeGroupElement.parentNode.insertBefore(exersizeGroupElement.nextSibling, exersizeGroupElement);
                }
                // console.log(structure);
            }

            function moveExersize(exerciseElement, exersizeGroupElement, up){
                const groupIndex = getIndexOfGroupName(getGroupName(exersizeGroupElement));
                const exerciseIndex = getIndexOfExerciseName(getGroupName(exersizeGroupElement), getExerciseName(exerciseElement));
                // console.log("exerciseIndex: " + exerciseIndex);
                let tmp = structure[groupIndex]['exercises'][exerciseIndex];
                // console.log("move " + up + ", index: " + exerciseIndex)
                if(up && exerciseIndex > 0){
                    structure[groupIndex]['exercises'][exerciseIndex] = structure[groupIndex]['exercises'][exerciseIndex - 1];
                    structure[groupIndex]['exercises'][exerciseIndex - 1] = tmp;
                    exerciseElement.parentNode.insertBefore(exerciseElement, exerciseElement.previousSibling);
                } else if(!up && exerciseIndex + 1 < structure[groupIndex]['exercises'].length){
                    structure[groupIndex]['exercises'][exerciseIndex] = structure[groupIndex]['exercises'][exerciseIndex + 1];
                    structure[groupIndex]['exercises'][exerciseIndex + 1] = tmp;
                    exerciseElement.parentNode.insertBefore(exerciseElement.nextSibling, exerciseElement);
                }
                $(exerciseElement).slideDown(400);
                // console.log(structure);
            }

            function getIndexOfGroupName(groupName){
                for (let i = 0; i < structure.length; i++) {
                    const element = structure[i];
                    if(element.hasOwnProperty("exerciseGroupName")){
                        if(element['exerciseGroupName'] == groupName){
                            return i;
                        }
                    }
                }
                return -1;
            }

            function getIndexOfExerciseName(groupName, exersizeName){
                for (let i = 0; i < structure.length; i++) {
                    const element = structure[i];
                    if(element.hasOwnProperty("exerciseGroupName")){
                        if(element['exerciseGroupName'] == groupName){
                            for (let j = 0; j < element['exercises'].length; j++) {
                                if(element['exercises'][j].name == exersizeName){
                                    return j;
                                }
                            }
                        }
                    }
                }
                return -1;
            }

            function removeExersize(exerciseElement, exersizeGroupElement){
                //exersizeGroupElement.querySelector(".accordionContent").removeChild(exerciseElement);
                $(exerciseElement).hide(200);
                const exerciseName = exerciseElement.querySelector(".exerciseName").innerText;
                const groupName = exersizeGroupElement.querySelector(".exerciseGroupName").innerText;
                structure[getIndexOfGroupName(groupName)]['exercises'].splice([getIndexOfExerciseName(groupName, exerciseName)], 1);
                // console.log(structure);
            }

            function getNewExerciseGroupElement(name){
                const group = document.createElement("div");
                group.classList.add("exerciseGroup");
                const accordionButton = document.createElement("div");
                accordionButton.classList.add("accordionButton", "accordionButtonActive");
                accordionButton.innerHTML = "<span class='exerciseGroupName'>" + name + "</span>";
                const removeButton = document.createElement("button");
                removeButton.classList.add("removeButton", "buttonRight");
                removeButton.innerText = "-";
                removeButton.type = "button";
                removeButton.onclick = () => {
                    removeExersizeGroup(group);
                }
                const upButton = document.createElement("button");
                upButton.classList.add("upButton", "buttonRight");
                upButton.innerText = "Up";
                upButton.type = "button";
                upButton.onclick = () => {
                    event.stopPropagation();
                    moveExersizeGroup(group, true);
                }
                const downButton = document.createElement("button");
                downButton.classList.add("downButton", "buttonRight");
                downButton.innerText = "Down";
                downButton.type = "button";
                downButton.onclick = () => {
                    event.stopPropagation();
                    moveExersizeGroup(group, false);
                }
                accordionButton.appendChild(removeButton);
                accordionButton.appendChild(downButton);
                accordionButton.appendChild(upButton);
                const accordionContent = document.createElement("div");
                accordionContent.classList.add("accordionContent");
                const input = document.createElement("input");
                input.placeholder = "hinzufügen";
                input.type = "text";
                const addButton = document.createElement("button");
                addButton.classList.add("addButton");
                addButton.innerText = "+";
                addButton.onclick = () => {
                    addExercise(input.value, group, input);
                    //addExerciseFromName(input.value, name);
                    input.value = "";
                };
                input.onkeydown = () =>{if(event.keyCode == 13/*(Enter)*/){addButton.click()}};
                accordionContent.appendChild(input);
                accordionContent.appendChild(addButton);
                group.appendChild(accordionButton);
                group.appendChild(accordionContent);
                return group;
            }

            function getExerciseElementFromObject(exerciseObject){
                const exercise = document.createElement("div");
                if(exerciseObject != undefined){
                    exercise.innerHTML =    "<div class='exerciseInputRow'><label for='name'>Name: </label><input required onchange='exerciseValueChanged(this)' onkeyup='exerciseValueChanged(this)' name='name' type='text' placeholder='Name' value='" + exerciseObject.name + "'></div>" +
                                            "<div class='exerciseInputRow'><label for='description'>Beschreibung: </label><textarea required onchange='exerciseValueChanged(this)' onkeyup='exerciseValueChanged(this)' rows='5' name='description' placeholder='Beschreibung'>" + exerciseObject.description + "</textarea ></div>" +
                                            "<div class='exerciseInputRow'><label for='intensity'>Intensität: </label><input required onchange='exerciseValueChanged(this)' onkeyup='exerciseValueChanged(this)' name='intensity' min='0' step='1' max='10' type='number' placeholder='Intensität' value='" + exerciseObject.intensity + "'></div>" +
                                            "<div class='exerciseInputRow'><label for='time'>Dauer(sek): </label><input required onchange='exerciseValueChanged(this)' onkeyup='exerciseValueChanged(this)' name='time' min='0' step='1' type='number' placeholder='Dauer' value='" + exerciseObject.time + "'></div>" +
                                            "<div class='exerciseInputRow'><label for='pauseAfter'>Pause im Anschluss: </label><input required onchange='exerciseValueChanged(this)' onkeyup='exerciseValueChanged(this)' name='pauseAfter' min='0' step='1' type='number' placeholder='Pause' value='" + exerciseObject.pauseAfter + "'></div>";
                }
                return exercise;
            }

            function exerciseValueChanged(changedInputElement){
                // console.log(changedInputElement.name + " changed!")
                if(!changedInputElement.checkValidity()){
                    $(changedInputElement).addClass('errorClass');
                    return false;
                } else{
                    if(changedInputElement.name == "name"){
                        if(!renameExercise(activeExercise, changedInputElement.value)){
                            $(changedInputElement).addClass('errorClass');
                            return false;
                        }
                    }
                    $(changedInputElement).removeClass('errorClass');
                    activeExercise[changedInputElement.name] = changedInputElement.value;
                    return true;
                }
            }

            function doesNameExistInExerciseGroup(groupObj, name){
                // console.log(groupObj);
                let groupIndex = getIndexOfGroupName(groupObj.exerciseGroupName);
                // console.log("groupIndex: " + groupIndex);
                groupExercises = structure[groupIndex].exercises;   
                for (let index = 0; index < groupExercises.length; index++) {
                    if(groupExercises[index].name == name){
                        // console.log("exerciseName taken!");
                        return true;
                    }
                }
                return false;
            }

            function renameExercise(exerciseObject, name){
                if(name.length > 0 && !doesNameExistInExerciseGroup(structure[getIndexOfGroupName(exerciseObject.groupName)], name)){
                    getExerciseElement(exerciseObject.name, exerciseObject.groupName).querySelector(".exerciseName").innerText = name;
                    return true;
                }else{
                    return false;
                }
            }

            Object.size = function(obj) {
                var size = 0, key;
                for (key in obj) {
                    if (obj.hasOwnProperty(key)) size++;
                }
                return size;
            };
        </script>
        <style>

            .accordionContent{
                -webkit-box-shadow: inset 0px -11px 130px -128px rgba(0,0,0,0.75);
                -moz-box-shadow: inset 0px -11px 130px -128px rgba(0,0,0,0.75);
                box-shadow: inset 0px -11px 130px -128px rgba(0,0,0,0.75);
            }

            .accordionButton{
                border-top: 2px solid gray;
            }

            #createTrainingsBlueprint{
                overflow: hidden;
            }

            #exerciseGroups{
                vertical-align: top;
                display: inline-block;
                width: 300px;
                height: 400px;
            }

            #exerciseGroups button{ 
                padding: 0px;
                min-width: 25px;
            }

            #exerciseGroups input{
                border: none;
                border-bottom: 1px solid gray;
                padding: 5px;
            }

            .buttonRight{
                float: right;
                border: none;
                cursor: pointer;
                background-color: #CCC;
                margin-left: 3px;
            }

            .addButton{
                color: green;
            }

            .removeButton{
                color: red;
            }

            .leftButton{
                float: left;
            }

            .exerciseGroup{
                padding: 5px;
            }

            .exerciseItem{
                padding: 4px;
                width: 100%;
            }

            .errorClass{
                outline: 2px solid red;
            }

            #exersice{
                display: inline-block;
                margin-left: 100px;
            }

            #exersice textarea, #exersice input{
                padding: 5px;
                border: none;
                border-bottom: 1px solid black;
            }

            #exersice label{
                display: block;
            }

            .exerciseInputRow{
                display: block;
            }

            #sendArea{
                width: 100%;
            }

            #sendArea button{
                float: right;
                padding: 20px;
                font-size: 15pt;
                border: none;
                cursor: pointer;
            }
        </style>
    </main>
<?php
    include "../footer.php";
?>