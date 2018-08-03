<?php
/**
 * Copyright © MageKey. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageKey\CategoryListWidget\Block\Adminhtml\Category;

class WidgetChooser extends \Magento\Catalog\Block\Adminhtml\Category\Widget\Chooser
{
    /**
     * Prepare chooser element HTML
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element Form Element
     * @return \Magento\Framework\Data\Form\Element\AbstractElement
     */
    public function prepareElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $uniqId = $this->mathRandom->getUniqueHash($element->getId());
        $sourceUrl = $this->getUrl(
            'catalog/category_widget/chooser',
            ['uniq_id' => $uniqId, 'use_massaction' => false]
        );

        $chooser = $this->getLayout()->createBlock(
            \Magento\Widget\Block\Adminhtml\Widget\Chooser::class
        )->setElement(
            $element
        )->setConfig(
            $this->getConfig()
        )->setFieldsetId(
            $this->getFieldsetId()
        )->setSourceUrl(
            $sourceUrl
        )->setUniqId(
            $uniqId
        );

        if ($element->getValue()) {
            $value = explode('/', $element->getValue());
            $categoryId = false;
            if (isset($value[0]) && isset($value[1]) && $value[0] == 'category') {
                $categoryId = $value[1];
            }
            if ($categoryId) {
                $label = $this->_categoryFactory->create()->load($categoryId)->getName();
                $chooser->setLabel($label);
            }
        }

        $element->setData('after_element_html', $chooser->toHtml());

        $this->_prepareRemoveHtml($element, $uniqId);

        return $element;
    }

    /**
     * Prepare remove element HTML
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element Form Element
     * @param string $uniqId
     * @return void
     */
    protected function _prepareRemoveHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element, $uniqId)
    {
        $elemId = $element->getId();
        $buttons = $this->getConfig('button');

        $removeButton = $this->getLayout()->createBlock(
            \Magento\Backend\Block\Widget\Button::class
        )->setType(
            'button'
        )->setId(
            $elemId . 'remove_control'
        )->setClass(
            'btn-remove'
        )->setLabel(
            isset($buttons['remove']) ? $buttons['remove'] : __('Reset')
        )->setOnclick(
            $elemId . '_clearWidgetChooser()'
        )->setDisabled(
            false
        );

        $chooser = $element->getForm()->getElement('chooser' . $elemId);
        $afterElementHtml = $chooser->getData('after_element_html');
        $afterElementHtml .= $removeButton->toHtml();

        $afterElementHtml .= "<script>
        function {$elemId}_clearWidgetChooser() {
            require(['jquery'], function($){
                $('#{$uniqId}label').html('" . __('Not Selected') . "')
                $('#{$uniqId}value').val('');
            });
        }
        </script>";

        $chooser->setData('after_element_html', $afterElementHtml);
    }
}
