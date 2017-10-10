<?php
/**
 * AS2 (Applicability Statement 2)
 * RFC 4130
 *  + support for widely used SHA-2 which is NOT part of original RFC
 *
 * @see http://www.rfc-base.org/txt/rfc-4130.txt
 */

namespace PhpAS2;

use Exception;

use Partner;

/**
 *
 * @package  PhpAS2/AS2
 * @access   public
 */
class AS2
{

    /**
     * @var Partner[] List of added partners
     */
    private $partners = [];

    /**
     * @var string OpenSSL shell command
     */
    private $oSslCmd;
    /**
     * @var string OpenSSL version
     */
    private $oSslVer;

    /**
     *
     */
    public function __construct()
    {
        if (!function_exists('curl_init')) {
            throw new Exception('pureAS2 requires cURL enabled');
        }


    }

    /**
     * Add partner to AS2 pool.
     * AS2 needs partner added to store it's information internally
     *
     * @param Partner $partner Instance of Partner class
     * @throws Exception Partner was already added
     * @return Integer Partner ID
     */
    public function addPartner(Partner $partner)
    {
        $newPartner = $partner->getName();

        foreach ($this->partners as $pid => $pobj) {
            if ($pobj->getName() == $newPartner) {
                throw new Exception('Partner "' . $newPartner . '" already exists');
            }
        }

        $this->partners[] = $partner;
        $keys = array_keys($this->partners);

        return array_pop($keys);
    }

    /**
     * Remove partner from AS2 pool
     *
     * @param Integer|Partner $partner Partner ID or Class
     * @return Boolean Success
     */
    public function removePartner($partner)
    {
        if (is_integer($partner) && isset($this->partners[$partner])) {
            unset($this->partners[$partner]);
            return true;
        }

        if ($partner instanceof Partner) {
            foreach ($this->partners as $pid => $pObject) {
                if ($partner === $pObject) {
                    unset($this->partners[$pid]);
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Check if partner has been added to AS2 pool
     *
     * @param  Integer|Partner $partner Partner ID or Class
     * @return Boolean
     */
    public function hasPartner($partner)
    {
        if (is_integer($partner) && isset($this->partners[$partner])) {
            return true;
        }

        if ($partner instanceof Partner) {
            foreach ($this->partners as $pid => $pObject) {
                if ($partner === $pObject) {
                    return true;
                }
            }
        }

        return false;
    }





    /**
     * We want to return something...
     */
    public function __toString()
    {
        return 'AS2';
    }

    public function __clone()
    {
    }

    public function __sleep()
    {
    }

}
