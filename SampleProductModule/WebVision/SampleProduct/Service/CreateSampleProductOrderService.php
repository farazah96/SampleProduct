<?php
declare(strict_types=1);

namespace WebVision\SampleProduct\Service;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Customer\Model\Session;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Model\Quote\Address\RateFactory;
use Magento\Quote\Model\QuoteFactory;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Quote\Model\QuoteManagement;

class CreateSampleProductOrderService
{
    private const PRODUCT_SKU = 'product_sku';

    public function __construct(
        private RateFactory $rateFactory,
        private QuoteFactory $quoteFactory,
        private QuoteManagement $quoteManagement,
        private CartRepositoryInterface $cartRepository,
        private ProductRepositoryInterface $productRepository,
        private StoreManagerInterface $storeManager,
        private OrderRepositoryInterface $orderRepository
    ) {}

    /**
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function create(Session $customerSession, RequestInterface $request): void
    {
        $productSku = $request->getParam(self::PRODUCT_SKU);
        $product = $this->productRepository->get($productSku);

        /** @var Store $store */
        $store = $this->storeManager->getStore();
        $addressData = $this->getAddressData($request);

        /** Creating and assigning store and product to quote */
        $quote = $this->quoteFactory->create();
        $quote->setStore($store);
        $quote->addProduct($product);

        /** Setting customer details to quote */
        $quote->setCustomerIsGuest(true);
        $quote->setCustomerEmail($request->getParam('email'));
        $quote->setCustomerFirstname($request->getParam('firstname'));
        $quote->setCustomerLastname($request->getParam('lastname'));

        /** Setting shipping rates and payment method to quote */
        $shippingRate = $this->rateFactory->create();
        $quote->getBillingAddress()->addData($addressData);
        $quote->getShippingAddress()->addData($addressData);
        $shippingAddress = $quote->getShippingAddress();
        $shippingAddress->setCollectShippingRates(true)
            ->collectShippingRates()
            ->setShippingMethod('flatrate_flatrate');
        $quote->getShippingAddress()->addShippingRate($shippingRate);

        $quote->getPayment()->addData(['method' => 'checkmo']);
        $this->cartRepository->save($quote);

        /** If Customer is logged-in than assign customer to quote */
        if ($customerSession->isLoggedIn()) {
            $quote->setCustomerIsGuest(false);
            $customer = $customerSession->getCustomerData();
            $quote->assignCustomer($customer);
        }

        /** Creating order */
        $order = $this->quoteManagement->submit($quote);
        $order->setEmailSent(0);
        $order->setSampleOrder(true);
        $this->orderRepository->save($order);
    }

    private function getAddressData(RequestInterface $request): array
    {
        $params = $request->getParams();
        unset($params[self::PRODUCT_SKU]);
        return $params;
    }
}
