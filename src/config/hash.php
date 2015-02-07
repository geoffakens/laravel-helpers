<?php

return array(

    /*
    |--------------------------------------------------------------------------
    | Hash Salt
    |--------------------------------------------------------------------------
    |
    | The salt to use when making a new hash.  Defaults to no salt.
    |
    */

    'salt' => env('HASH_SALT', ''),

    /*
    |--------------------------------------------------------------------------
    | Hash Type
    |--------------------------------------------------------------------------
    |
    | The algorithm to use when making a new hash.  Defaults to SHA1.  Possible
    | values include SHA1, SHA256, MD5
    |
    */

    'type' => 'SHA1',

);