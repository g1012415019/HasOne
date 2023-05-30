<?php

namespace Asfop\HasOne\contract;

use Asfop\HasOne\Cache;
use Asfop\HasOne\DB;

interface AttrInterface
{
    public static function getNames(): string;
    /**
     * 需要查询的字段
     * @return array
     */
    public function getField(): array;

    /**
     * 更加多个id查询数据
     */
    public function getInfoByIds(): AttrInterface;

    /**
     * 获取第一条数据
     */
    public function first(): AttrInterface;

    /**
     * 转换数据
     */
    public function transform($data);

    /**
     * 批量转换数据
     * @return AttrInterface
     */
    public function transforms(): AttrInterface;

    /**
     * 获取全部的数据
     * @return array
     */
    public function get();
}
