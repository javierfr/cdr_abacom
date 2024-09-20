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
  <!-- DataTables -->
  <link rel="stylesheet" href="<?=base_url();?>assets/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
  <link rel="stylesheet" href="<?=base_url();?>assets/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
  <link rel="stylesheet" href="<?=base_url();?>assets/plugins/datatables-buttons/css/buttons.bootstrap4.min.css">

  <!-- Rango de fechas -->
  <link rel="stylesheet" href="<?=base_url()?>assets/plugins/daterangepicker/daterangepicker.css">
  <link rel="stylesheet" href="<?=base_url()?>assets/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">
  <link rel="stylesheet" href="<?=base_url()?>assets/plugins/select2/css/select2.min.css">
  <link rel="stylesheet" href="<?=base_url()?>assets/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">
  <link rel="stylesheet" href="<?=base_url()?>assets/plugins/bootstrap4-duallistbox/bootstrap-duallistbox.min.css">
  <link rel="stylesheet" href="<?=base_url()?>assets/plugins/bs-stepper/css/bs-stepper.min.css">
  <link rel="stylesheet" href="<?=base_url()?>assets/plugins/dropzone/min/dropzone.min.css">

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

  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 id="razon_social" value="<?=$lineas[0]->razon_social?>">
              <?php 
              if(!empty($lineas)){
                echo($lineas[0]->razon_social);
              }?>  
            </h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Inicio</a></li>
              <li class="breadcrumb-item active">Llamadas</li>
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
                <h3 class="card-title">Filtros</h3>

                <div class="card-tools">
                  <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
                  <!-- <button type="button" class="btn btn-tool" data-card-widget="remove"><i class="fas fa-times"></i></button> -->
                </div>
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                <form id="form-filtro-calls">
                  <div class="row">
                    <div class="col-md-5">
                      <!-- Date -->
                      <div class="form-group">
                        <label>Líneas:</label>
                        <select id="select-lienas" name="select-lienas" class="form-control select2bs4" style="width: 100%;">
                          <option selected="selected">Todas</option>
                          <?php
                            if (!empty($lineas)) {
                              foreach($lineas as $value){
                          ?>
                          <option value="<?=$value->linea;?>"><?=$value->linea;?></option>
                          <?php
                              }     
                            }
                          ?>

                          <!-- <option>Alaska</option>
                          <option>California</option>
                          <option>Delaware</option>
                          <option>Tennessee</option>
                          <option>Texas</option>
                          <option>Washington</option> -->
                        </select>
                      </div>
                      <!-- /.form group -->
                    </div>
                    <!-- /.col -->
                    <div class="col-md-5">
                      <!-- Date range -->
                      <div class="form-group">
                        <label>Rango Fechas:</label>

                        <div class="input-group">
                          <div class="input-group-prepend">
                            <span class="input-group-text">
                              <i class="far fa-calendar-alt"></i>
                            </span>
                          </div>
                          <input type="text" id="inp-filtro-calls-fechas" name="inp-filtro-calls-fechas" class="form-control float-right">
                        </div>
                        <!-- /.input group -->
                      </div>
                      <!-- /.form group -->
                    </div>
                    <!-- /.col -->
                    <div class="col-md-2">
                      <button type="submit" id="btn-form-filtro-calls" name="btn-form-filtro-calls" class="btn btn-primary btn-block" value="<?=$rfc;?>" style="position: relative;top: 35%;">Buscar</button>
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
                <h3 class="card-title">DataTable with default features</h3>
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                <table id="tb-calls-usuario" class="table table-bordered table-striped">
                  <thead>
                  <tr>
                    <th>#</th>
                    <th>Origen</th>
                    <th>Destino</th>
                    <th>Poblacion Destino</th>
                    <th>Fecha</th>
                    <th>Duracion</th>
                    <th>Monto Final</th>
                    <th>Tarifa Base</th>
                    <th>Tipo Trafico</th>
                    <th>Tipo Tel. Destino</th>
                  </tr>
                  </thead>
                  <tbody>
                    <?php
                      if (!empty($llamadas)) {
                        $row = 1;
                        foreach($llamadas as $value){
                    ?>
                          <tr>
                              <td>
                                  <?=$row?>
                              </td>
                              <td>
                                  <?=$value->origen;?>
                              </td>
                              <td>
                                  <?=$value->destino;?>
                              </td>
                              <td>
                                  <?=$value->poblacion_destino;?>
                              </td>
                              <td>
                                  <?=$value->fecha;?>
                              </td>
                              <td>
                                  <?=$value->duracion;?>
                              </td>
                              <td>
                                  <?=$value->monto_final;?>
                              </td>
                              <td>
                                  <?=$value->tarifa_base;?>
                              </td>
                              <td>
                                  <?=$value->tipo_trafico;?>
                              </td>
                              <td>
                                  <?=$value->tipo_tel_destino;?>
                              </td>
                          </tr>
                    <?php
                          $row++;
                        }     
                      }
                    ?>
                  </tbody>
                </table>
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
<!-- DataTables  & Plugins -->
<script src="<?=base_url();?>assets/plugins/datatables/jquery.dataTables.min.js"></script>
<script src="<?=base_url();?>assets/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="<?=base_url();?>assets/plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
<script src="<?=base_url();?>assets/plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
<script src="<?=base_url();?>assets/plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
<script src="<?=base_url();?>assets/plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
<script src="<?=base_url();?>assets/plugins/jszip/jszip.min.js"></script>
<script src="<?=base_url();?>assets/plugins/pdfmake/pdfmake.min.js"></script>
<script src="<?=base_url();?>assets/plugins/pdfmake/vfs_fonts.js"></script>
<script src="<?=base_url();?>assets/plugins/datatables-buttons/js/buttons.html5.min.js"></script>
<script src="<?=base_url();?>assets/plugins/datatables-buttons/js/buttons.print.min.js"></script>
<script src="<?=base_url();?>assets/plugins/datatables-buttons/js/buttons.colVis.min.js"></script>
<!-- AdminLTE App -->
<script src="<?=base_url();?>assets/dist/js/adminlte.min.js"></script>
<!-- AdminLTE for demo purposes -->
<!-- <script src="<?=base_url();?>assets/dist/js/demo.js"></script> -->
<!-- bs-custom-file-input -->
<script src="<?=base_url();?>assets/plugins/bs-custom-file-input/bs-custom-file-input.min.js"></script>

<!-- <script src="<?=base_url()?>assets/plugins/jquery-validation/jquery.validate.js"></script> -->
<script src="<?=base_url()?>assets/plugins/jquery-validation/jquery.validate.min.js"></script>
<!-- <script src="<?=base_url()?>assets/plugins/jquery-validation/additional-methods.js"></script> -->
<script src="<?=base_url()?>assets/plugins/jquery-validation/additional-methods.min.js"></script>
<!-- Page specific script -->

<!-- Rango de fechas -->
<script src="<?=base_url();?>assets/plugins/select2/js/select2.full.min.js"></script>
<script src="<?=base_url();?>assets/plugins/bootstrap4-duallistbox/jquery.bootstrap-duallistbox.min.js"></script>
<script src="<?=base_url();?>assets/plugins/moment/moment.min.js"></script>
<script src="<?=base_url();?>assets/plugins/inputmask/jquery.inputmask.min.js"></script>
<script src="<?=base_url();?>assets/plugins/daterangepicker/daterangepicker.js"></script>
<script src="<?=base_url();?>assets/plugins/bootstrap-colorpicker/js/bootstrap-colorpicker.min.js"></script>
<script src="<?=base_url();?>assets/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>
<script src="<?=base_url();?>assets/plugins/bootstrap-switch/js/bootstrap-switch.min.js"></script>
<script src="<?=base_url();?>assets/plugins/bs-stepper/js/bs-stepper.min.js"></script>
<script src="<?=base_url();?>assets/plugins/dropzone/min/dropzone.min.js"></script>
<script src="<?=base_url();?>assets/dist/js/adminlte.min.js?v=3.2.0"></script>
<script src="<?=base_url();?>assets/dist/js/demo.js"></script>

<script>
  $(document).ready( function () {
    var dt_clientes_llamadas = $('#tb-calls-usuario').DataTable();
  } );

  $(function(){
    //Date range picker
    // $('#inp-filtro-calls-fechas').daterangepicker()
    $("#inp-filtro-calls-fechas").daterangepicker({
        locale: {
            format: 'YYYY/MM/DD'
        }
    });
  });

  $("#btn-form-filtro-calls").on('click',function(){
    $("#form-filtro-calls").validate({
        rules: {
          "inp-filtro-calls-fechas": {
            required: true,
          }
        },
        messages: {
          "inp-filtro-calls-fechas": {
            required: "* Seleccionar rango fechas"
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
          // uploadExcelAjax();
          // alert("Todo bien");
          filtrarLlamadasAjax();
        }
    });
    
  });

  function filtrarLlamadasAjax(){

    $('#tb-calls-usuario').DataTable().destroy();

    var linea = document.getElementById("select-lienas").value;
    var fecha = document.getElementById("inp-filtro-calls-fechas").value;
    var rfc = document.getElementById("btn-form-filtro-calls").value;

    // Initialize Select2 Elements
        var dt_clientes_llamadas = $('#tb-calls-usuario').DataTable({
          "dom": 'Bfrtip',
          "order": [[0, "asc"]],
          "buttons": [
              'copy', 'csv', 'excel', 'pdf', 'print'
          ],
          // "responsive": true,
          "processing":true,
          "ajax": {
              "url" : '<?= base_url();?>Clientes/filtrarLlamadasAjax/',
              "dataSrc": "",
              type : 'POST',
              data : { 'select-lienas' : linea, 'inp-filtro-calls-fechas' : fecha, 'btn-form-filtro-calls' : rfc },
              // dataType : "JSON",
              // processData : false,
              // contentType : false
              },
          "columns": [
              { "data": "origen" },
              { "data": "origen" },
              { "data": "destino" },
              { "data": "poblacion_destino" },
              { "data": "fecha" },
              { "data": "duracion" },
              { "data": "monto_final" },
              { "data": "tarifa_base" },
              { "data": "tipo_trafico" },
              { "data": "tipo_tel_destino" }
          ]
        });

        // Enumero los registros de datatables
        dt_clientes_llamadas.on( 'order.dt search.dt', function () {
            dt_clientes_llamadas.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
                cell.innerHTML = i+1;
            } );
        } ).draw();
  }
</script>

</body>
</html>