<?php namespace Akens\LaravelHelpers\Hashing;

use Illuminate\Contracts\Hashing\Hasher as HasherContract;
use Illuminate\Support\Facades\Config;

/**
 * A hasher that can be used to create hashes using the same algorithms used by CakePHP.
 *
 * @package Akens\Laravel\Hashing
 */
class CakeHasher implements HasherContract {

    /**
     * Check the given plain value against a hash.
     *
     * @param  string $value
     * @param  string $hashedValue
     * @param  array $options
     * @return bool
     */
    public function check($value, $hashedValue, array $options = array()) {
        return $this->make($value, $options) === $hashedValue;
    }

    /**
     * Hash the given value.
     *
     * @param  string $value
     * @param  array $options
     * @return string
     */
    public function make($value, array $options = array()) {
        $salt = Config::get('laravel-helpers::hash.salt');
        $type = Config::get('laravel-helpers::hash.type');

        if (!empty($salt)) {
            $value = $salt . $value;
        }

        $type = strtolower($type);

        if ($type == 'sha1' || $type == null) {
            if (function_exists('sha1')) {
                $return = sha1($value);
                return $return;
            }
            $type = 'sha256';
        }

        if ($type == 'sha256' && function_exists('mhash')) {
            return bin2hex(mhash(MHASH_SHA256, $value));
        }

        return md5($value);
    }

    /**
     * Check if the given hash has been hashed using the given options.
     *
     * @param  string $hashedValue
     * @param  array $options
     * @return bool
     */
    public function needsRehash($hashedValue, array $options = array()) {
        return false;
    }
}