<?php
/**
 * Copyright © MageKey. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageKey\CategoryListWidget\Model\TemplateHandler;

use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Exception\LocalizedException;

class Pool
{
    /**
     * Fields
     */
    const FIELD_CODE = 'code';

    const FIELD_SOURCE = 'source';

    /**
     * @var ObjectManagerInterface
     */
    protected $sourceFactory;

    /**
     * @var array
     */
    protected $sources;

    /**
     * @var array
     */
    private $_instances = [];

    /**
     * @param ObjectManagerInterface $sourceFactory
     * @param array $sources
     */
    public function __construct(
        ObjectManagerInterface $sourceFactory,
        array $sources = []
    ) {
        $this->sourceFactory = $sourceFactory;
        $this->sources = $this->_initSources($sources);
    }

    /**
     * Init sources
     *
     * @param array $sources
     * @return array
     */
    private function _initSources(array $sources = [])
    {
        $sourcesData = [];
        foreach ($sources as $code => $source) {
            $source[static::FIELD_CODE] = $code;
            $sourcesData[$code] = $source;
        }
        return $sourcesData;
    }

    /**
     * Retrieve sources
     *
     * @param string $value
     * @param string|null $field
     * @return array
     */
    public function getSources($value = null, $field = null)
    {
        if ($value) {
            if (!$field) {
                $field = static::FIELD_CODE;
            }
            $result = [];
            foreach ($this->sources as $code => $source) {
                if (isset($source[$field]) && $source[$field] == $value) {
                    $result[$code] = $source;
                }
            }
            return $result;
        }
        return $this->sources;
    }

    /**
     * Retrieve source
     *
     * @param string $value
     * @param string|null $field
     * @return array|null
     */
    public function getSource($value, $field = null)
    {
        $sources = $this->getSources($value, $field);
        return array_shift($sources);
    }

    /**
     * Retrieve source instance
     *
     * @param string|int $code
     * @return object
     * @throws LocalizedException
     */
    public function get($code)
    {
        if ($source = $this->getSource($code)) {
            $code = $source[static::FIELD_CODE];
            if (!isset($this->_instances[$code])) {
                $sourceClass = isset($source[static::FIELD_SOURCE])
                    ? $source[static::FIELD_SOURCE]
                    : \Magento\Framework\DataObject::class;
                unset($source[static::FIELD_SOURCE]);
                $this->_instances[$code] = $this->sourceFactory->create(
                    $sourceClass,
                    [
                        'data' => $source
                    ]
                );
            }
            return $this->_instances[$code];
        }

        throw new LocalizedException(
            __('Source for code "%1" not found', $code)
        );
    }

    /**
     * Retrieve iterator
     *
     * @return object[]
     */
    public function getIterator()
    {
        foreach ($this->sources as $code => $source) {
            yield $this->get($code);
        }
    }
}
