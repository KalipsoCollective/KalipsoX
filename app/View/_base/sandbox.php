<div class="page">
  <!-- Navbar -->
  <header class="navbar navbar-expand-md d-print-none">
    <div class="container-xl">
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbar-menu" aria-controls="navbar-menu" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <h1 class="navbar-brand navbar-brand-autodark d-none-navbar-horizontal pe-0 pe-md-3">
        <a href="<?php echo $this->url('/sandbox'); ?>" class="font-monospace text-decoration-none">
          <img src=" <?php echo $Helper::assets('img/x/logo.svg'); ?>" alt="<?php echo \KX\Core\Helper::config('app.name'); ?>" class="navbar-brand-image">
          <small class="h6">_sandbox</small>
        </a>
      </h1>
      <div class="navbar-nav flex-row order-md-last">
        <div class="nav-item d-none d-md-flex me-3">
          <div class="btn-list">
            <a href="<?php echo $this->url('/'); ?>" class="btn">
              <i class="ti ti-arrow-left me-1"></i> <?php echo $Helper::lang('base.go_back'); ?>
            </a>
          </div>
        </div>
      </div>
    </div>
  </header>
  <header class="navbar-expand-md">
    <div class="collapse navbar-collapse" id="navbar-menu">
      <div class="navbar">
        <div class="container-xl">
          <ul class="navbar-nav">
            <li class="nav-item">
              <a class="nav-link<?php echo $this->currentLink('/sandbox'); ?>" href="<?php echo $this->url('/sandbox'); ?>">
                <i class="ti ti-box"></i>
                <span class="ms-1 nav-link-title">
                  <?php echo \KX\Core\Helper::lang('base.sandbox'); ?>
                </span>
              </a>
            </li>
            <?php
            foreach ($steps as $step => $details) {

              echo '
              <li class="nav-item">
                <a class="nav-link' . $this->currentLink('/sandbox/' . $step) . '" href="' . $this->url('/sandbox/' . $step) . '">
                  <i class="' . $details['icon'] . '"></i>  
                  <span class="ms-1 nav-link-title">
                    ' . \KX\Core\Helper::lang($details['lang']) . '
                  </span>
                </a>
              </li>';
            }  ?>
          </ul>
        </div>
      </div>
    </div>
  </header>
  <div class="page-wrapper">
    <div class="page-header d-print-none">
      <div class="container-xl">
        <div class="row g-2 align-items-center">
          <div class="col">
            <div class="page-pretitle">
              <h1>KX > _sandbox</h1>
            </div>
          </div>
          <div class="col-auto ms-auto d-print-none">
          </div>
        </div>
      </div>
    </div>
    <div class="page-body">
      <div class="container-xl">
        <div class="row row-deck row-cards">
          <div class="col-12">
            <div class="card card-md">
              <div class="card-stamp card-stamp-lg">
                <div class="card-stamp-icon bg-primary">
                  <i class="ti ti-info-square-rounded-filled"></i>
                </div>
              </div>
              <div class="card-body">
                <div class="row align-items-center">
                  <div class="col-10">
                    <h3 class="h1"><?php echo $head; ?></h3>
                    <div class="markdown text-muted">
                      <?php echo $description; ?>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <?php 
          // $Helper::dump($output);
          if (isset($output['alert']) && !empty($output['alert'])) {
            echo '
            <div class="col-12">
              <div class="d-flex flex-column w-100">';
            foreach ($output['alert'] as $i => $a) {
              $i++;
              echo '
                <div class="w-100 alert alert-' . $a['type'] . ($i === count($output['alert']) ? ' mb-0' : '') . '" role="alert">
                  <div class="d-flex">
                    <div>
                      <i class="me-2 ti' . ($a['type'] === 'success' ? ' ti-square-rounded-check' : 
                        ($a['type'] === 'warning' ? ' ti-alert-square-rounded' : 
                        ($a['type'] === 'danger' ? ' ti-alert-circle' : 
                        ($a['type'] === 'info' ? ' ti-info-square-rounded' : '')))
                      ) . '"></i>
                    </div>
                    <div>
                      <h4 class="alert-title">
                        '.$a['title']. '
                      </h4>
                      <div class="text-secondary">
                        ' . $a['message'] . '
                      </div>
                    </div>
                  </div>
                </div>';
            }
            echo '
              </div>
            </div>';
          }

          if (isset($output['table']) !== false) {
            echo '
            <div class="col-12">
              ' . (isset($output['table_form']) ? '<form method="get" class="w-100">' : '') . '
              <div class="card w-100">
                <div class="card-header">
                  <ul class="nav nav-pills card-header-pills" role="tablist">';

            $i = 0;
            foreach ($output['table'] as $tab => $details) {
              $i++;
              echo '
                    <li class="nav-item" role="presentation">
                      <a href="#'.$Helper::slugGenerator($tab).'" class="nav-link'.($i === 1 ? ' active' : ''). '" data-bs-toggle="tab" aria-selected="' . ($i === 1 ? 'true' : 'false') . '" role="tab" tabindex="-1">
                        <small>' . $tab . '</small>
                        <span class="badge bg-primary ms-2">' . count($details['cols']) . '</span>
                      </a>
                    </li>';
            }

            echo '
                  </ul>
                </div>
                <div class="tab-content">';

            $i = 0;
            foreach ($output['table'] as $tab => $details) {
              $i++;
              echo '
                  <div id="' . $Helper::slugGenerator($tab) . '" class="card tab-pane fade' . ($i === 1 ? ' active show' : '') . '" role="tabpanel">
                    <div class="table-responsive">
                      <table class="table table-vcenter card-table">
                        <thead>
                          <tr>';

              $tbody = [];
              foreach ($details['rows'] as $rowKey => $row) {
                echo '      <th>
                              ' . $row . '
                            </th>';

              }

              echo '
                          </tr>
                        </thead>
                        <tbody>';

              foreach ($details['cols'] as $colKey => $col) {
                echo '    <tr>';
                foreach ($details['rows'] as $rowKey => $row) {
                  echo '    <td>
                              ' . (isset($col[$rowKey]) ? $col[$rowKey] : $colKey) . '
                            </td>';
                }
                echo '    </tr>';
            }

            echo '
                        </tbody>
                      </table>
                    </div>
                  </div>
                  '.(isset($output['table_button']) ? '
                  <div class="card-footer d-flex">
                    ' . (isset($output['table_button']['type']) ? '<button type="' . $output['table_button']['type'] . '"' : '<a href="' . $output['table_button']['link'] . '"') . ' class="btn btn-primary ms-auto">
                      ' . $output['table_button']['text'] . '
                    ' . (isset($output['table_button']['type']) ? '</button>' : '</a>') . '
                  </div>' : ''). '
                </div>
              </div>
              ' . (isset($output['table_form']) ? '</form>' : '') . '
            </div>';
            }
          }

          if (isset($output['pre']) !== false) {
            echo '<div class="col-12"><pre class="w-100">'. $output['pre']. '</pre></div>';
          }
          
          ?>
        </div>
      </div>
    </div>
    <footer class="footer footer-transparent d-print-none">
      <div class="container-xl">
        <div class="row text-center align-items-center flex-row-reverse">
          <div class="col-lg-auto ms-lg-auto">
            <ul class="list-inline list-inline-dots mb-0">
              <li class="list-inline-item">
                <a href="https://github.com/KalipsoCollective/KalipsoX/blob/main/README.md" target="_blank" class="link-secondary" rel="noopener">
                  <?php echo $Helper::lang('base.documentation'); ?>
                </a>
              </li>
              <li class="list-inline-item">
                <a href="https://github.com/KalipsoCollective/KalipsoX/blob/main/LICENSE" target="_blank" class="link-secondary">
                  <?php echo $Helper::lang('base.licence'); ?>
                </a>
              </li>
              <li class="list-inline-item">
                <a href="https://github.com/KalipsoCollective/KalipsoX" target="_blank" class="link-secondary" rel="noopener">
                  <?php echo $Helper::lang('base.source_code'); ?>
                </a>
              </li>
            </ul>
          </div>
          <div class="col-12 col-lg-auto mt-3 mt-lg-0">
            <ul class="list-inline list-inline-dots mb-0">
              <li class="list-inline-item">
                <?php echo $Helper::lang('base.copyright') . ' © <a href="https://github.com/KalipsoCollective" target="_blank" class="link-secondary">KalipsoCollective</a>. ' . $Helper::lang('base.all_rights_reserved'); ?>
              </li>
              <li class="list-inline-item">
                <a href="https://github.com/KalipsoCollective/KalipsoX/blob/main/CHANGELOG.md" target="_blank" class="link-secondary" rel="noopener">
                  v<?php echo KX_VERSION; ?>
                </a>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </footer>
  </div>
</div>
<?php /*
<nav class="navbar navbar-expand-xl navbar-dark bg-black fixed-top shadow">
  <div class="container-fluid">
    <a class="navbar-brand" href="<?php echo $this->url('/sandbox'); ?>">
      <?php echo \KX\Core\Helper::config('app.name'); ?>
      <small class="h6">_sandbox</small>
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav">
        <li class="nav-item">
          <a class="nav-link<?php echo $this->currentLink('/sandbox'); ?>" href="<?php echo $this->url('/sandbox'); ?>">
            <i class="ti ti-box"></i> <?php echo \KX\Core\Helper::lang('base.sandbox'); ?>
          </a>
        </li>
        <?php
        foreach ($steps as $step => $details) {

          echo '
					<li class="nav-item">
						<a class="nav-link' . $this->currentLink('/sandbox/' . $step) . '" href="' . $this->url('/sandbox/' . $step) . '">
							<i class="' . $details['icon'] . '"></i>  ' . \KX\Core\Helper::lang($details['lang']) . '
						</a>
					</li>';
        }  ?>
      </ul>
      <ul class="navbar-nav ms-auto">
        <li class="nav-item">
          <a class="nav-link" href="<?php echo $this->url('/'); ?>">
            <i class="ti ti-arrow-left"></i> <?php echo \KX\Core\Helper::lang('base.go_to_home'); ?>
          </a>
        </li>
      </ul>
    </div>
  </div>
</nav>
<div class="wrap sandbox">
  <div class="container">
    <div class="row">
      <div class="col-12">
        <h1><?php echo $head; ?></h1>
        <h2 class="h4"><?php echo $description; ?></h2>
        <?php echo $output; ?>
      </div>
    </div>
  </div>
</div>
*/ ?>