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
        localStorage.setItem("me", response);
    });

    // Get Transações.
    $.get("/api/transacoes/listar").then(function(response){
        var html = "";
        $.each(response, function(ind, transacao){

            var acoes = ``;

            html += `
                <tr>
                    <th scope="row">`+transacao['id']+`</th>
                    <td>`+transacao['pagador']+`</td>
                    <td>`+transacao['recebedor']+`</td>
                    <td>R$ `+transacao['valor']+`</td>
                    <td>`+moment(transacao['atualizado']).startOf('hour').fromNow()+`</td>
                    <td>`+transacao['situacao']+`</td>
                    <td>
                        <div class="dropdown show">
                          <a class="btn btn-secondary dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Ações
                          </a>

                          <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                            <a class="dropdown-item" href="javascript:void" onclick="devolver(`+transacao['id']+`)">Devolver</a>
                            <a class="dropdown-item" href="javascript:void" onclick="estornar(`+transacao['id']+`)">Estornar</a>
                          </div>
                        </div>
                    </td>
                </tr>
            `;
        });

        $("#recebeTransacoes").html(html);
    });

    // get carteiras
    $.get("/api/minhas-carteiras/listar").then(function(response){
       var html = "";

        $.each(response, function(ind, carteira){
            html += `
                <div class="card">
                    <div class="card-header">
                       `+carteira['descricao']+`
                    </div>
                    <div class="card-body">
                        <h5 class="card-title"><small>saldo </small><span id="recebeSaldo">R$ `+carteira['saldo']+`</span></h5>
                        <a href="#" class="btn btn-primary" id="depositar">Depositar</a>
                        <a href="#" class="btn btn-primary" id="novaTransacao">Transferir</a>
                    </div>
                </div>
            `;
        });

       $("#recebeCarteiras").html(html);
    }).fail(function(response){

    });

    $("#novaTransacao").click(function(){
        var payee = $("#payee").val();
        var value = $("#valueTrns").val();

        var dados = {
            payer: localStorage.getItem("me")['id'],
            payee: payee
        };

        $.post("/api/transaction", dados).then(function(response){
           console.log(response)
        }).fail(function(response){
            console.log( "FAIL" )
        });

    });

});

function devolver(id){
    Swal.fire({
        title: 'Tem certeza que deseja devovler ?',
        showDenyButton: true,
        showCancelButton: false,
        confirmButtonText: `Sim`,
        denyButtonText: `Cancelar`,
    }).then((result) => {
        if (result.isConfirmed) {
            $.post("/api/transacoes/devolver/"+id).then(function(response){
                Swal.fire('Montante devolvido com sucesso!', '', 'success')
                window.reload();
            }).fail(function(response){
                Swal.fire('Não foi possível devolver esta transação.', '', 'error')
            });
        }
    });
}

function estornar(id){
    Swal.fire({
        title: 'Tem certeza que quer pedir o estorno deste valor?',
        showDenyButton: true,
        showCancelButton: false,
        confirmButtonText: `Sim`,
        denyButtonText: `Cancelar`,
    }).then((result) => {
        if (result.isConfirmed) {
            $.post("/api/transacoes/estornar/"+id).then(function(response){
                Swal.fire('O Valor foi estornado com sucesso!', '', 'success');
                window.reload();
            }).fail(function(response){
                Swal.fire('Não foi possível estornar esta transação', '', 'error')
            });
        }
    });
}
