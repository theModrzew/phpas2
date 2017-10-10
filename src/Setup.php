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
        // parent::__construct();
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
     * @param string  $path      Path where all the files should be placed
     * @param string  $filename  File name without extension
     * @param integer $size      Key size, eg 1024, 2048, 3072... or null for auto
     * @param string  $algo      Algorithm to use (sha1, sha256, ...)
     * @param string  $password  Your password for key and certificate
     *
     * @throws Exception Password should have 6 characters or more
     * @throws Exception Error creating keys
     * @throws Exception Error writing to file
     *
     * @return Boolean Success
     */
    public function generateCertificate($path, $filename, $size = 1024, $algo = 'sha1', $password = false, $dn = [])
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

        $algo = strtolower($algo);

        if ($algo === 'sha1') {
            $signatureAlgorithm = 'sha1WithRSAEncryption';
        }

        if (in_array($algo, ['sha2', 'sha256'], true)) {
            $signatureAlgorithm = 'sha256WithRSAEncryption';
        }

        if ($algo === 'sha224') {
            $signatureAlgorithm = 'sha224WithRSAEncryption';
        }
 
        if ($algo === 'sha384') {
            $signatureAlgorithm = 'sha384WithRSAEncryption';
        }

        if ($algo === 'sha512') {
            $signatureAlgorithm = 'sha512WithRSAEncryption';
        }

        if ($algo === 'md2') {
            $signatureAlgorithm = 'md2WithRSAEncryption';
        }

        if ($algo === 'md5') {
            $signatureAlgorithm = 'md5WithRSAEncryption';
        }

        $csr = $x509->signCSR($signatureAlgorithm);
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
        $issuer->setDN($x509->getDN());
        $issuer->setPrivateKey($privKey);

        $cert = new PslX509();
        $result = $cert->sign($issuer, $subject);
        $bytes = file_put_contents($outfile . '.crt', $cert->saveX509($result), LOCK_EX);
        if ($bytes === false || $bytes < 2) {
            $message = parent::log(__CLASS__, 'Error writing certificate to "' . $outfile . '.crt"');
            throw new Exception($message);
        }

        return true;
    }


}
