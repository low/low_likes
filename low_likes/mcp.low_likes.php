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

	/**
	 * Shortcut to module URL
	 *
	 * @access      private
	 * @var         string
	 */
	private $mod_url;

	/**
	 * Shortcut to single entry
	 *
	 * @access      private
	 * @var         string
	 */
	private $entry_url;

	/**
	 * Shortcut to single member
	 *
	 * @access      private
	 * @var         string
	 */
	private $member_url;

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
		// Set shortcuts
		// --------------------------------------

		$this->site_id = $this->EE->config->item('site_id');
		$this->mod_url = BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module='.LOW_LIKES_PACKAGE;
		$this->entry_url = $this->mod_url.AMP.'view=entry'.AMP.'id=%s';
		$this->member_url = $this->mod_url.AMP.'view=member'.AMP.'id=%s';
	}

	// --------------------------------------------------------------------

	/**
	 * Module landing page - list of recent likes
	 *
	 * @access     public
	 * @return     string
	 */
	public function index()
	{
		// --------------------------------------
		// What are we viewing?
		// --------------------------------------

		$id = $this->EE->input->get('id');
		$view = $this->EE->input->get('view');

		if ($id && $view == 'entry')
		{
			$this->EE->db->where('l.entry_id', $id, FALSE);
		}
		elseif ($id && $view == 'member')
		{
			$this->EE->db->where('l.member_id', $id, FALSE);
		}
		else
		{
			$view = 'index';
		}

		// --------------------------------------
		// Retrieve list of likes
		// --------------------------------------

		// Items to get
		$select = array(
			'l.entry_id', 'l.member_id', 'l.like_date',
			't.title', 'm.screen_name'
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

		foreach ($likes AS &$row)
		{
			$row['like_date']  = $this->EE->localize->set_human_time($row['like_date']);
			$row['entry_url']  = sprintf($this->entry_url, $row['entry_id']);
			$row['member_url'] = sprintf($this->member_url, $row['member_id']);
		}

		// --------------------------------------
		// Add likes to result array and feed to view
		// --------------------------------------

		$vars = array(
			'view'  => $view,
			'likes' => $likes
		);

		// --------------------------------------
		// Page title
		// --------------------------------------

		switch ($view)
		{
			case 'entry':
				$title = $query->row('title');
				$this->EE->cp->set_breadcrumb($this->mod_url, lang('low_likes_module_name'));
			break;

			case 'member':
				$title = $query->row('screen_name');
				$this->EE->cp->set_breadcrumb($this->mod_url, lang('low_likes_module_name'));
			break;

			default:
				$title = lang('low_likes_module_name');
		}

		$this->EE->cp->set_variable('cp_page_title', $title);
		$this->_nav();

		return $this->EE->load->view('mcp_index', $vars, TRUE);
	}

	// --------------------------------------------------------------------

	/**
	 * List of popular entries
	 *
	 * @access     public
	 * @return     string
	 */
	public function entries()
	{
		// --------------------------------------
		// Retrieve list of entries
		// --------------------------------------

		// Items to get
		$select = array(
			'l.entry_id', 'COUNT(*) AS num_likes',
			't.channel_id', 't.title'
		);

		// Query DB
		$query = $this->EE->db->select($select)
		       ->from('low_likes l')
		       ->join('channel_titles t', 'l.entry_id = t.entry_id')
		       ->group_by('l.entry_id')
		       ->order_by('num_likes', 'desc')
		       ->limit(50)
		       ->get();

		// Get results
		$entries = $query->result_array();

		// --------------------------------------
		// Modify results
		// --------------------------------------

		foreach ($entries AS &$row)
		{
			$row['entry_url'] = sprintf($this->entry_url, $row['entry_id']);
		}

		// --------------------------------------
		// Add likes to result array and feed to view
		// --------------------------------------

		$vars = array('entries' => $entries);

		// --------------------------------------
		// Page title
		// --------------------------------------

		$this->EE->cp->set_variable('cp_page_title', lang('popular_entries'));
		$this->EE->cp->set_breadcrumb($this->mod_url, lang('low_likes_module_name'));
		$this->_nav();

		return $this->EE->load->view('mcp_entries', $vars, TRUE);
	}

	// --------------------------------------------------------------------

	/**
	 * List of active members
	 *
	 * @access     public
	 * @return     string
	 */
	public function members()
	{
		// --------------------------------------
		// Retrieve list of members
		// --------------------------------------

		// Items to get
		$select = array(
			'l.member_id', 'COUNT(*) AS num_likes',
			'm.screen_name'
		);

		// Query DB
		$query = $this->EE->db->select($select)
		       ->from('low_likes l')
		       ->join('members m', 'l.member_id = m.member_id')
		       ->group_by('l.member_id')
		       ->order_by('num_likes', 'desc')
		       ->limit(50)
		       ->get();

		// Get results
		$members = $query->result_array();

		// --------------------------------------
		// Modify results
		// --------------------------------------

		foreach ($members AS &$row)
		{
			$row['member_url'] = sprintf($this->member_url, $row['member_id']);
		}

		// --------------------------------------
		// Add likes to result array and feed to view
		// --------------------------------------

		$vars = array('members' => $members);

		// --------------------------------------
		// Page title
		// --------------------------------------

		$this->EE->cp->set_variable('cp_page_title', lang('active_members'));
		$this->EE->cp->set_breadcrumb($this->mod_url, lang('low_likes_module_name'));
		$this->_nav();

		return $this->EE->load->view('mcp_members', $vars, TRUE);
	}

	// --------------------------------------------------------------------

	/**
	 * Add navigation and CSS to CP
	 *
	 * @access     private
	 * @return     void
	 */
	private function _nav()
	{
		$this->EE->cp->set_right_nav(array(
			'low_likes_module_name' => $this->mod_url,
			'popular_entries' => $this->mod_url.AMP.'method=entries',
			'active_members'  => $this->mod_url.AMP.'method=members'
		));

		$this->EE->cp->load_package_css(LOW_LIKES_PACKAGE.'&amp;v='.time());
	}
}
/* End of file mcp.low_likes.php */