<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Abacom Telecomunicaciones | Log in</title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="<?=base_url();?>assets/plugins/fontawesome-free/css/all.min.css">
  <!-- icheck bootstrap -->
  <link rel="stylesheet" href="<?=base_url();?>assets/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="<?=base_url();?>assets/dist/css/adminlte.min.css">

  <link href="<?=base_url()?>assets/images/icon.png" type="image/x-icon" rel="shortcut icon" />
</head>
<body class="hold-transition login-page">
<div class="login-box">
  <div class="login-logo">
    <img src="<?=base_url()?>assets/images/logo_blue.png" class="img-responsive" alt="Abacom - Telecomunicaciones" width="70%">
    <!-- <a href="../../index2.html" style="color: #fff"><b>Grupo-</b>Esscalo</a> -->
  </div>
  <!-- /.login-logo -->
  <div class="card">
    <div class="card-body login-card-body">
      <p class="login-box-msg">INICIO DE SESIÓN</p>
      <div id="alert">
              
      </div>
      <form id="form-login">
        <div class="input-group mb-3">
          <input type="email" class="form-control" id="inp-login-email" name="inp-login-email" placeholder="Email">
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-envelope"></span>
            </div>
          </div>
        </div>
        <div class="input-group mb-3">
          <input type="password" class="form-control" id="inp-login-password" name="inp-login-password" placeholder="Contraseña">
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-lock"></span>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-8">
            <!-- <div class="icheck-primary">
              <input type="checkbox" id="remember">
              <label for="remember">
                Recuérdame
              </label>
            </div> -->
          </div>
          <!-- /.col -->
          <div class="col-4">
            <button type="submit" class="btn btn-primary btn-block" id="btn-form-login">Ingresar</button>
          </div>
          <!-- /.col -->
        </div>
      </form>

      <!-- <div class="social-auth-links text-center mb-3">
        <p>- O -</p>
        <a href="#" class="btn btn-block btn-primary">
          <i class="fab fa-facebook mr-2"></i> Ingresar con Facebook
        </a>
        <a href="#" class="btn btn-block btn-danger">
          <i class="fab fa-google-plus mr-2"></i> Ingresar con Google+
        </a>
      </div> -->
      <!-- /.social-auth-links -->

      <!-- <p class="mb-1">
        <a href="forgot-password.html">Olvidé mi contraseña</a>
      </p>
      <p class="mb-0">
        <a href="register.html" class="text-center">Registrar nuevo usuario</a>
      </p> -->
    </div>
    <!-- /.login-card-body -->
  </div>
</div>
<!-- /.login-box -->

<!-- jQuery -->
<script src="<?=base_url();?>assets/plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="<?=base_url();?>assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="<?=base_url();?>assets/dist/js/adminlte.min.js"></script>
<!-- JQuery Validate -->
<script src="<?=base_url();?>assets/plugins/jquery-validation/jquery.validate.min.js"></script>
<script src="<?=base_url();?>assets/plugins/jquery-validation/additional-methods.min.js"></script>
<script>
  $("#btn-form-login").on('click',function(){

    $("#form-login").validate({
        rules: {
          "inp-login-email": {
            required: true,
            email: true
          },
          "inp-login-password": {
            required: true
          }
        },
        messages: {
          "inp-login-email": {
            required: "* Ingresar Correo",
            email: "* Ingresar Correo Valido"
          },
          "inp-login-password": {
            required: "* Ingresar Contraseña"
          }
        },
        errorElement: 'span',
        errorPlacement: function (error, element) {
          error.addClass('invalid-feedback');
          element.closest('.form-group').append(error);
        },
        highlight: function (element, errorClass, validClass) {
          $(element).addClass('is-invalid');
        },
        unhighlight: function (element, errorClass, validClass) {
          $(element).removeClass('is-invalid');
        },
        submitHandler: function(form){
          crearSesionAjax();
        }
    });

  });

  function crearSesionAjax(){

    $.ajax({ 
        url : '<?= base_url();?>Auth/crearSesionAjax',
        type: "POST",
        data: $('#form-login').serialize(),
        dataType: "JSON",
        success: function(data) {
          location.href ='<?= base_url();?>Clientes';
        },
        error: function (jqXHR, textStatus, errorThrown) {
          var html = '';
          html += `<div class="alert alert-danger alert-dismissible text-center">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                    <strong>Error!</strong> Usuario / Contraseña incorrectos
                  </div>`;

          $('#alert').html(html);
        }
    });
  }
</script>
</body>
</html>
