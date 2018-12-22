const token = Cookies.get('token');
if(token !== undefined && token !== null){
    location.href = 'todo.html'
}
$(document).ready(function () {
    $("#btt_login").on("click", function(){
        var username = $("#username").val();
        if(username == ''){
            return cannotBeEmpty("username")
        }
        var password = $("#password").val();
        if(password == ''){
            return cannotBeEmpty("password")
        }
        $.ajax({
            url: 'http://localhost/myproject/todolist/api/auth/login.php',
            data: {
                nickname: username,
                password: password
            },
            type: 'POST',
            success: successfulHandler,
            statusCode: {
                401: function(res){
                    log(res.responseJSON.error);
                }
            }
            
        });
    });
    $("#btt_register").on("click", function(){
        var username = $("#username").val();
        if(username == ''){
            return cannotBeEmpty("username")
        }
        var password = $("#password").val();
        if(password == ''){
            return cannotBeEmpty("password")
        }
        $.ajax({
            url: 'http://localhost/myproject/todolist/api/user/',
            data: {
                nickname: username,
                password: password
            },
            type: 'POST',
            success: successfulHandler,
            statusCode: {
                401: function(res){
                    log(res.responseJSON.error);
                },
                503: function(res){
                    log(res.responseJSON.error);
                },
                400: function(res){
                    log("invalid input data");
                }
            }
            
        });
    });
    
});
function log(msg){
    $("#log").text(msg)
}
function cannotBeEmpty(field){
    $("#log").text("Field "+field+" cannot be empty");
    return $("#"+field).focus()
}
function successfulHandler(response) { 
    console.log(response) 
    $("#log").val('')
    let {token, id, username} = response.data;
    Cookies.set('token', token, { expires: 7 });
    Cookies.set('id', id, { expires: 7 });
    Cookies.set('username', username, { expires: 7 });
    $("#title-welcome").show();
    $("#title-app").addClass("fadeOutRight");
    $("#title-app").hide();
    $("#title-welcome").addClass("fadeInLeft");
    location.href = "todo.html";
}