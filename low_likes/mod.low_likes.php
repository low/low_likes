<?php

use Low\Likes\Service\Like;

/**
 * Low Likes Module class
 *
 * @package        low_likes
 * @author         Lodewijk Schutte <hi@gotolow.com>
 * @link           https://github.com/low/low_likes
 * @copyright      Copyright (c) 2019, Low
 */
class Low_likes extends Low\Likes\Base {

	protected $owner_id;

	public function __construct()
	{
		$this->owner_id = ee()->TMPL->fetch_param('owner_id',
			ee()->session->userdata('member_id'));
	}

	public function total()
	{
		$likes = Like::getAllByOwner($this->owner_id);
		return count($likes);
	}

	public function total_entries()
	{
		$likes = Like::getEntriesByOwner($this->owner_id);
		return count($likes);
	}

	public function total_members()
	{
		$likes = Like::getMembersByOwner($this->owner_id);
		return count($likes);
	}

	public function form()
	{
		// Initiate data array for form creation
		$form = $data = [];
		$pfx  = 'form:';

		foreach (ee()->TMPL->tagparams as $key => $val) {
			if (strpos($key, $pfx) !== 0) continue;
			$form[] = sprintf('%s="%s"', substr($key, strlen($pfx)), htmlspecialchars($val));
		}

		// Define default hidden fields
		$data['hidden_fields'] = ['ACT' => ee()->functions->fetch_action_id('Low_likes', 'toggle_like')];

		$output = ee()->functions->form_declaration($data) . ee()->TMPL->tagdata . '</form>';

		if ($form) {
			$output = str_replace('<form', '<form '.implode(' ', $form), $output);
		}

		// Wrap form around tagdata
		return $output;
	}

	public function entry()
	{
		return $this->content('channel');
	}

	public function member()
	{
		return $this->content('member');
	}

	protected function content($content_type)
	{
		$content_id = ee()->TMPL->fetch_param('id');

		if (!$this->owner_id || !$content_id) {
			ee()->TMPL->log_item('Low Likes: no valid owner ID or content ID given');
			return ee()->TMPL->no_results();
		}

		$method = ($content_type == 'member') ? 'getMembersByOwner' : 'getEntriesByOwner';
		$likes = Like::$method($this->owner_id);

		$vars = [
			'is_liked' => in_array($content_id, $likes)
		];

		return ee()->TMPL->parse_variables_row(ee()->TMPL->tagdata, $vars);
	}



	// --------------------------------------------------------------------

	/**
	 * ACT: (un)like posted entry
	 *
	 * @access     public
	 * @return     void
	 */
	public function toggle_like()
	{
		// --------------------------------------
		// Get entry id from post, member_id from session
		// --------------------------------------

		$owner_id = ee()->session->userdata('member_id');
		$entry_id = ee()->input->post('entry_id') ?: null;
		$member_id = ee()->input->post('member_id') ?: null;

		// --------------------------------------
		// if we have an entry_id and a member_id,
		// add/remove a record to/from low_likes table,
		// depending on whether a record already exists or not
		// --------------------------------------

		if ($owner_id && ($entry_id XOR $member_id))
		{
			// Data to work with
			$data = compact('owner_id', 'entry_id', 'member_id');

			// Liked or not?
			ee()->db->where($data);
			$liked = ee()->db->count_all_results('low_likes');

			if ($liked)
			{
				ee()->db->delete('low_likes', $data);
			}
			else
			{
				$data['like_date'] = ee()->localize->now;
				ee()->db->insert('low_likes', $data);
			}

			// Cater for Ajax requests
			if (AJAX_REQUEST)
			{
				die($liked ? '-1' : '1');
			}

		}

		// --------------------------------------
		// Go back to where you came from
		// --------------------------------------

		ee()->functions->redirect($_SERVER['HTTP_REFERER']);
	}

	// --------------------------------------------------------------------

	/**
	 * Show entries liked by logged in member
	 *
	 * @access     public
	 * @return     string
	 */
	public function entries()
	{
		if (!$this->owner_id) {
			return ee()->TMPL->no_results();
		}

		$entry_ids = Like::getEntriesByOwner($this->owner_id);

		return $this->_channel_entries($entry_ids);
	}

	/**
	 * Get a pipe-separated list of liked Entry IDs
	 *
	 * @access     public
	 * @return     string
	 */
	public function entry_ids()
	{
		if (!$this->owner_id) {
			return 0;
		}

		return ($ids = Like::getEntriesByOwner($this->owner_id))
			? implode('|', $ids)
			: '-1';
	}

	/**
	 * Get a pipe-separated list of liked Member IDs
	 *
	 * @access     public
	 * @return     string
	 */
	public function member_ids()
	{
		if (!$this->owner_id) {
			return 0;
		}

		return ($ids = Like::getMembersByOwner($this->owner_id))
			? implode('|', $ids)
			: '-1';
	}

	// --------------------------------------------------------------------

	/**
	 * Loads the Channel module and runs its entries() method
	 *
	 * @access      private
	 * @return      string
	 */
	private function _channel_entries($entry_ids = array())
	{
		// --------------------------------------
		// Return no results if no entry IDs are given
		// --------------------------------------

		if (empty($entry_ids))
		{
			return ee()->TMPL->no_results();
		}

		// --------------------------------------
		// Make sure the following params are set
		// --------------------------------------

		$params = array(
			'dynamic'  => 'no',
			'paginate' => 'bottom'
		);

		foreach ($params AS $key => $val)
		{
			if ( ! ee()->TMPL->fetch_param($key))
			{
				ee()->TMPL->tagparams[$key] = $val;
			}
		}

		// --------------------------------------
		// Set the fixed_order parameter
		// --------------------------------------

		$param = ee()->TMPL->fetch_param('orderby')
			? 'entry_id' : 'fixed_order';

		ee()->TMPL->tagparams[$param] = implode('|', $entry_ids);

		// --------------------------------------
		// Get the Channel module if it doesn't exist yet
		// --------------------------------------

		if ( ! class_exists('channel'))
		{
			require_once PATH_MOD.'channel/mod.channel.php';
		}

		// --------------------------------------
		// Create new Channel instance and call entries() method
		// --------------------------------------

		$channel = new Channel;

		return $channel->entries();
	}

} // End Class

/* End of file mod.low_likes.php */