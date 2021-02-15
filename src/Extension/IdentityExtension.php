<?php

declare(strict_types=1);

namespace SatraGlobalPackage\User;


use Nette\Configurator;
use Nette\DI\Compiler;
use Nette\DI\CompilerExtension;
use Nette\Security\IUserStorage;

/**
 * Class IdentityExtension
 * @package SatraGlobalPackage\User
 */
class IdentityExtension extends CompilerExtension
{

	/**
	 * @param Configurator $configurator
	 */
	public static function register(Configurator $configurator): void
	{
		$configurator->onCompile[] = function (Configurator $sender, Compiler $compiler) {
			$compiler->addExtension('doctrine2identity', new IdentityExtension);
		};
	}

	public function beforeCompile(): void
	{
		$builder = $this->getContainerBuilder();
		
		$userStorageDefinitionName = $builder->getByType(IUserStorage::class) ? : 'nette.userStorage';
		
		$builder->getDefinition($userStorageDefinitionName)
			->setFactory(UserStorage::class);
	}

}
