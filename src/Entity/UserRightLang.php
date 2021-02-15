<?php

declare(strict_types=1);

namespace MatiCore\User;

use Baraja\Doctrine\UUID\UuidIdentifier;
use Doctrine\ORM\Mapping as ORM;
use Nette\SmartObject;

/**
 * Class UserRightLang
 * @package MatiCore\User
 * @ORM\Entity()
 * @ORM\Table(name="user__user_right_lang",
 *    uniqueConstraints={
 *        @ORM\UniqueConstraint(
 *                name="right_lang_unique",
 *                columns={"right_id", "lang"}
 *          )
 *    }
 * )
 */
class UserRightLang
{

	use UuidIdentifier;
	use SmartObject;

	/**
	 * @var UserRight
	 * @ORM\ManyToOne(targetEntity="\MatiCore\User\UserRight", inversedBy="names")
	 * @ORM\JoinColumn(name="right_id")
	 */
	private $right;

	/**
	 * @var string
	 * @ORM\Column(type="string")
	 */
	private $lang;

	/**
	 * @var string
	 * @ORM\Column(type="string")
	 */
	private $name;

	/**
	 * UserRightLang constructor.
	 * @param UserRight $right
	 * @param string $lang
	 * @param string $name
	 */
	public function __construct(UserRight $right, string $lang, string $name)
	{
		$this->right = $right;
		$this->lang = $lang;
		$this->name = $name;
	}

	/**
	 * @return UserRight
	 */
	public function getRight(): UserRight
	{
		return $this->right;
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