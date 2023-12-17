<?php

/**
 * @package KX
 * @subpackage Controller\FileController
 */

declare(strict_types=1);

namespace KX\Controller;

use KX\Core\Controller;
use KX\Core\Helper;
use KX\Helper\KalipsoTable;
use KX\Model\Files as FilesModel;
use KX\Model\Users as UsersModel;
use Verot\Upload\Upload;

final class FileController extends Controller
{

  public function medias()
  {


    return [
      'status' => true,
      'statusCode' => 200,
      'arguments' => [
        'title' => Helper::lang('base.users') . ' | ' . Helper::lang('base.media'),
        'description' => Helper::lang('base.media_message'),
        'modules' => $this->modules,
        'forms' => $this->forms,
      ],
      'view' => ['admin.medias', 'admin']
    ];
  }

  public function removeFileWithId($id)
  {

    $model = new FilesModel();
    $getFile = $model->select('files')->where('id', $id)->get();
    if ($getFile and isset($getFile->files)) {

      $files = json_decode($getFile->files);
      foreach ($files as $file) {
        if (file_exists($path = Helper::path('upload/' . $file))) {
          unlink($path);
        }
      }
      $model->where('id', $id)->delete();
    }
  }

  public function mediaList()
  {

    $container = $this->get();

    $tableOp = (new KalipsoTable())
      ->db((new FilesModel)->pdo)
      ->from('(SELECT 
                    x.id, 
                    x.module, 
                    x.mime,
                    x.size, 
                    x.name,
                    x.files,
                    FROM_UNIXTIME(x.created_at, "%Y.%m.%d %H:%i") AS created,
                    IFNULL(FROM_UNIXTIME(x.updated_at, "%Y.%m.%d"), "-") AS updated
                FROM `files` x) AS raw')
      ->process([
        'id' => [
          'primary' => true,
        ],
        'module' => [
          'formatter' => function ($row) {
            return Helper::lang('base.' . $row->module);
          }
        ],
        'name' => [
          'formatter' => function ($row) {
            return $row->name == '' ? '-' : $row->name;
          }
        ],
        'mime' => [],
        'size' => [
          'formatter' => function ($row) {
            return Helper::formatSize($row->size);
          }
        ],
        'files' => [
          'formatter' => function ($row) {
            $return = '';
            if ($row->files and $srcset = @json_decode($row->files)) {
              $return = '<div class="image-group">';

              $hrefDir = 'upload/' . $srcset->original;
              $srcDir = 'upload/' . (isset($srcset->sm) !== false ? $srcset->sm : $srcset->original);
              $href = Helper::base($hrefDir);
              $src = Helper::base($srcDir);
              if (strpos(mime_content_type(Helper::path($srcDir)), 'image') === false) {
                $src = Helper::base('assets/img/KX/file_icon.svg');
              }
              $return .= '<a href="' . $href . '" target="_blank"><img class="table-image" src="' . $src . '" /></a>';

              $return .= '</div>';
            } else {
              $return = '-';
            }
            return $return;
          }
        ],
        'created' => [],
        'updated' => [],
        'action' => [
          'exclude' => true,
          'formatter' => function ($row) use ($container) {

            $buttons = '';
            if ($container->authority('management/media/:id')) {
              $buttons .= '
                            <button type="button" class="btn btn-light" 
                                data-KX-action="' . $this->get()->url('/management/media/' . $row->id) . '">
                                ' . Helper::lang('base.view') . '
                            </button>';
            }

            if ($container->authority('management/media/:id/delete')) {
              $buttons .= '
                            <button type="button" class="btn btn-danger" 
                                data-KX-again="' . Helper::lang('base.are_you_sure') . '" 
                                data-KX-action="' . $this->get()->url('/management/media/' . $row->id . '/delete') . '">
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

  public function mediaDelete()
  {

    $id = (int)$this->get('request')->attributes['id'];

    $alerts = [];
    $arguments = [];

    $model = new FilesModel();

    $getMedia = $model->select('id, files')->where('id', $id)->get();
    if (!empty($getMedia)) {

      $files = json_decode($getMedia->files);
      foreach ($files as $dir) {
        if (file_exists($path = Helper::path('upload/' . $dir)))
          unlink($path);
      }

      $delete = $model->where('id', $getMedia->id)->delete();
      if ($delete) {

        $alerts[] = [
          'status' => 'success',
          'message' => Helper::lang('base.file_successfully_deleted')
        ];
        $arguments['table_reset'] = 'mediasTable';
      } else {

        $alerts[] = [
          'status' => 'error',
          'message' => Helper::lang('base.file_delete_problem')
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
  /*
    public function userDetail() {

        $id = (int)$this->get('request')->attributes['id'];

        $alerts = [];
        $arguments = [];

        $model = new Users();
        $getUser = $model->select('id, u_name, f_name, l_name, email, role_id')->where('id', $id)->get();
        if (! empty($getUser)) {

            $userRoles = (new UserRoles)->select('name, id')->orderBy('name', 'asc')->getAll();
            $options = '';

            foreach ($userRoles as $role) {
                $selected = $role->id == $getUser->role_id ? true : false;
                $options .= '
                <option value="' . $role->id. '"' . ($selected ? ' selected' : '') . '>
                    ' . $role->name . '
                </option>';
            }

            $arguments['modal_open'] = ['#editModal'];
            $arguments['manipulation'] = [
                '#userUpdate' => [
                    'attribute' => ['action' => $this->get()->url('management/users/' . $id . '/update')],
                ],
                '#theUserEmail' => [
                    'attribute' => ['value' => $getUser->email],
                ],
                '#theUserName' => [
                    'attribute' => ['value' => $getUser->u_name],
                ],
                '#thefName' => [
                    'attribute' => $getUser->f_name ? ['value' => $getUser->f_name] : ['value' => ''],
                ],
                '#thelName' => [
                    'attribute' => $getUser->l_name ? ['value' => $getUser->l_name] : ['value' => ''],
                ],
                '#theRoles' => [
                    'html'  => $options
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

    public function userUpdate() {

        extract(Helper::input([
            'email' => 'nulled_text',
            'u_name' => 'nulled_text',
            'f_name' => 'nulled_text',
            'l_name' => 'nulled_text',
            'role_id' => 'nulled_int',
            'password' => 'nulled_password'
        ], $this->get('request')->params));

        $id = (int)$this->get('request')->attributes['id'];

        $alerts = [];
        $arguments = [];

        $model = new Users();
        $getUser = $model->select('id, u_name, f_name, l_name, email, role_id')->where('id', $id)->get();
        if (! empty($getUser)) {
        
            if ($email AND $u_name AND $role_id) {

                $userNameCheck = $model->count('id', 'total')->where('u_name', $u_name)->notWhere('id', $id)->get();
                if ((int)$userNameCheck->total === 0) {

                    $userEmailCheck = $model->count('id', 'total')->where('email', $email)->notWhere('id', $id)->get();
                    if ((int)$userEmailCheck->total === 0) {

                        $update = [
                            'email' => $email,
                            'u_name' => $u_name,
                            'f_name' => $f_name,
                            'l_name' => $l_name,
                            'role_id' => $role_id,
                        ];

                        if ($password) {
                            $update['password'] = $password;
                        }

                        $update = $model->where('id', $id)->update($update);

                        if ($update) {

                            if ($getUser->role_id !== $role_id) {
                                (new Sessions)->where('user_id', $id)->update([
                                    'role_id' => $role_id,
                                    'update_session' => 'true'
                                ]);
                            }

                            $alerts[] = [
                                'status' => 'success',
                                'message' => Helper::lang('base.user_successfully_updated')
                            ];
                            $arguments['table_reset'] = 'usersTable';

                        } else {

                            $alerts[] = [
                                'status' => 'error',
                                'message' => Helper::lang('base.user_update_problem')
                            ];
                        }

                    } else {

                        $alerts[] = [
                            'status' => 'warning',
                            'message' => Helper::lang('base.email_is_already_used')
                        ];
                        $arguments['manipulation'] = [
                            '#userAdd [name="email"]' => [
                                'class' => ['is-invalid'],
                            ]
                        ];

                    }

                } else {

                    $alerts[] = [
                        'status' => 'warning',
                        'message' => Helper::lang('base.username_is_already_used')
                    ];
                    $arguments['manipulation'] = [
                        '#userAdd [name="u_name"]' => [
                            'class' => ['is-invalid'],
                        ]
                    ];
                }

            } else {

                $alerts[] = [
                    'status' => 'warning',
                    'message' => Helper::lang('base.form_cannot_empty')
                ];

                $arguments['manipulation'] = [];

                if ($email) {
                    $arguments['manipulation']['#userUpdate [name="email"]'] = [
                        'class' => ['is-invalid'],
                    ];
                }

                if ($u_name) {
                    $arguments['manipulation']['#userUpdate [name="u_name"]'] = [
                        'class' => ['is-invalid'],
                    ];
                }

                if ($role_id) {
                    $arguments['manipulation']['#userUpdate [name="role_id"]'] = [
                        'class' => ['is-invalid'],
                    ];
                }

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
    */

  public function mediaAdd()
  {

    extract(Helper::input([
      'email' => 'nulled_text',
      'u_name' => 'nulled_text',
      'f_name' => 'nulled_text',
      'l_name' => 'nulled_text',
      'role_id' => 'nulled_int',
      'password' => 'nulled_password'
    ], $this->get('request')->params));

    $alerts = [];
    $arguments = [];

    $model = new UsersModel();

    if ($email and $u_name and $role_id and $password) {

      $userNameCheck = $model->count('id', 'total')->where('u_name', $u_name)->get();
      if ((int)$userNameCheck->total === 0) {

        $userEmailCheck = $model->count('id', 'total')->where('email', $email)->get();
        if ((int)$userEmailCheck->total === 0) {

          $insert = [
            'email' => $email,
            'u_name' => $u_name,
            'f_name' => $f_name,
            'l_name' => $l_name,
            'role_id' => $role_id,
            'password' => $password,
            'token' => Helper::tokenGenerator(80),
            'status' => 'active'
          ];

          $insert = $model->insert($insert);

          if ($insert) {

            $alerts[] = [
              'status' => 'success',
              'message' => Helper::lang('base.user_successfully_added')
            ];
            $arguments['form_reset'] = true;
            $arguments['modal_close'] = '#addModal';
            $arguments['table_reset'] = 'usersTable';
          } else {

            $alerts[] = [
              'status' => 'error',
              'message' => Helper::lang('base.user_add_problem')
            ];
          }
        } else {

          $alerts[] = [
            'status' => 'warning',
            'message' => Helper::lang('base.email_is_already_used')
          ];
          $arguments['manipulation'] = [
            '#userAdd [name="email"]' => [
              'class' => ['is-invalid'],
            ]
          ];
        }
      } else {

        $alerts[] = [
          'status' => 'warning',
          'message' => Helper::lang('base.username_is_already_used')
        ];
        $arguments['manipulation'] = [
          '#userAdd [name="u_name"]' => [
            'class' => ['is-invalid'],
          ]
        ];
      }
    } else {

      $alerts[] = [
        'status' => 'warning',
        'message' => Helper::lang('base.form_cannot_empty')
      ];

      $arguments['manipulation'] = [];

      if ($email) {
        $arguments['manipulation']['#userAdd [name="email"]'] = [
          'class' => ['is-invalid'],
        ];
      }

      if ($u_name) {
        $arguments['manipulation']['#userAdd [name="u_name"]'] = [
          'class' => ['is-invalid'],
        ];
      }

      if ($role_id) {
        $arguments['manipulation']['#userAdd [name="role_id"]'] = [
          'class' => ['is-invalid'],
        ];
      }

      if ($password) {
        $arguments['manipulation']['#userAdd [name="password"]'] = [
          'class' => ['is-invalid'],
        ];
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

  public function directUpload($module = 'general', $files = [], $parameters = [])
  {

    if (!is_dir($path = Helper::path('upload')))
      mkdir($path);

    if (!is_dir($path .= '/' . $module))
      mkdir($path);

    $uploadedFiles = [];

    if (count($files)) {

      if (isset($files['tmp_name']) !== false and isset($files['size']) !== false) {
        $files = [...$files];
      }

      foreach ($files as $name => $file) {

        if (isset($file[0]) === false) {
          $file = [$file];
        }

        foreach ($file as $k => $f) {

          if (is_string($k) and is_string($f)) {
            $f = $file;
          }

          $handle = new Upload($f, Helper::lang('lang.iso_code'));
          if ($handle->uploaded) {

            $fileNewName = isset($parameters['name']) !== false
              ? $parameters['name']
              : Helper::stringShortener(Helper::slugGenerator($handle->file_src_name_body), 200, false);

            $fileDimension = ['original' => Helper::config('app.upload_max_dimension')];

            if (isset($parameters['dimension']) !== false) {
              $fileDimension = $parameters['dimension'];
            }

            $fileSize = 0;
            $fileOutput = [];

            foreach ($fileDimension as $dimensionTag => $dimensionVar) {

              $handle->file_new_name_body   = $fileNewName . '_' . $dimensionTag;

              if (isset($parameters['max_size']) !== false) $handle->file_max_size = $parameters['max_size'];
              elseif ($maxSize = Helper::config('app.upload_max_size')) $handle->file_max_size = $maxSize;

              if (isset($parameters['accept_mime']) !== false) $handle->allowed = $parameters['accept_mime'];
              elseif ($acceptMime = Helper::config('app.upload_accept')) $handle->allowed = $acceptMime;

              if (isset($parameters['convert']) !== false) $handle->image_convert = $parameters['convert'];
              elseif ($uploadConvert = Helper::config('app.upload_convert')) $handle->image_convert = $uploadConvert;

              if ($quality = Helper::config('app.upload_webp_quality')) {
                $handle->webp_quality = $quality;
              }

              if ($quality = Helper::config('app.upload_png_quality')) {
                $handle->webp_quality = $quality;
              }

              if ($quality = Helper::config('app.upload_jpeg_quality')) {
                $handle->webp_quality = $quality;
              }

              if ($dimensionVar[0] !== null and $dimensionVar[1] !== null) {

                $handle->image_resize           = true;
                $handle->image_ratio            = true;
                $handle->image_ratio_crop       = true;

                $handle->image_resize           = true;
                $handle->image_ratio            = true;
                $handle->image_ratio_crop       = true;

                if ($dimensionVar[0])
                  $handle->image_x      = $dimensionVar[0];
                else
                  $handle->image_ratio_x = true;

                if ($dimensionVar[1])
                  $handle->image_y      = $dimensionVar[1];
                else
                  $handle->image_ratio_y = true;
              }

              $handle->process($path);
              if ($handle->processed) {

                $url = $module . '/' . $handle->file_dst_name_body . '.' . $handle->file_dst_name_ext;
                $fileOutput[$dimensionTag] = $url;
                $fileSize += filesize($handle->file_dst_pathname);
              }
            }

            if ($fileSize) {

              $insert = (new FilesModel)->insert([
                'module' => $module,
                'size' => $fileSize,
                'mime' => $handle->file_dst_name_ext,
                'name' => $fileNewName,
                'files' => json_encode($fileOutput)
              ]);

              if ($insert) {
                $uploadedFiles[$insert] = $fileOutput;
              }
            }

            $handle->clean();
          }
        }
      }
    }

    return $uploadedFiles;
  }

  public function getFilesInId($ids = [])
  {

    return (new FilesModel)->select('id, name, files')
      ->in('id', $ids)
      ->getAll();
  }
}
