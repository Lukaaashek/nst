<?php

namespace App\Forms;

use App\Model;
use Nette;
use Nette\Application\UI\Form;
use Nette\Security\User;

final class ChangePasswordFormFactory
{
	use Nette\SmartObject;
	
	const PASSWORD_MIN_LENGTH = 6;

	/** @var FormFactory */
	private $factory;

	/** @var Model\UserManager */
	private $userManager;
	
	/** @var User */
	private $user;


	public function __construct(FormFactory $factory, Model\UserManager $userManager, User $user)
	{
		$this->factory = $factory;
		$this->userManager = $userManager;
		$this->user = $user;
	}


	/**
	 * @return Form
	 */
	public function create(callable $onSuccess)
	{
		$form = $this->factory->create();
		$form->addPassword('old_password', 'Staré heslo:')
			->setRequired('Prosím vložte staré heslo.');

		$form->addPassword('new_password', 'Nové heslo:')
			->addRule($form::MIN_LENGTH, sprintf('Heslo musí mít minimálně %d znaků', self::PASSWORD_MIN_LENGTH), self::PASSWORD_MIN_LENGTH)
			->setRequired('Prosím vložte nové heslo.');
		
		$form->addPassword('check_password', 'Kontrolní heslo:')
			->setRequired('Prosím vložte kontrolní heslo.')
			->setOption('description', 'Hesla se musí shodovat')
			->addConditionOn($form['new_password'], Form::NOT_EQUAL, $form['check_password'])
				->addRule(FORM::EQUAL, 'Hesla se neshodují', 0);

		$form->addSubmit('send', 'Změnit heslo');

		$form->onSuccess[] = function (Form $form, $values) use ($onSuccess) {
			try {
				$this->userManager->changePassword($this->user->id, $values->old_password, $values->new_password);
			} catch (Nette\Security\AuthenticationException $e) {
				$form->addError('Heslo není správné.');
				return;
			}
			$onSuccess();
		};

		return $form;
	}
}
