<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// include config file
include(PATH_THIRD.'low_likes/config.php');

/**
 * Low Likes Extension class
 *
 * @package        low_likes
 * @author         Lodewijk Schutte <hi@gotolow.com>
 * @link           https://github.com/lodewijk/low_likes
 * @copyright      Copyright (c) 2013, Low
 */
class Low_likes_ext {

	// --------------------------------------------------------------------
	// PROPERTIES
	// --------------------------------------------------------------------

	/**
	 * Name, required for Extensions page
	 *
	 * @access      public
	 * @var         string
	 */
	public $name = LOW_LIKES_NAME;

	/**
	 * Docs URL, required for Extensions page
	 *
	 * @access      public
	 * @var         string
	 */
	public $docs_url = LOW_LIKES_DOCS;

	/**
	 * This version
	 *
	 * @access      public
	 * @var         string
	 */
	public $version = LOW_LIKES_VERSION;

	/**
	 * Do settings exist?
	 *
	 * @var        string	y|n
	 * @access     public
	 */
	public $settings_exist = 'n';

	/**
	 * This add-on's extension settings
	 *
	 * @var        array
	 * @access     public
	 */
	public $settings = array();

	/**
	 * Required by module moves (un)install of extension to module
	 *
	 * @var        array
	 * @access     public
	 */
	public $required_by = array('module');

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
	 * @param      mixed
	 * @return     void
	 */
	public function __construct($settings = array())
	{
		// --------------------------------------
		// Get global object
		// --------------------------------------

		$this->EE =& get_instance();
	}

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

		if ($this->EE->extensions->last_call !== FALSE)
		{
			$entries = $this->EE->extensions->last_call;
		}

		// -------------------------------------------
		// Is there a low_likes tag here?
		// -------------------------------------------

		if ( ! strpos($this->EE->TMPL->tagdata, 'exp:low_likes:show'))
		{
			return $entries;
		}

		// -------------------------------------------
		// Initiate likes for all given entry_ids
		// -------------------------------------------

		$likes = array();

		foreach ($entries AS $row)
		{
			$likes[$row['entry_id']] = array();
		}

		// -------------------------------------------
		// Query DB for these entry_ids
		// -------------------------------------------

		$query = $this->EE->db->select('entry_id, member_id')
		       ->from('low_likes')
		       ->where_in('entry_id', array_keys($likes))
		       ->get();

		foreach ($query->result() AS $row)
		{
			$likes[$row->entry_id][] = $row->member_id;
		}

		// -------------------------------------------
		// Set cache
		// -------------------------------------------

		$this->EE->session->set_cache(LOW_LIKES_PACKAGE, 'likes', $likes);

		// -------------------------------------------
		// Return $entries
		// -------------------------------------------

		return $entries;

	}

	/**
	 * Remove likes when entry is deleted
	 *
	 * @access      public
	 * @param       int
	 * @param       int
	 * @return      void
	 */
	public function delete_entries_loop($entry_id, $channel_id)
	{
		// -------------------------------------------
		// Remove likes with given entry_id
		// -------------------------------------------

		$this->EE->db->where('entry_id', $entry_id);
		$this->EE->db->delete('low_likes');
	}

	/**
	 * Remove likes when member is deleted
	 *
	 * @access      public
	 * @param       array
	 * @return      void
	 */
	public function member_delete($member_ids)
	{
		// -------------------------------------------
		// Remove likes with given member_ids
		// -------------------------------------------

		$this->EE->db->where_in('member_id', $member_ids);
		$this->EE->db->delete('low_likes');
	}

	// --------------------------------------------------------------------

} // End Class low_likes_ext

/* End of file ext.low_likes.php */