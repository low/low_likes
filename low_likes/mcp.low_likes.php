<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// include config file
include(PATH_THIRD.'low_likes/config.php');

/**
 * Low Likes Module Control Panel class
 *
 * @package        low_likes
 * @author         Lodewijk Schutte <hi@gotolow.com>
 * @link           http://gotolow.com/addons/low-likes
 * @copyright      Copyright (c) 2012, Low
 */
class Low_likes_mcp {

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

	/**
	 * Shortcut to site id
	 *
	 * @access      private
	 * @var         int
	 */
	private $site_id;

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

		// --------------------------------------
		// Set shortcut
		// --------------------------------------

		$this->site_id = $this->EE->config->item('site_id');
	}

	/**
	 * Module landing page - list of recent likes
	 *
	 * @access     public
	 * @return     string
	 */
	public function index()
	{
		// --------------------------------------
		// Page title
		// --------------------------------------

		$this->EE->cp->set_variable('cp_page_title', lang('low_likes_module_name'));

		// --------------------------------------
		// Retrieve list of likes
		// --------------------------------------

		// Items to get
		$select = array(
			'l.entry_id', 'l.member_id', 'l.like_date',
			't.channel_id', 't.title',
			'm.screen_name'
		);

		// Query DB
		$query = $this->EE->db->select($select)
		       ->from('low_likes l')
		       ->join('channel_titles t', 'l.entry_id = t.entry_id')
		       ->join('members m', 'l.member_id = m.member_id')
		       ->order_by('l.like_date', 'desc')
		       ->limit(50)
		       ->get();

		// Get results
		$likes = $query->result_array();

		// --------------------------------------
		// Modify results
		// --------------------------------------

		$entry_url = BASE.AMP.'C=content_publish'.AMP.'M=entry_form'.AMP.'channel_id=%s'.AMP.'entry_id=%s';
		$member_url = BASE.AMP.'C=myaccount'.AMP.'id=%s';

		foreach ($likes AS &$row)
		{
			$row['like_date']  = $this->EE->localize->set_human_time($row['like_date']);
			$row['entry_url']  = sprintf($entry_url, $row['channel_id'], $row['entry_id']);
			$row['member_url'] = sprintf($member_url, $row['member_id']);
		}

		// --------------------------------------
		// Add likes to result array and feed to view
		// --------------------------------------

		$vars = array('likes' => $likes);

		return $this->EE->load->view('mcp_index', $vars, TRUE);
	}

}

/* End of file mcp.low_likes.php */