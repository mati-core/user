<?php

declare(strict_types=1);

namespace MatiCore\User;

use Baraja\Doctrine\UUID\UuidIdentifier;
use Doctrine\ORM\Mapping as ORM;
use Nette\SmartObject;

/**
 * Class UserRoleLang
 * @package MatiCore\User
 * @ORM\Entity()
 * @ORM\Table(name="user__user_role_lang",
 *    uniqueConstraints={
 *        @ORM\UniqueConstraint(
 *                name="role_lang_unique",
 *                columns={"role_id", "lang"}
 *          )
 *    }
 * )
 */
class UserRoleLang
{

	use UuidIdentifier;
	use SmartObject;

	/**
	 * @var UserRole
	 * @ORM\ManyToOne(targetEntity="\MatiCore\User\UserRole", inversedBy="names", cascade={"persist", "remove"})
	 * @ORM\JoinColumn(name="role_id")
	 */
	private $userRole;

	/**
	 * @var string
	 * @ORM\Column(name="lang", type="string")
	 */
	private $lang;

	/**
	 * @var string
	 * @ORM\Column(name="name", type="string")
	 */
	private $name;

	/**
	 * UserRoleLang constructor.
	 * @param UserRole $userRole
	 * @param string $lang
	 * @param string $name
	 */
	public function __construct(UserRole $userRole, string $lang, string $name)
	{
		$this->userRole = $userRole;
		$this->lang = $lang;
		$this->name = $name;
	}

	/**
	 * @return UserRole
	 */
	public function getUserRole(): UserRole
	{
		return $this->userRole;
	}

	/**
	 * @return string
	 */
	public function getLang(): string
	{
		return $this->lang;
	}

	/**
	 * @return string
	 */
	public function getName(): string
	{
		return $this->name;
	}

	/**
	 * @param string $name
	 */
	public function setName(string $name): void
	{
		$this->name = $name;
	}

}