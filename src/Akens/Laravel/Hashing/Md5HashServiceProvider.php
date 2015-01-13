<?php
namespace Akens\Laravel\Hashing;

use Illuminate\Hashing;

/**
 * A provider for the simple MD5 hasher.
 *
 * @package Akens\Laravel\Hashing
 */
class Md5HashServiceProvider extends HashServiceProvider {

    /**
     * Boots the service provider by binding the MD5 hasher to the 'hash' key.
     */
    public function boot()
    {
        App::bindShared('hash', function()
        {
            return new Md5Hasher;
        });

        parent::boot();
    }

}