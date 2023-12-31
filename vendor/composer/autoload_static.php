<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit86ae166e5c371a25b88a2a63dee62263
{
    public static $prefixLengthsPsr4 = array (
        'Y' => 
        array (
            'Yabacon\\' => 8,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Yabacon\\' => 
        array (
            0 => __DIR__ . '/..' . '/yabacon/paystack-php/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit86ae166e5c371a25b88a2a63dee62263::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit86ae166e5c371a25b88a2a63dee62263::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit86ae166e5c371a25b88a2a63dee62263::$classMap;

        }, null, ClassLoader::class);
    }
}
