<?php
/*
 * Copyright © 2022 Beehexa All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Beehexa\HexaSync\Cron;

use Magento\AdminNotification\Model\Inbox;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\FlagManager;
use Magento\Framework\Filesystem\DirectoryList;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Store\Model\StoreManagerInterface;

class CheckNewLogs
{
    /**
     * @var Inbox
     */
    protected Inbox $inbox;

    /**
     * @var FlagManager
     */
    protected FlagManager $flagManager;

    /**
     * @var ScopeConfigInterface
     */
    protected ScopeConfigInterface $scopeConfig;

    /**
     * @var DirectoryList
     */
    protected DirectoryList $directoryList;

    /**
     * @var TransportBuilder
     */
    protected TransportBuilder $transportBuilder;

    /**
     * @var StateInterface
     */
    protected StateInterface $inlineTranslation;

    /**
     * @var StoreManagerInterface
     */
    protected StoreManagerInterface $storeManager;

    /**
     * @var \Beehexa\HexaSync\Model\ResourceModel\Log\CollectionFactory
     */
    protected $logCollectionFactory;

    /**
     * CheckNewLogs constructor.
     *
     * @param Inbox $inbox
     * @param FlagManager $flagManager
     * @param ScopeConfigInterface $scopeConfig
     * @param DirectoryList $directoryList
     * @param TransportBuilder $transportBuilder
     * @param StateInterface $inlineTranslation
     * @param StoreManagerInterface $storeManager
     * @param \Beehexa\HexaSync\Model\ResourceModel\Log\CollectionFactory $logCollectionFactory
     */
    public function __construct(
        Inbox $inbox,
        FlagManager $flagManager,
        ScopeConfigInterface $scopeConfig,
        DirectoryList $directoryList,
        TransportBuilder $transportBuilder,
        StateInterface $inlineTranslation,
        StoreManagerInterface $storeManager,
        \Beehexa\HexaSync\Model\ResourceModel\Log\CollectionFactory $logCollectionFactory
    ) {
        $this->inbox = $inbox;
        $this->flagManager = $flagManager;
        $this->scopeConfig = $scopeConfig;
        $this->directoryList = $directoryList;
        $this->transportBuilder = $transportBuilder;
        $this->inlineTranslation = $inlineTranslation;
        $this->storeManager = $storeManager;
        $this->logCollectionFactory = $logCollectionFactory;
    }

    /**
     * Execute the cron job to check for new logs
     */
    public function execute()
    {
        $lastCheck = $this->flagManager->getFlagData('hexasync_last_log_check') ?: 0;
        $notifiedItemIds = $this->flagManager->getFlagData('hexasync_notified_item_ids') ?: [];
        $notifiedItemIds = is_array($notifiedItemIds) ? $notifiedItemIds : [];
        $currentTime = time();

        $collection = $this->logCollectionFactory->create();
        $collection->addFieldToFilter('created_at', ['gt' => date('Y-m-d H:i:s', $lastCheck)]);
        $collection->addFieldToFilter('item_id', ['notnull' => true]);
        $collection->addFieldToFilter('item_id', ['neq' => '']);

        $hasNewLogs = $collection->getSize() > 0;

        if ($hasNewLogs) {
            $newItemIds = [];
            foreach ($collection as $log) {
                $itemId = $log->getItemId();
                if ($itemId && !in_array($itemId, $notifiedItemIds)) {
                    $newItemIds[] = $itemId;
                }
            }

            if (!empty($newItemIds)) {
                $this->inbox->addMajor(
                    'New Logs Detected',
                    'New Sync log entries have been added to the system logs. Please check the logs for details.',
                    '',
                    true
                );

                // Send email notifications
                $enableEmails = $this->scopeConfig->getValue('beehexa/notifications/enable_email_notifications');
                $emails = $this->scopeConfig->getValue('beehexa/notifications/notification_emails');
                if ($enableEmails && $emails) {
                    $emailList = array_map('trim', explode(',', $emails));
                    $subject = 'New Logs Detected';
                    $message = 'New log Sync entries have been added to the system logs. Please check the logs for details.';

                    $this->inlineTranslation->suspend();
                    $store = $this->storeManager->getStore();
                    $baseUrl = $store->getBaseUrl();
                    $host = parse_url($baseUrl, PHP_URL_HOST);
                    $storeName = $store->getName();
                    $fromEmail = $this->scopeConfig->getValue('trans_email/ident_general/email') ?: 'noreply@' . $host;
                    $fromName = $this->scopeConfig->getValue('trans_email/ident_general/name') ?: $storeName . ' Admin';
                    $transport = $this->transportBuilder
                        ->setTemplateOptions(['area' => 'adminhtml', 'store' => 0])
                        ->setTemplateVars([
                            'subject' => $subject,
                            'message' => $message
                        ])
                        ->setFrom(['email' => $fromEmail, 'name' => $fromName])
                        ->addTo($emailList)
                        ->setTemplateIdentifier('hexasync_log_notification_template')
                        ->getTransport();
                    $transport->sendMessage();
                    $this->inlineTranslation->resume();
                }

                $this->flagManager->saveFlag('hexasync_last_log_check', $currentTime);
                $notifiedItemIds = array_merge($notifiedItemIds, $newItemIds);
                $this->flagManager->saveFlag('hexasync_notified_item_ids', $notifiedItemIds);
            }
        }
    }
}
