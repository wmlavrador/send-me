$("#logout").click(function(){
   $.post("/logout").then(function(response){
       window.location = '/login';
       localStorage.removeItem("token");
   }).fail(function(){
       window.location = '/admin/home';
   });
});

$(function(){
    $.get('/api/user/me').then(function(response){
        $(".recebeNomeUser").html("Sr(a) " + response.nome_completo);
    });
});
