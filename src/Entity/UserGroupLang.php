<?php

declare(strict_types=1);

namespace MatiCore\User;

use Baraja\Doctrine\UUID\UuidIdentifier;
use Doctrine\ORM\Mapping as ORM;
use Nette\SmartObject;

/**
 * Class UserGroupLang
 * @package MatiCore\User
 * @ORM\Entity()
 * @ORM\Table(name="user__user_group_lang",
 *    uniqueConstraints={
 *        @ORM\UniqueConstraint(
 *     			name="group_lang_unique",
 *            	columns={"group_id", "lang"}
 *	 	  )
 *    }
 * )
 */
class UserGroupLang
{

	use UuidIdentifier;
	use SmartObject;

	/**
	 * @var UserGroup
	 * @ORM\ManyToOne(targetEntity="\MatiCore\User\UserGroup", inversedBy="names")
	 * @ORM\JoinColumn(name="group_id")
	 */
	private $userGroup;

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
	 * UserGroupLang constructor.
	 * @param UserGroup $userGroup
	 * @param string $lang
	 * @param string $name
	 */
	public function __construct(UserGroup $userGroup, string $lang, string $name)
	{
		$this->userGroup = $userGroup;
		$this->name = $name;
		$this->lang = $lang;
	}

	/**
	 * @return UserGroup
	 */
	public function getUserGroup(): UserGroup
	{
		return $this->userGroup;
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

	/**
	 * @return string
	 */
	public function getLang(): string
	{
		return $this->lang;
	}

}