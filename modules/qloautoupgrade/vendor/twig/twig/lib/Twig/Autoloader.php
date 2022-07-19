<?php

/*
 * This file is part of Twig.
 *
 * (c) Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

@trigger_error('The Twig_Autoloader class is deprecated since version 1.21 and will be removed in 2.0. Use Composer instead.', E_USER_DEPRECATED);

/**
 * Autoloads Twig classes.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 *
 * @deprecated since 1.21 and will be removed in 2.0. Use Composer instead. 2.0.
 */
class Twig_Autoloader
{
    /**
     * Registers Twig_Autoloader as an SPL autoloader.
     *
     * @param bool $prepend whether to prepend the autoloader or not
     */
    public static function register($prepend = false)
    {
        @trigger_error('Using Twig_Autoloader is deprecated since version 1.21. Use Composer instead.', E_USER_DEPRECATED);

        spl_autoload_register([__CLASS__, 'autoload'], true, $prepend);
    }

    /**
     * Handles autoloading of classes.
     *
     * @param string $class a class name
     */
    public static function autoload($class)
    {
        if (0 !== strpos($class, 'Twig')) {
            return;
        }

        if (is_file($file = __DIR__.'/../'.str_replace(['_', "\0"], ['/', ''], $class).'.php')) {
            require $file;
        } elseif (is_file($file = __DIR__.'/../../src/'.str_replace(['Twig\\', '\\', "\0"], ['', '/', ''], $class).'.php')) {
            require $file;
        }
    }
}
