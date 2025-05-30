<?php
/**
 * @link https://craftcms.com/
 * @copyright Copyright (c) Pixel & Tonic, Inc.
 * @license https://craftcms.github.io/license/
 */

namespace craft\events;

use craft\base\Event;

/**
 * RegisterUserPermissionsEvent class.
 *
 * @author Pixel & Tonic, Inc. <support@pixelandtonic.com>
 * @since 3.0.0
 */
class RegisterUserPermissionsEvent extends Event
{
    /**
     * @var array List of registered user permissions.
     */
    public array $permissions = [];
}
