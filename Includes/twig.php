<?php
require_once('vendor/autoload.php');

function init_twig($directory, $theme)
{
    $loader = new \Twig\Loader\FilesystemLoader("$directory/Themes/$theme/templates");

    // Ajout du dossier public à Twig
    $loader->addPath("$directory/Themes/$theme/public", 'public');

    $twig = new \Twig\Environment($loader, ['debug' => true]);
    $twig->addExtension(new \Twig\Extension\DebugExtension());

    return $twig;
}
