<?php

/**
 * @package KX
 * @subpackage Controller\MenuController
 */

declare(strict_types=1);

namespace KX\Controller;

use KX\Core\Controller;
use KX\Core\Helper;
use KX\Helper\HTML;
use KX\Helper\KalipsoTable;
use KX\Model\Menus;
use KX\Controller\ContentController;

final class MenuController extends Controller
{

  public function __construct($container)
  {

    parent::__construct($container);
  }

  public function menus()
  {

    return [
      'status' => true,
      'statusCode' => 200,
      'arguments' => [
        'title' => Helper::lang('base.menus') . ' | ' . Helper::lang('base.management'),
        'description' => Helper::lang('base.menus_message'),
        'modules' => $this->modules,
        'forms' => $this->forms,
        'menuJson' => json_encode([
          'dragger' => true,
          'manipulation' => [
            '#addModal .KX-menu-drag' => [
              'html_append' => HTML::menuModuleUrlWidget([
                'menu_options' => $this->menuOptionsAsHTML(),
                'KX_drag' => true
              ]),
              'html_append_dynamic' => true
            ]
          ]
        ])
      ],
      'view' => ['admin.menus', 'admin']
    ];
  }

  public function menuOptions()
  {

    $options = [
      'basic' => [
        'home' => Helper::lang('base.home'),
        'login' => Helper::lang('base.login'),
        'register' => Helper::lang('base.register'),
        'recovery' => Helper::lang('base.recovery_account'),
      ],
      'modules' => [],
      'forms' => [],
    ];
    foreach ($this->modules as $key => $data) {
      if ($data['routes']['listing'] or $data['routes']['detail'])
        $options['modules'][$key] = Helper::lang($data['name']);
    }

    foreach ($this->forms as $key => $data) {
      if ($data['routes']['listing'] or $data['routes']['detail'])
        $options['forms'][$key] = Helper::lang($data['name']);
    }

    return $options;
  }

  public function urlGenerator($section, $area, $parameter = null)
  {

    $return = '';
    switch ($section) {
      case 'basic':
        switch ($area) {
          case 'home':
            $return = '/';
            break;

          case 'login':
            $return = '/auth/login';
            break;

          case 'register':
            $return = '/auth/register';
            break;

          case 'recovery':
            $return = '/auth/recovery';
            break;
        }
        break;

      case 'modules':
        $lang = Helper::lang('lang.code');
        foreach ($this->modules as $key => $data) {
          if (($data['routes']['listing'] or $data['routes']['detail']) and $key === $area) {

            if (
              $data['routes']['listing'] and
              isset($data['routes']['listing'][$lang]) !== false and
              ($parameter === 'list' or $parameter === 'list_as_dropdown') or is_null($parameter)
            ) {
              $return = $data['routes']['listing'][$lang][1];
            } elseif (
              $data['routes']['detail'] and
              isset($data['routes']['detail'][$lang]) !== false and
              (int)$parameter
            ) {

              $content = (new ContentController($this->get()))
                ->getContent((int)$parameter);

              if ($content) {

                $content->input = json_decode($content->input);
                if (isset($content->input->slug->{$lang}) !== false) {

                  $return = Helper::dynamicURL(
                    $data['routes']['detail'][$lang][1],
                    ['slug' => $content->input->slug->{$lang}]
                  );
                }
              }
            }
          }
        }
        break;

      case 'forms':
        $lang = Helper::lang('lang.code');
        foreach ($this->forms as $key => $data) {
          if (($data['routes']['listing'] or $data['routes']['detail']) and $key === $area) {

            if (
              $data['routes']['listing'] and
              isset($data['routes']['listing'][$lang]) !== false and
              ($parameter === 'list' or $parameter === 'list_as_dropdown')
            ) {
              $return = $data['routes']['listing'][$lang][1];
            } elseif (
              $data['routes']['detail'] and
              isset($data['routes']['detail'][$lang]) !== false and
              ($parameter === 'detail')
            ) {
              $return = $data['routes']['detail'][$lang][1];
            }
          }
        }
        break;
    }
    return $return;
  }

  public function menuOptionsAsHTML($currentValue = null)
  {

    $options = '<option value=""></option>';
    foreach ($this->menuOptions() as $section => $links) {
      $options .= '<optgroup label="' . Helper::lang('base.' . $section) . '">';
      foreach ($links as $value => $name) {
        $optValue = $section . '_' . $value;
        $options .= '<option value="' . $optValue . '"' . ($currentValue == $optValue ? ' selected' : '') . '>' . $name . '</option>';
      }
      $options .= '</optgroup>';
    }
    return $options;
  }

  public function getMenuParameters($module = null, $parameter = null)
  {


    if (is_null($module)) {
      extract(Helper::input([
        'module'  => 'nulled_text', // related module
        'target'  => 'nulled_text', // target
        'widget'  => 'check_as_boolean', // direct link parameter for other modules
      ], $this->get('request')->params));
    } else {
      $returnDirect = true;
      $widget = false;
    }

    $arguments = [];
    $html = ' ';

    if (!is_null($module) and strpos($module, '_') !== false) {

      $module = explode('_', $module, 2);
      if ($module[0] === 'modules') {


        if (isset($this->modules[$module[1]]) !== false) {

          $module = $module[1];
          $moduleDetail = $this->modules[$module];
          if ($moduleDetail['routes']['listing']) {
            $html .= '<option value="list"' . ($parameter == 'list' ? ' selected' : '') . '>' . Helper::lang('base.list') . '</option>';
          }

          if ($moduleDetail['routes']['detail']) {

            if (!$widget) {
              $html .= '<option value="list_as_dropdown"' . ($parameter == 'list_as_dropdown' ? ' selected' : '') . '>' . Helper::lang('base.list_as_dropdown') . '</option>';
            }

            $contents = (new ContentController($this->get()))->getModuleDatas($module);

            if (count($contents)) {
              $html .= '<optgroup label="' . Helper::lang('base.contents') . '">';
              foreach ($contents as $content) {
                $contentDetails = json_decode($content->input);
                $val = $content->id;
                if (
                  isset($contentDetails->title) and
                  (is_string($contentDetails->title) or
                    isset($contentDetails->title->{Helper::lang('lang.code')}) !== false
                  )
                ) {

                  $text = is_string($contentDetails->title) ? $contentDetails->title : $contentDetails->title->{Helper::lang('lang.code')};
                } elseif (
                  isset($contentDetails->name) and
                  (is_string($contentDetails->name) or
                    isset($contentDetails->name->{Helper::lang('lang.code')}) !== false
                  )
                ) {

                  $text = is_string($contentDetails->name) ? $contentDetails->name : $contentDetails->name->{Helper::lang('lang.code')};
                } else {
                  $text = $val;
                }
                $html .= '<option value="' . $val . '"' . ($parameter == $val ? ' selected' : '') . '>' . $text . '</option>';
              }
              $html .= '</optgroup>';
            }
          }
        }
      } elseif ($module[0] === 'forms') {

        if (isset($this->forms[$module[1]]) !== false) {

          $module = $module[1];
          $moduleDetail = $this->forms[$module];
          if ($moduleDetail['routes']['listing']) {
            $html .= '<option value="list"' . ($parameter == 'list' ? ' selected' : '') . '>' . Helper::lang('base.list') . '</option>';
          }

          if ($moduleDetail['routes']['detail']) {
            $html .= '<option value="detail"' . ($parameter == 'detail' ? ' selected' : '') . '>' . Helper::lang('base.detail') . '</option>';
          }
        }
      }
    }



    if (isset($returnDirect) !== false) {
      $return = $html;
    } else {
      $arguments['manipulation'] = [
        $target => [
          'html'  => $html
        ]
      ];
      $return = [
        'status' => true,
        'statusCode' => 200,
        'arguments' => $arguments,
        'view' => null
      ];
    }

    return $return;
  }

  public function menuIntegrityCheck($items)
  {

    $return = true;
    $availableLangs = Helper::config('app.available_languages');
    if (count((array)$items)) {
      foreach ($items as $detail) {

        if ($detail->direct_link === '' and $detail->dynamic_link->module === '') {
          $return = false;
          break;
        }

        foreach ($availableLangs as $lang) {
          if (isset($detail->name->{$lang}) === false or $detail->name->{$lang} === '') {
            $return = false;
            break;
          }
        }

        if (isset($detail->sub) !== false) {
          $return = $this->menuIntegrityCheck($detail->sub);
          if (!$return)
            break;
        }
      }
    }
    /*
		else {
			$return = false;
		}
		*/
    return $return;
  }

  public function menuList()
  {

    $container = $this->get();

    $tableOp = (new KalipsoTable())
      ->db((new Menus)->pdo)
      ->from('(SELECT 
					x.id, 
					x.menu_key, 
					x.items,
					FROM_UNIXTIME(x.created_at, "%Y.%m.%d %H:%i") AS created,
					IFNULL(FROM_UNIXTIME(x.updated_at, "%Y.%m.%d"), "-") AS updated
				FROM `menus` x) AS raw')
      ->process([
        'id' => [
          'primary' => true,
        ],
        'menu_key' => [],
        'item_count' => [
          'exclude' => true,
          'formatter' => function ($row) {

            $total = 0;
            $arr = json_decode($row->items, true);
            array_walk_recursive($arr, function ($val, $key) use (&$total) {
              if ($key == 'direct_link') {
                $total++;
              }
            });
            return $total;
          }
        ],
        'created' => [],
        'updated' => [],
        'action' => [
          'exclude' => true,
          'formatter' => function ($row) use ($container) {

            $buttons = '';
            if ($container->authority('management/menus/:id')) {
              $buttons .= '
							<button type="button" class="btn btn-light" 
								data-KX-action="' . $this->get()->url('/management/menus/' . $row->id) . '">
								' . Helper::lang('base.view') . '
							</button>';
            }

            if ($container->authority('management/menus/:id/delete')) {
              $buttons .= '
							<button type="button" class="btn btn-danger" 
								data-KX-again="' . Helper::lang('base.are_you_sure') . '" 
								data-KX-action="' . $this->get()->url('/management/menus/' . $row->id . '/delete') . '">
								' . Helper::lang('base.delete') . '
							</button>';
            }



            return '
						<div class="btn-group btn-group-sm" role="group" aria-label="' . Helper::lang('base.action') . '">
							' . $buttons . '
						</div>';
          }
        ],
      ])
      ->output();

    return [
      'status' => true,
      'statusCode' => 200,
      'arguments' => $tableOp,
      'view' => null
    ];
  }

  public function menuAdd()
  {

    extract(Helper::input([
      'menu_key' => 'nulled_text',
      'items' => 'nulled_text'
    ], $this->get('request')->params));

    $alerts = [];
    $arguments = [];

    $model = new Menus();

    if ($menu_key and $items) {

      $keyCheck = $model->count('id', 'total')->where('menu_key', $menu_key)->get();
      if ((int)$keyCheck->total === 0) {

        $items = htmlspecialchars_decode($items);
        $itemsObj = @json_decode($items);
        $insert = $this->menuIntegrityCheck($itemsObj);

        if ($insert) {

          $insert = [
            'menu_key' => $menu_key,
            'items' => $items
          ];

          $insert = $model->insert($insert);
          if ($insert) {

            $alerts[] = [
              'status' => 'success',
              'message' => Helper::lang('base.menu_successfully_added')
            ];
            $arguments['form_reset'] = true;
            $arguments['modal_close'] = '#addModal';
            $arguments['table_reset'] = 'menusTable';
          } else {

            $alerts[] = [
              'status' => 'error',
              'message' => Helper::lang('base.menu_add_problem')
            ];
          }
        } else {

          $alerts[] = [
            'status' => 'warning',
            'message' => Helper::lang('base.menu_integrity_problem')
          ];
        }
      } else {

        $alerts[] = [
          'status' => 'warning',
          'message' => Helper::lang('base.key_is_already_used')
        ];
        $arguments['manipulation'] = [
          '#addModal [name="key"]' => [
            'class' => ['is-invalid'],
          ]
        ];
      }
    } else {

      $alerts[] = [
        'status' => 'warning',
        'message' => Helper::lang('base.form_cannot_empty')
      ];
    }

    return [
      'status' => true,
      'statusCode' => 200,
      'arguments' => $arguments,
      'alerts' => $alerts,
      'view' => null
    ];
  }

  public function menuDelete()
  {

    $id = (int)$this->get('request')->attributes['id'];

    $alerts = [];
    $arguments = [];

    $model = new Menus();

    $getMenu = $model->select('id')->where('id', $id)->get();
    if (!empty($getMenu)) {

      $delete = $model->where('id', $id)->delete();

      if ($delete) {

        $alerts[] = [
          'status' => 'success',
          'message' => Helper::lang('base.menu_successfully_deleted')
        ];
        $arguments['table_reset'] = 'menusTable';
      } else {

        $alerts[] = [
          'status' => 'error',
          'message' => Helper::lang('base.menu_delete_problem')
        ];
      }
    } else {

      $alerts[] = [
        'status' => 'warning',
        'message' => Helper::lang('base.record_not_found')
      ];
    }

    return [
      'status' => true,
      'statusCode' => 200,
      'arguments' => $arguments,
      'alerts' => $alerts,
      'view' => null
    ];
  }

  public function menuDetail()
  {

    $id = (int)$this->get('request')->attributes['id'];

    $alerts = [];
    $arguments = [];

    $model = new Menus();
    $getMenu = $model->select('id, menu_key, items')->where('id', $id)->get();
    if (!empty($getMenu)) {

      $getMenu->items = json_decode($getMenu->items);
      $menuContent = HTML::menuUrlWidgetList(
        $getMenu->items
      );

      $arguments['modal_open'] = ['#editModal'];
      $arguments['dragger'] = true;
      $arguments['manipulation'] = [
        '#menuUpdate' => [
          'attribute' => ['action' => $this->get()->url('management/menus/' . $id . '/update')],
        ],
        '#editModal [name="menu_key"]' => [
          'attribute' => ['value' => $getMenu->menu_key],
        ],
        '#editModal #menuItems' => [
          'html' => $menuContent,
        ],
      ];
    } else {

      $alerts[] = [
        'status' => 'warning',
        'message' => Helper::lang('base.record_not_found')
      ];
    }

    return [
      'status' => true,
      'statusCode' => 200,
      'arguments' => $arguments,
      'alerts' => $alerts,
      'view' => null
    ];
  }

  public function menuUpdate()
  {

    $id = (int)$this->get('request')->attributes['id'];

    extract(Helper::input([
      'menu_key' => 'nulled_text',
      'items' => 'nulled_text'
    ], $this->get('request')->params));

    $alerts = [];
    $arguments = [];

    $model = new Menus();
    $getMenu = $model->select('id, menu_key, items')->where('id', $id)->get();
    if (!empty($getMenu)) {

      if ($menu_key and $items) {

        $keyCheck = $model->count('id', 'total')->where('menu_key', $menu_key)->notWhere('id', $id)->get();
        if ((int)$keyCheck->total === 0) {

          $items = htmlspecialchars_decode($items);
          $itemsObj = @json_decode($items);
          $update = $this->menuIntegrityCheck($itemsObj);

          if ($update) {

            $update = [
              'menu_key' => $menu_key,
              'items' => $items
            ];

            $update = $model->where('id', $id)->update($update);
            if ($update) {

              $alerts[] = [
                'status' => 'success',
                'message' => Helper::lang('base.menu_successfully_updated')
              ];
              $arguments['modal_close'] = '#editModal';
              $arguments['table_reset'] = 'menusTable';
            } else {

              $alerts[] = [
                'status' => 'error',
                'message' => Helper::lang('base.menu_update_problem')
              ];
            }
          } else {

            $alerts[] = [
              'status' => 'warning',
              'message' => Helper::lang('base.menu_integrity_problem')
            ];
          }
        } else {

          $alerts[] = [
            'status' => 'warning',
            'message' => Helper::lang('base.key_is_already_used')
          ];
          $arguments['manipulation'] = [
            '#editModal [name="key"]' => [
              'class' => ['is-invalid'],
            ]
          ];
        }
      } else {

        $alerts[] = [
          'status' => 'warning',
          'message' => Helper::lang('base.form_cannot_empty')
        ];
      }
    } else {
      $alerts[] = [
        'status' => 'warning',
        'message' => Helper::lang('base.record_not_found')
      ];
    }

    return [
      'status' => true,
      'statusCode' => 200,
      'arguments' => $arguments,
      'alerts' => $alerts,
      'view' => null
    ];
  }

  public function getMenuDetails($menuKey)
  {

    $return = [];
    $lang = Helper::lang('lang.code');

    if (is_string($menuKey)) {

      $model = new Menus();
      $getMenu = $model->select('items')->where('menu_key', $menuKey)->get();
      $getMenu = isset($getMenu->items) !== false ? json_decode($getMenu->items, true) : null;
    } else {
      $getMenu = $menuKey;
    }

    if (!empty($getMenu)) {

      foreach ($getMenu as $key => $value) {

        $link = '#';
        if (!empty($value['direct_link'])) {
          $link = $value['direct_link'];
        } else {

          $module = strpos($value['dynamic_link']['module'], '_') !== false ? explode('_', $value['dynamic_link']['module'], 2) : ['basic', 'home'];
          $link = $this->urlGenerator($module[0], $module[1], $value['dynamic_link']['parameter']);

          if ($value['dynamic_link']['parameter'] === 'list_as_dropdown' and $module[0] === 'modules') {

            $getContents = (new ContentController($this->get()))
              ->getModuleDatas($module[1]);

            if (!empty($getContents)) {

              if (isset($value['sub']) === false)
                $value['sub'] = [];

              $baseRoute = '/';
              if (isset($this->modules[$module[1]]['routes']['detail'][$lang][1]) !== false)
                $baseRoute = $this->modules[$module[1]]['routes']['detail'][$lang][1];

              foreach ($getContents as $content) {
                $content->input = json_decode($content->input);
                $contentLink = Helper::dynamicURL(
                  $baseRoute,
                  ['slug' => $content->input->slug->{$lang}]
                );

                $contentName = $content->id;
                if (isset($content->input->name->{$lang}) !== false) {
                  $contentName = $content->input->name->{$lang};
                } elseif (isset($content->input->name) !== false) {
                  $contentName = $content->input->name;
                } elseif (isset($content->input->title->{$lang}) !== false) {
                  $contentName = $content->input->title->{$lang};
                } elseif (isset($content->input->title) !== false) {
                  $contentName = $content->input->title;
                }

                $value['sub'][] = [
                  'direct_link' => $contentLink,
                  'name' => [$lang => $contentName],
                  'blank' => false,
                ];
              }
            }
          }
        }

        $return[$key]['name'] = $value['name'][$lang];
        $return[$key]['blank'] = isset($value['blank']) !== false ? $value['blank'] : false;
        $return[$key]['link'] = $link;
        if (isset($value['sub']) !== false) {
          $return[$key]['sub'] = $this->getMenuDetails($value['sub']);
        }
      }
    }

    return $return;
  }
}
