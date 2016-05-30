<?php
/**
*
* @package liveSearch
* @copyright (c) alg
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

namespace alg\liveSearch\migrations;

class v_2_0 extends \phpbb\db\migration\migration
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
		return array('\alg\liveSearch\migrations\v_1_0_0');
	}

	public function update_data()
	{
		return array(
			// Add new configs
			array('config.add', array('live_search_topic_link_type', v_2_0::ON)),

			array('config.add', array('live_search_on_off_post', v_2_0::ON)),
			array('config.add', array('live_search_min_num_symblols_post', v_2_0::MIN_CHARS_POST)),
			array('config.add', array('live_search_max_items_to_show_post', v_2_0::MAX_ITEMS_TO_SHOW)),

			array('config.add', array('live_search_on_off_acp', v_2_0::ON)),
			array('config.add', array('live_search_min_num_symblols_acp_user', v_2_0::MIN_CHARS)),
			array('config.add', array('live_search_min_num_symblols_acp_forum', v_2_0::MIN_CHARS)),
			array('config.add', array('live_search_min_num_symblols_acp_group', v_2_0::MIN_CHARS)),
			array('config.add', array('live_search_max_items_to_show_acp', v_2_0::MAX_ITEMS_TO_SHOW)),
			array('config.add', array('live_search_max_items_to_show_acp', v_2_0::MAX_ITEMS_TO_SHOW)),
			// Current version
			//array('config.add', array('live_search', '2.0.*')),
		);
	}
	public function revert_data()
	{
		return array(
			// remove from configs
			array('config.remove', array('live_search_topic_link_type')),
			array('config.remove', array('live_search_on_off_acp')),
			array('config.remove', array('live_search_min_num_symblols_acp_user')),
			array('config.remove', array('live_search_min_num_symblols_acp_forum')),
			array('config.remove', array('live_search_min_num_symblols_acp_group')),
			array('config.remove', array('live_search_max_items_to_show_acp')),
			// Current version
			//array('config.remove', array('live_search')),

		);
	}
}
