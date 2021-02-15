<?php

declare(strict_types=1);

namespace SatraGlobalPackage\User;


use Baraja\Console\Helpers;
use Baraja\Doctrine\EntityManager;
use SatraGlobalPackage\User\UserManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Tracy\Debugger;

class UserGroupCreateCommand extends Command
{

	/**
	 * @var EntityManager
	 */
	private $entityManager;

	/**
	 * @var UserManager
	 */
	private $userManager;

	/**
	 * UserGroupCreateCommand constructor.
	 * @param EntityManager $entityManager
	 * @param UserManager $userManager
	 */
	public function __construct(EntityManager $entityManager, UserManager $userManager)
	{
		parent::__construct();
		$this->entityManager = $entityManager;
		$this->userManager = $userManager;
	}


	protected function configure(): void
	{
		$this->setName('app:usergroup:create')
			->setDescription('Create new user group.')
			->addArgument('groupname', InputArgument::REQUIRED, 'The name of user group.');
	}

	/**
	 * @param InputInterface $input
	 * @param OutputInterface $output
	 * @return int
	 */
	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		try {
			$userGroupName = $input->getArgument('groupname');

			$output->writeln('Creating user group: ' . $userGroupName);
			$output->writeln('Please wait...');

			$userGroup = $this->userManager->createUserGroup($userGroupName);

			Helpers::terminalRenderSuccess('User group was created.');

			return Command::SUCCESS;
		} catch (\Throwable $e) {
			Debugger::log($e);
			Helpers::terminalRenderError($e->getMessage() . ' | file: ' . $e->getFile() ?? '' . ' | line: ' . $e->getLine() ?? '');
		}

		return Command::FAILURE;
	}

}