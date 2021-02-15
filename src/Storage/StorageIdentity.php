<?php

declare(strict_types=1);

namespace SatraGlobalPackage\User;


use Nette\Security\IIdentity;

class StorageIdentity implements IIdentity
{

	/**
	 * @var string|null
	 */
	private $id;

	/**
	 * @var string
	 */
	private $class;

	/**
	 * @var BaseUser|null
	 */
	private $user;

	/**
	 * StorageIdentity constructor.
	 * @param string|null $id
	 * @param BaseUser $user
	 */
	public function __construct(?string $id, BaseUser $user)
	{
		$this->id = $id;
		$this->class = get_class($user);
		$this->user = $user;
	}

	/**
	 * @return string|null
	 */
	public function getId(): ?string
	{
		return $this->id;
	}

	/**
	 * @return string
	 */
	public function getClass(): string
	{
		return $this->class;
	}

	/**
	 * @return string[]
	 */
	public function getRoles(): array
	{
		return [];
	}

	/**
	 * @return BaseUser
	 * @throws UserException
	 */
	public function getUser(): BaseUser
	{
		if($this->user === null || !$this->user instanceof BaseUser){
			throw new UserException('User entity doesn\'t loaded!');
		}

		return $this->user;
	}

	/**
	 * @param BaseUser $user
	 */
	public function setUser(BaseUser $user): void
	{
		$this->user = $user;
	}

	/**
	 * @return array<string>|string[]
	 */
	public function __serialize(): array
	{
		return [
			'id' => $this->id,
			'class' => $this->class,
		];
	}

	/**
	 * @param array|string[] $data
	 */
	public function __unserialize(array $data): void
	{
		$this->id = $data['id'] ?? null;
		$this->class = $data['class'] ?? null;
		$this->user = null;
	}

}
