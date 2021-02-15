<?php

declare(strict_types=1);

namespace SatraGlobalPackage\User;


use Nette\DI\CompilerExtension;

/**
 * Class UserExtension
 * @package SatraGlobalPackage\User
 */
class UserExtension extends CompilerExtension
{

	public function beforeCompile(): void
	{
		$builder = $this->getContainerBuilder();
		$builder->getDefinitionByType(UserManager::class)->addSetup('validUserLogin');
	}

}