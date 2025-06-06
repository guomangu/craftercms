<?php
/**
 * @link https://craftcms.com/
 * @copyright Copyright (c) Pixel & Tonic, Inc.
 * @license https://craftcms.github.io/license/
 */

namespace craft\base;

use craft\enums\CmsEdition;

/**
 * PluginTrait implements the common methods and properties for plugin classes.
 *
 * @author Pixel & Tonic, Inc. <support@pixelandtonic.com>
 * @since 3.0.0
 */
trait PluginTrait
{
    /**
     * @var string|null The plugin’s package name
     */
    public ?string $packageName = null;

    /**
     * @var string|null The plugin’s display name
     */
    public ?string $name = null;

    /**
     * @var string The plugin’s schema version number
     */
    public string $schemaVersion = '1.0.0';

    /**
     * @var string|null The plugin’s description
     */
    public ?string $description = null;

    /**
     * @var string|null The plugin developer’s name
     */
    public ?string $developer = null;

    /**
     * @var string|null The plugin developer’s website URL
     */
    public ?string $developerUrl = null;

    /**
     * @var string|null The plugin developer’s support email
     */
    public ?string $developerEmail = null;

    /**
     * @var string|null The plugin’s documentation URL
     */
    public ?string $documentationUrl = null;

    /**
     * @var string|null The plugin’s changelog URL.
     *
     * The URL should begin with `https://` and point to a plain text Markdown-formatted changelog.
     * Version headers must follow the general format:
     *
     * ```
     * ## X.Y.Z - YYYY-MM-DD
     * ```
     *
     * with the following possible deviations:
     *
     * - other text can come before the version number, like the plugin’s name
     * - a 4th version number is allowed (e.g. `1.2.3.4`)
     * - pre-release versions are allowed (e.g. `1.0.0-alpha.1`)
     * - the version can start with `v` (e.g. `v1.2.3`)
     * - the version can be hyperlinked (e.g. `[1.2.3]`)
     * - dates can use dots as separators, rather than hyphens (e.g. `YYYY.MM.DD`)
     * - a `[CRITICAL]` flag can be appended after the date to indicate a critical release
     *
     * More notes:
     *
     * - Releases should be listed in descending order (newest on top). Craft will stop parsing the changelog as soon as it hits a version that is older than or equal to the installed version.
     * - Any content that does not follow a version header line will be ignored.
     * - For consistency and clarity, release notes should follow [keepachangelog.com](http://keepachangelog.com/), but it’s not enforced.
     * - Release notes can contain notes using the format `> {note} Some note`. `{warning}` and `{tip}` are also supported.
     */
    public ?string $changelogUrl = null;

    /**
     * @var string|null The plugin’s download URL
     */
    public ?string $downloadUrl = null;

    /**
     * @var string|null The translation category that this plugin’s translation messages should use. Defaults to the lowercased plugin handle.
     */
    public ?string $t9nCategory = null;

    /**
     * @var string The language that the plugin’s messages were written in
     */
    public string $sourceLanguage = 'en-US';

    /**
     * @var bool Whether the plugin has a settings page in the control panel
     */
    public bool $hasCpSettings = false;

    /**
     * @var bool Whether the plugin supports a read-only settings page in the control panel, which
     * can be shown when admin changes are disallowed.
     * @since 5.6.0
     */
    public bool $hasReadOnlyCpSettings = false;

    /**
     * @var bool Whether the plugin has its own section in the control panel
     */
    public bool $hasCpSection = false;

    /**
     * @var bool Whether the plugin is currently installed. (Will only be false when a plugin is currently being installed.)
     */
    public bool $isInstalled = false;

    /**
     * @var string The minimum required version the plugin has to be so it can be updated.
     */
    public string $minVersionRequired = '';

    /**
     * @var CmsEdition The minimum required Craft CMS edition.
     * @since 5.0.0
     */
    public CmsEdition $minCmsEdition = CmsEdition::Solo;

    /**
     * @var string The active edition.
     */
    public string $edition = 'standard';
}
