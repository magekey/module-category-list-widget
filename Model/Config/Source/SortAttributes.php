<?php
/**
 * Copyright © MageKey. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageKey\CategoryListWidget\Model\Config\Source;

class SortAttributes implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var \Magento\Eav\Model\Config
     */
    protected $eavConfig;

    /**
     * @param \Magento\Eav\Model\Config $eavConfig
     */
    public function __construct(
        \Magento\Eav\Model\Config $eavConfig
    ) {
        $this->eavConfig = $eavConfig;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $options = [
            'label' => ' --- Sort by Position --- ',
            'value' => '',
        ];
        
        $attributes = $this->eavConfig->getAttributes(\Magento\Catalog\Model\Category::ENTITY);
        foreach ($attributes as $attribute) {
            if ($attribute->getData('frontend_input') == 'text'
             && in_array($attribute->getData('backend_type'), ['text', 'varchar', 'int'])) {
                $options[] = [
                    'label' => '[' . $attribute->getAttributeCode() . '] ' . $attribute->getFrontendLabel(),
                    'value' => $attribute->getAttributeCode(),
                ];
            }
        }
        
        return $options;
    }
}
