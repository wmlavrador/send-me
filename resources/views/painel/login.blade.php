<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>My Transfer -</title>
    <!-- Favicon-->
    <link rel="icon" type="image/x-icon" href="assets/img/favicon.ico" />

    <link href="//maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
    <link rel="stylesheet" href="/assets/css/login.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
    <div class="wrapper fadeInDown">

        <center><a href="/"><small>Voltar para o início</small></a></center>
        <div id="formContent" class="mt-2">
            <!-- Icon -->
            <div class="fadeIn first p-3">
                <h5>Identifique-se</h5>
            </div>

            <!-- Login Form -->
            <form id="formLogin" novalidate>
                <div id="show-erros-form"></div>

                <input type="text" id="email" class="fadeIn second" name="email" placeholder="E-mail">
                <input type="password" id="password" class="fadeIn third" name="senha" placeholder="Senha">
                <input type="submit" class="fadeIn fourth" value="Entrar">
            </form>

            <!-- Remind Passowrd -->
            <div id="formFooter">
                <a class="underlineHover" href="#">Esqueceu sua senha?</a>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    </script>

    <script>
        $("#formLogin").on("submit", function(event){
            var form = document.getElementById("formLogin");

            event.preventDefault();
            event.stopPropagation();

            var email = $("#formLogin #email").val();
            var password = $("#formLogin #password").val();

            if(form.checkValidity()){
                $.post("/login", {email: email, password: password}).then(function(response){
                    localStorage.setItem("token", response);
                    window.location = "/admin/home";
                }).fail(function(response){
                    $erros = "";
                    $.each(response.responseJSON.errors, function(ind, erro){
                        $.each(erro, function(ind, message){
                            $erros += `
                            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                              `+message+`
                              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                              </button>
                            </div>
                            `;
                        });
                    });

                    $("#show-erros-form").html($erros);
                })
            }

        });
    </script>
</body>
</html>
