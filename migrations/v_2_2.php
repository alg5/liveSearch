<?php
/**
*
* @package liveSearch
* @copyright (c) alg
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

namespace alg\liveSearch\migrations;


class v_2_2 extends \phpbb\db\migration\migration
{
	const ON = 1;

	/**
	* Assign migration file dependencies for this migration
	*
	* @return array Array of migration files
	* @static
	* @access public
	*/
	static public function depends_on()
	{
		return array('\alg\liveSearch\migrations\v_2_1');
	}

	public function update_data()
	{
		return array(
			// Add new configs
			array('config.add', array('live_search_hide_after_select', v_2_2::ON)),
		);
	}
	public function revert_data()
	{
		return array(
			// remove from configs
			array('config.remove', array('live_search_hide_after_select')),

		);
	}
}
