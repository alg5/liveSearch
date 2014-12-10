<?php
/**
*
* @package ForumSponsor
* @copyright (c) alg
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

namespace alg\ForumSponsor\migrations;


class v_1_0_0 extends \phpbb\db\migration\migration
{

	public function effectively_installed()
	{
		return isset($this->config['ForumSponsor']) && version_compare($this->config['ForumSponsor'], '1.0.0', '>=');
	}

	static public function depends_on()
	{
			return array('\phpbb\db\migration\data\v310\dev');
	}

	public function update_schema()
	{
		//return 	array(
		//	'add_columns' => array(
		//		$this->table_prefix . 'forums' => array(
		//			'forum_sponsor' => array('VCHAR:500', ''),
		//		),
		//	),
		//);
		$add_fields =  array();
		if (!$this->db_tools->sql_column_exists($this->table_prefix . 'forums', 'forum_sponsor'))
		{
			$add_fields  = array_merge ($add_fields,  array(
									'add_columns' => array (
										$this->table_prefix . 'forums' => array  (
											'forum_sponsor' => array('VCHAR:500', ''),
										) )
								)
			);
		}
		return $add_fields;
	}

	public function revert_schema()
	{
		return 	array( );
	}

	public function update_data()
	{
		return array(
			array('config.add', array('forum_sponsor', '1.0.0')),
		);
	}
	public function revert_data()
	{
		return array(
			array('config.remove', array('forum_sponsor')),

		);
	}
}
