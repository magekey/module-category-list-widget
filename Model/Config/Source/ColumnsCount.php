<?php
/**
 * Copyright © MageKey. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageKey\CategoryListWidget\Model\Config\Source;

class ColumnsCount implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        $options = [];
        
        for ($i = 1; $i <= 12; $i++) {
            $options[] = [
                'value' => $i,
                'label' => $i,
            ];
        }
        
        return $options;
    }
}
