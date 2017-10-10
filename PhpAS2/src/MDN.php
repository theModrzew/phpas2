<?php
/**
 * MDN (message disposition acknowledgement)
 * RFC: "A MDN is a receipt."
 */

namespace pureAS2;

use Message;

/**
 *
 * @package  pureAS2/MDN
 * @access   public
 */
class MDN extends Message
{

    /**
     *
     */
    public function __construct()
    {
    }

    public function getMessage($returnHeaders = false)
    {
    }

    public function send()
    {
        /*
         * For SMTP MDN's return the message with headers
         */
        if (false) {
            return $this->getMessage(true);
        }

    }






    public function __toString()
    {
        return $this->getMessage();
    }

    public function __clone()
    {
    }

    public function __sleep()
    {
    }

}
