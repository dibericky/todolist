$(document).ready(function(){
    const name = Cookies.get('username');
    setName(name);
    getUserTasks();
    showButtonAddTask(true)
});
function deletehandler(e){
    const target = $(e.target)[0]
    const id = target.dataset.id
    const token = Cookies.get('token');
    const url = `http://localhost/myproject/todolist/api/task/?id=${id}&token=${token}`
    const success = function(res){
        console.log(res)
        $("#taskId"+id).hide();
    }
    const statusCode = {
        401: invalidToken,
        500: serverError
    }
    call(url, 'DELETE', success, statusCode)
}
function logout(){
    Cookies.remove('username')
    Cookies.remove('id')
    Cookies.remove('token')
    location.href='index.html'
}
function setName(name){
    if(name === null || name === undefined){
        name = ""
    }
    $("#title-app").text("Ciao, "+name);
}

function getUserTasks(state){
    let stateNum
    if(state !== undefined && (state === 1 || state === 2 || state === 3)){
        stateNum = state
        state = '&state='+state
    }else{
        state = ''
        stateNum = 0
    }
    const token = Cookies.get('token');
    const url = 'http://localhost/myproject/todolist/api/user/tasks/?token='+token+state
    const type = 'GET'
    const statusCode = {
        401: invalidToken,
        500: serverError
    }
    var success = (response) => {  
        if(response.data.length>0){
            let content = ""
            response.data.forEach((d)=>{
                content += dataToTaskHtml(d);
            })
            $("#task-container").html(content);
            let children = $("#task-container")[0].children;
            for(i = 0; i < children.length; i++){
                makeAnimation(children, 0);
            }
            bindFunctionsToButtons()
        }else{
            $("#task-container").html("<h2 id='noTask' class='animated'>No task</h2>");
            $("#noTask").addClass('fadeInLeft')
        }

        const buttons = $(".showBtt")
        console.log(buttons)
        for(i = 0; i < buttons.length; i++){
            $(buttons[i]).addClass((buttons[i].dataset.state == stateNum)?'btn-primary':'btn-outline-primary')
            $(buttons[i]).removeClass((buttons[i].dataset.state == stateNum)?'btn-outline-primary':'btn-primary')
        }
    }
    call(url, type, success, statusCode)
}

function dataToTaskHtml(data){
    return ""+
`<div id='taskId${data.id}' class='taskCard animated col-sm-12 col-md-6 state-${data.state}'>`+
    `<div class='card'>`+
        "<div class=\"card-body\">"+
        `<h5 class=\"card-title\">${data.title}</h5>`+
    `      <h6 class=\"card-subtitle mb-2 text-muted\">${data.date}</h6>`+
    `      <p class=\"card-text\">${data.description}</p>`+
    getButtons(data)+
            `<input type='hidden' class='_id' value='${data.id}'>`+
    "   </div>"+
    "</div>"+
"</div>";
}
function prova(e){
    console.log(e);
}
function getButtons(data){
    return ""+
    `<div data-id=${data.id} class="btn-group" role="group" aria-label="State">`+
    `  <button data-state=1 type="button" ${(data.state == 1)?'disabled':''} class="btn btn-secondary btt-state">ToDo</button>`+
    `  <button data-state=2 type="button" ${(data.state == 2)?'disabled':''} class="btn btn-secondary btt-state">Running</button>`+
    ` <button data-state=3 type="button" ${(data.state == 3)?'disabled':''} class="btn btn-secondary btt-state">Done</button>`+
    "</div>"+
`<button data-id=${data.id} class=\"card-link delete\">X</button>`
}
function stateButtonHandler(event){
    const target = $(event.target)[0]
    const state = target.dataset.state
    const id = target.parentElement.dataset.id
    const url = `http://localhost/myproject/todolist/api/task/?id=${id}&token=${getToken()}`
    const body = {
        state: state
    }
    const success = function(res){
        updateCard(res.data)
    }
    const statusCode = {
        401: invalidToken,
        500: serverError
    }
    callWithBody(url, 'PUT', success, statusCode, body)
}
function bindFunctionsToButtons(){
    $(".card-link.delete").on('click', deletehandler)
    $(".btt-state").on('click', stateButtonHandler)
}
function makeAnimation(children, index){
    if(index >= children.length)return

    return setTimeout(function(){
        $(children[index]).addClass('fadeInLeft')
        makeAnimation(children, index+1);
    }, 150);
}
function callWithBody(url, type, success, statusCode, body){
    console.log(url)
    $.ajax({
        url: url,
        type: type,
        success: success,
        data: body,
        statusCode: statusCode
    });
}
function call(url, type, success, statusCode){
    console.log(url)
    $.ajax({
        url: url,
        type: type,
        success: success,
        statusCode: statusCode
    });
}
function invalidToken(res){
    alert("Invalid token");
    location.href = 'index.html'
}
function serverError(res){
    alert("Server error...");
}
function getToken(){
    return Cookies.get('token');
}
function updateCard(data){
    const id = data.id
    const buttons = $('#taskId'+id+' .btt-state')
    for(i = 0; i < buttons.length; i++){
        $(buttons[i]).prop('disabled', buttons[i].dataset.state == data.state)
    }
}
function closeNewTask(){
    const inDiv = 'fadeInLeft'
    const outDiv = 'fadeOutRight'
    $("#titleNewTask").val('')
    $("#descriptionNewTask").val('')
    $("#newTaskContainer").removeClass(inDiv)
    $("#newTaskContainer").addClass(outDiv)
    $("#newTaskContainer").hide()
    $("#mainTaskContainer").removeClass(outDiv)
    $("#mainTaskContainer").show()
    $("#mainTaskContainer").addClass(inDiv)
    showButtonAddTask(true)
}
function addTask(){
    showButtonAddTask(false)
    const inDiv = 'fadeInLeft'
    const outDiv = 'fadeOutRight'
    $("#mainTaskContainer").removeClass(inDiv)
    $("#mainTaskContainer").addClass(outDiv)
    $("#mainTaskContainer").hide()
    $("#newTaskContainer").removeClass(outDiv)
    $("#newTaskContainer").show()
    $("#newTaskContainer").addClass(inDiv)
}
function showButtonAddTask(show){
    console.log(show)
    $("#addTask").removeClass(show?'zoomOut':'zoomIn')
    $("#addTask").addClass(show?'zoomIn':'zoomOut')
}
function submitTask(){
    console.log('submit...')
    const title = $("#titleNewTask").val()
    if(title == '')return noTitleSet()
    const description = $("#descriptionNewTask").val()
    if(description == '')return noDescriptionSet()
    
    const url = 'http://localhost/myproject/todolist/api/task/?token='+getToken()
    const success = function(res){
        $("#logNewTask").text('Task created!')
        setTimeout(function(){
            $("#titleNewTask").val('')
            $("#descriptionNewTask").val('')
            $("#logNewTask").text('')
            getUserTasks(1)
            closeNewTask()
        },500)
    }
    const badRequest = function(res){
        console.log(res)
    }
    const statusCode = {
        401: invalidToken,
        400: badRequest,
        500: serverError,
        503: function(res){
            alert('Unable to create task')
        }
    }
    const body = {
        title: title,
        description: description,
        state: 1
    }
    callWithBody(url, 'POST', success, statusCode, body)
}
function noTitleSet(){
    $("#titleNewTask").focus()
}
function noDescriptionSet(){
    $("#descriptionNewTask").focus()
}
