<?php
/**
 * Copyright © MageKey. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageKey\CategoryListWidget\Model\TemplateHandler;

use Magento\Catalog\Model\ResourceModel\Category\Collection as CategoryCollection;

class InlineImages extends DefaultList
{
    /**
     * {@inheritdoc}
     */
    public function prepareCollection(CategoryCollection $collection, \Magento\Framework\DataObject $options)
    {
        $attributes = [
            'image'
        ];

        if ($imageAttribute = $options->getData('image_attribute')) {
            $attributes[] = $imageAttribute;
        }

        $collection->addAttributeToSelect(array_unique($attributes));

        parent::prepareCollection($collection, $options);
    }

    /**
     * {@inheritdoc}
     */
    protected function getWrapperType()
    {
        return ['inline-images'];
    }
}
