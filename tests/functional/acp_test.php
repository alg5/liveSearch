<?php
/**
 *
 * Thanks for posts extension. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2019, alg
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace alg\liveSearch\tests\functional;

/**
 * @group functional
 */
class acp_test extends \phpbb_functional_test_case
{
	static protected function setup_extensions()
	{
		return array('alg/liveSearch');
	}

	public function test_acp_module()
	{
		$this->login();
		$this->admin_login();

		$crawler = self::request('GET', "adm/index.php?sid={$this->sid}&i=-alg-liveSearch-acp-acp_live_search_module&mode=live_search");
		$form = $crawler->selectButton('Submit')->form();
		$values = $form->getValues();
		// Specify the number of users to show in the toplist on index page
		$values['live_search_min_num_symblols_forum'] = 5;
		$form->setValues($values);
		$crawler = self::submit($form);
		$this->assertContains($this->lang('CONFIG_UPDATED'), $crawler->filter('.successbox')->text());
	}
}
