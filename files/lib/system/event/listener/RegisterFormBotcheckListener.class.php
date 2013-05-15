<?php
namespace wcf\system\event\listener;

use wcf\system\event\IEventListener;
use wcf\system\exception\UserInputException;
use wcf\system\WCF;
use wcf\util\StringUtil;

/**
 * Handles botcheck stuff in register form.
 * 
 * @author		Markus Bartz <roul@codingcorner.info>
 * @copyright	2013 Markus Bartz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		info.codingcorner.wcf.user.botcheck
 * @subpackage	system.event.listener
 * @category	Community Framework
 */
class RegisterFormBotcheckListener implements IEventListener {
	/**
	 * question cache
	 * @var array<wcf\data\botcheck\BotcheckQuestion>
	 */
	protected $questions = null;

	/**
	 * instance of RegisterForm
	 * @var	wcf\form\RegisterForm
	 */
	protected $eventObj = null;

	/**
	 * question
	 * @var wcf\data\botcheck\BotcheckQuestion
	 */
	protected $question = null;

	/**
	 * answer
	 * @var string
	 */
	protected $answer = '';

	/**
	 * botcheck question enabled
	 * @var bool
	 */
	protected $enabled = MODULE_USER_BOTCHECK;
	
	protected function getQuestions() {
		if ($this->questions) {
			$this->questions = BotcheckQuestionCacheBuilder::getInstance()->getData();
		}

		return $this->questions;
	}

	/**
	 * @see	wcf\system\event\IEventListener::execute()
	 */
	public function execute($eventObj, $className, $eventName) {
		$this->eventObj = $eventObj;
		
		if ($this->enabled && (!WCF::getSession()->getVar('botcheckQuestionSolved') || $eventName == 'save')) {
			$questions = $this->getQuestions();

			if (!count($questions)) {
				$this->enabled = false;

				return;
			}

			$this->$eventName();
		}
	}
	
	/**
	 * Handles the assignVariables event.
	 */
	protected function assignVariables() {
		WCF::getTPL()->assign(array(
			'question' => 'Testfrage',
			'answer' => $this->answer,
		));
	}

	/**
	 * Handles the readData event.
	 * This is only called in UserGroupEditForm.
	 */
	protected function readData() {
		if (empty($_POST)) {
			$questions = $this->getQuestions();
			$questionIDs = array_keys($questions);

			$i = mt_rand(0, count($questionIDs) - 1);
			$questionID = $questionIDs[$i];

			$this->question = $questions[$questionID];
			WCF::getSession()->register('questionID', $questionID);
		}
	}

	/**
	 * Handles the readFormParameters event.
	 */
	protected function readFormParameters() {
		$this->question = $questions[$questionID];
		$questionID = WCF::getSession()->getVar('questionID');
		$this->question = $questions[$questionID];

		if (isset($_POST['answer'])) $this->answer = StringUtil::trim($_POST['answer']);
	}

	/**
	 * Handles the validate event.
	 */
	protected function validate() {
		//TODO: implement validation
		throw new UserInputException('answer', 'false');

		WCF::getSession()->register('botcheckQuestionSolved', true);
	}

	/**
	 * Handles the save event.
	 */
	protected function save() {
		WCF::getSession()->unregister('botcheckQuestionSolved');
	}
}
