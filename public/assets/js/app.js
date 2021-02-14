(function($) {
    "use strict"; // Start of use strict

    // Smooth scrolling using jQuery easing
    $('a.js-scroll-trigger[href*="#"]:not([href="#"])').click(function() {
        if (location.pathname.replace(/^\//, '') == this.pathname.replace(/^\//, '') && location.hostname == this.hostname) {
            var target = $(this.hash);
            target = target.length ? target : $('[name=' + this.hash.slice(1) + ']');
            if (target.length) {
                $('html, body').animate({
                    scrollTop: (target.offset().top - 71)
                }, 1000, "easeInOutExpo");
                return false;
            }
        }
    });

    // Scroll to top button appear
    $(document).scroll(function() {
        var scrollDistance = $(this).scrollTop();
        if (scrollDistance > 100) {
            $('.scroll-to-top').fadeIn();
        } else {
            $('.scroll-to-top').fadeOut();
        }
    });

    // Closes responsive menu when a scroll trigger link is clicked
    $('.js-scroll-trigger').click(function() {
        $('.navbar-collapse').collapse('hide');
    });

    // Activate scrollspy to add active class to navbar items on scroll
    $('body').scrollspy({
        target: '#mainNav',
        offset: 80
    });

    // Collapse Navbar
    var navbarCollapse = function() {
        if ($("#mainNav").offset().top > 100) {
            $("#mainNav").addClass("navbar-shrink");
        } else {
            $("#mainNav").removeClass("navbar-shrink");
        }
    };
    // Collapse now if page is not at top
    navbarCollapse();
    // Collapse the navbar when page is scrolled
    $(window).scroll(navbarCollapse);

    // Floating label headings for the contact form
    $(function() {
        $("body").on("input propertychange", ".floating-label-form-group", function(e) {
            $(this).toggleClass("floating-label-form-group-with-value", !!$(e.target).val());
        }).on("focus", ".floating-label-form-group", function() {
            $(this).addClass("floating-label-form-group-with-focus");
        }).on("blur", ".floating-label-form-group", function() {
            $(this).removeClass("floating-label-form-group-with-focus");
        });
    });

})(jQuery); // End of use strict

$("#isPerfilLojista").click(function(event){
    var isChecked = $(this).prop("checked");
    var doc_cpf = $("#doc_cpf");
    var doc_cnpj = $("#doc_cnpj");

    if(isChecked){
        doc_cnpj.show(300);
        doc_cpf.hide();
    }
    else {
        doc_cnpj.hide();
        doc_cpf.show(300);
    }

});

$("#formCadastro").on("submit", function(event){
    var form = document.getElementById("formCadastro");

    if($("#isPerfilLojista").prop("checked")){
        $("#documento_cnpj").prop("required", true);
        $("#documento_cpf").prop("required", false);
    }
    else {
        $("#documento_cnpj").prop("required", false);
        $("#documento_cpf").prop("required", true);
    }

    event.preventDefault();
    event.stopPropagation();

    if (form.checkValidity())
    {

        var docCpf = $("#formCadastro #documento_cpf").val();
        var docCnpj = $("#formCadastro #documento_cnpj").val();
        var documento = "";

        if(docCpf == ""){
            documento = docCnpj;
        }
        else {
            documento = docCpf;
        }

        var dados = {
          nome_completo: $("#formCadastro #nome_completo").val(),
          email: $("#formCadastro #email").val(),
          password: $("#formCadastro #password").val(),
          password_confirmation: $("#formCadastro #password").val(),
          documento: documento,
        };

        $.post("/api/user/create", dados).then(function(respoonse){
            setTimeout(function(){
                window.location = "/admin/home";
            }, 2000)
        }).fail(function(response){
            $(".loading-modal").hide();

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
        });
    }

    $(this).addClass('was-validated');

});
