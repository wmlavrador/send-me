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
        localStorage.setItem("me", JSON.stringify(response));
    });

    // Get Transações.
    $.get("/api/transacoes/listar").then(function(response){
        var html = "";
        $.each(response, function(ind, transacao){

            html += `
                <tr>
                    <th scope="row">`+transacao['id']+`</th>
                    <td>`+transacao['pagador']+`</td>
                    <td>`+transacao['recebedor']+`</td>
                    <td>R$ `+transacao['valor']+`</td>
                    <td>`+moment(transacao['atualizado']).calendar()+`</td>
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
                        <a href="javascript:void(0)" class="btn btn-primary" onclick="depositar(`+carteira['id']+`)">Depositar</a>
                        <a href="javascript:void(0)" class="btn btn-primary" onclick="novaTransacao()">Transferir</a>
                    </div>
                </div>
            `;
        });

       $("#recebeCarteiras").html(html);
    }).fail(function(response){

    });

});

function depositar(){

    Swal.fire({
        title: 'Valor do depósito',
        input: 'number',
        showCancelButton: true,
        confirmButtonText: 'Depositar',
    }).then((result) => {
        if (result.isConfirmed) {
            $.post('/api/minhas-carteiras/depositar', {value: result.value})
            .then(function(response){
                Swal.fire({
                    title: 'Tudo certo!',
                    type: "success",
                    text: "Deposito concluido com sucesso!",
                    timer: 3500
                });
                setTimeout(function(){
                    location.reload();
                }, 3500);
            })
            .fail(function(response){
                Swal.fire({
                    title: 'Ops!',
                    type: "error",
                    text: "Não foi possível concluir o depósito, tente novamente",
                    timer: 3500
                });
            })
        }
    })


}

function novaTransacao() {
    $.get("/api/transacoes/destinatarios", function(response){
        var combo = "{";

        $.each(response, function(ind, val){
            if(ind == 0){
                combo += '"'+val.id+'" : "'+val.nome_completo+'"';
            }
            else {
                combo += ',"'+val.id+'" : "'+val.nome_completo+'"';
            }
        });
        combo += "}";

        combo = JSON.parse(combo);

        Swal.mixin({
            input: 'text',
            confirmButtonText: 'Avançar &rarr;',
            showCancelButton: true,
            progressSteps: ['1', '2']
        }).queue([
            {
                title: 'Selecione um destinatário',
                text: '',
                input: 'select',
                inputOptions: combo,
                inputPlaceholder: 'Destinatário',
                inputValidator: function (value) {
                    return new Promise(function (resolve, reject) {
                        if (value !== '') {
                            resolve();
                        } else {
                            resolve('Selecione o destinatário para continuar!');
                        }
                    });
                }
            },
            {
                title: 'Informe o valor da transferência',
                text: '',
                input: 'number',
                inputPlaceholder: 'Destinatário',
                inputValidator: function (value) {
                    return new Promise(function (resolve, reject) {
                        if (value !== '') {
                            resolve();
                        } else {
                            resolve('Informe um valor para continuar!');
                        }
                    });
                }
            },
        ]).then((result) => {
            if (result.value) {
                const respostas = result.value;

                var dados = {
                    payer: JSON.parse(localStorage.getItem("me"))['id'],
                    payee: respostas[0],
                    value: respostas[1]
                }
                $.post("/api/transaction", dados).then(function(response){
                    Swal.fire({
                        title: 'Tudo certo!',
                        type: "success",
                        text: "Sua transferência foi realizada com sucesso!",
                        timer: 3500
                    });
                    setTimeout(function(){
                        location.reload();
                    }, 3500);
                })
                .fail(function(response){
                    Swal.fire({
                        type: "error",
                        title: "Infelizmente não foi possível concluir a transferência!"
                    });
                });

            }
        })
    });

};

function devolver(id){
    Swal.fire({
        text: 'Tem certeza que deseja devovler ?',
        title: "Atenção!",
        showDenyButton: true,
        showCancelButton: false,
        confirmButtonText: `Sim`,
        denyButtonText: `Cancelar`,
    }).then((result) => {
        if (result.isConfirmed) {
            $.post("/api/transacoes/devolver/"+id).then(function(response){
                Swal.fire('Tudo certo!', 'Montante devolvido com sucesso!', 'success')
                setTimeout(function(){
                    location.reload();
                }, 3500)
            }).fail(function(response){
                Swal.fire('Ops!', 'Não foi possível devolver esta transação.', 'error')
            });
        }
    });
}

function estornar(id){
    Swal.fire({
        title: 'Atenção',
        text: "Tem certeza que quer pedir o estorno deste valor?",
        showDenyButton: true,
        showCancelButton: false,
        confirmButtonText: `Sim`,
        denyButtonText: `Cancelar`,
    }).then((result) => {
        if (result.isConfirmed) {
            $.post("/api/transacoes/estornar/"+id).then(function(response){
                Swal.fire('Tudo certo!', 'O Valor foi estornado com sucesso!', 'success');
                setTimeout(function(){
                    location.reload();
                }, 3500)
            }).fail(function(response){
                Swal.fire('Ops!', 'Não foi possível estornar esta transação', 'error')
            });
        }
    });
}
