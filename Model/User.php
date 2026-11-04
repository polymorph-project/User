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
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

/**
 * Storage agnostic user object.
 *
 * @author Mlanawo Mbechezi <mlanawo.mbechezi@kemeter.io>
 */
abstract class User implements UserInterface, GroupableInterface, PasswordAuthenticatedUserInterface, \Stringable
{
    public const ROLE_DEFAULT = 'ROLE_USER';
    public const ROLE_SUPER_ADMIN = 'ROLE_SUPER_ADMIN';

    /**
     * @var string|null
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
     * @var \DateTimeInterface|null
     */
    protected $lastLogin;

    /**
     * Random string sent to the user email address in order to verify it.
     *
     * @var string
     */
    protected $confirmationToken;

    /**
     * @var \DateTimeInterface|null
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
     * @var \DateTimeInterface|null
     */
    protected $createdAt;

    /**
     * @var \DateTimeInterface|null
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
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTime();
        $this->emails = new ArrayCollection();
    }

    /**
     * Returns the user unique id.
     */
    public function getId(): ?string
    {
        return $this->id;
    }

    public function addRole(string $role): UserInterface
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

    public function getUserIdentifier(): string
    {
        return $this->username ?? '';
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function getSalt(): ?string
    {
        return $this->salt;
    }

    public function addEmail(Email $email): void
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

            // owning side of the relation, otherwise the email is stored without its user
            $email->setUser($this);
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
     */
    public function getLastLogin(): ?\DateTime
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

    public function removeRole(string $role): UserInterface
    {
        if (false !== $key = array_search(mb_strtoupper($role), $this->roles, true)) {
            unset($this->roles[$key]);
            $this->roles = array_values($this->roles);
        }

        return $this;
    }

    public function setUsername(string $username): UserInterface
    {
        $this->username = $username;

        return $this;
    }

    public function setSlug(string $slug): UserInterface
    {
        $this->slug = $slug;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setCredentialsExpireAt(?\DateTimeInterface $date = null): self
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
    public function setUpdatedAt(\DateTimeInterface $updatedAt): void
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

    public function setEnabled(bool $boolean): UserInterface
    {
        $this->enabled = $boolean;

        return $this;
    }

    public function getExpiresAt(): ?\DateTimeInterface
    {
        return $this->expiresAt;
    }

    /**
     * Sets this user to expired.
     */
    public function setExpired(bool $boolean): self
    {
        $this->expired = $boolean;

        return $this;
    }

    /**
     * @param \DateTime $date
     */
    public function setExpiresAt(?\DateTimeInterface $date = null): self
    {
        $this->expiresAt = $date;

        return $this;
    }

    public function setPassword(string $password): UserInterface
    {
        $this->password = $password;

        return $this;
    }

    public function setSalt(string $salt)
    {
        $this->salt = $salt;

        return $this;
    }

    public function setSuperAdmin(bool $boolean): UserInterface
    {
        if ($boolean) {
            $this->addRole(static::ROLE_SUPER_ADMIN);
        } else {
            $this->removeRole(static::ROLE_SUPER_ADMIN);
        }

        return $this;
    }

    public function setPlainPassword(string $password): UserInterface
    {
        $this->plainPassword = $password;
        // plainPassword is not persisted: touch a mapped field so the change is flushed and hashed
        $this->updatedAt = new \DateTime();

        return $this;
    }

    public function setLastLogin(?\DateTimeInterface $time = null): UserInterface
    {
        $this->lastLogin = $time;

        return $this;
    }

    public function setLocked(bool $boolean): UserInterface
    {
        $this->locked = $boolean;

        return $this;
    }

    public function setConfirmationToken(string $confirmationToken): UserInterface
    {
        $this->confirmationToken = $confirmationToken;

        return $this;
    }

    public function setPasswordRequestedAt(?\DateTimeInterface $date = null): UserInterface
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

    public function isPasswordRequestNonExpired($ttl): bool
    {
        return $this->getPasswordRequestedAt() instanceof \DateTimeInterface
        && $this->getPasswordRequestedAt()->getTimestamp() + $ttl > time();
    }

    public function setRoles(array $roles): UserInterface
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

    public function addGroup(GroupInterface $group): GroupableInterface
    {
        if (!$this->getGroups()->contains($group)) {
            $this->getGroups()->add($group);
        }

        return $this;
    }

    public function removeGroup(GroupInterface $group): GroupableInterface
    {
        if ($this->getGroups()->contains($group)) {
            $this->getGroups()->removeElement($group);
        }

        return $this;
    }

    public function __toString(): string
    {
        return (string) $this->getUsername();
    }
}
