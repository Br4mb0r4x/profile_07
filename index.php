<?php

$page = $_GET['page'] ?? 'home';

switch ($page) {
    case 'home':
        require 'pages/home.php';
        break;

    case 'interests':
        require 'pages/interests.php';
        break;

    case 'skills':
        require 'pages/skills.php';
        break;

    default:
        require 'pages/not_found.php';
        break;
}