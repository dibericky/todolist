$(document).ready(function () {
    $("#btt_login").on("click", function(){
        var username = $("#username").val();
        var password = $("#password").val();
    
        $.ajax({
            url: 'http://localhost/myproject/todolist/api/auth/login.php',
            data: {
                nickname: username,
                password: password
            },
            type: 'POST',
            success: function (response) {  
                let {token, id} = response.data;
                console.log(token);
                console.log(id);
                Cookies.set('token', token, { expires: 7 });
                Cookies.set('id', id, { expires: 7 });
                console.log(Cookies.get());
                
            },
            statusCode: {
                401: function(res){
                    alert(res.responseJSON.error);
                }
            }
            
        });
    });
    $("#btt_register").on("click", function(){

    });
    
});
