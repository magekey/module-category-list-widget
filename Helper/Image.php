<?php
/**
 * Copyright © MageKey. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageKey\CategoryListWidget\Helper;

use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Image\Adapter\AdapterInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Asset\Repository as AssetRepository;

class Image extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * Module name
     */
    const MODULE_NAME = 'MageKey_CategoryListWidget';

    /**
     * Default placeholder
     */
    const DEFAULT_PLACEHOLDER = 'placeholder.jpg';

    /**
     * Media config node
     */
    const MEDIA_TYPE_CONFIG_NODE = 'images';

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $filesystem;

    /**
     * @var \Magento\Framework\Image\AdapterFactory
     */
    protected $imageFactory;

    /**
     * @var \Magento\Framework\View\ConfigInterface
     */
    protected $viewConfig;

    /**
     * @var \Magento\Framework\Config\View
     */
    protected $configView;

    /**
     * @var AssetRepository
     */
    protected $assetRepo;

    /**
     * @var string
     */
    protected $placeholder;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param StoreManagerInterface $storeManager
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\Framework\Image\AdapterFactory $imageFactory
     * @param \Magento\Framework\View\ConfigInterface $viewConfig
     * @param AssetRepository $assetRepo
     * @param string $placeholder
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        StoreManagerInterface $storeManager,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\Image\AdapterFactory $imageFactory,
        \Magento\Framework\View\ConfigInterface $viewConfig,
        AssetRepository $assetRepo,
        $placeholder = null
    ) {
        $this->storeManager = $storeManager;
        $this->filesystem = $filesystem;
        $this->imageFactory = $imageFactory;
        $this->viewConfig = $viewConfig;
        $this->assetRepo = $assetRepo;
        $this->placeholder = $placeholder ?: self::DEFAULT_PLACEHOLDER;
        parent::__construct($context);
    }

    /**
     * Get image url
     *
     * @param string $path
     * @param string $imageId
     * @param array $attributes
     * @param string $moduleName
     * @return string
     */
    public function getImage($path, $imageId = null, array $attributes = [], $moduleName = self::MODULE_NAME)
    {
        $attributes = $this->mergeAttributes($moduleName, $imageId, $attributes);
        $sourcePath = $this->filesystem
            ->getDirectoryRead(DirectoryList::MEDIA)
            ->getAbsolutePath($path);

        $targetPath = dirname($sourcePath) . '/';
        if (!is_file($sourcePath)) {
            if (is_dir($sourcePath)) {
                $targetPath = rtrim($sourcePath, '/') . '/';
            }
            $sourcePath = $this->getPlaceholderPath();
        }

        if (!is_file($sourcePath)) {
            return '';
        }

        $targetPath = $this->getTargetPath($sourcePath, $targetPath, $attributes);

        if (!is_file($targetPath)) {
            $image = $this->imageFactory->create();
            $image->open($sourcePath);
            $this->setImageAttributes($image, $attributes);
            $image->resize($attributes['width'], $attributes['height']);
            $image->save($targetPath);
        }

        $relativePath = $this->filesystem
            ->getDirectoryRead(DirectoryList::MEDIA)
            ->getRelativePath($targetPath);

        return $this->storeManager
            ->getStore()
            ->getBaseUrl(UrlInterface::URL_TYPE_MEDIA) . $relativePath;
    }

    /**
     * Get media attributes
     *
     * @param string $imageId
     * @param string $moduleName
     * @return array
     */
    public function getMediaAttributes($imageId = null, $moduleName = self::MODULE_NAME)
    {
        return $this->getConfigView()->getMediaAttributes($moduleName, self::MEDIA_TYPE_CONFIG_NODE, $imageId);
    }

    /**
     * Merge attributes
     *
     * @param string $moduleName
     * @param string $imageId
     * @param array $attributes
     * @return array
     */
    protected function mergeAttributes($moduleName, $imageId, array $attributes)
    {
        foreach ($attributes as $key => $val) {
            if (is_null($val)) {
                unset($attributes[$key]);
            }
        }
        return array_merge(
            [
                'type' => '',
                'constrain' => true,
                'transparency' => true,
                'frame' => false,
                'aspect_ratio' => true,
                'width' => null,
                'height' => null
            ],
            $this->getMediaAttributes($imageId, $moduleName),
            $attributes
        );
    }

    /**
     * Retrieve config view
     *
     * @return \Magento\Framework\Config\View
     */
    protected function getConfigView()
    {
        if (!$this->configView) {
            $this->configView = $this->viewConfig->getViewConfig();
        }
        return $this->configView;
    }

    /**
     * Set image attributes
     *
     * @param AdapterInterface $image
     * @param array $attributes
     * @return void
     */
    protected function setImageAttributes(AdapterInterface $image, array $attributes = [])
    {
        $image->keepFrame($attributes['frame']);
        $image->constrainOnly($attributes['constrain']);
        $image->keepAspectRatio($attributes['aspect_ratio']);
        $image->keepTransparency($attributes['transparency']);
        if (!empty($attributes['background'])) {
            $image->backgroundColor($attributes['background']);
        }
    }

    /**
     * Get placeholder
     *
     * @return string
     */
    protected function getPlaceholderPath()
    {
        try {
            $placeholder = $this->assetRepo->createAsset(
                self::MODULE_NAME . '::images/' . $this->placeholder
            );
            return $placeholder->getSourceFile();
        } catch (\Exception $e) {
            return '';
        }
    }

    /**
     * Get target path
     *
     * @param string $sourcePath
     * @param string $targetPath
     * @param array $attributes
     * @return string
     */
    public function getTargetPath($sourcePath, $targetPath, array $attributes)
    {
        $targetPath .= 'cache/'
                    . $attributes['width'] . 'x' . $attributes['height']
                    . '/' . basename($sourcePath);
        return $targetPath;
    }
}
