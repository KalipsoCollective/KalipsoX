<!DOCTYPE html>
<html lang="<?php echo $Helper::lang('lang.code'); ?>" dir="<?php echo $Helper::lang('lang.dir'); ?>">

<head>
    <title><?php echo $title; ?></title>
    <?php
    if (isset($description) !== false) {
        echo '<meta name="description" content="' . $description . '" />' . PHP_EOL;
    }   ?>
    <link href="<?php echo $Helper::assets('libs/tabler/tabler.min.css'); ?>" rel="stylesheet">
    <link href="<?php echo $Helper::assets('libs/nprogress/nprogress.min.css'); ?>" rel="stylesheet">
    <link href="<?php echo $Helper::assets('libs/tabler-icons/tabler-icons.min.css'); ?>" rel="stylesheet">
    <link href="<?php echo $Helper::assets('libs/toastify/toastify.min.css'); ?>" rel="stylesheet">
    <link href="<?php echo $Helper::assets('css/style.css'); ?>" rel="stylesheet">
    <link href="<?php echo $Helper::assets('css/app.css'); ?>" rel="stylesheet">
</head>

<body <?php if (isset($layout['bodyClass']) !== false) echo 'class="' . $layout['bodyClass'] . '"'; ?>>
    <?php if (isset($layout['beforeBody']) !== false) echo $layout['beforeBody']; ?>