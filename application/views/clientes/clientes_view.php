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
            <h1><?=$modulo;?></h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Inicio</a></li>
              <li class="breadcrumb-item active"><?=$modulo;?></li>
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
                <h3 class="card-title">Sincronizar clientes</h3>

                <div class="card-tools">
                  <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
                  <!-- <button type="button" class="btn btn-tool" data-card-widget="remove"><i class="fas fa-times"></i></button> -->
                </div>
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                  <div class="row justify-content-md-center" style="padding-bottom: 1%;">
                    <div class="col-md-2">
                      <button type="button" id="btn-sync-clientes" class="btn btn-primary btn-block">Sincronizar</button>
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
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Listado de clientes</h3>
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                <table id="tb-clientes-llamadas" class="table table-bordered table-striped">
                  <thead>
                  <tr>
                    <th>#</th>
                    <th>Empresa</th>
                    <th>RFC</th>
                    <th>Movimientos</th>
                  </tr>
                  </thead>
                  <tbody>
                 
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
<script src="<?=base_url();?>assets/dist/js/demo.js"></script>
<!-- bs-custom-file-input -->
<script src="<?=base_url();?>assets/plugins/bs-custom-file-input/bs-custom-file-input.min.js"></script>

<!-- <script src="<?=base_url()?>assets/plugins/jquery-validation/jquery.validate.js"></script> -->
<script src="<?=base_url()?>assets/plugins/jquery-validation/jquery.validate.min.js"></script>
<!-- <script src="<?=base_url()?>assets/plugins/jquery-validation/additional-methods.js"></script> -->
<script src="<?=base_url()?>assets/plugins/jquery-validation/additional-methods.min.js"></script>
<!-- Page specific script -->

<script>
  // Seleccionar archivo
  $(document).ready(function () {
    getClientes();
    // bsCustomFileInput.init();

    // var pathname = window.location.pathname;

    // switch (pathname.replace("/AbaCalls/","")) {
    //   case "Board":
    //     getDataClientesAJAX();
    //     getDataClientesGraficaAJAX();
    //     break;
    //   case "Calls":
        

    //     break;
    //   default:
    //     //Declaraciones ejecutadas cuando ninguno de los valores coincide con el valor de la expresión
    //     break;
    // }


  });

  $("#btn-sync-clientes").on('click',function(){
    uploadClientesAjax();
  });
  // Funcion actuliza clientes
  function uploadClientesAjax(){

    $.ajax({
      url: '<?= base_url();?>Clientes/uploadClientesAjax',
      type: 'post',
      dataType: "JSON",
      success: function(data) {
        // console.log(data);

        // var result = [];

        // for(var i in data)
        //     result.push([i, data [i]]);

        // console.log(result);
        var html = '';
          html += `<div class="alert alert-success alert-dismissible text-center">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                    <strong>Super!</strong> Proceso finalizado
                  </div>`;

          $('#message-mysql-zoho').html(html);
      },
      error: function (jqXHR, textStatus, errorThrown) {
        // alert('error');
        var html = '';
          html += `<div class="alert alert-success alert-dismissible text-center">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                    <strong>Super!</strong> Proceso finalizado
                  </div>`;

          $('#message-mysql-zoho').html(html);
      }
    });
  }

  function getClientes(){
    //Initialize Select2 Elements
        var dt_clientes_llamadas = $('#tb-clientes-llamadas').DataTable({
          "dom": 'Bfrtip',
          "order": [[0, "asc"]],
          "buttons": [
              'copy', 'csv', 'excel', 'pdf', 'print'
          ],
          // "responsive": true,
          "processing":true,
          "ajax": {
              "url": '<?= base_url();?>Clientes/getClientesAJAX',
              "dataSrc": "",
              type : 'POST'
              },
          "columns": [
              { "data": "razon_social" },
              { "data": "razon_social" },
              { "data": "rfc" },
              {"data": 'rfc',
                "render": function (data, type, row, meta) {
                  return '<a type="button" value='+data+' class="btn btn-info" href="<?= base_url('Clientes/getCallsUsuario/')?>'+data+'"><i class="far fa-eye"></i></a>';
              }},
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