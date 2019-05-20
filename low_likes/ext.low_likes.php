<?php

use Low\Likes\Service\Like;

/**
 * Low Likes Extension class
 *
 * @package        low_likes
 * @author         Lodewijk Schutte <hi@gotolow.com>
 * @link           https://github.com/low/low_likes
 * @copyright      Copyright (c) 2019, Low
 */
class Low_likes_ext extends Low\Likes\Base {

	/**
	 * Pre-load likes for given entries
	 *
	 * @access      public
	 * @param       object
	 * @param       array
	 * @return      array
	 */
	public function channel_entries_query_result($obj, $entries)
	{
		// -------------------------------------------
		// Get the latest version of $entries
		// -------------------------------------------

		if (ee()->extensions->last_call !== FALSE)
		{
			$entries = ee()->extensions->last_call;
		}

		// -------------------------------------------
		// Is there a low_likes tag here?
		// -------------------------------------------

		if ( ! strpos(ee()->TMPL->tagdata, 'exp:low_likes:show'))
		{
			return $entries;
		}

		// -------------------------------------------
		// Initiate likes for all given entry_ids
		// -------------------------------------------

		if ($id = ee()->session->userdata('member_id')) {
			$likes = Like::getEntriesByOwner($id);
		}

		// -------------------------------------------
		// Return $entries
		// -------------------------------------------

		return $entries;

	}

} // End Class low_likes_ext

/* End of file ext.low_likes.php */