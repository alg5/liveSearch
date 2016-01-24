<?php
/**
*
* @package liveSearch
* @copyright (c) alg
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

namespace alg\liveSearch\migrations;


class v_2_1 extends \phpbb\db\migration\migration
{
	const MIN_CHARS = 1;
	const MIN_CHARS_POST = 5;
	const MAX_ITEMS_TO_SHOW = 20;
	const OFF = 0;
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
		return array('\alg\liveSearch\migrations\v_2_0');
	}

	public function update_data()
	{
		return array(
			// Add new configs
			array('config.add', array('live_search_on_off_mcp', v_2_1::ON)),
			array('config.add', array('live_search_min_num_symblols_mcp_user', v_2_1::MIN_CHARS)),
			array('config.add', array('live_search_min_num_symblols_mcp_forum', v_2_1::MIN_CHARS)),
			array('config.add', array('live_search_min_num_symblols_mcp_group', v_2_1::MIN_CHARS)),
			array('config.add', array('live_search_max_items_to_show_mcp', v_2_1::MAX_ITEMS_TO_SHOW)),
			array('config.add', array('live_search_max_items_to_show_mcp', v_2_1::MAX_ITEMS_TO_SHOW)),
		);
	}
	public function revert_data()
	{
		return array(
			// remove from configs
			array('config.remove', array('live_search_on_off_mcp')),
			array('config.remove', array('live_search_min_num_symblols_mcp_user')),
			array('config.remove', array('live_search_min_num_symblols_mcp_forum')),
			array('config.remove', array('live_search_min_num_symblols_mcp_group')),
			array('config.remove', array('live_search_max_items_to_show_mcp')),

		);
	}
}
