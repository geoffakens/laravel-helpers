<?php
namespace Akens\Laravel\Hashing;

use Illuminate\Hashing\HasherInterface;

/**
 * A simple MD5 hasher.
 *
 * @package Akens\Laravel\Hashing
 */
class Md5Hasher implements HasherInterface {

    /**
     * Hash the given value.
     *
     * @param  string $value
     * @param  array $options
     * @return string
     */
    public function make($value, array $options = array())
    {
        return md5($value);
    }

    /**
     * Check the given plain value against a hash.
     *
     * @param  string $value
     * @param  string $hashedValue
     * @param  array $options
     * @return bool
     */
    public function check($value, $hashedValue, array $options = array())
    {
        return $this->make($value) === $hashedValue;
    }

    /**
     * Check if the given hash has been hashed using the given options.
     *
     * @param  string $hashedValue
     * @param  array $options
     * @return bool
     */
    public function needsRehash($hashedValue, array $options = array())
    {
        return false;
    }
}