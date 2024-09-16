<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit6cdbcefc50ff1f2cc27b0a801359946d
{
    public static $files = array (
        '0e6d7bf4a5811bfa5cf40c5ccd6fae6a' => __DIR__ . '/..' . '/symfony/polyfill-mbstring/bootstrap.php',
        'c15d4a1253e33e055d05e547c61dcb71' => __DIR__ . '/..' . '/smarty/smarty/src/functions.php',
    );

    public static $prefixLengthsPsr4 = array (
        'S' => 
        array (
            'Symfony\\Polyfill\\Mbstring\\' => 26,
            'Smarty\\' => 7,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Symfony\\Polyfill\\Mbstring\\' => 
        array (
            0 => __DIR__ . '/..' . '/symfony/polyfill-mbstring',
        ),
        'Smarty\\' => 
        array (
            0 => __DIR__ . '/..' . '/smarty/smarty/src',
        ),
    );

    public static $prefixesPsr0 = array (
        'M' => 
        array (
            'Mustache' => 
            array (
                0 => __DIR__ . '/..' . '/mustache/mustache/src',
            ),
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit6cdbcefc50ff1f2cc27b0a801359946d::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit6cdbcefc50ff1f2cc27b0a801359946d::$prefixDirsPsr4;
            $loader->prefixesPsr0 = ComposerStaticInit6cdbcefc50ff1f2cc27b0a801359946d::$prefixesPsr0;
            $loader->classMap = ComposerStaticInit6cdbcefc50ff1f2cc27b0a801359946d::$classMap;

        }, null, ClassLoader::class);
    }
}
