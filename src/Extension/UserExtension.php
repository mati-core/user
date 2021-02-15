<?php

declare(strict_types=1);

namespace MatiCore\User;


use Nette\DI\CompilerExtension;

/**
 * Class UserExtension
 * @package MatiCore\User
 */
class UserExtension extends CompilerExtension
{

	public function beforeCompile(): void
	{
		$builder = $this->getContainerBuilder();
		$builder->getDefinitionByType(UserManager::class)->addSetup('validUserLogin');
	}

}