<!DOCTYPE html>
<html lang="<?php echo $Helper::lang('lang.code'); ?>" dir="<?php echo $Helper::lang('lang.dir'); ?>">

<head>
  <title><?php echo $title; ?></title>
  <link href="<?php echo \KX\Core\Helper::assets('libs/tabler/tabler.min.css'); ?>" rel="stylesheet">
  <link href="<?php echo \KX\Core\Helper::assets('libs/tabler-icons/tabler-icons.min.css'); ?>" rel="stylesheet">
  <link href="<?php echo \KX\Core\Helper::assets('css/style.css'); ?>" rel="stylesheet">
  <link href="<?php echo \KX\Core\Helper::assets('css/app.css'); ?>" rel="stylesheet">
</head>

<body <?php if (isset($layout['bodyClass']) !== false) echo 'class="' . $layout['bodyClass'] . '"'; ?>>