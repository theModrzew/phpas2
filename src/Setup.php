<?php
/**
 *
 *
 */

namespace PhpAS2;

use Exception;

use phpseclib\File\X509 as PslX509;
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

/* Possible $dn values:
    'C'    // Country
    'ST'   // State or Province name
    'L'    // Locality name
    'PC'   // Postal Code / zip code
    'O'    // Organization name
    'OU'   // Organizational Unit name
    'CN'   // Common Name
    'MAIL' // Email address
    '_D'   // Domain that certificate is valid with
*/

    /**
     * @param integer $size      Key size, eg 1024, 2048, 3072... or null for auto
     * @param string  $crypt     Encryption to use (des3, aes128, ...)
     * @param string  $algo      Algorithm to use (sha1, sha256, ...)
     * @param string  $password  Your password for key and certificate
     * @param string  $path      Path where all the files should be placed
     * @param string  $filename  File name without extension
     *
     * @throws Exception Password should have 6 characters or more
     * @throws Exception Error creating private key
     *
     * @return Boolean Success
     */
    public function generateCertificate($size = 1024, $crypt = 'des3', $algo = 'sha1', $password = false, $path, $filename, $dn = [])
    {
        $outfile = rtrim($path, '\\/') . DIRECTORY_SEPARATOR . $filename;

        // @TODO: check for path / file problems!

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

        if ($password !== false) {
            $rsa->setPassword($password);
        }

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

        // save public key to a file
        $bytes = file_put_contents($outfile . '.key', $result['publickey'], LOCK_EX);
        if ($bytes === false || $bytes < 2) {
            $message = parent::log(__CLASS__, 'Error writing public key to "' . $outfile . '.key"');
            throw new Exception($message);
        }

        // save private key to a file
        $bytes = file_put_contents($outfile . '.private.key', $result['privatekey'], LOCK_EX);
        if ($bytes === false || $bytes < 2) {
            $message = parent::log(__CLASS__, 'Error writing private key to "' . $outfile . '.private.key"');
            throw new Exception($message);
        }

        // generate CSR
        $x509 = new PslX509();
        $x509->setPrivateKey($privKey);

        if (!empty($dn['C'])) {
            $x509->setDNProp('id-at-countryName', $dn['C']);
        }
        if (!empty($dn['ST'])) {
            $x509->setDNProp('id-at-stateOrProvinceName', $dn['ST']);
        }
        if (!empty($dn['L'])) {
            $x509->setDNProp('id-at-localityName', $dn['L']);
        }
        if (!empty($dn['PC'])) {
            $x509->setDNProp('id-at-postalCode', $dn['PC']);
        }
        if (!empty($dn['O'])) {
            $x509->setDNProp('id-at-organizationName', $dn['O']);
        }
        if (!empty($dn['OU'])) {
            $x509->setDNProp('id-at-organizationalUnitName', $dn['OU']);
        }
        if (!empty($dn['CN'])) {
            $x509->setDNProp('id-at-commonName', $dn['CN']);
        }
        if (!empty($dn['MAIL'])) {
            $x509->setDNProp('id-emailAddress', $dn['MAIL']);
        }

        if (!empty($dn['_D'])) {
            $x509->setDomain($dn['_D']);
        }

        $csr = $x509->signCSR();
        $outCSR = $x509->saveCSR($csr);
        $bytes = file_put_contents($outfile . '.csr', $outCSR, LOCK_EX);
        if ($bytes === false || $bytes < 2) {
            $message = parent::log(__CLASS__, 'Error writing CSR to "' . $outfile . '.csr"');
            throw new Exception($message);
        }

        // create self-signed certificate
        $subject = new PslX509();
        $subject->setDN($x509->getDN());
        $subject->setPublicKey($pubKey);

        $issuer = new PslX509();
        $issuer->setPrivateKey($privKey);
        $issuer->setDN($x509->getDN());

        $cert = new PslX509();
        $result = $cert->sign($issuer, $x509);
        echo $cert->saveX509($result);

    }


}
