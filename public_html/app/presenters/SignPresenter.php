<?php

namespace App\Presenters;

use App\Forms;
use Nette\Application\UI\Form;


final class SignPresenter extends BasePresenter
{
	/** @persistent */
	public $backlink = '';

	/** @var Forms\SignInFormFactory */
	private $signInFactory;

	/** @var Forms\SignUpFormFactory */
	private $signUpFactory;
	
	/** @var Forms\ChangePasswordFormFactory */
	private $changePasswordFactory;


	public function __construct(
			Forms\SignInFormFactory $signInFactory, 
			Forms\SignUpFormFactory $signUpFactory, 
			Forms\ChangePasswordFormFactory $changePasswordFactory
		)
	{
		$this->signInFactory = $signInFactory;
		$this->signUpFactory = $signUpFactory;
		$this->changePasswordFactory = $changePasswordFactory;
	}


	/**
	 * Sign-in form factory.
	 * @return Form
	 */
	protected function createComponentSignInForm()
	{
		return $this->signInFactory->create(function () {
			$this->restoreRequest($this->backlink);
			$this->flashMessage('Přihlášení byla úspěšné', 'success');
			$this->redirect('Homepage:');
		});
	}


	/**
	 * Sign-up form factory.
	 * @return Form
	 */
	protected function createComponentSignUpForm()
	{
		return $this->signUpFactory->create(function () {
			$this->flashMessage('Registrace byla úspěšná', 'success');
			$this->redirect('Homepage:');
		});
	}
	
	
	public function actionChangePassword() {
		if(!$this->user->isLoggedIn()) {
			$this->flashMessage('Nejprve se přihlašte.');
			$this->redirect('Sign:In');
		}
	}
	
	/**
	 * Change password form factory.
	 * @return Form
	 */
	protected function createComponentChangePasswordForm()
	{
		return $this->changePasswordFactory->create(function () {
			$this->flashMessage('Heslo bylo úspěšně změněno.', 'success');
			$this->redirect('Homepage:');
		});
	}


	public function actionOut()
	{
		$this->getUser()->logout();
		$this->flashMessage('Byli jste odhlášeni', 'success');
		$this->redirect('Homepage:');
	}
}
