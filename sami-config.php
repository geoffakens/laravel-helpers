<?php

use Sami\Sami;
use Symfony\Component\Finder\Finder;

$iterator = Finder::create()
    ->files()
    ->name('*.php')
    ->in('src');

return new Sami($iterator, array(
    'title'                => 'Laravel Helpers',
    'build_dir'            => __DIR__.'/.sami',
    'cache_dir'            => __DIR__.'/.sami-cache',
    'default_opened_level' => 2,
));