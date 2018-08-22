<?php
/**
 * Copyright © MageKey. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageKey\CategoryListWidget\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\Registry $registry
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\Registry $registry
    ) {
        $this->registry = $registry;
        parent::__construct($context);
    }

    /**
     * Get image template
     *
     * @param string $name
     * @return string
     */
    public function getImageTemplate($name)
    {
        $category = $this->registry->registry('current_category');
        if ($category) {
            if (!$category->getData('is_show_image')) {
                return '';
            }
        }
        return $name;
    }
}
