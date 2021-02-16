<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Send-me: Envie e receba com precisão, segurança e facilidade!</title>
    <!-- Favicon-->
    <link rel="icon" type="image/x-icon" href="assets/img/favicon.ico" />
    <script src="https://use.fontawesome.com/releases/v5.15.1/js/all.js" crossorigin="anonymous"></script>

    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700" rel="stylesheet" type="text/css" />
    <link href="https://fonts.googleapis.com/css?family=Lato:400,700,400italic,700italic" rel="stylesheet" type="text/css" />
    <!-- Core theme CSS (includes Bootstrap)-->
    <link href="/assets/css/app.css" rel="stylesheet" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body id="page-top">
<!-- Navigation-->
<nav class="navbar navbar-expand-lg bg-secondary text-uppercase fixed-top" id="mainNav">
    <div class="container">
        <a class="navbar-brand js-scroll-trigger" href="#page-top">Send-me</a>
        <button class="navbar-toggler navbar-toggler-right text-uppercase font-weight-bold bg-primary text-white rounded" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
            Menu
            <i class="fas fa-bars"></i>
        </button>
        <div class="collapse navbar-collapse" id="navbarResponsive">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item mx-0 mx-lg-1"><a class="nav-link py-3 px-0 px-lg-3 rounded js-scroll-trigger" href="#minhas-transacoes">Minhas Transações</a></li>
                <li class="nav-item mx-0 mx-lg-1"><a class="nav-link py-3 px-0 px-lg-3 rounded js-scroll-trigger" href="#" id="logout">Deslogar</a></li>
            </ul>
        </div>
    </div>
</nav>
<!-- Masthead-->
<header class="masthead bg-primary text-white text-center">
    <div class="container d-flex align-items-center flex-column">
        <!-- Masthead Avatar Image-->
        <img class="masthead-avatar mb-5" src="assets/img/avataaars.svg" alt="" />
        <!-- Masthead Heading-->
        <h1 class="masthead-heading text-uppercase mb-0">Bem Vindo !</h1>
        <h3 class="text-uppercase mb-0 recebeNomeUser"></h3>
        <!-- Icon Divider-->
        <div class="divider-custom divider-light">
            <div class="divider-custom-line"></div>
            <div class="divider-custom-icon"><i class="fas fa-star"></i></div>
            <div class="divider-custom-line"></div>
        </div>
        <!-- Masthead Subheading-->
        <p class="masthead-subheading font-weight-light mb-0">Parabéns, agora você tem toda facilidade da send-me para efetuar transferencias.</p>
    </div>
</header>

<!-- Portfolio Section-->
<section class="page-section portfolio" id="portfolio">
    <div class="container">
        <!-- Portfolio Section Heading-->
        <h2 class="page-section-heading text-center text-uppercase text-secondary mb-0">Ações</h2>
        <!-- Icon Divider-->
        <div class="divider-custom">
            <div class="divider-custom-line"></div>
            <div class="divider-custom-icon"><i class="fas fa-star"></i></div>
            <div class="divider-custom-line"></div>
        </div>
        <!-- Portfolio Grid Items-->
        <div class="row justify-content-center">
            <div id="recebeCarteiras">

            </div>
            <div class="col-md-12 mt-4">
                <div class="card">
                    <div class="card-header">
                        Minhas Transações
                    </div>
                    <div class="card-body">
                        <table class="table">
                            <thead class="thead-dark">
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Origem</th>
                                <th scope="col">Favorecido</th>
                                <th scope="col">Valor</th>
                                <th scope="col">Autorizado dia</th>
                                <th scope="col">Ações</th>
                            </tr>
                            </thead>
                            <tbody id="recebeTransacoes">

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- Copyright Section-->
<div class="copyright py-4 text-center text-white">
    <div class="container"><small>Copyright © Send-me 2020</small></div>
</div>
<!-- Scroll to Top Button (Only visible on small and extra-small screen sizes)-->
<div class="scroll-to-top d-lg-none position-fixed">
    <a class="js-scroll-trigger d-block text-center text-white rounded" href="#page-top"><i class="fa fa-chevron-up"></i></a>
</div>

<!-- Bootstrap core JS-->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.13.4/jquery.mask.min.js"></script>
<script>
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
        },
        statusCode: {
            401: function () {
                window.location = '/login'
            }
        }
    });
</script>
<script src="/assets/js/app.js"></script>
<script src="/assets/js/admin.home.js"></script>
<script type="text/javascript">
    $('#documento_cpf').mask('000.000.000-00');
    $('#documento_cnpj').mask('00.000.000/0000-00');
</script>

</body>
</html>
