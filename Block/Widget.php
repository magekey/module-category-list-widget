<?php
/**
 * Copyright © MageKey. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageKey\CategoryListWidget\Block;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Data\Tree\Node;
use Magento\Widget\Block\BlockInterface;
use Magento\Catalog\Model\ResourceModel\Category\TreeFactory as CategoryTreeFactory;

use MageKey\CategoryListWidget\Model\TemplateHandler\Pool as TemplateHandlerPool;

class Widget extends \Magento\Framework\View\Element\Template implements BlockInterface
{
    /**
     * Default node renderer class
     */
    const DEFAULT_NODE_RENDERER_CLASS = \Magento\Framework\View\Element\Template::class;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var CategoryTreeFactory
     */
    protected $_categoryTreeFactory;

    /**
     * @var TemplateHandlerPool
     */
    protected $_templateHandlerPool;

    /**
     * @var Node
     */
    protected $_categoryNode;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param CategoryTreeFactory $categoryTreeFactory
     * @param TemplateHandlerPool $templateHandlerPool
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        CategoryTreeFactory $categoryTreeFactory,
        TemplateHandlerPool $templateHandlerPool,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        $this->_categoryTreeFactory = $categoryTreeFactory;
        $this->_templateHandlerPool = $templateHandlerPool;
        parent::__construct(
            $context,
            $data
        );
    }

    /**
     * Retrieve current category model object
     *
     * @return \Magento\Catalog\Model\Category
     */
    public function getCurrentCategory()
    {
        if (!$this->hasData('current_category')) {
            $this->setData('current_category', $this->_coreRegistry->registry('current_category'));
        }
        return $this->getData('current_category');
    }

    /**
     * Get value of widgets title parameter
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->getData('title');
    }

    /**
     * Get parent category
     *
     * @return int
     */
    public function getParentCategory()
    {
        if (!$this->hasData('parent_category')) {
            $arr = explode('/', $this->getData('parent_category_path'));
            if (isset($arr[1])) {
                $this->setData('parent_category', (int)$arr[1]);
            } else {
                $currentCategory = $this->getCurrentCategory();
                $this->setData('parent_category', $currentCategory ? $currentCategory->getId() : 0);
            }
        }
        return $this->getData('parent_category');
    }

    /**
     * Get recursion level
     *
     * @return int
     */
    public function getRecursionLevel()
    {
        return (int)$this->getData('recursion_level');
    }

    /**
     * Get display mode
     *
     * @return string
     */
    public function getDisplayMode()
    {
        return $this->getData('display_mode');
    }

    /**
     * Retrieve category node
     *
     * @return Node|false
     */
    public function getCategoryNode()
    {
        if (null === $this->_categoryNode) {
            try {
                $categoryId = $this->getParentCategory();
                if (!$categoryId) {
                    throw new LocalizedException(
                        __('Category not found.')
                    );
                }

                $mode = $this->getDisplayMode();
                $templateHandler = $this->_templateHandlerPool->get($mode);
                $this->setNodeRendererTemplate(
                    $templateHandler->getTemplate()
                );

                $categoryTree = $this->_categoryTreeFactory->create();
                $categoryNode = $categoryTree->loadNode($categoryId);
                if (!$categoryNode->getId()) {
                    throw new LocalizedException(
                        __('Category not found.')
                    );
                }

                $categoryNode->loadChildren($this->getRecursionLevel());
                $categoryTree->addCollectionData(null, true, [], false, false);
                $collection = $categoryTree->getCollection();
                $collection->addAttributeToFilter('is_active', 1);
                if ($templateHandler) {
                    $templateHandler->prepareCollection($collection, $this->toArray());
                }
                $collection->load();

                foreach ($collection as $category) {
                    if ($categoryTree->getNodeById($category->getId())) {
                        $data = array_merge(
                            $category->getData(),
                            [
                                'url' => $category->getUrl(),
                            ],
                            $templateHandler ? $templateHandler->getCategoryData($category) : []
                        );
                        $categoryTree->getNodeById($category->getId())->addData($data);
                    }
                }

                foreach ($categoryTree->getNodes() as $node) {
                    if (!$collection->getItemById($node->getId()) && $node->getParent()) {
                        $categoryTree->removeNode($node);
                    }
                }

                $this->_categoryNode = $categoryNode;
            } catch (\Exception $e) {
                $this->_categoryNode = false;
            }
        }
        return $this->_categoryNode;
    }

    /**
     * Get node renderer class
     *
     * @return string
     */
    public function getNodeRendererClass()
    {
        if (!$this->hasData('node_renderer_class')) {
            $this->setData('node_renderer_class', static::DEFAULT_NODE_RENDERER_CLASS);
        }
        return $this->getData('node_renderer_class');
    }

    /**
     * Get node renderer
     *
     * @return Node
     */
    public function getNodeRenderer()
    {
        return $this->getLayout()->createBlock(
            $this->getNodeRendererClass(),
            '',
            [
                'data' => array_merge(
                    $this->getData(),
                    ['template' => $this->getNodeRendererTemplate()]
                )
            ]
        );
    }

    /**
     * Get cache key informative items
     *
     * @return array
     */
    public function getCacheKeyInfo()
    {
        return array_merge(
            parent::getCacheKeyInfo(),
            [
                $this->getParentCategory(),
                $this->getRecursionLevel(),
                $this->getNodeRendererTemplate(),
            ]
        );
    }
}
