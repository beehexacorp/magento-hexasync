<?php
/**
 *
 * Created By Hidro Le
 * Date: 2/25/26
 *
 */

namespace Beehexa\HexaSync\Ui\Component\Listing\Column;

use Beehexa\HexaSync\Helper\Data;

class AppendURL extends \Magento\Ui\Component\Listing\Columns\Column
{
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            $fieldName = $this->getData('name');
            foreach ($dataSource['data']['items'] as & $item) {
                $href = implode(
                    '?',
                    [implode(
                         '/',
                         [
                             Data::HEXASYNC_BASE_URL,
                             'monitoring',
                             'profiles',
                             $item['profile_id'],
                             'jobs'
                         ]
                     ),
                     http_build_query([
                         'type' => 'sync-history',
                         'id'   => $item[$this->getData('name')],
                     ])]
                );
                $item[$fieldName] = '<a target="_blank" alt="Click to open HexaSync page" href="'
                    . $href
                    . '"  onclick="event.stopPropagation();">'
                    . __('View on HexaSync') . '</a>';
            }
        }
        return $dataSource;
    }
}
