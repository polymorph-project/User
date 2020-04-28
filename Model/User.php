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

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * Storage agnostic user object.
 *
 * @author Mlanawo Mbechezi <mlanawo.mbechezi@kemeter.io>
 */
abstract class User implements UserInterface, GroupableInterface
{
    public const ROLE_DEFAULT = 'ROLE_USER';
    public const ROLE_SUPER_ADMIN = 'ROLE_SUPER_ADMIN';

    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $username;

    /**
     * @var string
     */
    protected $slug;

    /**
     * @var Collection
     */
    protected $emails;

    /**
     * @var bool
     */
    protected $enabled;

    /**
     * The salt to use for hashing.
     *
     * @var string
     */
    protected $salt;

    /**
     * Encrypted password. Must be persisted.
     *
     * @var string
     */
    protected $password;

    /**
     * Plain password. Used for model validation. Must not be persisted.
     *
     * @var string
     */
    protected $plainPassword;

    /**
     * @var \DateTime
     */
    protected $lastLogin;

    /**
     * Random string sent to the user email address in order to verify it.
     *
     * @var string
     */
    protected $confirmationToken;

    /**
     * @var \DateTime
     */
    protected $passwordRequestedAt;

    /**
     * @var Collection
     */
    protected $groups;

    /**
     * @var bool
     */
    protected $locked;

    /**
     * @var bool
     */
    protected $expired;

    /**
     * @var \DateTime
     */
    protected $expiresAt;

    /**
     * @var \DateTime
     */
    protected $createdAt;

    /**
     * @var \DateTime
     */
    protected $updatedAt;

    /**
     * @var array
     */
    protected $roles;

    /**
     * @var bool
     */
    protected $credentialsExpired;

    /**
     * @var \DateTime
     */
    protected $credentialsExpireAt;

    public function __construct()
    {
        $this->enabled = false;
        $this->locked = false;
        $this->expired = false;
        $this->roles = [];
        $this->credentialsExpired = false;
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
        $this->emails = new ArrayCollection();
    }

    /**
     * Returns the user unique id.
     */
    public function getId()
    {
        return $this->id;
    }

    public function addRole(string $role)
    {
        $role = mb_strtoupper($role);

        if ($role === static::ROLE_DEFAULT) {
            return $this;
        }

        if (!\in_array($role, $this->roles, true)) {
            $this->roles[] = $role;
        }

        return $this;
    }

    /**
     * Removes sensitive data from the user.
     */
    public function eraseCredentials(): void
    {
        $this->plainPassword = null;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function getSalt(): ?string
    {
        return $this->salt;
    }

    public function addEmail(Email $email)
    {
        if (!$this->emails->contains($email)) {
            if ($email->isMain()) {
                foreach ($this->emails as $mail) {
                    $mail->setMain(false);
                }
            }

            if (0 === $this->emails->count()) {
                $email->setMain(true);
            }

            $this->emails->add($email);
        }
    }

    public function getEmail(): ?string
    {
        foreach ($this->emails as $email) {
            if ($email->isMain()) {
                return $email->getEmail();
            }
        }

        return null;
    }

    public function getEmails(): Collection
    {
        return $this->emails;
    }

    /**
     * Gets the encrypted password.
     *
     * @return string
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    /**
     * Gets the last login time.
     *
     * @return \DateTime
     */
    public function getLastLogin(): ?\DateTimeInterface
    {
        return $this->lastLogin;
    }

    public function getConfirmationToken(): ?string
    {
        return $this->confirmationToken;
    }

    /**
     * Returns the user roles.
     *
     * @return array The roles
     */
    public function getRoles(): array
    {
        $roles = $this->roles;

        foreach ($this->getGroups() as $group) {
            $roles = array_merge($roles, $group->getRoles());
        }

        // we need to make sure to have at least one role
        $roles[] = static::ROLE_DEFAULT;

        return array_unique($roles);
    }

    /**
     * Never use this to check if this user has access to anything!
     *
     * Use the SecurityContext, or an implementation of AccessDecisionManager
     * instead, e.g.
     *
     *         $securityContext->isGranted('ROLE_USER');
     */
    public function hasRole(string $role): bool
    {
        return \in_array(mb_strtoupper($role), $this->getRoles(), true);
    }

    public function isAccountNonExpired()
    {
        if (true === $this->expired) {
            return false;
        }

        if (null !== $this->expiresAt && $this->expiresAt->getTimestamp() < time()) {
            return false;
        }

        return true;
    }

    public function isAccountNonLocked(): bool
    {
        return !$this->locked;
    }

    public function isCredentialsNonExpired(): bool
    {
        if (true === $this->credentialsExpired) {
            return false;
        }

        if (null !== $this->credentialsExpireAt && $this->credentialsExpireAt->getTimestamp() < time()) {
            return false;
        }

        return true;
    }

    public function isCredentialsExpired(): bool
    {
        return !$this->isCredentialsNonExpired();
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function isExpired(): bool
    {
        return !$this->isAccountNonExpired();
    }

    public function isLocked(): bool
    {
        return !$this->isAccountNonLocked();
    }

    public function isSuperAdmin(): bool
    {
        return $this->hasRole(static::ROLE_SUPER_ADMIN);
    }

    public function removeRole(string $role)
    {
        if (false !== $key = array_search(mb_strtoupper($role), $this->roles, true)) {
            unset($this->roles[$key]);
            $this->roles = array_values($this->roles);
        }

        return $this;
    }

    public function setUsername(string $username)
    {
        $this->username = $username;

        return $this;
    }

    public function setSlug(string $slug)
    {
        $this->slug = $slug;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    /**
     * @param \DateTimeInterface $date
     */
    public function setCredentialsExpireAt(\DateTimeInterface $date = null): self
    {
        $this->credentialsExpireAt = $date;

        return $this;
    }

    public function getCredentialsExpireAt(): ?\DateTimeInterface
    {
        return $this->credentialsExpireAt;
    }

    /**
     * @param bool $boolean
     */
    public function setCredentialsExpired($boolean): self
    {
        $this->credentialsExpired = $boolean;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }

    /**
     * {@inheritdoc}
     */
    public function setUpdatedAt(\DateTimeInterface $updatedAt)
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * {@inheritdoc}
     */
    public function getUpdatedAt(): \DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setEnabled(bool $boolean)
    {
        $this->enabled = (bool) $boolean;

        return $this;
    }

    public function getExpiresAt(): ?\DateTimeInterface
    {
        return $this->expiresAt;
    }

    /**
     * Sets this user to expired.
     *
     * @return User
     */
    public function setExpired(bool $boolean)
    {
        $this->expired = $boolean;

        return $this;
    }

    /**
     * @param \DateTime $date
     *
     * @return User
     */
    public function setExpiresAt(\DateTimeInterface $date = null)
    {
        $this->expiresAt = $date;

        return $this;
    }

    public function setPassword(string $password)
    {
        $this->password = $password;

        return $this;
    }

    public function setSalt(string $salt)
    {
        $this->salt = $salt;

        return $this;
    }

    public function setSuperAdmin(bool $boolean)
    {
        if ($boolean) {
            $this->addRole(static::ROLE_SUPER_ADMIN);
        } else {
            $this->removeRole(static::ROLE_SUPER_ADMIN);
        }

        return $this;
    }

    public function setPlainPassword(string $password)
    {
        $this->plainPassword = $password;

        return $this;
    }

    public function setLastLogin(\DateTimeInterface $time = null)
    {
        $this->lastLogin = $time;

        return $this;
    }

    public function setLocked(bool $boolean)
    {
        $this->locked = $boolean;

        return $this;
    }

    public function setConfirmationToken(string $confirmationToken)
    {
        $this->confirmationToken = $confirmationToken;

        return $this;
    }

    public function setPasswordRequestedAt(\DateTimeInterface $date = null)
    {
        $this->passwordRequestedAt = $date;

        return $this;
    }

    /**
     * Gets the timestamp that the user requested a password reset.
     */
    public function getPasswordRequestedAt(): ?\DateTimeInterface
    {
        return $this->passwordRequestedAt;
    }

    public function isPasswordRequestNonExpired($ttl)
    {
        return $this->getPasswordRequestedAt() instanceof \DateTimeInterface &&
        $this->getPasswordRequestedAt()->getTimestamp() + $ttl > time();
    }

    public function setRoles(array $roles)
    {
        $this->roles = [];

        foreach ($roles as $role) {
            $this->addRole($role);
        }

        return $this;
    }

    /**
     * Gets the groups granted to the user.
     */
    public function getGroups(): iterable
    {
        return $this->groups ?: $this->groups = new ArrayCollection();
    }

    public function getGroupNames(): array
    {
        $names = [];
        foreach ($this->getGroups() as $group) {
            $names[] = $group->getName();
        }

        return $names;
    }

    public function hasGroup(string $name): bool
    {
        return \in_array($name, $this->getGroupNames(), true);
    }

    public function addGroup(GroupInterface $group)
    {
        if (!$this->getGroups()->contains($group)) {
            $this->getGroups()->add($group);
        }

        return $this;
    }

    public function removeGroup(GroupInterface $group)
    {
        if ($this->getGroups()->contains($group)) {
            $this->getGroups()->removeElement($group);
        }

        return $this;
    }

    public function __toString()
    {
        return (string) $this->getUsername();
    }
}
