<?php

declare(strict_types=1);

namespace SatraGlobalPackage\User;


interface UserManagerAccessor
{

	/**
	 * @return UserManager
	 */
	public function get(): UserManager;

}