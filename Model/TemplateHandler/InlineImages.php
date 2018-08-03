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
    public function prepareCollection(CategoryCollection $collection)
    {
        $collection->addAttributeToSelect([
            'image'
        ]);
    }
}
