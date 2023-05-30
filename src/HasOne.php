<?php

namespace Asfop\HasOne;
class HasOne
{
    /**
     * 这里只能改版本号
     * @var string
     */
    protected $prefix = 'i:f:user:v1:_';

    protected $baseExpire = 86400;

    private $cache;
    private $db;

    /**
     * @param $cache
     * @param $db
     */
    public function __construct($cache, $db)
    {
        $this->cache = $cache;
        $this->db = $db;
    }

    /**
     * @param $id
     * @param $key
     * @return string
     */
    private function getUnique($id, $key): string
    {
        return $this->prefix . "{$id}:_" . $key;
    }


    /**
     * 获取多个信息
     * @param $ids
     * @param array $attrs
     * @return array
     */
    public function getInfoList($ids, array $attrs = []): array
    {
        //先从缓存中获取数据
        list($dataList, $dbDataList) = $this->getFromByCache($ids, $attrs);

        list($dataList) = $this->getFromByDB($dbDataList, $dataList);

        return $dataList;
    }

    /**
     * 获取单个信息
     * @param int $id
     * @param array $attrs
     * @return array
     */
    public function getInfo(int $id, array $attrs = []): array
    {
        $list = $this->getInfoList([$id], $attrs);

        $dataList = [];
        foreach ($attrs as $attr) {
            $dataList[$attr] = $list[$attr][$id];
        }

        return $dataList;
    }


    private function getFromByCache(array $ids = [], array $attrs = []): array
    {
        $dataList = [];
        $keys = [];
        foreach ($ids as $id) {
            foreach ($attrs as $attr) {
                $keys[] = $this->getUnique($id, $attr);
            }
        }

        $list = $this->cache->many($keys);

        $cacheDataList = [];
        $dbDataList = [];
        foreach ($list as $index => $item) {
            //i:f:user:v1:_用户id:_user
            list(, $id, $attr) = explode(":_", $index);
            // attr =>[id1,id2,id3]
            //需要走db
            if (is_null($item)) {
                $dbDataList[$attr][] = $id;
                continue;
            }
            //从缓存中读取的
            $cacheDataList[$attr][$id] = $item;
        }

        //处理缓存中的数据
        foreach ($cacheDataList as $attr => $attrItems) {
            foreach ($attrItems as $id => $value) {
                $factory = Factory::analysis($attr);
                $factory->setItems([$id => $value]);
                $value = $factory->transforms()->first()->get();
                $dataList[$attr][$id] = $value;
            }
        }

        return [
            $dataList,
            $dbDataList,
        ];
    }

    private function getFromByDB(array $dbDataList, $dataList): array
    {
        $putCacheDataList = [];
        //从db中获取数据
        foreach ($dbDataList as $attr => $ids) {
            $factory = Factory::analysis($attr);
            $factory->setIds($ids);
            $factory->setDb($this->db);
            $factory->getInfoByIds();
            $values = $factory->get();
            foreach ($ids as $id) {
                $dataList[$attr][$id] = $values[$id] ?? [];
                //存储未转换的数据
                $putCacheDataList[$this->getUnique($id, $attr)] = $dataList[$attr][$id];
                //获取转换后的
                $factory->transforms();
                $values = $factory->get();
                $dataList[$attr][$id] = $values[$id] ?? [];
            }

        }

        if (!empty($putCacheDataList)) {
            $this->putAttrCache($putCacheDataList);
        }
        return [$dataList];
    }

    private function putAttrCache($putCacheDataList)
    {
        $this->cache->putMany($putCacheDataList, $this->baseExpire + mt_rand(1, 2 * 60));
    }

}
