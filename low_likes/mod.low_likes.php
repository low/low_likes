<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// include config file
include(PATH_THIRD.'low_likes/config.php');

/**
 * Low Likes Module class
 *
 * @package        low_likes
 * @author         Lodewijk Schutte <hi@gotolow.com>
 * @link           http://gotolow.com/addons/low-likes
 * @copyright      Copyright (c) 2012, Low
 */
class Low_likes {

	// --------------------------------------------------------------------
	// PROPERTIES
	// --------------------------------------------------------------------

	/**
	 * EE Superobject
	 *
	 * @access      private
	 * @var         object
	 */
	private $EE;

	// --------------------------------------------------------------------
	// METHODS
	// --------------------------------------------------------------------

	/**
	 * Constructor
	 *
	 * @access     public
	 * @return     void
	 */
	public function __construct()
	{
		// --------------------------------------
		// Get global object
		// --------------------------------------

		$this->EE =& get_instance();
	}

	// --------------------------------------------------------------------

	/**
	 * Show Likes for given entry, possibly wrapped around a form tag
	 *
	 * @access     public
	 * @return     string
	 */
	public function show()
	{
		// --------------------------------------
		// Initiate return data
		// --------------------------------------

		$tagdata = $this->EE->TMPL->tagdata;

		// --------------------------------------
		// Get entry ID and bail out if it's not there
		// --------------------------------------

		$entry_id = $this->EE->TMPL->fetch_param('entry_id');

		if (empty($entry_id))
		{
			// Make a note of it in the template log
			$this->EE->TMPL->log_item('Low Likes: no entry_id given in Show tag');

			return $tagdata;
		}

		// --------------------------------------
		// Are we showing a form later on?
		// --------------------------------------

		$form = (($this->EE->TMPL->fetch_param('form') == 'yes') &&
				$this->EE->session->userdata('member_id'));

		// --------------------------------------
		// Get all Likes for this entry
		// --------------------------------------

		$likes = array();

		$query = $this->EE->db->select('member_id')
			   ->from('low_likes')
			   ->where('entry_id', $entry_id)
			   ->get();

		foreach ($query->result() AS $row)
		{
			$likes[] = $row->member_id;
		}

		// --------------------------------------
		// Compose variables for tagdata and parse
		// --------------------------------------

		$vars = array(
			'total_likes' => count($likes),
			'is_liked'    => in_array($this->EE->session->userdata('member_id'), $likes),
			'has_form'    => $form
		);

		// Parse the tagdata
		$tagdata = $this->EE->TMPL->parse_variables_row($tagdata, $vars);

		// --------------------------------------
		// If we're showing a form, generate it and wrap around tagdata
		// --------------------------------------

		if ($form)
		{
			// Initiate data array for form creation
			$data = array(
				'id'    => $this->EE->TMPL->fetch_param('form_id'),
				'class' => $this->EE->TMPL->fetch_param('form_class')
			);

			// Define default hidden fields
			$data['hidden_fields'] = array(
				'ACT' => $this->EE->functions->fetch_action_id('Low_likes', 'toggle_like'),
				'EID' => $entry_id
			);

			// Wrap form around tagdata
			$tagdata = $this->EE->functions->form_declaration($data) . $tagdata . '</form>';
		}

		// --------------------------------------
		// Return output: parsed tagdata
		// --------------------------------------

		return $tagdata;

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

		$entry_id = $this->EE->input->post('EID');
		$member_id = $this->EE->session->userdata('member_id');

		// --------------------------------------
		// if we have an entry_id and a member_id,
		// add/remove a record to/from low_likes table,
		// depending on whether a record already exists or not
		// --------------------------------------

		if ($entry_id && $member_id)
		{
			// Data to work with
			$data = array(
				'entry_id'  => $entry_id,
				'member_id' => $member_id
			);

			// Liked or not?
			$this->EE->db->where($data);
			$liked = $this->EE->db->count_all_results('low_likes');

			if ($liked)
			{
				$this->EE->db->delete('low_likes', $data);
			}
			else
			{
				$data['like_date'] = $this->EE->localize->now;
				$this->EE->db->insert('low_likes', $data);
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

		$this->EE->functions->redirect($_SERVER['HTTP_REFERER']);
	}

	// --------------------------------------------------------------------

	/**
	 * Show entries with most likes
	 *
	 * @access     public
	 * @return     string
	 */
	public function popular()
	{
		// --------------------------------------
		// Get entry IDs ordered by count
		// --------------------------------------

        // Query DB
        $query = $this->EE->db->select(array('entry_id', 'COUNT(*) AS num_likes'))
               ->from('low_likes')
               ->group_by('entry_id')
               ->having('num_likes >', '0')
               ->order_by('num_likes', 'desc')
               ->get();

        // Initiate entry_ids array
        $entry_ids = array();

        // Flatten the query results
        foreach ($query->result() AS $row)
        {
            $entry_ids[] = $row->entry_id;
        }

		// --------------------------------------
		// Call channel:entries
		// --------------------------------------

		return $this->_channel_entries($entry_ids);
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
		// --------------------------------------
		// Get member ID
		// --------------------------------------

		$member_id = $this->EE->session->userdata('member_id');

		// --------------------------------------
		// Show no results when no member ID
		// --------------------------------------

		if ( ! $member_id)
		{
			return $this->EE->TMPL->no_results();
		}

		// --------------------------------------
		// Get entry IDs for member
		// --------------------------------------

        // Query DB
        $query = $this->EE->db->select('entry_id')
               ->from('low_likes')
               ->where('member_id', $member_id)
               ->order_by('like_date', 'desc')
               ->get();

        // Initiate entry_ids array
        $entry_ids = array();

        // Flatten the query results
        foreach ($query->result() AS $row)
        {
            $entry_ids[] = $row->entry_id;
        }

		// --------------------------------------
		// Call channel:entries
		// --------------------------------------

		return $this->_channel_entries($entry_ids);
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
    		return $this->EE->TMPL->no_results();
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
            if ( ! $this->EE->TMPL->fetch_param($key))
            {
                $this->EE->TMPL->tagparams[$key] = $val;
            }
        }

		// --------------------------------------
		// Set the fixed_order parameter
		// --------------------------------------

    	$this->EE->TMPL->tagparams['fixed_order'] = implode('|', $entry_ids);

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