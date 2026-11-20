<?php
/**
 *
 * Created By Hidro Le
 * Date: 2/25/26
 *
 */

namespace Beehexa\HexaSync\Ui\Component\Listing\Column;

use Beehexa\HexaSync\Helper\Data;
use Magento\Framework\Escaper;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;

class AppendURL extends \Magento\Ui\Component\Listing\Columns\Column
{
    /**
     * @var Escaper
     */
    private Escaper $escaper;

    /**
     * @var Data
     */
    private Data $hexasyncHelper;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param Escaper $escaper
     * @param Data $hexasyncHelper
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        Escaper $escaper,
        Data $hexasyncHelper,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->escaper = $escaper;
        $this->hexasyncHelper = $hexasyncHelper;
    }

    /**
     * Turn the HexaSync log detail id into a link to the HexaSync monitoring page
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            $fieldName = $this->getData('name');
            // Follow the configured Service Endpoint so the link opens the same HexaSync the store is registered with.
            $baseUrl = rtrim((string)$this->hexasyncHelper->getServiceEndpoint(), '/') ?: Data::HEXASYNC_BASE_URL;
            foreach ($dataSource['data']['items'] as & $item) {
                $href = $baseUrl
                    . '/monitoring/profiles/' . rawurlencode((string)($item['profile_id'] ?? '')) . '/jobs?'
                    . http_build_query([
                        'type' => 'sync-history',
                        'id' => $item[$fieldName] ?? '',
                    ]);
                $item[$fieldName] = '<a target="_blank" rel="noopener" title="'
                    . $this->escaper->escapeHtmlAttr(__('Click to open HexaSync page'))
                    . '" href="' . $this->escaper->escapeUrl($href)
                    . '" onclick="event.stopPropagation();">'
                    . $this->escaper->escapeHtml(__('View on HexaSync')) . '</a>';
            }
        }
        return $dataSource;
    }
}
