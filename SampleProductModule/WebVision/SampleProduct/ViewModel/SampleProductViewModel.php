<?php
declare(strict_types=1);

namespace WebVision\SampleProduct\ViewModel;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class SampleProductViewModel implements ArgumentInterface
{
    public function __construct(
        private Registry $registry,
        private ProductRepositoryInterface $productRepository
    ) {}

    public function productHaveSample(?string $sku = null): bool
    {
        $product = $this->getCurrentProduct($sku);

        try {
            /** Getting sample for current product and checking if it's associated with current product */
            $sampleSku = $product->getSku() . '_sample';
            $sampleProduct = $this->productRepository->get($sampleSku);
            $relatedProductIds = $product->getRelatedProductIds();
            return in_array($sampleProduct->getId(), $relatedProductIds, true);
        } catch (NoSuchEntityException $e) {
            return false;
        }
    }

    public function getCurrentProduct(?string $sku = null): ProductInterface
    {
        if ($sku) {
            return $this->productRepository->get($sku);
        }
        return $this->registry->registry('current_product');
    }
}
