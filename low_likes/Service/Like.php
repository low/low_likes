<?php

namespace Low\Likes\Service;

/**
 * Low Likes Like Service class
 *
 * @package        low_likes
 * @author         Lodewijk Schutte <hi@gotolow.com>
 * @link           http://gotolow.com/
 * @copyright      Copyright (c) Low
 */
class Like
{
    public static function getAllByOwner($id)
    {
        static $cache = [];

        if (!array_key_exists($id, $cache)) {
            $cache[$id] = ee('Model')
                ->get('low_likes:Like')
                ->filter('owner_id', $id)
                ->order('like_date', 'desc')
                ->all();
        }

        return $cache[$id];
    }

    public static function getEntriesByOwner($id)
    {
        return self::pluckByAttr($id, 'entry_id');
    }

    public static function getMembersByOwner($id)
    {
        return self::pluckByAttr($id, 'member_id');
    }

    protected static function pluckByAttr($id, $attr)
    {
        $likes = self::getAllByOwner($id);
        $pluck = $likes->pluck($attr);
        $pluck = array_filter(array_unique($pluck));
        return $pluck;
    }

    // Likes are owner-centric for now.
    //
    // public static function getLikesByEntries($ids)
    // {
    //     static $cache = [];
    //
    //     if ($missing = array_diff($ids, array_keys($cache))) {
    //         $query = ee('Model')
    //             ->get('low_likes:Like')
    //             ->filter('entry_id', 'IN', $missing)
    //             ->all();
    //
    //         foreach ($query as $row) {
    //             $cache[$row->entry_id][] = $row->owner_id;
    //         }
    //
    //         foreach ($missing as $key) {
    //             if (!array_key_exists($key, $cache)) {
    //                 $cache[$key] = [];
    //             }
    //         }
    //     }
    //
    //     return $cache;
    // }
    //
    // public static function getLikesByEntry($id)
    // {
    //     $rows = self::getLikesByEntries([$id]);
    //     return $rows[$id] ?? [];
    // }
    //
    // public static function getLikesByMember($id)
    // {
    //     static $cache = [];
    //
    //     if (!array_key_exists($id, $cache)) {
    //         $cache[$id] = ee('Model')
    //             ->get('low_likes:Like')
    //             ->filter('member_id', $id)
    //             ->all();
    //     }
    //
    //     return $cache[$id];
    // }
}