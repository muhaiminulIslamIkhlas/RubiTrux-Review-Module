<?php

namespace Harriswebworks\feeforeview\Model;

use Magento\Framework\Model\AbstractModel;

class Item extends AbstractModel
{
    protected function _construct()
    {
        $this->_init(\Harriswebworks\feeforeview\Model\ResourceModel\Item::class);
    }
}