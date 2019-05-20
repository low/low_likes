<?php


/**
 * Low Likes Update class
 *
 * @package        low_likes
 * @author         Lodewijk Schutte <hi@gotolow.com>
 * @link           https://github.com/low/low_likes
 * @copyright      Copyright (c) 2019, Low
 */
class Low_likes_upd extends Low\Likes\Base {

    // --------------------------------------------------------------------
    // PROPERTIES
    // --------------------------------------------------------------------

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
    );

    // --------------------------------------------------------------------
    // METHODS
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
        ee()->load->dbforge();

        // Define fields to create
        ee()->dbforge->add_field(array(
            'like_id'   => ['type' => 'int', 'constraint' => '11', 'unsigned' => TRUE],
            'owner_id'  => ['type' => 'int', 'constraint' => '10', 'unsigned' => TRUE],
            'entry_id'  => ['type' => 'int', 'constraint' => '10', 'unsigned' => TRUE],
            'member_id' => ['type' => 'int', 'constraint' => '10', 'unsigned' => TRUE],
            'like_date' => ['type' => 'int', 'constraint' => '11', 'unsigned' => TRUE],
        ));

        // Create primary key
        ee()->dbforge->add_key('like_id', TRUE);
        ee()->dbforge->add_key('owner_id');
        ee()->dbforge->add_key('entry_id');
        ee()->dbforge->add_key('member_id');

        // Creates the table
        ee()->dbforge->create_table('low_likes');

        // --------------------------------------
        // Add row to modules table
        // --------------------------------------

        ee()->db->insert('modules', array(
            'module_name'    => $this->class_name,
            'module_version' => $this->version,
            'has_cp_backend' => 'y'
        ));

        // --------------------------------------
        // Add rows to actions table
        // --------------------------------------

        foreach ($this->actions as $row)
        {
            $this->_add_action($row);
        }

        // --------------------------------------
        // Add rows to extensions table
        // --------------------------------------

        foreach ($this->hooks AS $hook)
        {
            $this->_add_hook($hook);
        }

        return TRUE;
    }

    // --------------------------------------------------------------------

    /**
     * Uninstall the module
     *
     * @return    bool
     */
    public function uninstall()
    {
        // --------------------------------------
        // get module id
        // --------------------------------------

        $query = ee()->db->select('module_id')
            ->from('modules')
            ->where('module_name', $this->class_name)
            ->get();

        // --------------------------------------
        // remove references from module_member_groups
        // --------------------------------------

        ee()->db->where('module_id', $query->row('module_id'));
        ee()->db->delete('module_member_groups');

        // --------------------------------------
        // remove references from modules
        // --------------------------------------

        ee()->db->where('module_name', $this->class_name);
        ee()->db->delete('modules');

        // --------------------------------------
        // remove references from actions
        // --------------------------------------

        ee()->db->where('class', $this->class_name);
        ee()->db->delete('actions');

        // --------------------------------------
        // remove references from extensions
        // --------------------------------------

        ee()->db->where('class', $this->class_name.'_ext');
        ee()->db->delete('extensions');

        // --------------------------------------
        // Uninstall tables
        // --------------------------------------

        ee()->load->dbforge();
        ee()->dbforge->drop_table('low_likes');

        return TRUE;
    }

    // --------------------------------------------------------------------

    /**
     * Update the module
     *
     * @access    public
     * @param    string
     * @return    bool
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
        //     // ...
        // }

        // Return TRUE to update version number in DB
        return TRUE;
    }

    // --------------------------------------------------------------------

    // --------------------------------------------------------------------

	/**
	 * Add action to actions table
	 *
	 * @access     private
	 * @param      array
	 * @return     void
	 */
	private function _add_action($array)
	{
		list($class, $method) = $array;

		ee()->db->insert('actions', array(
			'class'  => $class,
			'method' => $method
		));
	}

	/**
	 * Add extension hook
	 *
	 * @access     private
	 * @param      string
	 * @return     void
	 */
	private function _add_hook($name)
	{
		ee()->db->insert('extensions',
			array(
				'class'    => $this->class_name.'_ext',
				'method'   => $name,
				'hook'     => $name,
				'settings' => '',
				'priority' => 5,
				'version'  => $this->version,
				'enabled'  => 'y'
			)
		);
	}

} // End class

/* End of file upd.low_likes.php */