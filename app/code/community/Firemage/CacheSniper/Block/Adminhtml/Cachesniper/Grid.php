<?php

class Firemage_CacheSniper_Block_Adminhtml_Cachesniper_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Set defaults
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('cachesniperGrid');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }
}
