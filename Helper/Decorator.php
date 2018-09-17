<?php
/**
 * Copyright © MageKey. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageKey\CategoryListWidget\Helper;

use Magento\Framework\Data\Tree\Node;

class Decorator extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * Prefix
     */
    const CLASS_PREFIX = 'clw';

    /**
     * Render attributes
     *
     * @param Node $node
     * @param array $parts = []
     * @return string
     */
    public function renderAttributes(Node $node, array $parts = [])
    {
        $attrs = [
            'id' => $this->renderId($node, reset($parts)),
            'class' => $this->renderClass($node, $parts),
        ];

        $return = '';
        foreach ($attrs as $key => $value) {
            if (!empty($value)) {
                $return .= ' ' . $key . "='" . $value . "'";
            }
        }
        return $return;
    }

    /**
     * Render id
     *
     * @param Node $node
     * @param string|null $part
     * @return string
     */
    public function renderId(Node $node, $part = null)
    {
        $id = self::CLASS_PREFIX;
        if ($part) {
            $id .= '-' .$part;
        }
        $id .= '-' . $node->getId();

        return $id;
    }

    /**
     * Render class
     *
     * @param Node $node
     * @param array $parts
     * @return string
     */
    public function renderClass(Node $node, array $parts = [])
    {
        $classes = [];
        if (!empty($parts)) {
            foreach ($parts as $part) {
                if ($part) {
                    $classes[] = self::CLASS_PREFIX . '-' . $part;
                }
            }
        } else {
            $classes[] = self::CLASS_PREFIX;
        }
        $classes[] = 'level-' . ((int)$node->getClwLevel());
        if ($node->hasChildren()) {
            $classes[] = 'has-children';
        }
        if ($node->getHasCollapsed()) {
            $classes[] = 'has-collapsed';
        }

        return implode(array_unique($classes), ' ');
    }

    /**
     * Render class
     *
     * @param Node $node
     * @return string
     */
    public function renderWrapperClass(Node $node)
    {
        $classes = [];
        $wrapperTypes = $node->getWrapperType();
        if (!empty($wrapperTypes)) {
            foreach ($wrapperTypes as $part) {
                $classes[] = self::CLASS_PREFIX . '-' . $part;
            }
        }

        return implode(array_unique($classes), ' ');
    }
}
