<?php

/**
 * @package KX
 * @subpackage Controller\AppController
 */

declare(strict_types=1);

namespace KX\Controller;

use KX\Core\Controller as CoreController;
use KX\Core\Helper;
use KX\Core\Model;

final class AppController extends CoreController
{

    public function index()
    {

        $return = [
            'status' => true,
            'statusCode' => 200,
            'arguments' => [
                'message' => Helper::lang('base.welcome'),
            ],
            'alerts' => [],
            'view' => null,
        ];

        return $return;
    }

    public function sandbox()
    {
        if (Helper::config('app.dev_mode')) {

            $steps = [
                'db-init' => [
                    'icon' => 'ti ti-database', 'lang' => 'base.db_init'
                ],
                'db-seed' => [
                    'icon' => 'ti ti-database-import', 'lang' => 'base.db_seed'
                ],
                'php-info' => [
                    'icon' => 'ti ti-brand-php', 'lang' => 'base.php_info'
                ],
                'session' => [
                    'icon' => 'ti ti-fingerprint', 'lang' => 'base.session'
                ],
                'clear-storage' => [
                    'icon' => 'ti ti-folders', 'lang' => 'base.clear_storage'
                ],
                'check_languages' => [
                    'icon' => 'ti ti-language', 'lang' => 'base.check_languages'
                ],
            ];

            $action = '';
            if (
                isset($this->get('request')->attributes['action']) !== false and
                in_array($this->get('request')->attributes['action'], array_keys($steps))
            )
                $action = $this->get('request')->attributes['action'];

            $title = Helper::lang('base.sandbox');
            $output = '';
            $dbSchema = require Helper::path('app/External/db_schema.php');

            switch ($action) {
                case 'db-init':
                    $head = Helper::lang('base.db_init');
                    $title = $head . ' | ' . $title;
                    $description = Helper::lang('base.db_init_message');

                    if (isset($_GET['start']) !== false) {

                        $init = (new Model)->dbInit($dbSchema);
                        if ($init) {
                            $output .= '<p class="text-success">' . Helper::lang('base.db_init_success') . '</p>';
                        } else {
                            $init = (string) $init;
                            $output .= '<p class="text-danger">' . str_replace('[ERROR]', $init, (string) Helper::lang('base.db_init_problem')) . '</p>';
                        }
                    } else {

                        foreach ($dbSchema['tables'] as $table => $detail) {

                            $cols = '
							<div class="table-responsive">
								<table class="table table-dark table-sm table-hover table-striped caption-bottom">
									<thead>
										<tr>
											<th scope="col">' . Helper::lang('base.column') . '</th>
											<th scope="col">' . Helper::lang('base.type') . '</th>
											<th scope="col">' . Helper::lang('base.auto_inc') . '</th>
											<th scope="col">' . Helper::lang('base.attribute') . '</th>
											<th scope="col">' . Helper::lang('base.default') . '</th>
											<th scope="col">' . Helper::lang('base.index') . '</th>
										</tr>
									</thead>
									<tbody>';

                            foreach ($detail['cols'] as $col => $colDetail) {

                                $cols .= '
										<tr>
											<th scope="row">' . $col . '</th>
											<td scope="col">
												' . $colDetail['type'] . (isset($colDetail['type_values']) !== false ?
                                    (is_array($colDetail['type_values']) ? '(' . implode(',', $colDetail['type_values']) . ')' :
                                        '(' . $colDetail['type_values']) . ')' : ''
                                ) . '
											</td>
											<td scope="col">' . Helper::lang('base.' . (isset($colDetail['auto_inc']) !== false ? 'yes' : 'no')) . '</td>
											<td scope="col">' . (isset($colDetail['attr']) !== false ? $colDetail['attr'] : '') . '</td>
											<td scope="col">' . (isset($colDetail['default']) !== false ? $colDetail['default'] : '') . '</td>
											<td scope="col">' . (isset($colDetail['index']) !== false ? $colDetail['index'] : '') . '</td>
										<tr>';
                            }

                            $tableValues = '';

                            $tableValues = '<h3 class="small text-muted">
								' . (isset($dbSchema['table_values']['specific'][$table]['charset']) !== false ?
                                Helper::lang('base.charset') . ': <strong>' . $dbSchema['table_values']['specific'][$table]['charset'] . '</strong><br>' :
                                ''
                            ) . '
								' . (isset($dbSchema['table_values']['specific'][$table]['collate']) !== false ?
                                Helper::lang('base.collate') . ': <strong>' . $dbSchema['table_values']['specific'][$table]['collate'] . '</strong><br>' :
                                ''
                            ) . '
								' . (isset($dbSchema['table_values']['specific'][$table]['engine']) !== false ?
                                Helper::lang('base.engine') . ': <strong>' . $dbSchema['table_values']['specific'][$table]['engine'] . '</strong><br>' :
                                ''
                            ) . '
							</h3>';

                            $cols .= '
									</tbody>
									<caption>' . $tableValues . '</caption>
								</table>
							</div>';

                            $output .= '<details><summary>' . $table . '</summary>' . $cols . '</details>';
                        }

                        if ($output != '') {
                            $output = '
							<h3 class="small text-muted">
								' . Helper::lang('base.db_name') . ': 
								<strong>' . Helper::config('database.name') . '</strong><br>
								' . Helper::lang('base.db_charset') . ': 
								<strong>' . (isset($dbSchema['table_values']['charset']) !== false ? $dbSchema['table_values']['charset'] : '-') . '</strong><br>
								' . Helper::lang('base.db_collate') . ': 
								<strong>' . (isset($dbSchema['table_values']['collate']) !== false ? $dbSchema['table_values']['collate'] : '-') . '</strong><br>
								' . Helper::lang('base.db_engine') . ': 
								<strong>' . (isset($dbSchema['table_values']['engine']) !== false ? $dbSchema['table_values']['engine'] : '-') . '</strong><br>
							</h3>
							' . $output . '
							<p class="small text-danger mt-5">
								' . str_replace(
                                [
                                    '[DB_NAME]',
                                    '[COLLATION]'
                                ],
                                [
                                    '<strong>' . Helper::config('database.name') . '</strong>',
                                    '<strong>' . Helper::config('database.collation') . '</strong>'
                                ],
                                (string) Helper::lang('base.db_init_alert')
                            ) . '
							</p>
							<a class="btn btn-light btn-sm" href="' . $this->get()->url('/sandbox/db-init?start') . '">
								' . Helper::lang('base.db_init_start') . '
							</a>';
                        }
                    }
                    break;

                case 'db-seed':
                    $head = Helper::lang('base.db_seed');
                    $title = $head . ' | ' . $title;
                    $description = Helper::lang('base.db_seed_message');

                    /* Fake User Insert
					function randomName() {

						$data = ["Adam", "Alex", "Aaron", "Ben", "Carl", "Dan", "David", "Edward", "Fred", "Frank", "George", "Hal", "Hank", "Ike", "John", "Jack", "Joe", "Larry", "Monte", "Matthew", "Mark", "Nathan", "Otto", "Paul", "Peter", "Roger", "Roger", "Steve", "Thomas", "Tim", "Ty", "Victor", "Walter"];

						return $data[array_rand($data)];
					}
					*/

                    if (isset($_GET['start']) !== false) {

                        $output = '<p class="text-muted">' . Helper::lang('base.seeding') . '</p>';

                        /* Fake User Insert
						for ($i=0; $i < 1000; $i++) { 
							
							$rand = rand(1, 100000);
							$fName = randomName();
							$lName = randomName();
							$userName = Helper::slugGenerator($lName . ' ' . $fName) . '_' . $rand;
							$statusses = ['active', 'passive', 'deleted'];
							$roles = [0, 1];
							$mailExt = ['gmail', 'outlook', 'hotmail', 'yahoo', 'github'];

							$dbSchema['data']['users'][] = [
								'u_name'                => $userName,
								'f_name'                => $fName,
								'l_name'                => $lName,
								'email'                 => $userName.'@'.$mailExt[array_rand($mailExt)].'.com',
								'password'              => password_hash($userName, PASSWORD_DEFAULT),
								'token'                 => Helper::tokenGenerator(80),
								'role_id'               => $roles[array_rand($roles)],
								'created_at'            => time(),
								'created_by'            => $i,
								'status'                => $statusses[array_rand($statusses)]
							];

						}*/

                        $init = (new Model)->dbSeed($dbSchema);

                        if ($init !== false) {
                            $output .= '<p class="text-success">' . Helper::lang('base.db_seed_success') . '</p>';
                        } else {
                            $init = (string) $init;
                            $output .= '<p class="text-danger">' . str_replace('[ERROR]', $init, (string) Helper::lang('base.db_seed_problem')) . '</p>';
                        }
                    } else {

                        foreach ($dbSchema['data'] as $table => $detail) {

                            $cols = '
							<div class="table-responsive">
								<table class="table table-dark table-sm table-hover table-striped">
									<thead>
										<tr>
											<th scope="col">' . Helper::lang('base.table') . '</th>
											<th scope="col">' . Helper::lang('base.data') . '</th>
										</tr>
									</thead>
									<tbody>';

                            foreach ($detail as $tableDataDetail) {

                                $dataList = '<ul class="list-group list-group-flush">';
                                foreach ($tableDataDetail as $col => $data) {
                                    $dataList .= '
									<li class="list-group-item d-flex justify-content-between align-items-start space">
										<strong>' . $col . '</strong> <span class="ml-2">' . $data . '</span>
									</li>';
                                }
                                $dataList .= '</ul>';

                                $cols .= '
								<tr>
									<th scope="row">' . $table . '</th>
									<td scope="col">
										' . $dataList . '
									</td>
								<tr>';
                            }
                            $cols .= '
								</table>
							</div>';

                            $output .= '<details><summary>' . $table . '</summary>' . $cols . '</details>';
                        }

                        if ($output != '') {
                            $output .= '<a class="btn btn-light mt-5 btn-sm" href="' . $this->get()->url('/sandbox/db-seed?start') . '">
								' . Helper::lang('base.db_seed_start') . '
							</a>';
                        }
                    }
                    break;

                case 'php-info':
                    $head = Helper::lang('base.php_info');
                    $title = $head . ' | ' . $title;
                    $description = Helper::lang('base.php_info_message');

                    ob_start();
                    phpinfo();
                    $output = ob_get_clean();
                    $output = Helper::cleanHTML($output, ['script', 'meta', 'style', 'title']);;
                    $output = '<pre>' . trim($output) . '</pre>';
                    break;

                case 'session':
                    $head = Helper::lang('base.session');
                    $title = $head . ' | ' . $title;
                    $description = Helper::lang('base.session_message');

                    $output = '';
                    foreach (Helper::config('app.available_languages') as $lang) {
                        $output .= '<a class="ms-2" href="' . $this->get()->url('/sandbox/session?lang=' . $lang) . '">
							' . $lang . '
						</a>';
                    }
                    $output = '<p class="text-muted">' . Helper::lang('base.change_language') . ': ' . $output . '</p>';

                    ob_start();
                    Helper::dump($_SESSION);
                    $output .= ob_get_clean();
                    break;

                case 'clear-storage':
                    $head = Helper::lang('base.clear_storage');
                    $title = $head . ' | ' . $title;
                    $description = Helper::lang('base.clear_storage_message');

                    if (!is_dir($dir = Helper::path('app/Storage'))) mkdir($dir);

                    $path = Helper::path('app/Storage/*');
                    $deleteAction = (isset($_GET['delete']) !== false and count($_GET['delete'])) ? $_GET['delete'] : null;
                    if ($deleteAction) {
                        $glob = glob($path, GLOB_BRACE);
                        if ($glob and count($glob)) {
                            foreach ($glob as $folder) {
                                if (in_array(basename($folder), $deleteAction))
                                    Helper::removeDir($folder);
                            }
                            echo '<p class="text-success">' . Helper::lang('base.clear_storage_success') . '</p>';
                        }
                    }

                    $glob = glob($path, GLOB_BRACE);

                    if ($glob and count($glob)) {

                        echo '
						<form method="get">
							<div class="table-responsive">
								<table class="table table-hover table-dark table-borderless table-striped">
									<thead>
										<tr>
											<th scope="col" width="5%">#</th>
											<th scope="col">' . Helper::lang('base.folder') . '</th>
										</tr>
									</thead>
									<tbody>';
                        $deleteBtn = false;
                        foreach ($glob as $folder) {

                            if (!is_dir($folder))
                                continue;

                            $size = Helper::dirSize($folder);
                            if (!$deleteBtn and $size)
                                $deleteBtn = true;

                            $basename = basename($folder);

                            echo '
										<tr>
											<td>
												<div class="form-check">
													<input class="form-check-input" 
														type="checkbox" name="delete[]" 
														value="' . $basename . '"
														' . (!$size ? ' disabled' : ' checked') . '>
												</div>
											</td>
											<td>/' . $basename . ' 
												<small class="' . (!$size ? 'text-muted' : 'text-primary') . '">
													' . Helper::formatSize($size) . '
												</small>
											</td>
										</tr>';
                        }
                        echo '
									</tbody>
								</table>
							</div>
							<button type="submit" class="btn btn-danger btn-sm"' . (!$deleteBtn ? ' disabled' : '') . '>
								' . Helper::lang('base.delete') . '
							</button>
						</form>';
                    } else {
                        echo '<p class="text-danger">' . Helper::lang('base.folder_not_found') . '</p>';
                    }
                    $output = ob_get_clean();
                    break;

                case 'check_languages':
                    $head = Helper::lang('base.check_languages');
                    $title = $head . ' | ' . $title;
                    $description = Helper::lang('base.check_languages_message');


                    $languagefiles = glob(Helper::path('app/Resources/localization/*.php'));
                    $languages = [];
                    foreach ($languagefiles as $languagefile) {
                        $require = require $languagefile;
                        $languages[basename($languagefile, '.php')] = Helper::arrayFlat($require);
                    }

                    $diff = [];
                    $langKeys = array_keys($languages);
                    foreach ($languages as $currentLangKey => $currentLangData) {
                        foreach ($currentLangData as $currentLangDataKey => $currentLangDataValue) {
                            foreach ($langKeys as $langKey) {
                                if ($langKey === $currentLangKey) {
                                    continue;
                                }
                                if (isset($languages[$langKey][$currentLangDataKey]) === false) {
                                    $diff[$langKey][] = $currentLangDataKey;
                                }
                            }
                        }
                    }

                    if (count($diff)) {

                        $cols = '
						<div class="table-responsive">
							<table class="table table-dark table-sm table-hover table-striped">
								<thead>
									<tr>
										<th scope="col">' . Helper::lang('base.language') . '</th>
										<th scope="col">' . Helper::lang('base.data') . '</th>
									</tr>
								</thead>
								<tbody>';

                        foreach ($diff as $key => $missing) {

                            $dataList = '<ul class="list-group list-group-flush">';
                            $missing = array_unique($missing);
                            foreach ($missing as $missingData) {
                                $dataList .= '
								<li class="list-group-item d-flex justify-content-between align-items-start space">
									<strong>' . $missingData . '</strong>
								</li>';
                            }
                            $dataList .= '</ul>';

                            $cols .= '
							<tr>
								<th scope="row">' . $key . '</th>
								<td scope="col">
									' . $dataList . '
								</td>
							<tr>';
                        }
                        $cols .= '
							</table>
						</div>';
                    } else {
                        $cols = '<p class="text-success">' . Helper::lang('base.no_missing_definitions.') . '</p>';
                    }

                    $output = $cols;
                    break;

                default:
                    $head = Helper::lang('base.welcome');
                    $description = Helper::lang('base.sandbox_message');
                    break;
            }

            return [
                'status' => true,
                'statusCode' => 200,
                'arguments' => [
                    'title' => $title,
                    'head'  => $head,
                    'description' => $description,
                    'output' => $output,
                    'steps' => $steps
                ],
                'log' => false,
                'view' => ['_base.sandbox', 'sandbox']
            ];
        } else {

            return [
                'status' => false,
                'statusCode' => 302,
                'redirect' => '/',
                'view' => null,
            ];
        }
    }

    public function cronJobs()
    {

        /*
        $arguments = [];

        // Clear old sessions
        $timeLimit = strtotime('-30 days');
        $arguments['sessions'] = (new Sessions())->where('last_action_date', '<', $timeLimit)->delete();

        if (!is_dir($dir = Helper::path('app/Storage'))) mkdir($dir);

        // Clear old cache files
        $cacheFolder = glob(Helper::path('app/Storage/*'));
        $timeLimit = strtotime('-10 days');
        $arguments['cache'] = 'Nothing found.';
        if (is_array($cacheFolder)) {
            foreach ($cacheFolder as $folder) {
                if (is_dir($folder) and strpos($folder, 'email') === false) {
                    $folderName = explode('/', $folder);
                    $folderName = array_pop($folderName);
                    $cacheFiles = glob($folder . '/*');
                    if (is_array($cacheFiles)) {
                        foreach ($cacheFiles as $file) {
                            if (filemtime($file) < $timeLimit) {
                                if (unlink($file)) {
                                    $fileName = explode('/', $file);
                                    $fileName = array_pop($fileName);
                                    $arguments['cache'][$folderName][] = $fileName;
                                }
                            }
                        }
                    }
                }
            }
        }

        // Send pending emails
        $arguments['email'] = (new Notification($this->get()))->mailQueue();

        return [
            'status' => true,
            'statusCode' => 200,
            'arguments' => $arguments,
            'view' => null
        ]; */
    }
}
