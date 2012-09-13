<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// include config file
include(PATH_THIRD.'low_likes/config.php');

/**
 * Low Likes Update class
 *
 * @package        low_likes
 * @author         Lodewijk Schutte <hi@gotolow.com>
 * @link           http://gotolow.com/addons/low-likes
 * @copyright      Copyright (c) 2012, Low
 */
class Low_likes_upd {

	// --------------------------------------------------------------------
	// PROPERTIES
	// --------------------------------------------------------------------

	/**
	 * This version
	 *
	 * @access      public
	 * @var         string
	 */
	public $version = LOW_LIKES_VERSION;

	/**
	 * EE Superobject
	 *
	 * @access      private
	 * @var         object
	 */
	private $EE;

	/**
	 * Class name
	 *
	 * @access      private
	 * @var         array
	 */
	private $class_name;

	/**
	 * Actions used
	 *
	 * @access      private
	 * @var         array
	 */
	private $actions = array(
		array('Low_likes', 'toggle_like')
	);

	/**
	 * Extension hooks
	 *
	 * @var        array
	 * @access     private
	 */
	private $hooks = array(
		'channel_entries_query_result',
		'delete_entries_loop',
		'delete_member'
	);

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
		// Set class name
		// --------------------------------------

		$this->class_name = ucfirst(LOW_LIKES_PACKAGE);
	}

	// --------------------------------------------------------------------

	/**
	 * Install the module
	 *
	 * @access      public
	 * @return      bool
	 */
	public function install()
	{
		// --------------------------------------
		// Install tables
		// --------------------------------------

		// Load DB Forge class
		$this->EE->load->dbforge();

		// Define fields to create
		$this->EE->dbforge->add_field(array(
			'entry_id'  => array('type' => 'int', 'constraint' => '10', 'unsigned' => TRUE),
			'member_id' => array('type' => 'int', 'constraint' => '10', 'unsigned' => TRUE),
			'like_date' => array('type' => 'int', 'constraint' => '11', 'unsigned' => TRUE)
		));

		// Create primary key
		$this->EE->dbforge->add_key('entry_id',  TRUE);
		$this->EE->dbforge->add_key('member_id', TRUE);

		// Creates the table
		$this->EE->dbforge->create_table('low_likes');

		// --------------------------------------
		// Add row to modules table
		// --------------------------------------

		$this->EE->db->insert('modules', array(
			'module_name'    => $this->class_name,
			'module_version' => $this->version,
			'has_cp_backend' => 'y'
		));

		// --------------------------------------
		// Add rows to actions table
		// --------------------------------------

		foreach ($this->actions AS $row)
		{
			list($class, $method) = $row;

			$this->EE->db->insert('actions', array(
				'class'  => $class,
				'method' => $method
			));
		}

		// --------------------------------------
		// Add rows to extensions table
		// --------------------------------------

		foreach ($this->hooks AS $hook)
		{
			$this->EE->db->insert('extensions', array(
				'class'    => $this->class_name.'_ext',
				'method'   => $hook,
				'hook'     => $hook,
				'settings' => '',
				'priority' => 5,
				'version'  => $this->version,
				'enabled'  => 'y'
			));
		}

		return TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Uninstall the module
	 *
	 * @return	bool
	 */
	public function uninstall()
	{
		// --------------------------------------
		// get module id
		// --------------------------------------

		$query = $this->EE->db->select('module_id')
		       ->from('modules')
		       ->where('module_name', $this->class_name)
		       ->get();

		// --------------------------------------
		// remove references from module_member_groups
		// --------------------------------------

		$this->EE->db->where('module_id', $query->row('module_id'));
		$this->EE->db->delete('module_member_groups');

		// --------------------------------------
		// remove references from modules
		// --------------------------------------

		$this->EE->db->where('module_name', $this->class_name);
		$this->EE->db->delete('modules');

		// --------------------------------------
		// remove references from actions
		// --------------------------------------

		$this->EE->db->where('class', $this->class_name);
		$this->EE->db->delete('actions');

		// --------------------------------------
		// remove references from extensions
		// --------------------------------------

		$this->EE->db->where('class', $this->class_name.'_ext');
		$this->EE->db->delete('extensions');

		// --------------------------------------
		// Uninstall tables
		// --------------------------------------

		$this->EE->load->dbforge();
		$this->EE->dbforge->drop_table('low_likes');

		return TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Update the module
	 *
	 * @access	public
	 * @param	string
	 * @return	bool
	 */
	public function update($current = '')
	{
		// --------------------------------------
		// Same version? A-okay, daddy-o!
		// --------------------------------------

		if ($current == '' || version_compare($current, $this->version) === 0)
		{
			return FALSE;
		}

		// // Update to next version
		// if (version_compare($current, 'next-version', '<'))
		// {
		// 	// ...
		// }

		// Return TRUE to update version number in DB
		return TRUE;
	}

	// --------------------------------------------------------------------

} // End class

/* End of file upd.low_likes.php */