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
            <form>
                @csrf
                <input type="text" id="email" class="fadeIn second" name="email" placeholder="E-mail">
                <input type="text" id="senha" class="fadeIn third" name="senha" placeholder="Senha">
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
</body>
</html>
