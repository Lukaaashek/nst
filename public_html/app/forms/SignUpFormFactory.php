<?php

namespace App\Forms;

use App\Model;
use Nette;
use Nette\Application\UI\Form;


final class SignUpFormFactory
{
	use Nette\SmartObject;

	const USERNAME_MAX_LENGTH = 50;
	const PASSWORD_MIN_LENGTH = 6;

	/** @var FormFactory */
	private $factory;

	/** @var Model\UserManager */
	private $userManager;


	public function __construct(FormFactory $factory, Model\UserManager $userManager)
	{
		$this->factory = $factory;
		$this->userManager = $userManager;
	}


	/**
	 * @return Form
	 */
	public function create(callable $onSuccess)
	{
		$form = $this->factory->create();
		$form->addText('username', 'Uživatelské jméno:')
			->setRequired('Prosím vyberte uživatelské jméno.')
			->addRule($form::MAX_LENGTH, sprintf('Uživatelské jméno nesmí být delší než %d znaků', self::USERNAME_MAX_LENGTH), self::USERNAME_MAX_LENGTH);

		$form->addPassword('password', 'Heslo:')
			->setOption('description', sprintf('Heslo musí mít minimálně %d znaků', self::PASSWORD_MIN_LENGTH))
			->setRequired('Prosím zadejte heslo.')
			->addRule($form::MIN_LENGTH, sprintf('Heslo musí mít minimálně %d znaků', self::PASSWORD_MIN_LENGTH), self::PASSWORD_MIN_LENGTH);

		$form->addSubmit('send', 'Registrovat se');

		$form->onSuccess[] = function (Form $form, $values) use ($onSuccess) {
			try {
				$this->userManager->add($values->username, $values->password);
			} catch (Model\DuplicateNameException $e) {
				$form['username']->addError('Uživatelské jméno je již zabrané.');
				return;
			}
			$onSuccess();
		};

		return $form;
	}
}
