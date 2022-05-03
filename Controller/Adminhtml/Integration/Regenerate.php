<?php
/*
 * Copyright © 2022 Beehexa All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Beehexa\HexaSync\Controller\Adminhtml\Integration;

use Beehexa\HexaSync\Api\HexaSyncIntegrationInterface;
use Magento\Backend\App\Action as BackendAction;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Filter\StripTags;

class Regenerate extends BackendAction
{
    /**
     * Authorization level of a basic admin session
     */
    public const ADMIN_RESOURCE = 'Magento_Integration::integrations';

    /**
     * @var HexaSyncIntegrationInterface
     */
    protected $hexaSyncManagement;

    /**
     * @var StripTags
     */
    protected $stripTags;

    /**
     * @param Context                      $context
     * @param HexaSyncIntegrationInterface $hexaSyncManager
     */
    public function __construct(
        Context                      $context,
        HexaSyncIntegrationInterface $hexaSyncManagement,
        StripTags                    $stripTags
    ) {
        parent::__construct($context);
        $this->hexaSyncManagement = $hexaSyncManagement;
        $this->stripTags = $stripTags;
    }

    /**
     * Register store with Hexasync
     *
     * @return ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     */
    public function execute()
    {
        if ($this->getRequest()->isAjax()) {
            $result = [
                'success'      => false,
                'errorMessage' => '',
            ];
            try {
                $this->hexaSyncManagement->generate();
            } catch (AlreadyExistsException $e) {
                $result['errorMessage'] = $e->getMessage();
            } catch (\Exception $e) {
                $message = __($e->getMessage());
                $result['errorMessage'] = $this->stripTags->filter($message);
            }

            /** @var \Magento\Framework\Controller\Result\Json $resultJson */
            $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
            return $resultJson->setData($result);
        } else {
            try {
                $this->hexaSyncManagement->generate();
                $this->messageManager->addSuccessMessage(__("The account was generated successfully."));
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(__($e->getMessage()));
            }

            /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
            $resultRedirect = $this->resultRedirectFactory->create();
            return $resultRedirect->setPath('adminhtml/integration');
        }
    }
}
