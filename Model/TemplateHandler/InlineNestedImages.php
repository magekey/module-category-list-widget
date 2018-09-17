<?php
/**
 * Copyright © MageKey. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageKey\CategoryListWidget\Model\TemplateHandler;

use Magento\Catalog\Model\ResourceModel\Category\Collection as CategoryCollection;

class InlineNestedImages extends InlineImages
{
    /**
     * {@inheritdoc}
     */
    protected function getWrapperType()
    {
        return array_merge(['inline-nested-images'], parent::getWrapperType());
    }
}
