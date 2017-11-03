<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit7666c3e1d86af8eada0bd218b2dab935
{
    public static $prefixLengthsPsr4 = array (
        'P' => 
        array (
            'Picqer\\Barcode\\' => 15,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Picqer\\Barcode\\' => 
        array (
            0 => __DIR__ . '/..' . '/picqer/php-barcode-generator/src',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit7666c3e1d86af8eada0bd218b2dab935::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit7666c3e1d86af8eada0bd218b2dab935::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}
