<?php

namespace Low\Likes\Model;

use EllisLab\ExpressionEngine\Service\Model\Model;

/**
 * Low Likes Like Model class
 *
 * @package        low_likes
 * @author         Lodewijk Schutte <hi@gotolow.com>
 * @link           https://github.com/low/low_likes
 * @copyright      Copyright (c) Low
 */
class Like extends Model {

    protected static $_primary_key = 'like_id';
    protected static $_table_name = 'low_likes';

    protected $like_id;
    protected $owner_id;
    protected $entry_id;
    protected $member_id;
    protected $like_date;

    protected static $_relationships = array(
        'Owner' => array(
            'model' => 'ee:Member',
            'type'  => 'BelongsTo',
            'from_key' => 'owner_id',
            'inverse' => array(
                'name' => 'Likes',
                'type' => 'hasMany'
            )
        ),
        'Entry' => array(
            'model' => 'ee:ChannelEntry',
            'type' => 'BelongsTo',
            'inverse' => array(
                'name' => 'LikedBy',
                'type' => 'hasMany'
            )
        ),
        'Member' => array(
            'model' => 'ee:Member',
            'type' => 'BelongsTo',
            'inverse' => array(
                'name' => 'LikedBy',
                'type' => 'hasMany'
            )
        )
    );

    protected static $_typed_columns = array(
        'like_date' => 'timestamp'
    );

    // --------------------------------------------------------------------

} // End class

/* End of file Like.php */