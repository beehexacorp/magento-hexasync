<?php
/*
 * Copyright © 2022 Beehexa All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Beehexa\HexaSync\Model;

use Magento\Framework\App\State;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\HTTP\Client\CurlFactory;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Integration\Api\OauthServiceInterface;
use Magento\Integration\Helper\Oauth\Data as OauthHelper;
use Magento\Integration\Model\Integration;
use Magento\Integration\Model\Oauth\TokenFactory;
use Magento\Store\Api\Data\StoreInterface;
use Psr\Log\LoggerInterface;

/**
 * Starts Magento's OAuth 1.0a integration handshake with HexaSync from the configuration page
 *
 * This is the flow Magento runs when an admin activates an integration that has a callback URL and an identity
 * link URL (System > Integrations > Activate), driven from the "Register Store" button instead:
 *
 *  1. Magento posts the consumer credentials and a one-time verifier to the HexaSync callback URL.
 *  2. The admin is sent to the HexaSync identity page in a popup and signs in.
 *  3. HexaSync exchanges the verifier for a request token and then an access token through Magento's
 *     /oauth/token/request and /oauth/token/access endpoints. Magento activates the integration at that point.
 *  4. HexaSync redirects the popup to Magento's success callback, which closes it.
 *
 * Step 1 sends the same fields as core Magento (plus the store code and name), server to server, so the
 * Service Endpoint must be https outside developer mode.
 */
class OauthHandshake
{
    /**
     * Path on the Service Endpoint that receives the consumer credentials (HTTP POST, server to server)
     */
    public const CALLBACK_PATH = '/callback/magento';

    /**
     * Path on the Service Endpoint the admin's browser is sent to for the HexaSync login
     */
    public const IDENTITY_PATH = '/callback/magento/identity';

    /**
     * @var HexaSyncIntegrationManagement
     */
    private HexaSyncIntegrationManagement $integrationManagement;

    /**
     * @var OauthServiceInterface
     */
    private OauthServiceInterface $oauthService;

    /**
     * @var TokenFactory
     */
    private TokenFactory $tokenFactory;

    /**
     * @var OauthHelper
     */
    private OauthHelper $oauthHelper;

    /**
     * @var CurlFactory
     */
    private CurlFactory $curlFactory;

    /**
     * @var DateTime
     */
    private DateTime $dateTime;

    /**
     * @var State
     */
    private State $appState;

    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * @param HexaSyncIntegrationManagement $integrationManagement
     * @param OauthServiceInterface $oauthService
     * @param TokenFactory $tokenFactory
     * @param OauthHelper $oauthHelper
     * @param CurlFactory $curlFactory
     * @param DateTime $dateTime
     * @param State $appState
     * @param LoggerInterface $logger
     */
    public function __construct(
        HexaSyncIntegrationManagement $integrationManagement,
        OauthServiceInterface $oauthService,
        TokenFactory $tokenFactory,
        OauthHelper $oauthHelper,
        CurlFactory $curlFactory,
        DateTime $dateTime,
        State $appState,
        LoggerInterface $logger
    ) {
        $this->integrationManagement = $integrationManagement;
        $this->oauthService = $oauthService;
        $this->tokenFactory = $tokenFactory;
        $this->oauthHelper = $oauthHelper;
        $this->curlFactory = $curlFactory;
        $this->dateTime = $dateTime;
        $this->appState = $appState;
        $this->logger = $logger;
    }

    /**
     * Start the handshake for the given store and return what the browser needs to continue it
     *
     * @param string $serviceEndpoint HexaSync base URL, for example https://app.hexasync.com
     * @param StoreInterface $store store view being registered
     * @return array{identity_link_url: string, oauth_consumer_key: string}
     * @throws LocalizedException
     */
    public function start(string $serviceEndpoint, StoreInterface $store): array
    {
        $serviceEndpoint = $this->normalizeServiceEndpoint($serviceEndpoint);
        $callbackUrl = $serviceEndpoint . self::CALLBACK_PATH;
        $identityLinkUrl = $serviceEndpoint . self::IDENTITY_PATH;

        $integration = $this->integrationManagement->getIntegration();
        if (!$integration->getId()) {
            $integration = $this->integrationManagement->generateIntegration();
        }
        $integration = $this->integrationManagement->prepareForOauthHandshake(
            $integration,
            $callbackUrl,
            $identityLinkUrl
        );

        $consumerKey = $this->postToConsumer($integration, $callbackUrl, $store);

        return [
            Integration::IDENTITY_LINK_URL => $identityLinkUrl,
            'oauth_consumer_key' => $consumerKey,
        ];
    }

    /**
     * Whether a plain http Service Endpoint is acceptable (only while developing)
     *
     * @return bool
     */
    public function isInsecureEndpointAllowed(): bool
    {
        return $this->appState->getMode() === State::MODE_DEVELOPER;
    }

    /**
     * Validate the Service Endpoint and strip trailing slashes
     *
     * @param string $serviceEndpoint
     * @return string
     * @throws LocalizedException
     */
    public function normalizeServiceEndpoint(string $serviceEndpoint): string
    {
        $serviceEndpoint = rtrim(trim($serviceEndpoint), '/');
        // phpcs:ignore Magento2.Functions.DiscouragedFunction
        $parts = $serviceEndpoint !== '' ? parse_url($serviceEndpoint) : false;
        $scheme = is_array($parts) ? strtolower($parts['scheme'] ?? '') : '';
        $allowedSchemes = $this->isInsecureEndpointAllowed() ? ['https', 'http'] : ['https'];
        if (!is_array($parts)
            || empty($parts['host'])
            || !in_array($scheme, $allowedSchemes, true)
            || isset($parts['user'])
            || isset($parts['query'])
            || isset($parts['fragment'])
        ) {
            throw new LocalizedException(
                __('Service Endpoint must be a valid https URL, for example https://app.hexasync.com')
            );
        }
        return $serviceEndpoint;
    }

    /**
     * Send the consumer credentials and a fresh verifier to HexaSync
     *
     * Same fields as \Magento\Integration\Model\OauthService::postToConsumer, plus the store code and name. Done
     * here rather than through core so the HTTP status is checked and the store being configured is used.
     *
     * @param Integration $integration
     * @param string $callbackUrl
     * @param StoreInterface $store
     * @return string consumer key
     * @throws LocalizedException
     */
    private function postToConsumer(Integration $integration, string $callbackUrl, StoreInterface $store): string
    {
        $consumer = $this->oauthService->loadConsumer($integration->getConsumerId());
        if (!$consumer->getId()) {
            throw new LocalizedException(__('The HexaSync integration has no OAuth consumer. Please try again.'));
        }
        // The token exchange is only accepted for a limited time after this moment (oauth/consumer/expiration_period).
        $consumer->setUpdatedAt($this->dateTime->gmtDate());
        $consumer->save();

        $verifier = $this->tokenFactory->create()->createVerifierToken($consumer->getId());

        $client = $this->curlFactory->create();
        $client->setTimeout($this->oauthHelper->getConsumerPostTimeout());
        $client->setOption(CURLOPT_FOLLOWLOCATION, false);
        try {
            $client->post($callbackUrl, [
                'oauth_consumer_key' => $consumer->getKey(),
                'oauth_consumer_secret' => $consumer->getSecret(),
                'store_base_url' => $store->getBaseUrl(),
                'oauth_verifier' => $verifier->getVerifier(),
                'store_code' => $store->getCode(),
                'store_name' => $store->getName(),
            ]);
            $status = (int)$client->getStatus();
        } catch (\Exception $exception) {
            $this->logger->critical($exception);
            throw new LocalizedException(
                __('Could not reach HexaSync at %1. Check the Service Endpoint and try again.', $callbackUrl)
            );
        }
        if ($status < 200 || $status >= 300) {
            throw new LocalizedException(
                __('HexaSync did not accept the registration request (HTTP %1). Please try again later.', $status)
            );
        }

        return (string)$consumer->getKey();
    }
}
