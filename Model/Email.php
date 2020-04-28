<?php

/*
 * This file is part of the Polymorph.
 *
 * (c) Mlanawo Mbechezi
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Polymorph\Component\User\Model;

abstract class Email
{
    protected $id;

    /**
     * @var bool
     */
    protected $main;

    /**
     * @var string
     */
    protected $email;

    /**
     * @var \DateTime
     */
    protected $createdAt;

    /**
     * @var User
     */
    protected $user;

    public function __construct(string $email = '')
    {
        $this->email = $email;
        $this->main = false;
        $this->createdAt = new \DateTime();
    }

    public function getId()
    {
        return $this->id;
    }

    /**
     * @return Email
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    public function isMain(): bool
    {
        return $this->main;
    }

    /**
     * @return Email
     */
    public function setMain(bool $main): self
    {
        $this->main = $main;

        return $this;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @return Email
     */
    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTime $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): void
    {
        $this->user = $user;
    }
}
