<?php
/*
 * This file is part of the XML Builder Library.
 *
 * (c) Aaron de Mello <https://aaron.de-mello.org/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

error_reporting(E_ALL | E_STRICT);
ini_set('display_errors', 1);
$root = realpath(dirname(dirname(__FILE__)));
$library = "$root/Rest";
$tests = "$root/Tests";
$path = array($library, $tests, get_include_path());
set_include_path(implode(PATH_SEPARATOR, $path));
$vendorFilename = dirname(__FILE__) . '/../vendor/autoload.php';
if (file_exists($vendorFilename)) {
    /* composer install */
    /** @noinspection PhpIncludeInspection */
    require $vendorFilename;
}
unset($root, $library, $tests, $path);