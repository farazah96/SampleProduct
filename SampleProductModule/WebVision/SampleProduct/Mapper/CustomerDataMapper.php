<?php
declare(strict_types=1);

namespace WebVision\SampleProduct\Mapper;

use Magento\Customer\Api\AddressRepositoryInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\Data\AddressFactory;
use Magento\Customer\Model\Session;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Stdlib\CookieManagerInterface;
use Magento\Framework\Stdlib\Cookie\CookieMetadataFactory;

class CustomerDataMapper
{
    private const COOKIE_NAME = 'customer_billing_details';
    private const COOKIE_DURATION = 86400;
    private const PRODUCT_SKU = 'product_sku';

    public function __construct(
        private AddressFactory $addressFactory,
        private AddressRepositoryInterface $addressRepository,
        private CustomerRepositoryInterface $customerRepository,
        private CookieManagerInterface $cookieManager,
        private CookieMetadataFactory $cookieMetadataFactory
    ) {}

    public function map(Session $customerSession, RequestInterface $request): void
    {
        $params = $request->getParams();

        if ($customerSession->isLoggedIn()) {
            $customerData = $customerSession->getCustomerData();
            $customerAddressId = $customerData->getDefaultBilling();
            if (!empty($customerAddressId)) {
                $customerAddress = $this->addressRepository->getById((int)$customerAddressId);
            } else {
                $customerAddress = $this->addressFactory->create();
            }

            $customerAddress->setFirstname($params['firstname']);
            $customerAddress->setLastname($params['lastname']);
            $customerAddress->setStreet([$params['street']]);
            $customerAddress->setTelephone($params['telephone']);
            $customerAddress->setCity($params['city']);
            $customerAddress->setPostcode($params['postcode']);
            $customerAddress->setRegionId($params['region_id']);
            $customerAddress->setCountryId($params['country_id']);
            $customerAddress->setCustomerId((int)$customerData->getId());

            $this->addressRepository->save($customerAddress);

            $customerData->setDefaultBilling($customerAddress->getId());
            $customerData->setDefaultShipping($customerAddress->getId());
            $this->customerRepository->save($customerData);
            return;
        }

        unset($params[self::PRODUCT_SKU]);

        $metadata = $this->cookieMetadataFactory
            ->createPublicCookieMetadata()
            ->setDuration(self::COOKIE_DURATION);
        $this->cookieManager->setPublicCookie(
            self::COOKIE_NAME,
            (string)json_encode($params, JSON_THROW_ON_ERROR),
            $metadata
        );
    }
}
