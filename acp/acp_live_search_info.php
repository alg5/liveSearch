<?php
/**
*
* @author Alg
* @version $Id: acp_live_search.php,v 1.0.0. Alg$
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

namespace alg\liveSearch\acp;

class acp_live_search_info
{
	function module()
	{
		return array(
			'filename'	=> '\alg\liveSearch\acp\acp_live_search_module',
			'title'		=> 'ACP_LIVE_SEARCH_SETTINGS',
			'version'	=> '1.0.5',
			'modes'		=> array(
				'live_search'			=> array('title' => 'ACP_LIVE_SEARCH_SETTINGS', 'auth' => 'ext_alg/liveSearch && acl_a_board', 'cat' => array('ACP_LIVE_SEARCH')),
			),
		);
	}

	function install()
	{
	}

	function uninstall()
	{
	}
}
