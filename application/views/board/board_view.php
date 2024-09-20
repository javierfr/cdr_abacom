<!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Gráficas</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">ChartJS</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-md-12">
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
                <form id="form-filtro-graficas">
                  <div class="row justify-content-center">
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
                          <input type="text" id="inp-filtro-graficas-fechas" name="inp-filtro-graficas-fechas" class="form-control float-right">
                          <!-- inp-filtro-calls-fechas -->
                        </div>
                        <!-- /.input group -->
                      </div>
                      <!-- /.form group -->
                    </div>
                    <!-- /.col -->
                    <div class="col-md-2">
                      <button type="submit" id="btn-form-filtro-graficas" class="btn btn-primary btn-block" style="position: relative;top: 35%;">Buscar</button>
                    </div>
                    <!-- /.col -->
                  </div>
                </form>
              </div>
              <!-- /.card-body -->
            </div>

          </div>
          <!-- /.col (LEFT) -->
          <div class="col-md-12">

            <!-- BAR CHART -->
            <div class="card card-success">
              <div class="card-header">
                <h3 class="card-title">Bar Chart</h3>

                <div class="card-tools">
                  <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i>
                  </button>
                  <button type="button" class="btn btn-tool" data-card-widget="remove"><i class="fas fa-times"></i></button>
                </div>
              </div>
              <div class="card-body">
                <div class="chart">
                  <canvas id="barChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                </div>
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->

          </div>
          <!-- /.col (RIGHT) -->
        </div>
        <!-- /.row -->
      </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
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
                <form id="form-filtro-graficas-cliente">
                  <div class="row justify-content-center">
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
                      <button type="submit" id="btn-form-filtro-graficas-cliente" class="btn btn-primary btn-block" style="position: relative;top: 35%;">Buscar</button>
                    </div>
                    <!-- /.col -->
                  </div>
                </form>
              </div>
              <!-- /.card-body -->
            </div>
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">DataTable with default features</h3>
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                <table id="tb-cliente-grafica" class="table table-bordered table-striped">
                  <thead>
                  <tr>
                    <th>Empresa</th>
                    <th>RFC</th>
                    <th>Movimientos</th>
                  </tr>
                  </thead>
                  <tbody id="tb-graficas">
                 
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
  </div>
  <!-- /.content-wrapper