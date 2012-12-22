<?php

class Firemage_CacheSniper_Model_PageCache_Cookie extends Enterprise_PageCache_Model_Cookie
{
    /**
     * Retrieve obscured cookie value
     *
     * NOTE: Do not use this for printing!
     *
     * Uses the current Magento FPC salt to set a cookie value.
     *
     * @param $value
     * @return string
     */
    public function getObscureValue($value)
    {
        return md5($this->_getSalt() . $value);
    }
}