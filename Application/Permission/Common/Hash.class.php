<?php
namespace Permission\Common;
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017-05-15
 * Time: 10:06
 */
class Hash {
    /**
     * @param string $algo The algorithm (sha256, sha1, whirlpool, etc)
     * @param string $data The data to encode
     * @param string $salt The salt (This should be the same throughout the system probably)
     * @return string The hashed/salted data  ( C('HASH_PASSWORD_KEY') )
     */
    public static function create($algo, $data, $salt) {
        $context = hash_init($algo, HASH_HMAC, $salt);
        hash_update($context, $data);
        return hash_final($context);
    }
}