<?php
declare(strict_types=1);

namespace WebVision\SampleProduct\Controller\Submit;

use Exception;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Psr\Log\LoggerInterface;
use WebVision\SampleProduct\Mapper\CustomerDataMapper;
use WebVision\SampleProduct\Service\CreateSampleProductOrderService;
use WebVision\SampleProduct\Validator\CustomerSampleProductOrderValidator;

class Index implements HttpPostActionInterface
{
    public function __construct(
        private LoggerInterface $logger,
        private Session $customerSession,
        private RequestInterface $request,
        private JsonFactory $resultJsonFactory,
        private CustomerDataMapper $customerDataMapper,
        private CreateSampleProductOrderService $createSampleProductOrder,
        private CustomerSampleProductOrderValidator $sampleProductOrderValidator
    ) {}
    public function execute(): Json
    {
        $result = $this->resultJsonFactory->create();

        try {
            $this->customerDataMapper->map($this->customerSession, $this->request);
            if (!$this->sampleProductOrderValidator->validate($this->customerSession, $this->request)) {
                $result->setData(['status' => false, 'message' => 'You are not allowed to order more samples.']);
                return $result;
            }

            $this->createSampleProductOrder->create($this->customerSession, $this->request);
            $result->setData(['status' => true, 'message' => 'Your order has been placed successfully.']);
            return $result;

        } catch (Exception $exception) {
            $this->logger->error('Sample Product Order Error: ' . $exception->getMessage() . $exception->getTraceAsString());
            $result->setData(['status' => false, 'message' => 'Something went wrong, please try again later.']);
            return $result;
        }
    }
}
