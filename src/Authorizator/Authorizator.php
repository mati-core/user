<?php

declare(strict_types=1);

namespace MatiCore\User;


use Nette\Security\IAuthorizator;
use Nette\Security\IIdentity;
use Tracy\Debugger;

/**
 * Class Authorizator
 * @package MatiCore\User
 */
class Authorizator implements IAuthorizator
{

	/**
	 * @param string|null $role
	 * @param string|null $resource
	 * @param string|null $privilege
	 * @return bool
	 * @throws UserException
	 */
	public function isAllowed($role, $resource, $privilege): bool
	{
		throw new UserException('Checking access deny! Using function isAllowed is rejected, use function checkAccess!');
	}

	/**
	 * @param IIdentity|null $identity
	 * @param string $privilege
	 * @return bool
	 */
	public function checkAccess(?IIdentity $identity, string $privilege): bool
	{
		if ($identity instanceof StorageIdentity && $identity->getUser() instanceof BaseUser) {
			return $this->validate($identity->getUser(), $privilege);
		}

		if ($identity instanceof BaseUser) {
			return $this->validate($identity, $privilege);
		}

		return self::DENY;
	}

	/**
	 * @param BaseUser $user
	 * @param string $privilege
	 * @return bool
	 */
	private function validate(BaseUser $user, string $privilege): bool
	{
		$group = $user->getGroup();
		if($group->isSuperAdmin()){
			return Authorizator::ALLOW;
		}

		return in_array($privilege, $group->getRights(), true) ? self::ALLOW : self::DENY;
	}

}