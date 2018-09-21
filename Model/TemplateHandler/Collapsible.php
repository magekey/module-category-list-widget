<?php
/**
 * Copyright © MageKey. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageKey\CategoryListWidget\Model\TemplateHandler;

class Collapsible extends DefaultList
{
    /**
     * Retrieve wrapper class
     *
     * @return array
     */
    protected function getWrapperType()
    {
        return array_merge(['default'], parent::getWrapperType());
    }
}
