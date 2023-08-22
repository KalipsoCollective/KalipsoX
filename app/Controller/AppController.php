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
            $output = [
                'alert' => []
            ];
            $dbSchema = require Helper::path('app/External/db_schema.php');

            switch ($action) {
                case 'db-init':
                    $head = Helper::lang('base.db_init');
                    $title = $head . ' | ' . $title;
                    $description = Helper::lang('base.db_init_message');

                    if (isset($_GET['start']) !== false) {

                        $init = (new Model)->dbInit($dbSchema);
                        if ($init) {
                            $output['alert'][] = [
                                'type' => 'success',
                                'title' => Helper::lang('base.success'),
                                'message' => Helper::lang('base.db_init_success')
                            ];
                        } else {
                            $output['alert'][] = [
                                'type' => 'danger',
                                'title' => Helper::lang('base.danger'),
                                'message' => str_replace('[ERROR]', (string) $init, (string) Helper::lang('base.db_init_problem'))
                            ];
                        }

                    } else {

                        $output['table'] = [];

                        $output['alert'][] = [
                            'type' => 'danger',
                            'title' => Helper::lang('base.danger'),
                            'message' => str_replace(
                                ['[DB_NAME]', '[COLLATION]'], 
                                ['<kbd>' . Helper::config('database.name') . '</kbd>', '<kbd>' . Helper::config('database.collation') . '</kbd>'],
                                (string) Helper::lang('base.db_init_alert')
                            )
                        ];

                        foreach ($dbSchema['tables'] as $table => $detail) {

                            $output['table'][$table] = [
                                'rows' => [
                                    'col' => Helper::lang('base.column'),
                                    'type' => Helper::lang('base.type'),
                                    'auto_inc' => Helper::lang('base.auto_inc'),
                                    'attr' => Helper::lang('base.attribute'),
                                    'default' => Helper::lang('base.default'),
                                    'index' => Helper::lang('base.index'),
                                ],
                                'table_values' => [
                                    'charset' => (isset($dbSchema['table_values']['specific'][$table]['charset']) !== false ? $dbSchema['table_values']['specific'][$table]['charset'] : $dbSchema['table_values']['charset']),
                                    'collate' => (isset($dbSchema['table_values']['specific'][$table]['collate']) !== false ? $dbSchema['table_values']['specific'][$table]['collate'] : $dbSchema['table_values']['collate']),
                                    'engine' => (isset($dbSchema['table_values']['specific'][$table]['engine']) !== false ? $dbSchema['table_values']['specific'][$table]['engine'] : $dbSchema['table_values']['engine']),
                                ],
                                'cols' => []
                            ];

                            foreach ($detail['cols'] as $col => $colDetail) {

                                $output['table'][$table]['cols'][$col] = [
                                    'type' => $colDetail['type'] . (isset($colDetail['type_values']) !== false ?
                                        (is_array($colDetail['type_values']) ? '(' . implode(',', $colDetail['type_values']) . ')' :
                                            '(' . $colDetail['type_values']) . ')' : ''
                                    ),
                                    'auto_inc' => Helper::lang('base.' . (isset($colDetail['auto_inc']) !== false ? 'yes' : 'no')),
                                    'attr' => (isset($colDetail['attr']) !== false ? $colDetail['attr'] : ''),
                                    'default' => (isset($colDetail['default']) !== false ? $colDetail['default'] : ''),
                                    'index' => (isset($colDetail['index']) !== false ? $colDetail['index'] : ''),
                                ];
                            }

                            if (count($output['table'])) {

                                $output['table_button'] = [
                                    'text' => Helper::lang('base.db_init_start'),
                                    'link' => $this->get()->url('/sandbox/db-init?start'),
                                ]; 
                            }
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

                        $output['alert'][] = [
                            'type' => 'info',
                            'title' => Helper::lang('base.info'),
                            'message' => Helper::lang('base.seeding')
                        ];

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
                            $output['alert'][] = [
                                'type' => 'success',
                                'title' => Helper::lang('base.success'),
                                'message' => Helper::lang('base.db_seed_success')
                            ];
                        } else {
                            $init = (string) $init;
                            $output['alert'][] = [
                                'type' => 'danger',
                                'title' => Helper::lang('base.danger'),
                                'message' => str_replace('[ERROR]', $init, (string) Helper::lang('base.db_seed_problem'))
                            ];
                        }
                    } else {

                        $output['table'] = [];

                        foreach ($dbSchema['data'] as $table => $detail) {

                            $output['table'][$table] = [
                                'rows' => [
                                    'col' => Helper::lang('base.column'),
                                    'data' => Helper::lang('base.data'),
                                ],
                                'cols' => []
                            ];

                            foreach ($detail as $tableDataDetail) {

                                foreach ($tableDataDetail as $col => $data) {

                                    $output['table'][$table]['cols'][$col] = [
                                        'data' => $data
                                    ];
                                }
                            }
                        }

                        if (count($output['table'])) {

                            $output['table_button'] = [
                                'text' => Helper::lang('base.db_seed_start'),
                                'link' => $this->get()->url('/sandbox/db-seed?start'),
                            ];
                        }
                    }
                    break;

                case 'php-info':
                    $head = Helper::lang('base.php_info');
                    $title = $head . ' | ' . $title;
                    $description = Helper::lang('base.php_info_message');

                    ob_start();
                    phpinfo();
                    $output['pre'] = ob_get_clean();
                    $output['pre'] = Helper::cleanHTML($output['pre'], ['script', 'meta', 'style', 'title', 'head']);
                    // get only body content
                    $output['pre'] = preg_replace('/^.*<body>(.*)<\/body>.*$/s', '$1', $output['pre']);
                    $output['pre'] = trim($output['pre']);
                    break;

                case 'session':
                    $head = Helper::lang('base.session');
                    $title = $head . ' | ' . $title;
                    $description = Helper::lang('base.session_message');

                    $output['table'][Helper::lang('base.available_languages')] = [
                        'rows' => [
                            'lang' => Helper::lang('base.language'),
                            'action' => '',
                        ],
                        'cols' => []
                    ];
                    foreach (Helper::config('app.available_languages') as $lang) {
                        $output['table'][Helper::lang('base.available_languages')]['cols'][$lang] = [
                            'lang' => $lang,
                            'action' => '<a class="btn btn-sm btn-primary" href="' . $this->get()->url('/sandbox/session?lang=' . $lang) . '">
                                ' . Helper::lang('base.change_language') . '
                            </a>'
                        ];
                    }

                    ob_start();
                    print_r($_SESSION);
                    $output['pre'] = ob_get_clean();
                    break;

                case 'clear-storage':
                    $head = Helper::lang('base.clear_storage');
                    $title = $head . ' | ' . $title;
                    $description = Helper::lang('base.clear_storage_message');

                    if (!is_dir($dir = Helper::path('app/Storage'))) mkdir($dir);

                    $path = Helper::path('app/Storage/*');
                    $deleteAction = (isset($_GET['delete']) !== false AND is_array($_GET['delete']) AND count($_GET['delete'])) ? $_GET['delete'] : null;
                    if ($deleteAction) {
                        $glob = glob($path, GLOB_BRACE);
                        if ($glob and count($glob)) {
                            foreach ($glob as $folder) {
                                if (in_array(basename($folder), $deleteAction))
                                    Helper::removeDir($folder);
                            }
                            $output['alert'][] = [
                                'type' => 'success',
                                'title' => Helper::lang('base.success'),
                                'message' => Helper::lang('base.clear_storage_success')
                            ];
                        }
                    }

                    $glob = glob($path, GLOB_BRACE);

                    if ($glob and count($glob)) {
                        $output['table_form'] = true;
                        $output['table'][Helper::lang('base.available_folders')] = [
                            'rows' => [
                                'folder' => Helper::lang('base.folder'),
                                'action' => '',
                            ],
                            'cols' => []
                        ];
                        $deleteBtn = false;
                        foreach ($glob as $folder) {

                            if (!is_dir($folder))
                                continue;

                            $size = Helper::dirSize($folder);
                            if (!$deleteBtn and $size)
                                $deleteBtn = true;

                            $basename = basename($folder);

                            $output['table'][Helper::lang('base.available_folders')]['cols'][$basename] = [
                                'folder' => '<div class="form-check">
                                                <input class="form-check-input" 
                                                    type="checkbox" name="delete[]" 
                                                    value="' . $basename . '"
                                                    ' . (!$size ? ' disabled' : ' checked') . '>
                                            </div>',
                                'action' => '/' . $basename . ' 
                                            <small class="' . (!$size ? 'text-muted' : 'text-primary') . '">
                                                ' . Helper::formatSize($size) . '
                                            </small>'
                            ];
                        }

                        if ($deleteBtn) {
                            $output['table_button'] = [
                                'text' => Helper::lang('base.clear_storage'),
                                'type' => 'submit',
                            ];
                        }

                    } else {
                        $output['alert'][] = [
                            'type' => 'danger',
                            'title' => Helper::lang('base.danger'),
                            'message' => Helper::lang('base.folder_not_found')
                        ];
                    }
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

                        $output['table'][Helper::lang('base.missing_definitions')] = [
                            'rows' => [
                                'lang' => Helper::lang('base.language'),
                                'data' => Helper::lang('base.data'),
                            ],
                            'cols' => []
                        ];

                        foreach ($diff as $key => $missing) {

                            $dataList .= '<ul class="list-group list-group-flush">';
                            $missing = array_unique($missing);
                            foreach ($missing as $missingData) {
                                $dataList .= '
								<li class="list-group-item d-flex justify-content-between align-items-start space">
									<strong>' . $missingData . '</strong>
								</li>';
                            }
                            $dataList .= '</ul>';

                            $output['table'][Helper::lang('base.missing_definitions')]['cols'][$key] = [
                                'lang' => $key,
                                'data' => $dataList
                            ];
                        }
                    } else {
                        $output['alert'][] = [
                            'type' => 'success',
                            'title' => Helper::lang('base.success'),
                            'message' => Helper::lang('base.no_missing_definitions')
                        ];
                    }
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
