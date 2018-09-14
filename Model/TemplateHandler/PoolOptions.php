<?php
/**
 * Copyright © MageKey. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageKey\CategoryListWidget\Model\TemplateHandler;

class PoolOptions implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var Pool
     */
    protected $pool;

    /**
     * @param Pool $pool
     */
    public function __construct(
        Pool $pool
    ) {
        $this->pool = $pool;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $options = [];
        foreach ($this->pool->getIterator() as $sourceInstance) {
            $options[] = [
                'value' => $sourceInstance->getCode(),
                'label' => $sourceInstance->getTitle(),
            ];
        }

        return $options;
    }
}
