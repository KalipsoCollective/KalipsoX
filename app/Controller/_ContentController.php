<?php

/**
 * @package KX
 * @subpackage Controller\ContentController
 */

declare(strict_types=1);

namespace KX\Controller;

use KX\Core\Controller;
use KX\Model\Contents as ContentsModel;
use KX\Core\Helper;
use KX\Helper\HTML;
use KX\Helper\KalipsoTable;


final class ContentController extends Controller
{

  public function __construct($container)
  {

    parent::__construct($container);
  }

  public function contents()
  {

    $title = Helper::lang('base.contents');
    $description = Helper::lang('base.contents_message');

    if (isset($this->modules[$this->module]) !== false) {

      $module = $this->modules[$this->module];
      $moduleName = Helper::lang($module['name']);
      $title = $moduleName . ' | ' . $title;
      $description = Helper::lang($module['description']);
      $icon = isset($module['icon']) !== false ? $module['icon'] : 'ti ti-folders';

      $arguments = [
        'title' => $title,
        'moduleName' => $moduleName,
        'moduleDatas' => $module,
        'icon' => $icon,
        'description' => $description,
        'module' => $this->module,
        'modules' => $this->modules,
        'forms' => $this->forms,
        'languages' => Helper::config('app.available_languages'),
        'moduleForm' => $this->prepareModuleForm($module['inputs']),
      ];

      return [
        'status' => true,
        'statusCode' => 200,
        'arguments' => $arguments,
        'view' => ['admin.contents', 'admin']
      ];
    } else {

      return [
        'status' => false,
        'statusCode' => 404,
        'redirect' => '/management',
        'alerts' => [
          [
            'status' => 'error',
            'message' => Helper::lang('error.module_not_found')
          ]
        ],
        'view' => null
      ];
    }
  }

  public function getModuleDatas($module = null)
  {

    $model = (new ContentsModel())->where('module', $module);

    return $model->getAll();
  }

  public function getModuleData($module = null, $id = null, $externalQuery = [])
  {


    $model = (new ContentsModel())->where('module', $module);
    if ($id) {
      $model->where('id', $id);
    }

    if (count($externalQuery)) {

      foreach ($externalQuery as $col => $value) {
        if (!in_array($col, ['id', 'created_at', 'created_by', 'updated_at', 'updated_by'])) {
          $col = '(JSON_UNQUOTE(JSON_EXTRACT(input, \'$.' . $col . '\')))';
        }
        $model->where($col, $value);
      }
    }

    $model->orderBy('id', 'desc');
    return $model->get();
  }

  public function getContent($id = 0)
  {

    $return = null;
    if ($id) {
      $return = (new ContentsModel())->where('id', $id)->get();
    }
    return $return;
  }

  public function prepareModuleForm($module, $fill = null)
  {

    $idPrefix = 'content_add';
    if (!is_null($fill)) {
      $fillDatas = json_decode($fill->input);
      $id = $fill->id;
      $idPrefix = 'content_edit';
      $fileController = new FileController($this->get());
    }

    $moduleForm = '';
    $languages = Helper::config('app.available_languages');
    $multilanguage = false;
    foreach ($module as $name => $input) {

      $col = isset($input['col']) !== false ? $input['col'] : 'col-12 col-md-6';

      if ($name === 'widget') { // Relational content

        foreach ($input as $key => $widget) {
          // from db or array
          $data = call_user_func([$this, $widget['source'][0]], ...$widget['source'][1]);

          $options = '';

          $allSelected = '';
          if (isset($fillDatas->{$key}) !== false and $fillDatas->{$key} == 0) {
            $allSelected = ' selected';
          }
          foreach ($data as $widgetData) {

            $widgetInputs = json_decode($widgetData->input);
            $text = isset($widgetInputs->{$widget['use_for_view']}->{Helper::lang('lang.code')}) !== false
              ? $widgetInputs->{$widget['use_for_view']}->{Helper::lang('lang.code')}
              : $widgetInputs->{$widget['use_for_view']};

            $selected = '';
            if (
              isset($fillDatas->{$key}) !== false and
              ($fillDatas->{$key} == $widgetData->id or (is_array($fillDatas->{$key}) and in_array($widgetData->id, $fillDatas->{$key}))
              )
            ) {
              $selected = ' selected';
            }

            $options .= '<option value="' . $widgetData->id . '"' . $selected . '>' . $text . '</option>';
          }

          $attributes = '';
          $requiredWidget = false;
          if (isset($widget['attributes']) !== false) {
            foreach ($widget['attributes'] as $attribute => $val) {

              if (in_array($attribute, ['required', 'selected', 'checked']) !== false)
                $attributes .= $attribute . ' ';
              else
                $attributes .= $attribute . '="' . $val . '" ';

              if ($attribute === 'required') {
                $requiredWidget = true;
              }
            }
          }

          $requiredBadge = '';
          if (isset($widget['attributes']['required']) !== false and $widget['attributes']['required']) {
            $requiredBadge = ' <sup class="text-danger">*</sup>';
          }

          $col = isset($widget['col']) !== false ? $widget['col'] : 'col-12 col-md-6';

          $moduleForm .= '
                    <div class="' . $col . '">
                        <div class="form-floating">
                            <select class="form-select" ' . $attributes . 'name="' . $key . (isset($widget['attributes']['multiple']) !== false ? '[]' : '') . '" id="' . $idPrefix . '_' . $key . '">
                                ' . (!$requiredWidget ?
            '<option value="-1"' . ((isset($fillDatas->{$key}) !== false and $fillDatas->{$key} == -1) ? ' selected' : '') . '></option>' :
            '') . '
                                <option value="0"' . $allSelected . '>' . Helper::lang('base.all') . '</option>
                                ' . $options . '
                            </select>
                            <label for="' . $idPrefix . '_' . $key . '">' . Helper::lang($widget['label']) . $requiredBadge . '</label>
                        </div>
                    </div>';
        }
      } else {

        if ($input['type'] === 'url_widget') { // URL Widget

          $menuController = new MenuController($this->get());
          $menuWidgetData = [
            'menu_options' => $menuController->menuOptionsAsHTML(
              (isset($fillDatas->{$name}->dynamic_link->module) !== false ? $fillDatas->{$name}->dynamic_link->module : null)
            ),
            'name' => $name,
            'label' => Helper::lang($input['label']),
            'values' => (isset($fillDatas->{$name}) !== false ? $fillDatas->{$name} : [])
          ];
          if (isset($fillDatas->{$name}->dynamic_link->module) !== false and isset($fillDatas->{$name}->dynamic_link->parameter) !== false) {
            $menuWidgetData['module_parameters'] = $menuController->getMenuParameters(
              $fillDatas->{$name}->dynamic_link->module,
              $fillDatas->{$name}->dynamic_link->parameter
            );
          }


          $moduleForm .= HTML::menuModuleUrlWidget($menuWidgetData);
        } else {

          $nameSubfix = [null];
          if (isset($input['multilanguage']) !== false and $input['multilanguage']) {

            $nameSubfix = $languages;
            $moduleForm .= '
                        <div class="' . $col . ' kn-multilang-content">
                            <div class="kn-multilang-content-switch">
                                <div class="nav nav-pills" id="' . $idPrefix . '_' . $name . '-tablist" role="tablist" aria-orientation="vertical">';
            foreach ($languages as $i => $lang) {
              $moduleForm .= '
                                    <button class="nav-link' . ($i === 0 ? ' active' : '') . '" id="' . $idPrefix . '_' . $name . '-tab-' . $lang . '" data-bs-toggle="pill" data-bs-target="#' . $idPrefix . '_' . $name . '-' . $lang . '" type="button" role="tab" aria-controls="' . $idPrefix . '_' . $name . '-' . $lang . '" aria-selected="' . ($i === 0 ? 'true' : 'false') . '">
                                        ' . Helper::lang('langs.' . $lang) . '
                                    </button>';
            }
            $moduleForm .= '
                                </div>
                            </div>
                            <div class="tab-content">';
          }

          $col = isset($input['col']) !== false ? $input['col'] : 'col-12 col-md-6';

          foreach ($nameSubfix as $i => $lang) {

            $dataList = '';
            $attributes = '';
            $iconPicker = false;
            if (isset($input['attributes']) !== false) {
              foreach ($input['attributes'] as $attribute => $val) {
                if (in_array($attribute, ['required', 'checked', 'selected']) !== false)
                  $attributes .= $attribute . ' ';
                else {

                  $val = is_bool($val) ? (string)$val : $val;

                  if (strpos($val, '"') !== false) $quote = '\'';
                  else $quote = '"';

                  if ($attribute === 'data-kn-icon-picker') {
                    $iconPicker = true;
                  }

                  if ($attribute === 'data-kn-autocomplete') {
                    $dataList .= '
                                        <datalist id="' . $idPrefix . '_' . $name . $lang . '_DataList">
                                        </datalist>';
                  }

                  $attributes .= $attribute . '=' . $quote . $val . $quote . ' ';
                }
              }
            }

            $currentVal = null;
            $inputName = is_null($lang) ? $name : $name . '[' . $lang . ']';
            // multilingual
            if (!is_null($lang)) {
              $col = 'col';
              $moduleForm .= '
                            <div class="tab-pane fade' . ($i === 0 ? ' show active' : '') . '" id="' . $idPrefix . '_' . $name . '-' . $lang . '" role="tabpanel" aria-labelledby="' . $idPrefix . '_' . $name . '-tab-' . $lang . '">';

              if (isset($fillDatas->{$name}->{$lang}) !== false) {
                $currentVal = $fillDatas->{$name}->{$lang};
              }
            } else {

              if (isset($fillDatas->{$name}) !== false) {
                $currentVal = $fillDatas->{$name};
              }
            }

            $requiredBadge = '';
            if (isset($input['attributes']['required']) !== false and $input['attributes']['required']) {
              $requiredBadge = ' <sup class="text-danger">*</sup>';
            }

            switch ($input['type']) {
              case 'input':
              case 'color':
              case 'number':
              case 'text':
              case 'email':
              case 'tel':


                if (!is_null($currentVal)) {
                  $attributes .= 'value="' . $currentVal . '" ';
                }
                $attributes .= 'data-kn-lang="' . $lang . '" ';
                if (isset($id) !== false) $attributes .= 'data-kn-id="' . $id . '" ';

                $moduleForm .= '
                                <div class="' . $col . '">
                                    <div class="form-floating' . ($iconPicker ? ' kn-iconpicker' : '') . '">
                                        <input list="' . $idPrefix . '_' . $name . $lang . '_DataList" type="' . ($input['type'] == 'input' ? 'text' : $input['type']) . '" class="form-control" ' . $attributes . 'name="' . $inputName . '" id="' . $idPrefix . '_' . $name . $lang . '" placeholder="' . Helper::lang($input['label']) . '"' . ($iconPicker ? ' aria-haspopup="true" aria-controls="icon_list_' . $idPrefix . '_' . $name . $lang . '"' : '') . ' />
                                        ' . ($iconPicker ? '<i id="icon_' . $idPrefix . '_' . $name . $lang . '" data-kn-icon class="' . (!is_null($currentVal) ? $currentVal : '') . '"></i>' : '') . '
                                        <label for="' . $idPrefix . '_' . $name . $lang . '">' . Helper::lang($input['label']) . $requiredBadge . '</label>
                                        ' . $dataList . '
                                        ' . ($iconPicker ? '<ul class="kn-iconpicker-list" id="icon_list_' . $idPrefix . '_' . $name . $lang . '" role="menu" aria-labelledby="' . $idPrefix . '_' . $name . $lang . '"></ul>' : '') . '
                                    </div>
                                </div>';
                break;

              case 'file':

                $externalBadge = '';
                if (!is_null($currentVal) and is_array($currentVal) and count($currentVal)) {

                  $getFiles = $fileController->getFilesInId($currentVal);

                  if (!empty($getFiles)) {

                    $externalBadge = '<div class="image-group">';
                    foreach ($getFiles as $fileIndex => $getFile) {

                      $getFile->files = json_decode($getFile->files);
                      $hrefDir = 'upload/' . $getFile->files->original;
                      $srcDir = 'upload/' . (isset($getFile->files->sm) !== false ? $getFile->files->sm : $getFile->files->original);
                      $href = Helper::base($hrefDir);
                      $src = Helper::base($srcDir);
                      if (strpos(mime_content_type(Helper::path($srcDir)), 'image') === false) {
                        $src = Helper::base('assets/img/kn/file_icon.svg');
                      }

                      $externalBadge .= '<a href="' . $href . '" target="_blank" data-target="current_file_delete_' . $inputName . $fileIndex . '">
                                                <img class="table-image" src="' . $src . '" />
                                                <button href="javascript:;" 
                                                    data-kn-again="' . Helper::lang('base.are_you_sure') . '" 
                                                    data-kn-action="manipulation"
                                                    data-kn-manipulation=\'' . json_encode(
                        [
                          'manipulation' => [
                            '[data-target="current_file_delete_' . $inputName . $fileIndex . '"]' => [
                              'remove_element' => true
                            ]
                          ]
                        ]
                      ) . '\'
                                                    >
                                                    ' . Helper::lang('base.delete') . '
                                                </button>
                                                <input type="hidden" name="current_file_' . $inputName . '[]" value="' . $getFile->id . '" />
                                            </a>';
                    }
                    $externalBadge .= '</div>';
                  }
                }

                if (isset($input['external_parameters']['size']) !== false and is_array($input['external_parameters']['size']) and count($input['external_parameters']['size'])) {

                  $dimTags = '';
                  foreach ($input['external_parameters']['size'] as $d => $s) {
                    $dimTags .= ', ' . $d . ': ' . (is_null($s[0]) ? '-' : $s[0]) . 'x' . (is_null($s[1]) ? '-' : $s[1]);
                  }
                  $dimTags = ' (' . trim($dimTags, ', ') . ')';
                  $requiredBadge = $dimTags . ' ' . $requiredBadge;
                }

                $moduleForm .= '
                                <div class="' . $col . '">
                                    <div class="">
                                        <label for="' . $idPrefix . '_' . $name . $lang . '" class="form-label small text-muted m-0">' . $externalBadge . Helper::lang($input['label']) . $requiredBadge . '</label>
                                        <input class="form-control" ' . $attributes . 'name="' . $inputName . (isset($input['attributes']['multiple']) !== false ? '[]' : '') . '" id="' . $idPrefix . '_' . $name . $lang . '" type="file">
                                    </div>
                                </div>';
                break;

              case 'select':
                $options = '';
                if (isset($input['data']) !== false) {
                  foreach ($input['data'] as $val => $text) {
                    $selected = '';
                    if (!is_null($currentVal) and $currentVal == $val) {
                      $selected .= ' selected';
                    }
                    $options .= '<option value="' . $val . '"' . $selected . '>' . $text . '</option>';
                  }
                }

                $moduleForm .= '
                                <div class="' . $col . '">
                                    <div class="form-floating">
                                        <select class="form-select" ' . $attributes . 'name="' . $inputName . '" id="' . $idPrefix . '_' . $name . $lang . '">
                                            ' . $options . '
                                        </select>
                                        <label for="' . $idPrefix . '_' . $name . $lang . '">' . Helper::lang($input['label']) . $requiredBadge . '</label>
                                    </div>
                                </div>';
                break;

              case 'textarea':
                $moduleForm .= '
                                <div class="' . $col . '">
                                    <div class="form-floating">
                                        <textarea class="form-control" ' . $attributes . 'name="' . $inputName . '" id="' . $idPrefix . '_' . $name . $lang . '" placeholder="' . Helper::lang($input['label']) . '" style="min-height: 100px">' . $currentVal . '</textarea>
                                        <label for="' . $idPrefix . '_' . $name . $lang . '">' . Helper::lang($input['label']) . $requiredBadge . '</label>
                                    </div>
                                </div>';
                break;

              case 'editor':
                $moduleForm .= '
                                <div class="' . $col . '">
                                    <div data-kn-toggle="editor" class="editor" data-options=\'' . json_encode(['placeholder' => Helper::lang($input['label'])])  . '\' data-name="' . $inputName . '" data-module="' . $this->module . '">' . htmlspecialchars_decode((string) $currentVal) . '</div>
                                </div>';
                break;
            }

            if (!is_null($lang)) {
              $moduleForm .= '
                            </div>';
            }
          }

          if (isset($input['multilanguage']) !== false and $input['multilanguage']) {
            $multilanguage = true;
            $moduleForm .= '
                            </div>
                        </div>';
          }
        }
      }
    }

    $moduleForm = '
        <form class="row g-2" data-kn-form id="' . (isset($id) !== false ? 'contentEdit' : 'contentAdd') . '"' . ($multilanguage ? ' novalidate' : '') . ' method="post" action="' . $this->get()->url('management/' . $this->module . '/' . (isset($id) !== false ? $id . '/update' : 'add')) . '">
            <div class="form-loader">
                <div class="spinner-border text-light" role="status">
                    <span class="visually-hidden">' . Helper::lang('base.loading') . '</span>
                </div>
            </div>
            <div class="col-12 form-info">
            </div>'
      . $moduleForm .
      '</form>';


    return $moduleForm;
  }

  public function uploadAFile()
  {

    $files = $this->get()->request->files;

    $alerts = [];
    $arguments = [];

    if (is_array($files) and count($files)) {

      $fileController = new FileController($this->get());
      $upload = $fileController->directUpload($this->module, $files);

      if (count($upload)) {

        $url = array_shift($upload);
        $url = $url['original'];

        $alerts[] = [
          'status' => 'success',
          'message' => Helper::lang('base.file_successfully_uploaded')
        ];

        $arguments['editor_upload'][] = $this->get()->url('upload/' . $url);
      } else {

        $alerts[] = [
          'status' => 'error',
          'message' => Helper::lang('base.file_upload_problem')
        ];
      }
    } else {
      $alerts[] = [
        'status' => 'warning',
        'message' => Helper::lang('base.file_not_found')
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

  public function contentAdd()
  {

    $alerts = [];
    $arguments = [];

    $rollBack = [];
    $inputAreas = [];
    $files = [];
    $requiredAreas = [
      'areas' => [],
      'files' => []
    ];
    if (isset($this->modules[$this->module]) !== false) {

      $module = $this->modules[$this->module];

      // Input area check
      foreach ($module['inputs'] as $name => $detail) {

        if ($name === 'widget') {

          foreach ($detail as $name => $widgetDetail) {

            $inputAreas[$name] = 'int';
            if (isset($detail['attributes']['required']) !== false and $detail['attributes']['required'] === 'true') {
              $requiredAreas['areas'][$name] = true;
            }
          }
        } else {

          if ($detail['type'] === 'input' or $detail['type'] === 'url_widget' or $detail['type'] === 'email' or $detail['type'] === 'tel' or $detail['type'] === 'textarea' or $detail['type'] === 'select' or $detail['type'] === 'text') {

            $inputAreas[$name] = 'nulled_text';
          } elseif ($detail['type'] === 'number') {

            $inputAreas[$name] = 'int';
          } elseif ($detail['type'] === 'checkbox') {

            $inputAreas[$name] = 'check_as_boolean';
          } elseif ($detail['type'] === 'editor') {

            $inputAreas[$name] = 'nulled_html';
          } elseif ($detail['type'] === 'color') {

            $inputAreas[$name] = 'color';
          } elseif ($detail['type'] === 'file') {

            $files[$name] = $detail;
          }

          if (isset($detail['attributes']['required']) !== false and $detail['attributes']['required'] === 'true') {
            $requiredAreas[($detail['type'] === 'file' ? 'files' : 'areas')][$name] = $detail;
          }
        }
      }

      extract(Helper::input($inputAreas, $this->get('request')->params));

      $insert = [];
      // Filter all inputs
      foreach ($inputAreas as $inputName => $inputType) {

        if (is_array($$inputName)) { // multilingual

          foreach ($$inputName as $lang => $inputVar) {

            if (
              isset($requiredAreas['areas'][$inputName]) === false or !empty($inputVar)
            ) {

              $insert[$inputName][$lang] = $inputVar;
            } else {

              if ($inputType === 'nulled_html') {

                $arguments['manipulation']['#contentAdd [data-name="' . $inputName . '[' . $lang . ']"]'] = [
                  'class' => ['border', 'border-1', 'border-danger'],
                ];
              } else {

                $arguments['manipulation']['#contentAdd [name="' . $inputName . '[' . $lang . ']"]'] = [
                  'class' => ['is-invalid'],
                ];
              }
            }
          }
        } elseif (isset($requiredAreas['areas'][$inputName]) === false or !empty($$inputName)) {

          $insert[$inputName] = $$inputName;
        } else {

          if ($inputType === 'nulled_html') {
            $arguments['manipulation']['#contentAdd [data-name="' . $inputName . '"]'] = [
              'class' => ['border', 'border-1', 'border-danger'],
            ];
          } else {
            $arguments['manipulation']['#contentAdd [name="' . $inputName . '"]'] = [
              'class' => ['is-invalid'],
            ];
          }
        }
      }

      if (isset($arguments['manipulation']) === false) {

        // Files
        if (count($files)) {

          $uploadFiles = $this->contentFileUploader($files, $insert, false);

          if (isset($uploadFiles['manipulation']) !== false) {
            $arguments['manipulation'] = array_merge(
              (isset($arguments['manipulation']) !== false ? $arguments['manipulation'] : []),
              $uploadFiles['manipulation']
            );
          }

          if (isset($uploadFiles['alerts']) !== false) {
            $alerts = array_merge($alerts, $uploadFiles['alerts']);
          }
          $insert = $uploadFiles['row'];
        }


        if (isset($arguments['manipulation']) === false) {

          $model = new ContentsModel;
          $insert = $model->insert([
            'module' => $this->module,
            'input' => json_encode($insert),
          ]);

          if ($insert) {

            $alerts[] = [
              'status' => 'success',
              'message' => Helper::lang('base.content_successfully_added')
            ];
            $arguments['form_reset'] = true;
            $arguments['modal_close'] = '#addModal';
            $arguments['table_reset'] = 'contentsTable';
            $rollBack = [];
          } else {

            $alerts[] = [
              'status' => 'error',
              'message' => Helper::lang('base.content_add_problem')
            ];
          }
        }
      } else {

        $alerts[] = [
          'status' => 'warning',
          'message' => Helper::lang('base.form_cannot_empty')
        ];
      }
    } else {

      $alerts[] = [
        'status' => 'error',
        'message' => Helper::lang('error.module_not_found')
      ];
    }

    if (isset($rollBack) !== false and count($rollBack)) {
      foreach ($rollBack as $fileId) {
        $fileController->removeFileWithId($fileId);
      }
    }

    return [
      'status' => true,
      'statusCode' => 200,
      'arguments' => $arguments,
      'alerts' => $alerts,
      'view' => null
    ];
  }

  public function contentList()
  {

    $container = $this->get();
    $moduleName = $this->module;
    if (isset($this->modules[$moduleName]) !== false) {

      $module = $this->modules[$moduleName];

      $tables = $module['table'];
      $tables['action'] = [
        'exclude' => true,
        'formatter' => function ($row) use ($container, $moduleName) {

          $buttons = '';
          if ($container->authority('management/:module/:id')) {
            $buttons .= '
                        <button type="button" class="btn btn-light" 
                            data-kn-action="' . $this->get()->url('/management/' . $moduleName . '/' . $row->id) . '">
                            ' . Helper::lang('base.view') . '
                        </button>';
          }

          if ($container->authority('management/:module/:id/delete')) {
            $buttons .= '
                        <button type="button" class="btn btn-danger" 
                            data-kn-again="' . Helper::lang('base.are_you_sure') . '" 
                            data-kn-action="' . $this->get()->url('/management/' . $moduleName . '/' . $row->id . '/delete') . '">
                            ' . Helper::lang('base.delete') . '
                        </button>';
          }

          return '
                    <div class="btn-group btn-group-sm" role="group" aria-label="' . Helper::lang('base.action') . '">
                        ' . $buttons . '
                    </div>';
        }
      ];
      $tableOp = (new KalipsoTable())
        ->db((new ContentsModel)->pdo)
        ->from($module['from'])
        ->process($tables)
        ->output();

      return [
        'status' => true,
        'statusCode' => 200,
        'arguments' => $tableOp,
        'view' => null
      ];
    } else {

      return [
        'status' => false,
        'statusCode' => 404,
        'redirect' => '/management',
        'alerts' => [
          [
            'status' => 'error',
            'message' => Helper::lang('error.module_not_found')
          ]
        ],
        'view' => null
      ];
    }
  }

  public function contentDetail()
  {

    $id = (int)$this->get('request')->attributes['id'];


    $alerts = [];
    $arguments = [];

    if (isset($this->modules[$this->module]) !== false) {

      $model = new ContentsModel();
      $getContent = $model->select('id, module, input')->where('id', $id)->where('module', $this->module)->get();
      if (!empty($getContent)) {

        $form = $this->prepareModuleForm($this->modules[$this->module]['inputs'], $getContent);
        $arguments['modal_open'] = ['#editModal'];
        $arguments['init'] = '#editModal';
        $arguments['manipulation'] = [
          '#editModal .modal-body' => [
            'html'  => $form
          ]
        ];
      } else {

        $alerts[] = [
          'status' => 'warning',
          'message' => Helper::lang('base.record_not_found')
        ];
      }
    } else {

      $alerts[] = [
        'status' => 'error',
        'message' => Helper::lang('error.module_not_found')
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

  public function contentUpdate()
  {

    $alerts = [];
    $arguments = [];

    $id = (int)$this->get('request')->attributes['id'];

    $rollBack = [];
    $inputAreas = [];
    $files = [];
    $requiredAreas = [
      'areas' => [],
      'files' => []
    ];
    if (isset($this->modules[$this->module]) !== false) {

      $module = $this->modules[$this->module];
      $availableLanguages = Helper::config('app.available_languages');

      // Input area check
      foreach ($module['inputs'] as $name => $detail) {

        if ($name === 'widget') {

          foreach ($detail as $name => $widgetDetail) {

            $inputAreas[$name] = 'int';
            if (isset($detail['attributes']['required']) !== false and $detail['attributes']['required'] === 'true') {
              $requiredAreas['areas'][$name] = true;
            }
          }
        } else {

          if ($detail['type'] === 'input' or $detail['type'] === 'url_widget' or $detail['type'] === 'email' or $detail['type'] === 'tel' or $detail['type'] === 'textarea' or $detail['type'] === 'select' or $detail['type'] === 'text') {

            $inputAreas[$name] = 'nulled_text';
          } elseif ($detail['type'] === 'number') {

            $inputAreas[$name] = 'int';
          } elseif ($detail['type'] === 'checkbox') {

            $inputAreas[$name] = 'check_as_boolean';
          } elseif ($detail['type'] === 'editor') {

            $inputAreas[$name] = 'nulled_html';
          } elseif ($detail['type'] === 'color') {

            $inputAreas[$name] = 'color';
          } elseif ($detail['type'] === 'file') {

            $files[$name] = $detail;
          }

          if (isset($detail['attributes']['required']) !== false and $detail['attributes']['required'] === 'true') {
            $requiredAreas[($detail['type'] === 'file' ? 'files' : 'areas')][$name] = $detail;
          }
        }
      }

      extract(Helper::input($inputAreas, $this->get('request')->params));

      $update = [];
      // Filter all inputs
      foreach ($inputAreas as $inputName => $inputType) {

        if (is_array($$inputName)) { // multilingual

          foreach ($$inputName as $lang => $inputVar) {

            if (
              isset($requiredAreas['areas'][$inputName]) === false or !empty($inputVar)
            ) {

              $update[$inputName][$lang] = $inputVar;
            } else {

              if ($inputType === 'nulled_html') {

                $arguments['manipulation']['#contentEdit [data-name="' . $inputName . '[' . $lang . ']"]'] = [
                  'class' => ['border', 'border-1', 'border-danger'],
                ];
              } else {

                $arguments['manipulation']['#contentEdit [name="' . $inputName . '[' . $lang . ']"]'] = [
                  'class' => ['is-invalid'],
                ];
              }
            }
          }
        } elseif (isset($requiredAreas['areas'][$inputName]) === false or !empty($$inputName)) {

          $update[$inputName] = $$inputName;
        } else {

          if ($inputType === 'nulled_html') {
            $arguments['manipulation']['#contentEdit [data-name="' . $inputName . '"]'] = [
              'class' => ['border', 'border-1', 'border-danger'],
            ];
          } else {
            $arguments['manipulation']['#contentEdit [name="' . $inputName . '"]'] = [
              'class' => ['is-invalid'],
            ];
          }
        }
      }

      if (isset($arguments['manipulation']) === false) {

        // Files

        if (count($files)) {

          $uploadFiles = $this->contentFileUploader($files, $update, true);

          if (isset($uploadFiles['manipulation']) !== false) {
            $arguments['manipulation'] = array_merge(
              (isset($arguments['manipulation']) !== false ? $arguments['manipulation'] : []),
              $uploadFiles['manipulation']
            );
          }

          if (isset($uploadFiles['alerts']) !== false) {
            $alerts = array_merge($alerts, $uploadFiles['alerts']);
          }
          $update = $uploadFiles['row'];
        }

        if (!count($files) or isset($arguments['manipulation']) === false) {

          $model = new ContentsModel;
          $update = $model->where('id', $id)->update([
            'input' => json_encode($update),
          ]);

          if ($update) {

            $alerts[] = [
              'status' => 'success',
              'message' => Helper::lang('base.content_successfully_updated')
            ];
            $arguments['modal_close'] = '#editModal';
            $arguments['table_reset'] = 'contentsTable';
            $rollBack = [];
          } else {

            $alerts[] = [
              'status' => 'error',
              'message' => Helper::lang('base.content_update_problem')
            ];
          }
        }
      } else {

        $alerts[] = [
          'status' => 'warning',
          'message' => Helper::lang('base.form_cannot_empty')
        ];
      }
    } else {

      $alerts[] = [
        'status' => 'error',
        'message' => Helper::lang('error.module_not_found')
      ];
    }

    if (count($rollBack)) {
      $controller = new FileController($this->get());
      foreach ($rollBack as $fileId) {
        $controller->removeFileWithId($fileId);
      }
    }

    return [
      'status' => true,
      'statusCode' => 200,
      'arguments' => $arguments,
      'alerts' => $alerts,
      'view' => null
    ];
  }

  public function contentDelete()
  {

    $alerts = [];
    $arguments = [];

    $id = (int)$this->get('request')->attributes['id'];
    if (isset($this->modules[$this->module]) !== false) {

      $model = new ContentsModel();
      $getContent = $model->select('id')->where('id', $id)->where('module', $this->module)->get();
      if (!empty($getContent)) {

        $delete = $model->where('id', $id)->delete();

        if ($delete) {

          $alerts[] = [
            'status' => 'success',
            'message' => Helper::lang('base.content_successfully_deleted')
          ];
          $arguments['table_reset'] = 'contentsTable';
        } else {

          $alerts[] = [
            'status' => 'error',
            'message' => Helper::lang('base.content_delete_problem')
          ];
        }
      } else {

        $alerts[] = [
          'status' => 'warning',
          'message' => Helper::lang('base.record_not_found')
        ];
      }
    } else {

      $alerts[] = [
        'status' => 'error',
        'message' => Helper::lang('error.module_not_found')
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

  public function contentSlugInquiry()
  {

    extract(Helper::input([
      'id'    => 'int',
      'slug'  => 'slug',
      'lang'  => 'nulled_text',
    ], $this->get('request')->params));


    $alerts = [];
    $arguments = [];

    if (isset($this->modules[$this->module]) !== false) {

      if (!is_null($slug)) {

        $multilanguage = $lang ? $lang : false;
        $whereQuery = 'JSON_UNQUOTE(JSON_EXTRACT(input, \'$.slug' . ($lang ? '.' . $lang : '') . '\'))';

        $model = new ContentsModel();

        $checkNum = 1;
        while (1) {

          $slugCheck = $slug . ($checkNum > 1 ? '-' . $checkNum : '');

          $recordCheck = $model->select('id')
            ->where('module', $this->module)
            ->where($whereQuery, $slugCheck);

          if ($id > 0) {
            $recordCheck->notWhere('id', $id);
          }

          $recordCheck = $recordCheck->get();

          if (empty($recordCheck)) {
            break;
          }
          $checkNum++;
        }

        $arguments['manipulation'] = [
          '#content' . ($id ? 'Edit' : 'Add') . ' #content_' . ($id ? 'edit' : 'add') . '_slug' . $lang => [
            'attribute'  => [
              'value' => $slugCheck
            ]
          ]
        ];
      } else {

        $alerts[] = [
          'status' => 'error',
          'message' => Helper::lang('error.missing_or_incorrect_parameter')
        ];
      }
    } else {

      $alerts[] = [
        'status' => 'error',
        'message' => Helper::lang('error.module_not_found')
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

  public function contentAutoCompleteInquiry()
  {

    extract(Helper::input([
      'id'        => 'int',
      'field'     => 'nulled_text',
      'lang'      => 'nulled_text',
      'value'     => 'nulled_text'
    ], $this->get('request')->params));


    $alerts = [];
    $arguments = [];

    if (isset($this->modules[$this->module]) !== false) {

      if (!is_null($field)) {

        $html = '';

        $multilanguage = $lang ? $lang : false;
        $whereQuery = 'JSON_UNQUOTE(JSON_EXTRACT(input, \'$.' . $field . ($lang ? '.' . $lang : '') . '\'))';

        $model = new ContentsModel();
        $records = $model->select('id, ' . $whereQuery . ' AS ' . $field);
        $records->where('module', $this->module);

        if (!is_null($value))
          $records->like($whereQuery, '%' . $value . '%');

        $records = $records->getAll();

        $fields = [];
        foreach ($records as $record) {
          if (!empty($record->{$field}) and $record->{$field} !== 'null') {
            $fields[$record->{$field}] = isset($fields[$record->{$field}]) !== false ? ($fields[$record->{$field}] + 1) : 1;
          }
        }
        // for convenience, we sort by number of records.
        arsort($fields);

        foreach ($fields as $_val => $_count) {
          $html .= '<option value="' . $_val . '">';
        }

        $arguments['manipulation'] = [
          '#content' . ($id ? 'Edit' : 'Add') . ' #content_' . ($id ? 'edit' : 'add') . '_' . $field . $lang . '_DataList' => [
            'html'  => $html
          ]
        ];
      } else {

        $alerts[] = [
          'status' => 'error',
          'message' => Helper::lang('error.missing_or_incorrect_parameter')
        ];
      }
    } else {

      $alerts[] = [
        'status' => 'error',
        'message' => Helper::lang('error.module_not_found')
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

  public function extractWidgetData($moduleName, $moduleInputs, $ids)
  {

    $externalSelectColumns = ['id'];
    $moduleContents = [];

    $ids = str_replace(['[', ']'], '', $ids);

    $model = new ContentsModel;

    foreach ($moduleInputs as $selectCol => $colAttributes) {

      if ($selectCol === 'widget') {

        foreach ($colAttributes as $moduleName => $moduleDetails) {
          $externalSelectColumns[] = '(JSON_UNQUOTE(JSON_EXTRACT(input, \'$.' . $moduleName . '\'))) AS ' . $moduleName;
        }
      } else {

        $multilanguage = false;
        if (isset($colAttributes['multilanguage']) !== false and $colAttributes['multilanguage']) {
          $multilanguage = true;
        }

        if (isset($colAttributes['type']) !== false and $colAttributes['type'] === 'file') {
          $externalSelectColumns[] = '
                    REPLACE(
                        REPLACE(
                            REPLACE(
                                REPLACE(
                                    IFNULL(JSON_EXTRACT(input, \'$.' . $selectCol . ($multilanguage ? '.' . Helper::lang('lang.code') : '') . '\'), ""),
                                    " ",
                                    ""
                                ),
                                "\"",
                                ""
                            ),
                            "]",
                            ""
                        ),
                        "[",
                        ""
                    ) AS ' . $selectCol;
          $externalSelectColumns[] = '(SELECT JSON_ARRAYAGG(files) AS files FROM files WHERE FIND_IN_SET(id, ' . $selectCol . ')) AS ' . $selectCol . '_src';
        } else {
          $externalSelectColumns[] = 'JSON_UNQUOTE(JSON_EXTRACT(input, \'$.' . $selectCol . ($multilanguage ? '.' . Helper::lang('lang.code') : '') . '\')) AS '
            . $selectCol;
        }
      }
    }
    $externalSelectColumns = implode(', ', $externalSelectColumns);

    $ids = strpos($ids, ',') !== false ? explode(',', $ids) : [$ids];

    foreach ($ids as $id) {

      $getData = $model->select($externalSelectColumns)
        ->where('module', $moduleName);

      if ($id != 0)
        $getData->where('id', $id);

      $getData = $getData->getAll();

      // Extract files as object
      $getData = $this->fillFileLinks($getData);

      $moduleContents = array_merge($moduleContents, $getData);
    }

    return $moduleContents;
  }

  public function extractContentData()
  {

    $return = null;

    foreach ($this->modules as $moduleKey => $moduleDetail) {

      $extractType = isset($this->get('request')->attributes) !== false ? 'detail' : 'listing';

      if (
        isset($moduleDetail['routes'][$extractType][$this->get()->lang]) !== false
      ) {
        $attributes = isset($this->get('request')->attributes) !== false ? $this->get('request')->attributes : [];
        $details = $moduleDetail['routes'][$extractType][$this->get()->lang];
        $route = trim($details[1], '/');

        if ($route === $this->get()->endpoint) {

          $this->module = $moduleKey;
          $selectColumns = [];
          $externalColumns = [];
          $relationalFields = [];
          $selectColumns[] = 'id';
          $selectColumns[] = 'created_at';
          foreach ($moduleDetail['inputs'] as $selectCol => $colAttributes) {

            if ($selectCol === 'widget') {

              foreach ($colAttributes as $moduleName => $colDetails) {
                $externalColumns[] = $moduleName;
                $selectColumns[] = '(JSON_UNQUOTE(JSON_EXTRACT(input, \'$.' . $moduleName . '\'))) AS ' . $moduleName;
                $selectColumns[] = '(
                                SELECT input FROM contents WHERE id = ' . $moduleName . ') AS ' . $moduleName . '_data';
                $relationalFields[] = $moduleName;
              }
            } else {

              $multilanguage = false;
              if (isset($colAttributes['multilanguage']) !== false and $colAttributes['multilanguage']) {
                $multilanguage = true;
              }

              if (isset($colAttributes['type']) !== false and $colAttributes['type'] === 'file') {
                $selectColumns[] = '
                                REPLACE(
                                    REPLACE(
                                        REPLACE(
                                            REPLACE(
                                                IFNULL(JSON_EXTRACT(input, \'$.' . $selectCol . ($multilanguage ? '.' . Helper::lang('lang.code') : '') . '\'), ""),
                                                " ",
                                                ""
                                            ),
                                            "\"",
                                            ""
                                        ),
                                        "]",
                                        ""
                                    ),
                                    "[",
                                    ""
                                ) AS ' . $selectCol;
                $selectColumns[] = '(SELECT JSON_ARRAYAGG(files) AS files FROM files WHERE FIND_IN_SET(id, ' . $selectCol . ')) AS ' . $selectCol . '_src';
              } else {
                $selectColumns[] = 'JSON_UNQUOTE(JSON_EXTRACT(input, \'$.' . $selectCol . ($multilanguage ? '.' . Helper::lang('lang.code') : '') . '\')) AS '
                  . $selectCol;
              }
            }
          }

          $selectColumns = implode(', ', $selectColumns);

          $model = new ContentsModel;
          $contentDetails = $model->select($selectColumns)
            ->where('module', $this->module);

          foreach ($attributes as $column => $columnVal) {

            if (!in_array($column, $relationalFields)) {

              $multilanguage = false;
              if (isset($moduleDetail['inputs'][$column]['multilanguage']) !== false and $moduleDetail['inputs'][$column]['multilanguage']) {
                $multilanguage = true;
              }
              $contentDetails
                ->where('JSON_UNQUOTE(JSON_EXTRACT(input, \'$.' . $column . ($multilanguage ? '.' . Helper::lang('lang.code') : '') . '\'))', $columnVal);
            }
          }

          $contentDetails = $contentDetails->getAll();

          // Module relations
          if (!empty($contentDetails) and count($relationalFields)) {

            foreach ($relationalFields as $field) {

              foreach ($contentDetails as $contentKey => $contentDetail) {

                if (isset($attributes[$field]) !== false) {

                  if (isset($contentDetail->{$field . '_data'}) !== false) {

                    $contentDetail->{$field . '_data'} = ($fieldAsObj = json_decode($contentDetail->{$field . '_data'}));

                    if (isset(
                      $fieldAsObj->slug->{Helper::lang('lang.code')}
                    ) === false or $fieldAsObj->slug->{Helper::lang('lang.code')} !== $attributes[$field]) {

                      $dynamicUrlParams = $attributes;
                      $dynamicUrlParams[$field] = $fieldAsObj->slug->{Helper::lang('lang.code')};
                      $newUrl = $this->get()->dynamicUrl($details[1], $dynamicUrlParams);

                      $contentDetails = null;
                      break 2;
                    }
                  } else {

                    $contentDetails = null;
                    break 2;
                  }
                }
              }
            }
          }

          $return = [
            'content_details' => null,
            'module_detail' => $moduleDetail,
            'module_key' => $moduleKey,
          ];
          if (!empty($contentDetails)) {
            // Extract files as object
            $contentDetails = $this->fillFileLinks($contentDetails);

            if (count($externalColumns)) {

              foreach ($externalColumns as $module) {

                foreach ($contentDetails as $index => $val) {

                  $moduleContents = [];

                  if (isset($this->modules[$module]['inputs']) !== false) {

                    $widget = $this->extractWidgetData(
                      $module,
                      $this->modules[$module]['inputs'],
                      $val->{$module}
                    );

                    $moduleContents = array_merge($moduleContents, $widget);
                    $contentDetails[$index]->{$module} = $moduleContents;
                  }
                }
              }
            }

            $return['content_details'] = $contentDetails;
          }
          break;
        }
      }
    }
    if (isset($newUrl) === false) {
      $newUrl = null;
    }

    return ! empty($newUrl) ? 
      $newUrl : 
      $return;
  }

  public function fillFileLinks($originalData = [])
  {

    foreach ($originalData as $index => $data) {

      foreach ($data as $col => $var) {

        if (strpos($col, '_src') !== false) {

          $data->{$col} = is_string($data->{$col}) ? json_decode($data->{$col}) : null;
          if (is_array($data->{$col})) {

            foreach ($data->{$col} as $fileIndex => $fileDetail) {
              $fileDetail = is_string($fileDetail) ? json_decode($fileDetail) : (object)[];
              $data->{$col}[$fileIndex] = (object)[];
              foreach ($fileDetail as $fileSize => $fileLink) {
                $data->{$col}[$fileIndex]->{$fileSize} = Helper::base('upload/' . $fileLink);
              }
            }
          }
        }
      }

      $originalData[$index] = $data;
    }
    return $originalData;
  }

  public function contentFileUploader($files, $row, $updateStep = false)
  {

    $manipulation = [];
    $alerts = [];
    $rollBack = [];

    $uploadedFiles = $this->get('request')->files;

    $fileController = new FileController($this->get());

    foreach ($files as $fileName => $fileDetails) {

      extract(Helper::input([
        'current_file_' . $fileName => 'int'
      ], $this->get('request')->params));

      $row[$fileName] = ${'current_file_' . $fileName};

      $requiredFile = false;
      $multipleFile = false;
      $multiLanguage = false;

      if (isset($fileDetails['attributes']['required']) !== false and $fileDetails['attributes']['required']) {
        $requiredFile = true;
      }

      if (isset($fileDetails['attributes']['multiple']) !== false and $fileDetails['attributes']['multiple']) {
        $multipleFile = true;
      }

      if (isset($fileDetails['multilanguage']) !== false and $fileDetails['multilanguage']) {
        $multiLanguage = true;
      }

      if (isset($uploadedFiles[$fileName]) !== false) {

        $uploadParameters = [];

        if (isset($fileDetails['external_parameters']['max_size']) !== false and $fileDetails['external_parameters']['max_size']) {
          $uploadParameters['max_size'] = $fileDetails['external_parameters']['max_size'];
        }

        if (isset($fileDetails['attributes']['accept']) !== false and $fileDetails['attributes']['accept']) {
          $uploadParameters['accept_mime'] = $fileDetails['attributes']['accept'];
        }

        if (isset($fileDetails['external_parameters']['convert']) !== false and $fileDetails['external_parameters']['convert']) {
          $uploadParameters['convert'] = $fileDetails['external_parameters']['convert'];
        }

        if (isset($fileDetails['external_parameters']['size']) !== false and $fileDetails['external_parameters']['size']) {
          $uploadParameters['dimension'] = $fileDetails['external_parameters']['size'];
        }

        if (isset($row['title']) !== false) {
          $newFileName = $row['title'];
        } elseif (isset($rows['name']) !== false) {
          $newFileName = $row['name'];
        }

        $fileLanguages = $multiLanguage ? array_keys($uploadedFiles[$fileName]) : [null];
        foreach ($fileLanguages as $fileLanguage) {

          // file name revision
          if (!is_null($fileLanguage)) {

            $newFileName = (isset($newFileName) === false) ? Helper::tokenGenerator(8) : $newFileName;
            if (is_array($newFileName)) {
              $uploadParameters['name'] = $newFileName[($fileLanguage ? $fileLanguage : Helper::lang('lang.code'))];
            }

            $uploadParameters['name'] = $uploadParameters['name'] . '_' . $fileLanguage;
          }

          $tmpData = !is_null($fileLanguage)
            ? $uploadedFiles[$fileName][$fileLanguage]
            : $uploadedFiles[$fileName];

          if (isset($tmpData['tmp_name']) !== false) {
            $tmpData = [$tmpData];
          }

          $upload = $fileController
            ->directUpload(
              $this->module,
              $tmpData,
              $uploadParameters
            );

          if (count($upload)) {

            foreach ($upload as $uploadId => $uploadDetails) {

              if (isset($row[$fileName]) === false or !is_array($row[$fileName]))
                $row[$fileName] = [];

              if (!is_null($fileLanguage)) {

                if (isset($row[$fileName][$fileLanguage]) === false)
                  $row[$fileName][$fileLanguage] = [];

                $row[$fileName][$fileLanguage][] = $uploadId;
              } else {
                $row[$fileName][] = $uploadId;
              }
              $rollBack[] = $uploadId;

              $alerts[] = [
                'status' => 'success',
                'message' => Helper::lang('base.file_successfully_uploaded') . ' (' . Helper::lang($fileDetails['label']) . (!is_null($fileLanguage) ? ' [' . $fileLanguage . ']' : '') . ')'
              ];
            }
          } else {

            $alerts[] = [
              'status' => 'warning',
              'message' => Helper::lang('base.file_not_uploaded')
                . ' (' . Helper::lang($fileDetails['label']) . (!is_null($fileLanguage) ? ' [' . $fileLanguage . ']' : '') . ')'
            ];

            $manipulation['#content' . ($updateStep ? 'Edit' : 'Add') . ' [name="' . $fileName . (!is_null($fileLanguage) ? '[' . $fileLanguage . ']' : '') . ($multipleFile ? '[]' : '') . '"]'] = [
              'class' => ['is-invalid'],
            ];
          }
        }

        if ($multiLanguage and count($availablaLanguages = Helper::config('app.available_languages')) !== count($fileLanguages)) {

          foreach ($availablaLanguages as $langKey) {
            if (isset($row[$fileName][$langKey]) === false) {
              $alerts[] = [
                'status' => 'warning',
                'message' => Helper::lang('base.file_not_uploaded')
                  . ' (' . Helper::lang($fileDetails['label']) . ' [' . $langKey . '])'
              ];

              $manipulation['#content' . ($updateStep ? 'Edit' : 'Add') . ' [name="' . $fileName . '[' . $langKey . ']' . ($multipleFile ? '[]' : '') . '"]'] = [
                'class' => ['is-invalid'],
              ];
            }
          }
        }
      } elseif ($requiredFile) {

        if ($multiLanguage) {
          foreach (Helper::config('app.available_languages') as $langKey) {
            if (isset($row[$fileName][$langKey]) === false or !count($row[$fileName][$langKey])) {
              $alerts[] = [
                'status' => 'warning',
                'message' => Helper::lang('base.file_not_uploaded') . ' (' . Helper::lang($fileDetails['label']) . ') [' . $langKey . ']'
              ];
              $manipulation['#content' . ($updateStep ? 'Edit' : 'Add') . ' [name="' . $fileName . '[' . $langKey . ']' . ($multipleFile ? '[]' : '') . '"]'] = [
                'class' => ['is-invalid'],
              ];
            }
          }
        } else {

          if (isset($row[$fileName]) === false or !$row[$fileName] or !count($row[$fileName])) {
            $alerts[] = [
              'status' => 'warning',
              'message' => Helper::lang('base.file_not_uploaded') . ' (' . Helper::lang($fileDetails['label']) . ')'
            ];
            $manipulation['#content' . ($updateStep ? 'Edit' : 'Add') . ' [name="' . $fileName . ($multipleFile ? '[]' : '') . '"]'] = [
              'class' => ['is-invalid'],
            ];
          }
        }
      } else {

        $row[$fileName] = (isset($row[$fileName]) !== false or !$row[$fileName]) ? $row[$fileName] : [];
      }
    }

    $return = [];
    $return['row'] = $row;
    if (count($manipulation)) $return['manipulation'] = $manipulation;
    if (count($alerts)) $return['alerts'] = $alerts;

    return $return;
  }

  public function iconPicker()
  {

    $alerts = [];
    $arguments = [];
    extract(Helper::input([
      'id'        => 'nulled_text',
      'value'     => 'nulled_text'
    ], $this->get('request')->params));

    if (!is_null($id)) {

      $iconList = Helper::path('app/Resources/icons.php');
      if (file_exists($iconList)) {

        $iconList = require_once $iconList;

        $viewAll = is_null($value) ? true : false;
        $selectedIcons = [];
        foreach ($iconList as $iconClass => $iconTags) {

          if ($viewAll or (stripos($iconClass, $value) !== false or stripos($iconTags, $value) !== false)) {

            $selectedIcons[] = $iconClass;
          }
        }

        $iconUlList = '';
        foreach ($selectedIcons as $selectedIcon) {
          $iconUlList .= '
                    <li>
                        <a data-kn-action="icon_pick" data-kn-id="' . $id . '" data-kn-icon-class="' . $selectedIcon . '" href="javascript:;"><i class="' . $selectedIcon . '"></i> ' . $selectedIcon . '</a>
                    </li>';
        }

        $arguments['manipulation']['#icon_list_' . $id] = [
          'html'  => $iconUlList,
        ];

        if ($iconUlList === '') {
          $arguments['manipulation']['#icon_list_' . $id]['remove_class'] = ['active'];
        } else {
          $arguments['manipulation']['#icon_list_' . $id]['class'] = ['active'];
        }
      }
    }

    return [
      'status' => true,
      'statusCode' => 200,
      'arguments' => $arguments,
      'alerts' => $alerts,
      'view' => null
    ];
  }

  public function contentListPage()
  {

    $extract = $this->extractContentData();

    if ($extract and is_array($extract)) {

      $arguments = [];
      $title = Helper::lang($extract['module_detail']['name']);
      $arguments['title'] = $title;
      $arguments['detail'] = $extract['content_details'];
      $arguments['moduleDetail'] = $extract['module_detail'];

      if (isset($extract['module_detail']['routes']['description']['listing']) !== false) {
        $arguments['description'] = Helper::lang($extract['module_detail']['routes']['description']['listing']);
      }

      if (isset($extract['content_details']->description) !== false) {
        $arguments['description'] = $extract['content_details']->description;
      }

      $return = [
        'status' => true,
        'statusCode' => 200,
        'arguments' => $arguments,
        'view' => $extract['module_detail']['routes']['view']['listing']
      ];

      $controllerHook = Helper::path('app/Resources/hook.php');
      if (file_exists($controllerHook)) {
        $controllerHook = require_once $controllerHook;
        if (is_object($controllerHook) and $controllerHook = $controllerHook('listing', $this->container, $this, $extract, $return)) {
          $return = $controllerHook;
        }
      }

      return $return;
    } elseif (is_string($extract)) {

      return [
        'status' => false,
        'statusCode' => 301,
        'redirect' => $extract
      ];
    } else {

      return [
        'status' => false,
        'statusCode' => 404,
        'arguments' => [
          'error' => 404,
          'output' => Helper::lang('error.page_not_found')
        ],
        'view' => ['error', 'error']
      ];
    }
  }

  public function contentDetailPage()
  {

    $extract = $this->extractContentData();

    if ($extract and is_array($extract) and isset($extract['content_details'][0]) !== false) {

      $arguments = [];
      $title = Helper::lang($extract['module_detail']['name']);

      $arguments['detail'] = $extract['content_details'][0];

      if (isset($arguments['detail']->{'description'}) !== false) {
        $arguments['description'] = $arguments['detail']->{'description'};
      } elseif (isset($arguments['detail']->{'content'}) !== false) {
        $arguments['description'] = trim(strip_tags(htmlspecialchars_decode($arguments['detail']->{'content'})));
      } elseif (isset($extract['module_detail']['routes']['description']['listing']) !== false) {
        $arguments['description'] = Helper::lang($extract['module_detail']['routes']['description']['listing']);
      }

      if (isset($arguments['detail']->{'title'}) !== false) {
        $title = $arguments['detail']->{'title'} . ' | ' . $title;
      }

      $arguments['title'] = $title;
      $arguments['moduleDetail'] = $extract['module_detail'];

      $return = [
        'status' => true,
        'statusCode' => 200,
        'arguments' => $arguments,
        'view' => $extract['module_detail']['routes']['view']['detail']
      ];

      $controllerHook = Helper::path('app/Resources/hook.php');
      if (file_exists($controllerHook)) {
        $controllerHook = require_once $controllerHook;
        if (is_object($controllerHook) and $controllerHook = $controllerHook('detail', $this->container, $this, $extract, $return)) {
          $return = $controllerHook;
        }
      }

      return $return;
    } elseif (is_string($extract)) {

      return [
        'status' => false,
        'statusCode' => 301,
        'redirect' => $extract
      ];
    } else {

      return [
        'status' => false,
        'statusCode' => 404,
        'arguments' => [
          'error' => 404,
          'output' => Helper::lang('error.page_not_found')
        ],
        'view' => ['error', 'error']
      ];
    }
  }
}
