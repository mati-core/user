<?php

declare(strict_types=1);

namespace MatiCore\User;


interface UserManagerAccessor
{

	/**
	 * @return UserManager
	 */
	public function get(): UserManager;

}