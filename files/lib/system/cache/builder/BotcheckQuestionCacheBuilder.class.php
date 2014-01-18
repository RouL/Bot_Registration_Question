<?php
namespace wcf\system\cache\builder;

use wcf\data\botcheck\question\BotcheckQuestionList;

/**
 * Caches the botcheck questions.
 *
 * @author		Markus Zhang <roul@codingcorner.info>
 * @copyright	2013 Markus Zhang
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		info.codingcorner.wcf.user.botcheck
 * @subpackage	system.cache.builder
 * @category	Community Framework
 */
class BotcheckQuestionCacheBuilder extends AbstractCacheBuilder {
	/**
	 * @see	wcf\system\cache\builder\AbstractCacheBuilder::rebuild()
	 */
	protected function rebuild(array $parameters) {
		$questionList = new BotcheckQuestionList();
		$questionList->readObjects();
		
		return $questionList->getObjects();
	}
}
