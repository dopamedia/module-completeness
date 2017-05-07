<?php
/**
 * User: Andreas Penz <office@dopa.media>
 * Date: 23.04.17
 */

namespace Dopamedia\ProductCompleteness\Ui\Component\Listing\Columns;

class Completeness extends \Magento\Ui\Component\Listing\Columns\Column
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
            $fieldName = $this->getData('name');
            foreach ($dataSource['data']['items'] as &$item) {
                $item[$fieldName] = sprintf('%s%%', $item[$fieldName]);
            }
        }

        return $dataSource;
    }
}