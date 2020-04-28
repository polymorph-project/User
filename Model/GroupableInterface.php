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

/**
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 * @author Christophe Coevoet <stof@notk.org>
 */
interface GroupableInterface
{
    /**
     * Gets the groups granted to the user.
     */
    public function getGroups(): iterable;

    /**
     * Gets the name of the groups which includes the user.
     */
    public function getGroupNames(): iterable;

    /**
     * Indicates whether the user belongs to the specified group or not.
     *
     * @param string $name Name of the group
     *
     * @return bool
     */
    public function hasGroup(string $name);

    /**
     * Add a group to the user groups.
     *
     * @return self
     */
    public function addGroup(GroupInterface $group);

    /**
     * Remove a group from the user groups.
     *
     * @return self
     */
    public function removeGroup(GroupInterface $group);
}
