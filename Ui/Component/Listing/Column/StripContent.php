<?php
/**
 *
 * Created By Hidro Le
 * Date: 2/25/26
 *
 */

namespace Beehexa\HexaSync\Ui\Component\Listing\Column;

class StripContent extends \Magento\Ui\Component\Listing\Columns\Column
{

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                try {
                    $item[$this->getData('name')] = strlen($item[$this->getData('name')]) > 150 ? substr($item[$this->getData('name')], 0, 150) . '...': '';
                } catch (\Exception $exception) {
                    //Displaying payment code (with no changes) if payment method is not available in system
                }
            }
        }

        return $dataSource;
    }
}
