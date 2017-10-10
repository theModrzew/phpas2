<?php

namespace pureAS2;

use Defintions;

use phpseclib\Crypt\RSA;

/**
 *
 * @package  pureAS2/Message
 * @access   public
 */
class Message
{

    private $from;
    private $to;

    private $message;
    private $encrypted;
    private $encoded;

    /**
     *
     */
    public function __construct(Partner $from, Partner $to)
    {
        $this->from = $from;
        $this->to   = $to;
    }



    /**
     *
     */
    public function encrypt($openSSLCompat = false)
    {
        $rsa = new Crypt_RSA();

        $rsa->loadKey($from->getPKCS12());

        // set hasing algorithm to use
        $rsa->setHash($from->getAlgorythm());

        // mask generation hash algorithm
        $rsa->setMGFHash();

        // encryption mode
        // CRYPT_RSA_ENCRYPTION_PKCS1 / CRYPT_RSA_ENCRYPTION_OAEP
        $rsa->setEncryptionMode();

        // encrypt

        if ($openSSLCompat === true) {
            if (!defined('CRYPT_RSA_PKCS15_COMPAT')) {
                define('CRYPT_RSA_PKCS15_COMPAT', true);
            }
        }

        $this->encrypted = $rsa->encrypt($this->message);
    }

    /**
     *
     */
    public function decrypt()
    {
        $rsa = new Crypt_RSA();

        // set hasing algorithm to use
        $rsa->loadKey();
        $rsa->setHash();
        $rsa->setEncryptionMode();


    }






    /**
     *
     */
    public function encode()
    {
    }

    /**
     *
     */
    public function decode()
    {
    }



    /**
     *
     */
    public function __toString()
    {
        return $this->message;
    }

    public function __clone()
    {
    }

    public function __sleep()
    {
    }

}
