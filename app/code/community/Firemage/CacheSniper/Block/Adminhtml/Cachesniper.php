<?php

class Firemage_CacheSniper_Block_Adminhtml_Cachesniper extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        parent::__construct();

        $this->_controller = 'adminhtml_cachesniper';
        $this->_blockGroup = 'firemage_cachesniper';
        $this->_headerText = Mage::helper('firemage_cachesniper')->__('Cache Sniper');
    }

    public function getActionUrl()
    {
        return $this->getUrl('adminhtml/cachesniper');
    }
}
