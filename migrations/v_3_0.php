<?php
/**
*
* @package liveSearch 3.2
* @copyright (c) alg
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

namespace alg\liveSearch\migrations;

class v_3_0 extends \phpbb\db\migration\migration
{
	/**
	* Assign migration file dependencies for this migration
	*
	* @return array Array of migration files
	* @static
	* @access public
	*/
	public function effectively_installed()
	{
		return isset($this->config['live_search']) && version_compare($this->config['live_search'], '3.0.*', '>=');
	}
	static public function depends_on()
	{
		return array('\alg\liveSearch\migrations\v_2_2');
	}

	public function update_data()
	{
		return array(
			// Add new configs
			array('config.add', array('live_search', '3.0.*')),
		);
	}
	public function revert_data()
	{
		return array();
	}
}
