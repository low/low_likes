<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// include config file
include(PATH_THIRD.'low_likes/config.php');

/**
 * Low Likes Extension class
 *
 *
 * @author         Lodewijk Schutte <hi@gotolow.com>
 * @link           http://gotolow.com/addons/lo
 * @copyright      Copyright (c) 2009-2012, Low
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

		// --------------------------------------
		// Assign current settings
		// --------------------------------------

		$this->settings = $settings;
	}

	// --------------------------------------------------------------------

	/**
	 * Add like-vars to channel:entries data
	 *
	 * @access      public
	 * @param       object
	 * @param       array
	 * @return      array
	 */
	public function channel_entries_query_result($obj, $entries)
	{
		// -------------------------------------------
		// Get the latest version of $query
		// -------------------------------------------

		if ($this->EE->extensions->last_call !== FALSE)
		{
			$entries = $this->EE->extensions->last_call;
		}

		// -------------------------------------------
		// Is there a low_likes tag here?
		// -------------------------------------------

		if ( ! strpos($this->EE->TMPL->tagdata, 'exp:low_likes:'))
		{
			return $entries;
		}

		// -------------------------------------------
		// Get all entry ids in current query
		// -------------------------------------------

		$entry_ids = array();

		foreach ($entries AS $row)
		{
			$entry_ids[] = $row['entry_id'];
		}

		// -------------------------------------------
		// Query DB for these entry ids
		// -------------------------------------------

		$likes = array();
		$query = $this->EE->db->select('entry_id, member_id')
		       ->from('low_likes')
		       ->where_in('entry_id', $entry_ids)
		       ->get();

		foreach ($query->result() AS $row)
		{
			$likes[$row->entry_id][] = $row->member_id;
		}

		// There could be entries without any likes;
		// Set these to an empty array
		foreach ($entry_ids AS $entry_id)
		{
			if ( ! isset($likes[$entry_id]))
			{
				$likes[$entry_id] = array();
			}
		}

		// -------------------------------------------
		// Set cache
		// -------------------------------------------

		$this->EE->session->set_cache(LOW_LIKES_PACKAGE, 'likes', $likes);

		// -------------------------------------------
		// Return query
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
	}

	/**
	 * Remove likes when entry is deleted
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
	}

	// --------------------------------------------------------------------

} // End Class low_likes_ext

/* End of file ext.low_likes.php */