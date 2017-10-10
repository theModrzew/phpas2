<?php
/**
 *
 *
 */

namespace PhpAS2;

use Exception;

use phpseclib\File\X509 as X509;
use phpseclib\Crypt\RSA as PslCryptRSA;

/**
 *
 * @package  PhpAS2/Setup
 * @access   public
 */
class Setup extends AS2
{

    /**
     *
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @param Int    $size      Key size, eg 1024, 2048, 3072... or null for auto
     * @param string $crypt     Encryption to use (des3, aes128, ...)
     * @param string $algo      Algorithm to use (sha1, sha256, ...)
     * @param string $password  Your password for key and certificate
     * @param string $path      Path where all the files should be placed
     * @param string $filename  File name wothout extension
     *
     * @throws Exception Password should have 6 characters or more
     * @throws Exception Error creating private key
     *
     * @return Boolean Success
     */
    public function generateCertificate($size = 1024, $crypt, $algo, $password = false, $path, $filename)
    {

        if ($password !== false) {
            if (empty($password) || strlen(trim($password)) < 6) {
                $message = parent::log(__CLASS__, 'Password must have 6 characters or more');
                throw new Exception($message);
            }
        } else {
            parent::log(__CLASS__, 'Creating keys with no password');
        }

        // create private and public keys

        $privKey = new PslCryptRSA();
        $result = $privKey->createKey($size);
        // make sure key was generated
        if (!isset($result['partialkey']) || $result['partialkey'] !== false) {
            $message = parent::log(__CLASS__, 'Error creating RSA keys');
            throw new Exception($message);
        }
        $privKey->loadKey($result['privatekey']);

        $pubKey = new PslCryptRSA();
        $pubKey->loadKey($result['publickey']);
        $pubKey->setPublicKey();

        // create self-signed certificate


    }

}
