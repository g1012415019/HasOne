<?php

namespace Asfop\HasOne\attribute;

use Asfop\HasOne\Cache;
use Asfop\HasOne\contract\AttrInterface;
use Asfop\HasOne\DB;

abstract class Base implements AttrInterface
{
    /**
     * 需要查询的数据
     * @var array
     */
    private $ids;

    /**
     * 查询出来的数据
     * @var array
     */
    private $items;

    /**
     * 缓存操作类
     * @var Cache
     */
    private $cache;

    public abstract static function getNames(): string;

    /**
     * @inheritDoc
     */
    public abstract function getInfoByIds(): array;

    /**
     * 需要查询的字段
     * @return mixed
     */
    public abstract function getField(): array;

    /**
     * 设置数据
     * @param array $items
     */
    public function setItems(array $items)
    {
        $this->items = $items;
        return $this;
    }

    /**
     * @return array
     */
    private function getIds()
    {
        return $this->ids;
    }

    /**
     * @param $ids
     * @return $this
     */
    public function setIds($ids)
    {
        $this->ids = $ids;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCache()
    {
        return $this->cache;
    }

    /**
     * @inheritDoc
     */
    public function first(): AttrInterface
    {
        $this->items = reset($this->items);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function transform($data): array
    {
        if (empty($data)) {
            return [];
        }

        return $data;
    }

    public function transforms(): AttrInterface
    {
        $list = [];
        foreach ($this->items as $id => $item) {
            $list[$item["id"] ?? $id] = $this->transform($item);
        }
        $this->items = $list;
        return $this;
    }

    /**
     * 获取全部的数据
     * @return array
     */
    public function get(): array
    {
        return $this->items;
    }
}
