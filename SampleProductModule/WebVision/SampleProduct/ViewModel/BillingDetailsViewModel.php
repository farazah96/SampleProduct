<?php
declare(strict_types=1);

namespace WebVision\SampleProduct\ViewModel;

use Magento\Customer\Api\AddressRepositoryInterface;
use Magento\Customer\Model\Session;
use Magento\Directory\Model\Country;
use Magento\Framework\Stdlib\CookieManagerInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class BillingDetailsViewModel implements ArgumentInterface
{
    private const COOKIE_NAME = 'customer_billing_details';

    public function __construct(
        private AddressRepositoryInterface $addressRepository,
        private CookieManagerInterface $cookieManager,
        private Session $customerSession,
        private Country $country
    ) {}

    public function getBillingDetails(): array
    {
        $billingDetails = [];

        /** Getting billing details from customer session if logged-in user */
        if ($this->customerSession->isLoggedIn()) {
            $customerData = $this->customerSession->getCustomerData();
            $billingDetails['email'] = $customerData->getEmail();
            $customerAddressId = $customerData->getDefaultBilling();
            if (empty($customerAddressId)) {
                return $billingDetails;
            }

            $customerAddress = $this->addressRepository->getById((int)$customerAddressId);
            $billingDetails['firstname'] = $customerAddress->getFirstname();
            $billingDetails['lastname'] = $customerAddress->getLastname();
            $billingDetails['phone'] = $customerAddress->getTelephone();
            $billingDetails['street'] = $customerAddress->getStreet()[0];
            $billingDetails['city'] = $customerAddress->getCity();
            $billingDetails['postcode'] = $customerAddress->getPostcode();
            $billingDetails['region_id'] = $customerAddress->getRegionId();
            return $billingDetails;
        }

        /** Getting billing details from browser cookie if guest user */
        $data = $this->cookieManager->getCookie(self::COOKIE_NAME);
        if (empty($data)) {
            return $billingDetails;
        }

        return json_decode($data, true, 512, JSON_THROW_ON_ERROR);
    }

    public function getRegions(): array
    {
        $country = $this->country->loadByCode('US');
        return $country->getRegions()->getItems();
    }
}
