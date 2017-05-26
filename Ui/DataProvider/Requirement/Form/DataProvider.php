<?php
/**
 * User: Andreas Penz <office@dopa.media>
 * Date: 26.05.17
 */

namespace Dopamedia\Completeness\Ui\DataProvider\Requirement\Form;

use Magento\Framework\View\Element\UiComponent\DataProvider\DataProviderInterface;

class DataProvider implements DataProviderInterface
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var array
     */
    protected $meta = [];

    /**
     * @var array
     */
    protected $data = [];

    /**
     * DataProvider constructor.
     * @param $name
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        array $meta = [],
        array $data = []
    ) {
        $this->name = $name;
        $this->meta = $meta;
        $this->data = $data;
    }


    /**
     * @inheritDoc
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @inheritDoc
     */
    public function getConfigData()
    {
        return isset($this->data['config']) ? $this->data['config']: [];
    }

    /**
     * @inheritDoc
     */
    public function setConfigData($config)
    {
        $this->data['config'] = $config;
    }

    /**
     * @inheritDoc
     */
    public function getMeta()
    {
        return $this->meta;
    }

    /**
     * @inheritDoc
     */
    public function getFieldMetaInfo($fieldSetName, $fieldName)
    {
        // TODO: Implement getFieldMetaInfo() method.
    }

    /**
     * @inheritDoc
     */
    public function getFieldSetMetaInfo($fieldSetName)
    {
        // TODO: Implement getFieldSetMetaInfo() method.
    }

    /**
     * @inheritDoc
     */
    public function getFieldsMetaInfo($fieldSetName)
    {
        // TODO: Implement getFieldsMetaInfo() method.
    }

    /**
     * @inheritDoc
     */
    public function getPrimaryFieldName()
    {
        // TODO: Implement getPrimaryFieldName() method.
    }

    /**
     * @inheritDoc
     */
    public function getRequestFieldName()
    {
        // TODO: Implement getRequestFieldName() method.
    }

    /**
     * @inheritDoc
     */
    public function getData()
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function addFilter(\Magento\Framework\Api\Filter $filter)
    {
        // TODO: Implement addFilter() method.
    }

    /**
     * @inheritDoc
     */
    public function addOrder($field, $direction)
    {
        // TODO: Implement addOrder() method.
    }

    /**
     * @inheritDoc
     */
    public function setLimit($offset, $size)
    {
        // TODO: Implement setLimit() method.
    }

    /**
     * @inheritDoc
     */
    public function getSearchCriteria()
    {
        // TODO: Implement getSearchCriteria() method.
    }

    /**
     * @inheritDoc
     */
    public function getSearchResult()
    {
        // TODO: Implement getSearchResult() method.
    }


}