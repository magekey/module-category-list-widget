<?php
/**
 * Copyright © MageKey. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageKey\CategoryListWidget\Model\TemplateHandler;

use Magento\Catalog\Model\ResourceModel\Category\Collection as CategoryCollection;

class InlineImages extends HandlerAbstract
{
    /**
     * {@inheritdoc}
     */
    public function prepareCollection(CategoryCollection $collection, array $options = [])
    {
        $attributes = [
            'image'
        ];

        if (!empty($options['image_attribute'])) {
            $attributes[] = $options['image_attribute'];
        }

        $collection->addAttributeToSelect(array_unique($attributes));
    }
}
