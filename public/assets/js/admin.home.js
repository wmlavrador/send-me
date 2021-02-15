$("#logout").click(function(){
   $.post("/logout").then(function(response){
       window.location = '/login';
       localStorage.removeItem("token");
   }).fail(function(){
       window.location = '/admin/home';
   });
});

$(function(){
    // Get Informações do Usuário logado.
    $.get('/api/user/me').then(function(response){
        $(".recebeNomeUser").html("Sr(a) " + response.nome_completo);
    });

    // Get Transações.
    $.get("/api/transacoes/listar").then(function(response){
        console.log();
    });

    $("#novaTransacao").click(function(){
        var dados = {
            payee: $()
        };

        $.post("/api/transactio", dados).then(function(response){
           console.log(response)
        }).fail(function(response){
            console.log( "FAIL" )
        });

    });


});
