<?php

declare(strict_types=1);

namespace MatiCore\User;


use Baraja\Console\Helpers;
use Baraja\Doctrine\EntityManager;
use Doctrine\DBAL\Exception\NonUniqueFieldNameException;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use MatiCore\User\UserManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Tracy\Debugger;

class UserInicializeCommand extends Command
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
		$this->setName('app:user:init')
			->setDescription('Inicialize default user groups and superadmin account.')
			->addArgument('username', InputArgument::REQUIRED, 'The login of user.')
			->addArgument('password', InputArgument::REQUIRED, 'The password of user.');
	}

	/**
	 * @param InputInterface $input
	 * @param OutputInterface $output
	 * @return int
	 */
	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		try {
			$userName = $input->getArgument('username');
			$password = $input->getArgument('password');

			$output->writeln('Creating access rights..');

			try{
				$mainRight = $this->userManager->getRightBySlug(BaseUser::RIGHT_CMS);
			}catch (NoResultException|NonUniqueResultException $e) {
				$mainRight = new UserRight('CMS main access', BaseUser::RIGHT_CMS);
				$this->entityManager->persist($mainRight);

				$rightLangCS = new UserRightLang($mainRight, 'cs', 'CMS - základní přístup');
				$this->entityManager->persist($rightLangCS);
				$mainRight->addName($rightLangCS);

				$rightLangEN = new UserRightLang($mainRight, 'en', 'CMS - main access');
				$this->entityManager->persist($rightLangEN);
				$mainRight->addName($rightLangEN);

				$this->entityManager->flush();
			}

			$output->writeln('Creating admin role..');
			
			try{
				$adminRole = $this->userManager->getRoleBySlug(BaseUser::ROLE_ADMIN);
			}catch (NoResultException|NonUniqueResultException $e){
				$adminRole = new UserRole('Admin', BaseUser::ROLE_ADMIN);
				$this->entityManager->persist($adminRole)->flush();

				$roleLangCS = new UserRoleLang($adminRole, 'cs', 'Admin');
				$this->entityManager->persist($roleLangCS);
				$adminRole->addName($roleLangCS);

				$roleLangEN = new UserRoleLang($adminRole, 'en', 'Admin');
				$this->entityManager->persist($roleLangEN);
				$adminRole->addName($roleLangEN);

				$adminRole->addRight($mainRight);

				$this->entityManager->flush();
			}

			$output->writeln('Creating groups..');

			try{
				$superAdminGroup = $this->userManager->getUserGroupBySlug(BaseUser::GROUP_SUPER_ADMIN);
			}catch (NoResultException|NonUniqueFieldNameException $e){
				$superAdminGroup = new UserGroup('Super admin', BaseUser::GROUP_SUPER_ADMIN);
				$superAdminGroup->setSuperAdmin(true);

				$this->entityManager->persist($superAdminGroup);

				$groupLangCS = new UserGroupLang($superAdminGroup, 'cs', 'Super admin');
				$this->entityManager->persist($groupLangCS);
				$superAdminGroup->addName($groupLangCS);

				$groupLangEN = new UserGroupLang($superAdminGroup, 'en', 'Super admin');
				$this->entityManager->persist($groupLangEN);
				$superAdminGroup->addName($groupLangEN);

				$this->entityManager->flush();
			}

			try{
				$adminGroup = $this->userManager->getUserGroupBySlug(BaseUser::GROUP_ADMIN);
			}catch (NoResultException|NonUniqueFieldNameException $e){
				$adminGroup = new UserGroup('Admin', BaseUser::GROUP_ADMIN);
				$adminGroup->setDefault(true);

				$this->entityManager->persist($adminGroup)->flush();

				$groupLangCS = new UserGroupLang($adminGroup, 'cs', 'Admin');
				$this->entityManager->persist($groupLangCS);
				$adminGroup->addName($groupLangCS);

				$groupLangEN = new UserGroupLang($adminGroup, 'en', 'Admin');
				$this->entityManager->persist($groupLangEN);
				$adminGroup->addName($groupLangEN);
				
				$adminGroup->addRole($adminRole);

				$this->entityManager->flush();
			}

			$output->writeln('Creating user: ' . $userName);

			$user = $this->userManager->createUser($userName, $password, $superAdminGroup);

			Helpers::terminalRenderSuccess('Success');

			return Command::SUCCESS;
		} catch (\Throwable $e) {
			Debugger::log($e);
			Helpers::terminalRenderError($e->getMessage() . ' | file: ' . $e->getFile() ?? '' . ' | line: ' . $e->getLine() ?? '');
		}

		return Command::FAILURE;
	}

}