<?php
declare(strict_types=1);

namespace WebVision\SampleProduct\Validator;

use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Model\Session;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\App\RequestInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface;

class CustomerSampleProductOrderValidator
{
    public function __construct(
        private SearchCriteriaBuilder $searchCriteriaBuilder,
        private OrderRepositoryInterface $orderRepository
    ) {}

    public function validate(Session $customerSession, RequestInterface $request): bool
    {
        $email = $request->getParam('email');
        $sampleOrderAllowed = 3;

        if ($customerSession->isLoggedIn()) {
            $email = $customerSession->getCustomerData()->getEmail();
            $sampleOrderAllowed = 10;
        }

        $this->searchCriteriaBuilder->addFilter(OrderInterface::CUSTOMER_EMAIL, $email);
        $this->searchCriteriaBuilder->addFilter('sample_order', true);
        $searchCriteria = $this->searchCriteriaBuilder->create();
        $result = $this->orderRepository->getList($searchCriteria);
        return $result->getTotalCount() < $sampleOrderAllowed;
    }
}
