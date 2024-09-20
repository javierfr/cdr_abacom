<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>AdminLTE 3 | DataTables</title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="<?=base_url();?>assets/plugins/fontawesome-free/css/all.min.css">
  <!-- iCheck for checkboxes and radio inputs -->
  <link rel="stylesheet" href="<?=base_url();?>assets/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="<?=base_url();?>assets/dist/css/adminlte.min.css">

  <!-- CSS Datatable -->
  <style type="text/css">
    thead input {
        width: 100%;
    }
  </style>
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">
  <!-- Navbar -->
  <?=$header?>
  <!-- /.navbar -->

  <!-- Main Sidebar Container -->
  <?=$menu_left?>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Sincronización</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Inicio</a></li>
              <li class="breadcrumb-item active">Sincronización</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-12">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Importar archivo de Excel a MySQL</h3>

                <div class="card-tools">
                  <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
                  <!-- <button type="button" class="btn btn-tool" data-card-widget="remove"><i class="fas fa-times"></i></button> -->
                </div>
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                <form id="form-subir-excel">
                  <div class="row justify-content-md-center">
                    <div class="col-md-5">
                      <!-- Date -->
                      <!-- <div class="form-group">
                        <label>Archivo:</label>
                        <div class="custom-file">
                          <input type="file" class="custom-file-input" id="inp-file-excel" name="inp-file-excel" accept=".xls,.xlsx,.csv">
                          <label class="custom-file-label" for="customFile">Seleccione archivo</label>
                        </div>
                      </div> -->
                      <div class="form-group">
                        <label for="exampleInputFile">Campo del archivo</label>
                        <div class="input-group">
                          <div class="custom-file">
                            <input type="file" class="custom-file-input" id="inp-file-excel" name="inp-file-excel">
                            <label class="custom-file-label" for="exampleInputFile">Elija el archivo</label>
                          </div>
                          <div class="input-group-append">
                            <button type="submit" class="input-group-text" id="btn-form-subir-excel" name="btn-form-subir-excel">Subir</button>
                          </div>
                        </div>
                      </div>
                      <!-- /.form group -->
                    </div>
                    <!-- /.col -->
                    <!-- <div class="col-md-2">
                      <button type="button" id="btn-form-sincronizar-calls" class="btn btn-primary btn-block" style="position: relative;top: 35%;">Importar</button>
                    </div> -->
                    <!-- /.col -->
                  </div>
                  <div class="row justify-content-md-center">
                    <div class="col-md-6">
                      <div id="message-excel-mysql">
                
                      </div>
                    </div>
                    <!-- /.col -->
                  </div>
                </form>
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Importar archivo de MySQL a ZOHO</h3>

                <div class="card-tools">
                  <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
                  <!-- <button type="button" class="btn btn-tool" data-card-widget="remove"><i class="fas fa-times"></i></button> -->
                </div>
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                  <div class="row justify-content-md-center" style="padding-bottom: 1%;">
                    <div class="col-md-2">
                      <button type="button" id="btn-form-insertar-calls" class="btn btn-primary btn-block">Sincronizar</button>
                    </div>
                    <!-- /.col -->
                  </div>
                  <div class="row justify-content-md-center">
                    <div class="col-md-6">
                      <div id="message-mysql-zoho">
                
                      </div>
                    </div>
                    <!-- /.col -->
                  </div>
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->
          </div>
          <!-- /.col -->
        </div>
        <!-- /.row -->
      </div>
      <!-- /.container-fluid -->
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
  <?=$footer?>

  <!-- Control Sidebar -->
  <aside class="control-sidebar control-sidebar-dark">
    <!-- Control sidebar content goes here -->
  </aside>
  <!-- /.control-sidebar -->
</div>
<!-- ./wrapper -->

<!-- jQuery -->
<script src="<?=base_url();?>assets/plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="<?=base_url();?>assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="<?=base_url();?>assets/dist/js/adminlte.min.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="<?=base_url();?>assets/dist/js/demo.js"></script>
<!-- bs-custom-file-input -->
<script src="<?=base_url();?>assets/plugins/bs-custom-file-input/bs-custom-file-input.min.js"></script>

<!-- <script src="<?=base_url()?>assets/plugins/jquery-validation/jquery.validate.js"></script> -->
<script src="<?=base_url()?>assets/plugins/jquery-validation/jquery.validate.min.js"></script>
<!-- <script src="<?=base_url()?>assets/plugins/jquery-validation/additional-methods.js"></script> -->
<script src="<?=base_url()?>assets/plugins/jquery-validation/additional-methods.min.js"></script>
<!-- Page specific script -->

<script>
  $(function () {
    bsCustomFileInput.init();
  });

  $("#btn-form-subir-excel").on('click',function(){
    $("#form-subir-excel").validate({
        rules: {
          "inp-file-excel": {
            required: true,
            extension: "xls|xlsx|csv"
          }
        },
        messages: {
          "inp-file-excel": {
            required: "* Seleccionar archivo"
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
          uploadExcelAjax();
        }
    });
    
  });

  function uploadExcelAjax(){
    var fd = new FormData();
    var files = $('#inp-file-excel')[0].files[0];
    fd.append('file', files);

    console.log(fd);

    $.ajax({
      url: '<?= base_url();?>Sincronizar/uploadExcelAjax',
      type: 'post',
      data: fd,
      dataType: "JSON",
      contentType: false,
      processData: false,
      success: function(data) {
        var html = '';
          html += `<div class="alert alert-success alert-dismissible text-center">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                    <strong>Super!</strong> Proceso finalizado
                  </div>`;

          $('#message-excel-mysql').html(html);
      },
      error: function (jqXHR, textStatus, errorThrown) {
        var html = '';
          html += `<div class="alert alert-success alert-dismissible text-center">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                    <strong>Super!</strong> Proceso finalizado
                  </div>`;

          $('#message-excel-mysql').html(html);
      }
    });
  }

  $("#btn-form-insertar-calls").on('click',function(){
    uploadMysqlZohoAjax();
  });

  function uploadMysqlZohoAjax(){

    $.ajax({
      url: '<?= base_url();?>Sincronizar/uploadMysqlZohoAjax',
      type: 'post',
      dataType: "JSON",
      success: function(data) {
        var html = '';
          html += `<div class="alert alert-success alert-dismissible text-center">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                    <strong>Super!</strong> Proceso finalizado
                  </div>`;

          $('#message-mysql-zoho').html(html);
      },
      error: function (jqXHR, textStatus, errorThrown) {
        var html = '';
          html += `<div class="alert alert-success alert-dismissible text-center">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                    <strong>Super!</strong> Proceso finalizado
                  </div>`;

          $('#message-mysql-zoho').html(html);
      }
    });
  }

</script>

</body>
</html>