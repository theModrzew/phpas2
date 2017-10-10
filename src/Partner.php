<?php

namespace pureAS2;

use Defintions;

/**
 *
 * @package  pureAS2/Partner
 * @access   public
 */
final class Partner
{

    // General settings
    private $local    = false;
    private $name;
    private $url;
    private $port     = 4080;
    private $ipv4;
    private $compress = false;

    // MDN
    private $mdnMode  = MDN_SYNC;
    private $mdnLogin;
    private $mdnPassword;

    // Security
    private $pkcs12;
    private $pkcs12Passwd;
    private $certificate;
    private $encrypt;
    private $signAlgorithm;

    /**
     * @param mixed[] Associative array
     */
    public function __construct(array $partnerData = [])
    {
        if (count($partnerData) > 0) {
            /* set all the oarameters using methods,
             * so input values will be checked
             */

            if (isset($partnerData['name'])) {
                $this->setName($partnerData['name']);
            }

            if (isset($partnerData['certificate'])) {
                $this->setCertificate($partnerData['certificate']);
            }

            if (isset($partnerData['pkcs12'])) {
                $this->setPKCS12($partnerData['pkcs12']);
            }

        }
    }

    /**
     * @return Boolean Success
     */
    public function setName(string $name)
    {
        if (strlen(trim($name)) > 0) {
            $this->name = $name;
            return true;
        }

        return false;
    }

    /**
     * @return String|NULL
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return Boolean Success
     */
    public function setPKCS12($p12File)
    {
        if (file_exists($p12File) && is_file($p12File)) {
            $this->pkcs12 = $p12File;
            return true;
        }

        return false;
    }

    /**
     * @return String|NULL
     */
    public function getPKCS12()
    {
        return $this->pkcs12;
    }

    /**
     * @return Boolean Success
     */
    public function setCertificate($certFile)
    {
        if (file_exists($certFile) && is_file($certFile)) {
            $this->certificate = $certFile;
            return true;
        }

        return false;
    }

    /**
     * @return String|NULL
     */
    public function getCertificate()
    {
        return $this->certificate;
    }






/*
 * TODO: THAT IS TEMPORARY STUFF!

    public function __set(string $name, $value)
    {
        if (property_exists($this, $name)) {
            $this->$name = $value;
        }
    }
 */

    public function __toString()
    {
        return is_string($this->name) ? $this->name : 'Unnamed Partner';
    }

    public function __clone()
    {
    }

    public function __sleep()
    {
    }

}
