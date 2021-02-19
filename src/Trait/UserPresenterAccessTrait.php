<?php

declare(strict_types=1);

namespace MatiCore\User;

use MatiCore\Menu\Entity\MenuItem;
use Nette\Application\UI\InvalidLinkException;

/**
 * Trait UserAccessTrait
 * @package MatiCore\User
 */
trait UserPresenterAccessTrait
{

	/**
	 * @var UserManagerAccessor
	 * @inject
	 */
	public $userManager;

	public function __construct()
	{
		parent::__construct();

		$this->init();
	}

	public function init(): void
	{
		$this->onStartup[] = function () {
			$action = $this->getAction(true);
			if ($this->user->isLoggedIn() === true) {

				if ($this->user->getIdentity() === null) {
					$this->user->logout(true);
					$this->redirect('Sign:in');
				}

				if (!$this->user->getIdentity() instanceof StorageIdentity) {
					throw new UserException('User must be instance of StorageIdentity!');
				}

				if (!$this->userManager->get()->checkAccess($this->user, $this->pageRight)) {
					$this->template->setFile(__DIR__ . '/templates/Error/403.latte');
					$this->template->missingPermissions = [$this->pageRight];
				}
			} elseif ($action !== ':Admin:Sign:in' && $action !== ':Admin:Sign:forgotPassword') {
				$this->redirect(':Admin:Sign:in', [
					'backLink' => $this->getHttpRequest()->getUrl()->getAbsoluteUrl(),
				]);
			}
		};
	}

	/**
	 * @param string $rightSlug
	 * @return bool
	 */
	public function checkAccess(string $rightSlug): bool
	{
		return $this->userManager->get()->checkAccess($this->user, $rightSlug);
	}

	/**
	 * @return bool
	 */
	public function isSuperAdmin(): bool
	{
		static $superAdmin;

		if($superAdmin === null) {
			$superAdmin = false;

			$identity = $this->user->getIdentity();
			if ($identity instanceof StorageIdentity) {
				$user = $identity->getUser();

				if($user instanceof BaseUser){
					$superAdmin = $user->getGroup()->isSuperAdmin();
				}
			}
		}

		return $superAdmin;
	}

	/**
	 * @param MenuItem $menuItem
	 * @return bool
	 * @throws InvalidLinkException
	 */
	public function isMenuItemActive(MenuItem $menuItem): bool
	{
		if ($menuItem->getRoute() !== null && $this->isLinkCurrent($menuItem->getRoute())) {
			return true;
		}

		if ($menuItem->hasChildren()) {
			foreach ($menuItem->getChildren() as $child) {
				if ($this->isMenuItemActive($child)) {
					return true;
				}
			}
		}

		return false;
	}

}