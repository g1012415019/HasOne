<?php

namespace Asfop\HasOne\attribute;

use Asfop\HasOne\Cache;
use Asfop\HasOne\contract\AttrInterface;
use Asfop\HasOne\DB;

class Base implements AttrInterface
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
     * 数据库操作类
     * @var DB
     */
    private $db;
    /**
     * 缓存操作类
     * @var Cache
     */
    private $cache;

    /**
     * 属性值
     * @return string
     */
    public static function getNames(): string
    {
        return "info";
    }

    /**
     * 设置数据
     * @param mixed $items
     */
    public function setItems($items)
    {
        $this->items = $items;
    }

    /**
     * @return array
     */
    private function getIds()
    {
        return $this->ids;
    }

    /**
     * @param mixed $ids
     */
    public function setIds($ids)
    {
        $this->ids = $ids;
    }

    /**
     * @param mixed $db
     */
    public function setDb($db)
    {
        $this->db = $db;
    }

    /**
     * @return mixed
     */
    public function getCache()
    {
        return $this->cache;
    }

    /**
     * 需要查询的字段
     * @return mixed
     */
    public function getField(): array
    {
        return [
            "*"
        ];
    }

    /**
     * @inheritDoc
     */
    public function getInfoByIds(): AttrInterface
    {
        $this->items = $this->db->get('member', ['*'], "id", $this->getIds());
        return $this;
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

        $item = [];
        $item['id'] = $data['id'];
        $item['nick'] = $data['nick'];
        $item['mobile'] = $data['mobile'];
        $item['password'] = $data['password'];
        return $item;
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
    public function get()
    {
        return $this->items;
    }
}
