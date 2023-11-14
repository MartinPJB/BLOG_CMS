<?php
require_once('vendor/autoload.php');

function init_twig($directory)
{
    $loader = new \Twig\Loader\FilesystemLoader($directory . '/View');

    $twig = new \Twig\Environment($loader, ['debug' => true]);
    $twig->addExtension(new \Twig\Extension\DebugExtension());

    return $twig;
}
