<?php
/**
 * @link https://craftcms.com/
 * @copyright Copyright (c) Pixel & Tonic, Inc.
 * @license https://craftcms.github.io/license/
 */

namespace craft\elements\db;

use craft\base\ElementInterface;
use craft\db\Query;
use craft\elements\ElementCollection;
use craft\models\FieldLayout;
use yii\base\Arrayable;
use yii\db\Connection;
use yii\db\QueryInterface;

/**
 * ElementQueryInterface defines the common interface to be implemented by element query classes.
 * The default implementation of this interface is provided by [[ElementQuery]].
 *
 * @mixin Query
 * @mixin ElementQuery
 * @phpstan-require-extends ElementQuery
 * @author Pixel & Tonic, Inc. <support@pixelandtonic.com>
 * @since 3.0.0
 */
interface ElementQueryInterface extends QueryInterface, Arrayable
{
    /**
     * Causes the query results to be returned in reverse order.
     *
     * ---
     *
     * ```twig
     * {# Fetch {elements} in reverse #}
     * {% set {elements-var} = {twig-method}
     *   .inReverse()
     *   .all() %}
     * ```
     *
     * ```php
     * // Fetch {elements} in reverse
     * ${elements-var} = {php-method}
     *     ->inReverse()
     *     ->all();
     * ```
     *
     * @param bool $value The property value
     * @return static self reference
     */
    public function inReverse(bool $value = true): static;

    /**
     * Causes the query to return matching {elements} as arrays of data, rather than [[{element-class}]] objects.
     *
     * ---
     *
     * ```twig
     * {# Fetch {elements} as arrays #}
     * {% set {elements-var} = {twig-method}
     *   .asArray()
     *   .all() %}
     * ```
     *
     * ```php
     * // Fetch {elements} as arrays
     * ${elements-var} = {php-method}
     *     ->asArray()
     *     ->all();
     * ```
     *
     * @param bool $value The property value (defaults to true)
     * @return static self reference
     */
    public function asArray(bool $value = true): static;

    /**
     * Causes the query to return provisional drafts for the matching elements,
     * when they exist for the current user.
     *
     * @param bool $value The property value (defaults to true)
     * @return static self reference
     * @since 5.6.0
     */
    public function withProvisionalDrafts(bool $value = true): static;

    /**
     * Causes the query to return matching {elements} as they are stored in the database, ignoring matching placeholder
     * elements that were set by [[\craft\services\Elements::setPlaceholderElement()]].
     *
     * @param bool $value The property value (defaults to true)
     * @return static self reference
     * @since 3.2.9
     */
    public function ignorePlaceholders(bool $value = true): static;

    /**
     * Narrows the query results to only drafts {elements}.
     *
     * ---
     *
     * ```twig
     * {# Fetch a draft {element} #}
     * {% set {elements-var} = {twig-method}
     *   .drafts()
     *   .id(123)
     *   .one() %}
     * ```
     *
     * ```php
     * // Fetch a draft {element}
     * ${elements-var} = {element-class}::find()
     *     ->drafts()
     *     ->id(123)
     *     ->one();
     * ```
     *
     * @param bool|null $value The property value (defaults to true)
     * @return static self reference
     * @since 3.2.0
     */
    public function drafts(?bool $value = true): static;

    /**
     * Narrows the query results based on the {elements}’ draft’s ID (from the `drafts` table).
     *
     * Possible values include:
     *
     * | Value | Fetches drafts…
     * | - | -
     * | `1` | for the draft with an ID of 1.
     *
     * ---
     *
     * ```twig
     * {# Fetch a draft #}
     * {% set {elements-var} = {twig-method}
     *   .draftId(10)
     *   .all() %}
     * ```
     *
     * ```php
     * // Fetch a draft
     * ${elements-var} = {php-method}
     *     ->draftId(10)
     *     ->all();
     * ```
     *
     * @param int|null $value The property value
     * @return static self reference
     * @since 3.2.0
     */
    public function draftId(?int $value = null): static;

    /**
     * Narrows the query results to only drafts of a given {element}.
     *
     * Possible values include:
     *
     * | Value | Fetches drafts…
     * | - | -
     * | `1` | for the {element} with an ID of 1.
     * | `[1, 2]` | for the {elements} with an ID of 1 or 2.
     * | a [[{element-class}]] object | for the {element} represented by the object.
     * | an array of [[{element-class}]] objects | for the {elements} represented by the objects.
     * | `'*'` | for any {element}
     * | `false` | that aren’t associated with a published {element}
     *
     * ---
     *
     * ```twig
     * {# Fetch drafts of the {element} #}
     * {% set {elements-var} = {twig-method}
     *   .draftOf({myElement})
     *   .all() %}
     * ```
     *
     * ```php
     * // Fetch drafts of the {element}
     * ${elements-var} = {php-method}
     *     ->draftOf(${myElement})
     *     ->all();
     * ```
     *
     * @param mixed $value The property value
     * @return static self reference
     * @since 3.2.0
     */
    public function draftOf(mixed $value): static;

    /**
     * Narrows the query results to only drafts created by a given user.
     *
     * Possible values include:
     *
     * | Value | Fetches drafts…
     * | - | -
     * | `1` | created by the user with an ID of 1.
     * | a [[User]] object | created by the user represented by the object.
     *
     * ---
     *
     * ```twig
     * {# Fetch drafts by the current user #}
     * {% set {elements-var} = {twig-method}
     *   .draftCreator(currentUser)
     *   .all() %}
     * ```
     *
     * ```php
     * // Fetch drafts by the current user
     * ${elements-var} = {php-method}
     *     ->draftCreator(Craft::$app->user->identity)
     *     ->all();
     * ```
     *
     * @param mixed $value The property value
     * @return static self reference
     * @since 3.2.0
     */
    public function draftCreator(mixed $value): static;

    /**
     * Narrows the query results to only provisional drafts.
     *
     * ---
     *
     * ```twig
     * {# Fetch provisional drafts created by the current user #}
     * {% set {elements-var} = {twig-method}
     *   .provisionalDrafts()
     *   .draftCreator(currentUser)
     *   .all() %}
     * ```
     *
     * ```php
     * // Fetch provisional drafts created by the current user
     * ${elements-var} = {php-method}
     *     ->provisionalDrafts()
     *     ->draftCreator(Craft::$app->user->identity)
     *     ->all();
     * ```
     *
     * @param bool|null $value The property value
     * @return static self reference
     * @since 3.7.0
     */
    public function provisionalDrafts(?bool $value = true): static;

    /**
     * Narrows the query results to only canonical elements, including elements
     * that reference another canonical element via `canonicalId` so long as they
     * aren’t a draft.
     *
     * Unpublished drafts can be included as well if `drafts(null)` and
     * `draftOf(false)` are also passed.
     *
     * @param bool $value The property value
     * @return static self reference
     * @since 5.7.0
     */
    public function canonicalsOnly(bool $value = true): static;

    /**
     * Narrows the query results to only unpublished drafts which have been saved after initial creation.
     *
     * ---
     *
     * ```twig
     * {# Fetch saved, unpublished draft {elements} #}
     * {% set {elements-var} = {twig-method}
     *   .draftOf(false)
     *   .savedDraftsOnly()
     *   .all() %}
     * ```
     *
     * ```php
     * // Fetch saved, unpublished draft {elements}
     * ${elements-var} = {element-class}::find()
     *     ->draftOf(false)
     *     ->savedDraftsOnly()
     *     ->all();
     * ```
     *
     * @param bool $value The property value (defaults to true)
     * @return static self reference
     * @since 3.6.6
     */
    public function savedDraftsOnly(bool $value = true): static;

    /**
     * Narrows the query results to only revision {elements}.
     *
     * ---
     *
     * ```twig
     * {# Fetch a revision {element} #}
     * {% set {elements-var} = {twig-method}
     *   .revisions()
     *   .id(123)
     *   .one() %}
     * ```
     *
     * ```php
     * // Fetch a revision {element}
     * ${elements-var} = {element-class}::find()
     *     ->revisions()
     *     ->id(123)
     *     ->one();
     * ```
     *
     * @param bool|null $value The property value (defaults to true)
     * @return static self reference
     * @since 3.2.0
     */
    public function revisions(?bool $value = true): static;

    /**
     * Narrows the query results based on the {elements}’ revision’s ID (from the `revisions` table).
     *
     * Possible values include:
     *
     * | Value | Fetches revisions…
     * | - | -
     * | `1` | for the revision with an ID of 1.
     *
     * ---
     *
     * ```twig
     * {# Fetch a revision #}
     * {% set {elements-var} = {twig-method}
     *   .revisionId(10)
     *   .all() %}
     * ```
     *
     * ```php
     * // Fetch a revision
     * ${elements-var} = {php-method}
     *     ->revisionIf(10)
     *     ->all();
     * ```
     *
     * @param int|null $value The property value
     * @return static self reference
     * @since 3.2.0
     */
    public function revisionId(?int $value = null): static;

    /**
     * Narrows the query results to only revisions of a given {element}.
     *
     * Possible values include:
     *
     * | Value | Fetches revisions…
     * | - | -
     * | `1` | for the {element} with an ID of 1.
     * | a [[{element-class}]] object | for the {element} represented by the object.
     *
     * ---
     *
     * ```twig
     * {# Fetch revisions of the {element} #}
     * {% set {elements-var} = {twig-method}
     *   .revisionOf({myElement})
     *   .all() %}
     * ```
     *
     * ```php
     * // Fetch revisions of the {element}
     * ${elements-var} = {php-method}
     *     ->revisionOf(${myElement})
     *     ->all();
     * ```
     *
     * @param mixed $value The property value
     * @return static self reference
     * @since 3.2.0
     */
    public function revisionOf(mixed $value): static;

    /**
     * Narrows the query results to only revisions created by a given user.
     *
     * Possible values include:
     *
     * | Value | Fetches revisions…
     * | - | -
     * | `1` | created by the user with an ID of 1.
     * | a [[User]] object | created by the user represented by the object.
     *
     * ---
     *
     * ```twig
     * {# Fetch revisions by the current user #}
     * {% set {elements-var} = {twig-method}
     *   .revisionCreator(currentUser)
     *   .all() %}
     * ```
     *
     * ```php
     * // Fetch revisions by the current user
     * ${elements-var} = {php-method}
     *     ->revisionCreator(Craft::$app->user->identity)
     *     ->all();
     * ```
     *
     * @param mixed $value The property value
     * @return static self reference
     * @since 3.2.0
     */
    public function revisionCreator(mixed $value): static;

    /**
     * Narrows the query results based on the {elements}’ IDs.
     *
     * Possible values include:
     *
     * | Value | Fetches {elements}…
     * | - | -
     * | `1` | with an ID of 1.
     * | `'not 1'` | not with an ID of 1.
     * | `[1, 2]` | with an ID of 1 or 2.
     * | `['not', 1, 2]` | not with an ID of 1 or 2.
     *
     * ---
     *
     * ```twig
     * {# Fetch the {element} by its ID #}
     * {% set {element-var} = {twig-method}
     *   .id(1)
     *   .one() %}
     * ```
     *
     * ```php
     * // Fetch the {element} by its ID
     * ${element-var} = {php-method}
     *     ->id(1)
     *     ->one();
     * ```
     *
     * ---
     *
     * ::: tip
     * This can be combined with [[fixedOrder()]] if you want the results to be returned in a specific order.
     * :::
     *
     * @param mixed $value The property value
     * @return static self reference
     */
    public function id(mixed $value): static;

    /**
     * Narrows the query results based on the {elements}’ UIDs.
     *
     * ---
     *
     * ```twig
     * {# Fetch the {element} by its UID #}
     * {% set {element-var} = {twig-method}
     *   .uid('xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx')
     *   .one() %}
     * ```
     *
     * ```php
     * // Fetch the {element} by its UID
     * ${element-var} = {php-method}
     *     ->uid('xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx')
     *     ->one();
     * ```
     *
     * @param mixed $value The property value
     * @return static self reference
     */
    public function uid(mixed $value): static;

    /**
     * Narrows the query results based on the {elements}’ IDs in the `elements_sites` table.
     *
     * Possible values include:
     *
     * | Value | Fetches {elements}…
     * | - | -
     * | `1` | with an `elements_sites` ID of 1.
     * | `'not 1'` | not with an `elements_sites` ID of 1.
     * | `[1, 2]` | with an `elements_sites` ID of 1 or 2.
     * | `['not', 1, 2]` | not with an `elements_sites` ID of 1 or 2.
     *
     * ---
     *
     * ```twig
     * {# Fetch the {element} by its ID in the elements_sites table #}
     * {% set {element-var} = {twig-method}
     *   .siteSettingsId(1)
     *   .one() %}
     * ```
     *
     * ```php
     * // Fetch the {element} by its ID in the elements_sites table
     * ${element-var} = {php-method}
     *     ->siteSettingsId(1)
     *     ->one();
     * ```
     *
     * @param mixed $value The property value
     * @return static self reference
     * @since 3.7.0
     */
    public function siteSettingsId(mixed $value): static;

    /**
     * Causes the query results to be returned in the order specified by [[id()]].
     *
     * ::: tip
     * If no IDs were passed to [[id()]], setting this to `true` will result in an empty result set.
     * :::
     *
     * ---
     *
     * ```twig
     * {# Fetch {elements} in a specific order #}
     * {% set {elements-var} = {twig-method}
     *   .id([1, 2, 3, 4, 5])
     *   .fixedOrder()
     *   .all() %}
     * ```
     *
     * ```php
     * // Fetch {elements} in a specific order
     * ${elements-var} = {php-method}
     *     ->id([1, 2, 3, 4, 5])
     *     ->fixedOrder()
     *     ->all();
     * ```
     *
     * @param bool $value The property value (defaults to true)
     * @return static self reference
     */
    public function fixedOrder(bool $value = true): static;

    /**
     * Narrows the query results based on the {elements}’ statuses.
     *
     * Possible values include:
     *
     * | Value | Fetches {elements}…
     * | - | -
     * | `'enabled'`  _(default)_ | that are enabled.
     * | `'disabled'` | that are disabled.
     * | `['not', 'disabled']` | that are not disabled.
     *
     * ---
     *
     * ```twig
     * {# Fetch disabled {elements} #}
     * {% set {elements-var} = {twig-method}
     *   .status('disabled')
     *   .all() %}
     * ```
     *
     * ```php
     * // Fetch disabled {elements}
     * ${elements-var} = {php-method}
     *     ->status('disabled')
     *     ->all();
     * ```
     *
     * @param string|string[]|null $value The property value
     * @return static self reference
     */
    public function status(array|string|null $value): static;

    /**
     * Sets the [[$archived]] property.
     *
     * @param bool $value The property value (defaults to true)
     * @return static self reference
     */
    public function archived(bool $value = true): static;

    /**
     * Narrows the query results to only {elements} that have been soft-deleted.
     *
     * ---
     *
     * ```twig
     * {# Fetch trashed {elements} #}
     * {% set {elements-var} = {twig-method}
     *   .trashed()
     *   .all() %}
     * ```
     *
     * ```php
     * // Fetch trashed {elements}
     * ${elements-var} = {element-class}::find()
     *     ->trashed()
     *     ->all();
     * ```
     *
     * @param bool|null $value The property value (defaults to true)
     * @return static self reference
     * @since 3.1.0
     */
    public function trashed(?bool $value = true): static;

    /**
     * Narrows the query results based on the {elements}’ creation dates.
     *
     * Possible values include:
     *
     * | Value | Fetches {elements}…
     * | - | -
     * | `'>= 2018-04-01'` | that were created on or after 2018-04-01.
     * | `'< 2018-05-01'` | that were created before 2018-05-01.
     * | `['and', '>= 2018-04-04', '< 2018-05-01']` | that were created between 2018-04-01 and 2018-05-01.
     * | `now`/`today`/`tomorrow`/`yesterday` | that were created at midnight of the specified relative date.
     *
     * ---
     *
     * ```twig
     * {# Fetch {elements} created last month #}
     * {% set start = date('first day of last month')|atom %}
     * {% set end = date('first day of this month')|atom %}
     *
     * {% set {elements-var} = {twig-method}
     *   .dateCreated(['and', ">= #{start}", "< #{end}"])
     *   .all() %}
     * ```
     *
     * ```php
     * // Fetch {elements} created last month
     * $start = (new \DateTime('first day of last month'))->format(\DateTime::ATOM);
     * $end = (new \DateTime('first day of this month'))->format(\DateTime::ATOM);
     *
     * ${elements-var} = {php-method}
     *     ->dateCreated(['and', ">= {$start}", "< {$end}"])
     *     ->all();
     * ```
     *
     * @param mixed $value The property value
     * @return static self reference
     */
    public function dateCreated(mixed $value): static;

    /**
     * Narrows the query results based on the {elements}’ last-updated dates.
     *
     * Possible values include:
     *
     * | Value | Fetches {elements}…
     * | - | -
     * | `'>= 2018-04-01'` | that were updated on or after 2018-04-01.
     * | `'< 2018-05-01'` | that were updated before 2018-05-01.
     * | `['and', '>= 2018-04-04', '< 2018-05-01']` | that were updated between 2018-04-01 and 2018-05-01.
     * | `now`/`today`/`tomorrow`/`yesterday` | that were updated at midnight of the specified relative date.
     *
     * ---
     *
     * ```twig
     * {# Fetch {elements} updated in the last week #}
     * {% set lastWeek = date('1 week ago')|atom %}
     *
     * {% set {elements-var} = {twig-method}
     *   .dateUpdated(">= #{lastWeek}")
     *   .all() %}
     * ```
     *
     * ```php
     * // Fetch {elements} updated in the last week
     * $lastWeek = (new \DateTime('1 week ago'))->format(\DateTime::ATOM);
     *
     * ${elements-var} = {php-method}
     *     ->dateUpdated(">= {$lastWeek}")
     *     ->all();
     * ```
     *
     * @param mixed $value The property value
     * @return static self reference
     */
    public function dateUpdated(mixed $value): static;

    /**
     * Determines which site(s) the {elements} should be queried in.
     *
     * The current site will be used by default.
     *
     * Possible values include:
     *
     * | Value | Fetches {elements}…
     * | - | -
     * | `'foo'` | from the site with a handle of `foo`.
     * | `['foo', 'bar']` | from a site with a handle of `foo` or `bar`.
     * | `['not', 'foo', 'bar']` | not in a site with a handle of `foo` or `bar`.
     * | a [[Site]] object | from the site represented by the object.
     * | `'*'` | from any site.
     *
     * ::: tip
     * If multiple sites are specified, elements that belong to multiple sites will be returned multiple times. If you
     * only want unique elements to be returned, use [[unique()]] in conjunction with this.
     * :::
     *
     * ---
     *
     * ```twig
     * {# Fetch {elements} from the Foo site #}
     * {% set {elements-var} = {twig-method}
     *   .site('foo')
     *   .all() %}
     * ```
     *
     * ```php
     * // Fetch {elements} from the Foo site
     * ${elements-var} = {php-method}
     *     ->site('foo')
     *     ->all();
     * ```
     *
     * @param mixed $value The property value
     * @return static self reference
     */
    public function site(mixed $value): static;

    /**
     * Determines which site(s) the {elements} should be queried in, per the site’s ID.
     *
     * The current site will be used by default.
     *
     * Possible values include:
     *
     * | Value | Fetches {elements}…
     * | - | -
     * | `1` | from the site with an ID of `1`.
     * | `[1, 2]` | from a site with an ID of `1` or `2`.
     * | `['not', 1, 2]` | not in a site with an ID of `1` or `2`.
     * | `'*'` | from any site.
     *
     * ---
     *
     * ```twig
     * {# Fetch {elements} from the site with an ID of 1 #}
     * {% set {elements-var} = {twig-method}
     *   .siteId(1)
     *   .all() %}
     * ```
     *
     * ```php
     * // Fetch {elements} from the site with an ID of 1
     * ${elements-var} = {php-method}
     *     ->siteId(1)
     *     ->all();
     * ```
     *
     * @param mixed $value The property value
     * @return static self reference
     */
    public function siteId(mixed $value): static;

    /**
     * Determines which site(s) the {elements} should be queried in, based on their language.
     *
     * Possible values include:
     *
     * | Value | Fetches {elements}…
     * | - | -
     * | `'en'` | from sites with a language of `en`.
     * | `['en-GB', 'en-US']` | from sites with a language of `en-GB` or `en-US`.
     * | `['not', 'en-GB', 'en-US']` | not in sites with a language of `en-GB` or `en-US`.
     *
     * ::: tip
     * Elements that belong to multiple sites will be returned multiple times by default. If you
     * only want unique elements to be returned, use [[unique()]] in conjunction with this.
     * :::
     *
     * ---
     *
     * ```twig
     * {# Fetch {elements} from English sites #}
     * {% set {elements-var} = {twig-method}
     *   .language('en')
     *   .all() %}
     * ```
     *
     * ```php
     * // Fetch {elements} from English sites
     * ${elements-var} = {php-method}
     *     ->language('en')
     *     ->all();
     * ```
     *
     * @param mixed $value The property value
     * @return static
     * @since 4.9.0
     */
    public function language(mixed $value): self;

    /**
     * Determines whether only elements with unique IDs should be returned by the query.
     *
     * This should be used when querying elements from multiple sites at the same time, if “duplicate” results is not
     * desired.
     *
     * ---
     *
     * ```twig
     * {# Fetch unique {elements} across all sites #}
     * {% set {elements-var} = {twig-method}
     *   .site('*')
     *   .unique()
     *   .all() %}
     * ```
     *
     * ```php
     * // Fetch unique {elements} across all sites
     * ${elements-var} = {php-method}
     *     ->site('*')
     *     ->unique()
     *     ->all();
     * ```
     *
     * @param bool $value The property value (defaults to true)
     * @return static self reference
     * @since 3.2.0
     */
    public function unique(bool $value = true): static;

    /**
     * If [[unique()]] is set, this determines which site should be selected when querying multi-site elements.
     *
     * For example, if element “Foo” exists in Site A and Site B, and element “Bar” exists in Site B and Site C,
     * and this is set to `['c', 'b', 'a']`, then Foo will be returned for Site B, and Bar will be returned
     * for Site C.
     *
     * If this isn’t set, then preference goes to the current site.
     *
     * ---
     *
     * ```twig
     * {# Fetch unique {elements} from Site A, or Site B if they don’t exist in Site A #}
     * {% set {elements-var} = {twig-method}
     *   .site('*')
     *   .unique()
     *   .preferSites(['a', 'b'])
     *   .all() %}
     * ```
     *
     * ```php
     * // Fetch unique {elements} from Site A, or Site B if they don’t exist in Site A
     * ${elements-var} = {php-method}
     *     ->site('*')
     *     ->unique()
     *     ->preferSites(['a', 'b'])
     *     ->all();
     * ```
     *
     * @param array|null $value The property value
     * @return static self reference
     * @since 3.2.0
     */
    public function preferSites(?array $value = null): static;

    /**
     * Narrows the query results to only {elements} that are not related to certain other elements.
     *
     * See [Relations](https://craftcms.com/docs/5.x/system/relations.html) for a full explanation of how to work with this parameter.
     *
     * ---
     *
     * ```twig
     * {# Fetch all {elements} that are related to myEntry #}
     * {% set {elements-var} = {twig-method}
     *   .notRelatedTo(myEntry)
     *   .all() %}
     * ```
     *
     * ```php
     * // Fetch all {elements} that are related to $myEntry
     * ${elements-var} = {php-method}
     *     ->notRelatedTo($myEntry)
     *     ->all();
     * ```
     *
     * @param mixed $value The property value
     * @return static self reference
     * @since 5.4.0
     */
    public function notRelatedTo(mixed $value): static;

    /**
     * Narrows the query results to only {elements} that are not related to certain other elements.
     *
     * See [Relations](https://craftcms.com/docs/5.x/system/relations.html) for a full explanation of how to work with this parameter.
     *
     * ---
     *
     * ```twig
     * {# Fetch all {elements} that are related to myCategoryA and not myCategoryB #}
     * {% set {elements-var} = {twig-method}
     *   .relatedTo(myCategoryA)
     *   .andNotRelatedTo(myCategoryB)
     *   .all() %}
     * ```
     *
     * ```php
     * // Fetch all {elements} that are related to $myCategoryA and not $myCategoryB
     * ${elements-var} = {php-method}
     *     ->relatedTo($myCategoryA)
     *     ->andNotRelatedTo($myCategoryB)
     *     ->all();
     * ```
     *
     * @param mixed $value The property value
     * @return static self reference
     * @since 5.4.0
     */
    public function andNotRelatedTo(mixed $value): static;

    /**
     * Narrows the query results to only {elements} that are related to certain other elements.
     *
     * See [Relations](https://craftcms.com/docs/5.x/system/relations.html) for a full explanation of how to work with this parameter.
     *
     * ---
     *
     * ```twig
     * {# Fetch all {elements} that are related to myCategory #}
     * {% set {elements-var} = {twig-method}
     *   .relatedTo(myCategory)
     *   .all() %}
     * ```
     *
     * ```php
     * // Fetch all {elements} that are related to $myCategory
     * ${elements-var} = {php-method}
     *     ->relatedTo($myCategory)
     *     ->all();
     * ```
     *
     * @param mixed $value The property value
     * @return static self reference
     */
    public function relatedTo(mixed $value): static;

    /**
     * Narrows the query results to only {elements} that are related to certain other elements.
     *
     * See [Relations](https://craftcms.com/docs/5.x/system/relations.html) for a full explanation of how to work with this parameter.
     *
     * ---
     *
     * ```twig
     * {# Fetch all {elements} that are related to myCategoryA and myCategoryB #}
     * {% set {elements-var} = {twig-method}
     *   .relatedTo(myCategoryA)
     *   .andRelatedTo(myCategoryB)
     *   .all() %}
     * ```
     *
     * ```php
     * // Fetch all {elements} that are related to $myCategoryA and $myCategoryB
     * ${elements-var} = {php-method}
     *     ->relatedTo($myCategoryA)
     *     ->andRelatedTo($myCategoryB)
     *     ->all();
     * ```
     *
     * @param mixed $value The property value
     * @return static self reference
     * @since 3.6.11
     */
    public function andRelatedTo(mixed $value): static;

    /**
     * Narrows the query results based on the {elements}’ titles.
     *
     * Possible values include:
     *
     * | Value | Fetches {elements}…
     * | - | -
     * | `'Foo'` | with a title of `Foo`.
     * | `'Foo*'` | with a title that begins with `Foo`.
     * | `'*Foo'` | with a title that ends with `Foo`.
     * | `'*Foo*'` | with a title that contains `Foo`.
     * | `'not *Foo*'` | with a title that doesn’t contain `Foo`.
     * | `['*Foo*', '*Bar*']` | with a title that contains `Foo` or `Bar`.
     * | `['not', '*Foo*', '*Bar*']` | with a title that doesn’t contain `Foo` or `Bar`.
     *
     * ---
     *
     * ```twig
     * {# Fetch {elements} with a title that contains "Foo" #}
     * {% set {elements-var} = {twig-method}
     *   .title('*Foo*')
     *   .all() %}
     * ```
     *
     * ```php
     * // Fetch {elements} with a title that contains "Foo"
     * ${elements-var} = {php-method}
     *     ->title('*Foo*')
     *     ->all();
     * ```
     *
     * @param mixed $value The property value
     * @return static self reference
     */
    public function title(mixed $value): static;

    /**
     * Narrows the query results based on the {elements}’ slugs.
     *
     * Possible values include:
     *
     * | Value | Fetches {elements}…
     * | - | -
     * | `'foo'` | with a slug of `foo`.
     * | `'foo*'` | with a slug that begins with `foo`.
     * | `'*foo'` | with a slug that ends with `foo`.
     * | `'*foo*'` | with a slug that contains `foo`.
     * | `'not *foo*'` | with a slug that doesn’t contain `foo`.
     * | `['*foo*', '*bar*']` | with a slug that contains `foo` or `bar`.
     * | `['not', '*foo*', '*bar*']` | with a slug that doesn’t contain `foo` or `bar`.
     *
     * ---
     *
     * ```twig
     * {# Get the requested {element} slug from the URL #}
     * {% set requestedSlug = craft.app.request.getSegment(3) %}
     *
     * {# Fetch the {element} with that slug #}
     * {% set {element-var} = {twig-method}
     *   .slug(requestedSlug|literal)
     *   .one() %}
     * ```
     *
     * ```php
     * // Get the requested {element} slug from the URL
     * $requestedSlug = \Craft::$app->request->getSegment(3);
     *
     * // Fetch the {element} with that slug
     * ${element-var} = {php-method}
     *     ->slug(\craft\helpers\Db::escapeParam($requestedSlug))
     *     ->one();
     * ```
     *
     * @param mixed $value The property value
     * @return static self reference
     */
    public function slug(mixed $value): static;

    /**
     * Narrows the query results based on the {elements}’ URIs.
     *
     * Possible values include:
     *
     * | Value | Fetches {elements}…
     * | - | -
     * | `'foo'` | with a URI of `foo`.
     * | `'foo*'` | with a URI that begins with `foo`.
     * | `'*foo'` | with a URI that ends with `foo`.
     * | `'*foo*'` | with a URI that contains `foo`.
     * | `'not *foo*'` | with a URI that doesn’t contain `foo`.
     * | `['*foo*', '*bar*']` | with a URI that contains `foo` or `bar`.
     * | `['not', '*foo*', '*bar*']` | with a URI that doesn’t contain `foo` or `bar`.
     *
     * ---
     *
     * ```twig
     * {# Get the requested URI #}
     * {% set requestedUri = craft.app.request.getPathInfo() %}
     *
     * {# Fetch the {element} with that URI #}
     * {% set {element-var} = {twig-method}
     *   .uri(requestedUri|literal)
     *   .one() %}
     * ```
     *
     * ```php
     * // Get the requested URI
     * $requestedUri = \Craft::$app->request->getPathInfo();
     *
     * // Fetch the {element} with that URI
     * ${element-var} = {php-method}
     *     ->uri(\craft\helpers\Db::escapeParam($requestedUri))
     *     ->one();
     * ```
     *
     * @param mixed $value The property value
     * @return static self reference
     */
    public function uri(mixed $value): static;

    /**
     * Narrows the query results to only {elements} that match a search query.
     *
     * See [Searching](https://craftcms.com/docs/5.x/system/searching.html) for a full explanation of how to work with this parameter.
     *
     * ---
     *
     * ```twig
     * {# Get the search query from the 'q' query string param #}
     * {% set searchQuery = craft.app.request.getQueryParam('q') %}
     *
     * {# Fetch all {elements} that match the search query #}
     * {% set {elements-var} = {twig-method}
     *   .search(searchQuery)
     *   .all() %}
     * ```
     *
     * ```php
     * // Get the search query from the 'q' query string param
     * $searchQuery = \Craft::$app->request->getQueryParam('q');
     *
     * // Fetch all {elements} that match the search query
     * ${elements-var} = {php-method}
     *     ->search($searchQuery)
     *     ->all();
     * ```
     *
     * @param mixed $value The property value
     * @return static self reference
     */
    public function search(mixed $value): static;

    /**
     * Narrows the query results to only {elements} that were involved in a bulk element operation.
     *
     * @param string|null $value The property value
     * @return static self reference
     * @since 5.0.0
     */
    public function inBulkOp(?string $value): static;

    /**
     * Narrows the query results based on a reference string.
     *
     * @param mixed $value The property value
     * @return static self reference
     */
    public function ref(mixed $value): static;

    /**
     * Causes the query to return matching {elements} eager-loaded with related elements.
     *
     * See [Eager-Loading Elements](https://craftcms.com/docs/5.x/development/eager-loading.html) for a full explanation of how to work with this parameter.
     *
     * ---
     *
     * ```twig
     * {# Fetch {elements} eager-loaded with the "Related" field’s relations #}
     * {% set {elements-var} = {twig-method}
     *   .with(['related'])
     *   .all() %}
     * ```
     *
     * ```php
     * // Fetch {elements} eager-loaded with the "Related" field’s relations
     * ${elements-var} = {php-method}
     *     ->with(['related'])
     *     ->all();
     * ```
     *
     * @param array|string|null $value The property value
     * @return static self reference
     */
    public function with(array|string|null $value): static;

    /**
     * Causes the query to return matching {elements} eager-loaded with related elements, in addition to the elements that were already specified by [[with()]]..
     *
     * @param array|string|null $value The property value to append
     * @return static self reference
     * @since 3.0.9
     */
    public function andWith(array|string|null $value): static;

    /**
     * Causes the query to be used to eager-load results for the query’s source element
     * and any other elements in its collection.
     *
     * @param string|bool $value The property value. If a string, the value will be used as the eager-loading alias.
     * @return static
     * @since 5.0.0
     */
    public function eagerly(string|bool $value = true): static;

    /**
     * Sets whether custom fields should be factored into the query.
     *
     * @param bool $value The property value (defaults to true)
     * @return static self reference
     * @since 5.2.0
     */
    public function withCustomFields(bool $value = true): static;

    /**
     * Explicitly determines whether the query should join in the structure data.
     *
     * @param bool $value The property value (defaults to true)
     * @return static self reference
     */
    public function withStructure(bool $value = true): static;

    /**
     * Determines which structure data should be joined into the query.
     *
     * @param int|null $value The property value
     * @return static self reference
     */
    public function structureId(?int $value = null): static;

    /**
     * Narrows the query results based on the {elements}’ level within the structure.
     *
     * Possible values include:
     *
     * | Value | Fetches {elements}…
     * | - | -
     * | `1` | with a level of 1.
     * | `'not 1'` | not with a level of 1.
     * | `'>= 3'` | with a level greater than or equal to 3.
     * | `[1, 2]` | with a level of 1 or 2.
     * | `[null, 1]` | without a level, or a level of 1.
     * | `['not', 1, 2]` | not with level of 1 or 2.
     *
     * ---
     *
     * ```twig
     * {# Fetch {elements} positioned at level 3 or above #}
     * {% set {elements-var} = {twig-method}
     *   .level('>= 3')
     *   .all() %}
     * ```
     *
     * ```php
     * // Fetch {elements} positioned at level 3 or above
     * ${elements-var} = {php-method}
     *     ->level('>= 3')
     *     ->all();
     * ```
     *
     * @param mixed $value The property value
     * @return static self reference
     */
    public function level(mixed $value = null): static;

    /**
     * Narrows the query results based on whether the {elements} have any descendants in their structure.
     *
     * (This has the opposite effect of calling [[leaves()]].)
     *
     * ---
     *
     * ```twig
     * {# Fetch {elements} that have descendants #}
     * {% set {elements-var} = {twig-method}
     *   .hasDescendants()
     *   .all() %}
     * ```
     *
     * ```php
     * // Fetch {elements} that have descendants
     * ${elements-var} = {php-method}
     *     ->hasDescendants()
     *     ->all();
     * ```
     *
     * @param bool $value The property value
     * @return static self reference
     * @since 3.0.4
     */
    public function hasDescendants(bool $value = true): static;

    /**
     * Narrows the query results based on whether the {elements} are “leaves” ({elements} with no descendants).
     *
     * (This has the opposite effect of calling [[hasDescendants()]].)
     *
     * ---
     *
     * ```twig
     * {# Fetch {elements} that have no descendants #}
     * {% set {elements-var} = {twig-method}
     *   .leaves()
     *   .all() %}
     * ```
     *
     * ```php
     * // Fetch {elements} that have no descendants
     * ${elements-var} = {php-method}
     *     ->leaves()
     *     ->all();
     * ```
     *
     * @param bool $value The property value
     * @return static self reference
     */
    public function leaves(bool $value = true): static;

    /**
     * Narrows the query results to only {elements} that are ancestors of another {element} in its structure.
     *
     * Possible values include:
     *
     * | Value | Fetches {elements}…
     * | - | -
     * | `1` | above the {element} with an ID of 1.
     * | a [[{element-class}]] object | above the {element} represented by the object.
     *
     * ---
     *
     * ```twig
     * {# Fetch {elements} above this one #}
     * {% set {elements-var} = {twig-method}
     *   .ancestorOf({myElement})
     *   .all() %}
     * ```
     *
     * ```php
     * // Fetch {elements} above this one
     * ${elements-var} = {php-method}
     *     ->ancestorOf(${myElement})
     *     ->all();
     * ```
     *
     * ---
     *
     * ::: tip
     * This can be combined with [[ancestorDist()]] if you want to limit how far away the ancestor {elements} can be.
     * :::
     *
     * @param int|ElementInterface|null $value The property value
     * @return static self reference
     */
    public function ancestorOf(ElementInterface|int|null $value): static;

    /**
     * Narrows the query results to only {elements} that are up to a certain distance away from the {element} specified by [[ancestorOf()]].
     *
     * ---
     *
     * ```twig
     * {# Fetch {elements} above this one #}
     * {% set {elements-var} = {twig-method}
     *   .ancestorOf({myElement})
     *   .ancestorDist(3)
     *   .all() %}
     * ```
     *
     * ```php
     * // Fetch {elements} above this one
     * ${elements-var} = {php-method}
     *     ->ancestorOf(${myElement})
     *     ->ancestorDist(3)
     *     ->all();
     * ```
     *
     * @param int|null $value The property value
     * @return static self reference
     */
    public function ancestorDist(?int $value = null): static;

    /**
     * Narrows the query results to only {elements} that are descendants of another {element} in its structure.
     *
     * Possible values include:
     *
     * | Value | Fetches {elements}…
     * | - | -
     * | `1` | below the {element} with an ID of 1.
     * | a [[{element-class}]] object | below the {element} represented by the object.
     *
     * ---
     *
     * ```twig
     * {# Fetch {elements} below this one #}
     * {% set {elements-var} = {twig-method}
     *   .descendantOf({myElement})
     *   .all() %}
     * ```
     *
     * ```php
     * // Fetch {elements} below this one
     * ${elements-var} = {php-method}
     *     ->descendantOf(${myElement})
     *     ->all();
     * ```
     *
     * ---
     *
     * ::: tip
     * This can be combined with [[descendantDist()]] if you want to limit how far away the descendant {elements} can be.
     * :::
     *
     * @param int|ElementInterface|null $value The property value
     * @return static self reference
     */
    public function descendantOf(ElementInterface|int|null $value): static;

    /**
     * Narrows the query results to only {elements} that are up to a certain distance away from the {element} specified by [[descendantOf()]].
     *
     * ---
     *
     * ```twig
     * {# Fetch {elements} below this one #}
     * {% set {elements-var} = {twig-method}
     *   .descendantOf({myElement})
     *   .descendantDist(3)
     *   .all() %}
     * ```
     *
     * ```php
     * // Fetch {elements} below this one
     * ${elements-var} = {php-method}
     *     ->descendantOf(${myElement})
     *     ->descendantDist(3)
     *     ->all();
     * ```
     *
     * @param int|null $value The property value
     * @return static self reference
     */
    public function descendantDist(?int $value = null): static;

    /**
     * Narrows the query results to only {elements} that are siblings of another {element} in its structure.
     *
     * Possible values include:
     *
     * | Value | Fetches {elements}…
     * | - | -
     * | `1` | beside the {element} with an ID of 1.
     * | a [[{element-class}]] object | beside the {element} represented by the object.
     *
     * ---
     *
     * ```twig
     * {# Fetch {elements} beside this one #}
     * {% set {elements-var} = {twig-method}
     *   .siblingOf({myElement})
     *   .all() %}
     * ```
     *
     * ```php
     * // Fetch {elements} beside this one
     * ${elements-var} = {php-method}
     *     ->siblingOf(${myElement})
     *     ->all();
     * ```
     *
     * @param int|ElementInterface|null $value The property value
     * @return static self reference
     */
    public function siblingOf(ElementInterface|int|null $value): static;

    /**
     * Narrows the query results to only the {element} that comes immediately before another {element} in its structure.
     *
     * Possible values include:
     *
     * | Value | Fetches the {element}…
     * | - | -
     * | `1` | before the {element} with an ID of 1.
     * | a [[{element-class}]] object | before the {element} represented by the object.
     *
     * ---
     *
     * ```twig
     * {# Fetch the previous {element} #}
     * {% set {element-var} = {twig-method}
     *   .prevSiblingOf({myElement})
     *   .one() %}
     * ```
     *
     * ```php
     * // Fetch the previous {element}
     * ${element-var} = {php-method}
     *     ->prevSiblingOf(${myElement})
     *     ->one();
     * ```
     *
     * @param int|ElementInterface|null $value The property value
     * @return static self reference
     */
    public function prevSiblingOf(ElementInterface|int|null $value): static;

    /**
     * Narrows the query results to only the {element} that comes immediately after another {element} in its structure.
     *
     * Possible values include:
     *
     * | Value | Fetches the {element}…
     * | - | -
     * | `1` | after the {element} with an ID of 1.
     * | a [[{element-class}]] object | after the {element} represented by the object.
     *
     * ---
     *
     * ```twig
     * {# Fetch the next {element} #}
     * {% set {element-var} = {twig-method}
     *   .nextSiblingOf({myElement})
     *   .one() %}
     * ```
     *
     * ```php
     * // Fetch the next {element}
     * ${element-var} = {php-method}
     *     ->nextSiblingOf(${myElement})
     *     ->one();
     * ```
     *
     * @param int|ElementInterface|null $value The property value
     * @return static self reference
     */
    public function nextSiblingOf(ElementInterface|int|null $value): static;

    /**
     * Narrows the query results to only {elements} that are positioned before another {element} in its structure.
     *
     * Possible values include:
     *
     * | Value | Fetches {elements}…
     * | - | -
     * | `1` | before the {element} with an ID of 1.
     * | a [[{element-class}]] object | before the {element} represented by the object.
     *
     * ---
     *
     * ```twig
     * {# Fetch {elements} before this one #}
     * {% set {elements-var} = {twig-method}
     *   .positionedBefore({myElement})
     *   .all() %}
     * ```
     *
     * ```php
     * // Fetch {elements} before this one
     * ${elements-var} = {php-method}
     *     ->positionedBefore(${myElement})
     *     ->all();
     * ```
     *
     * @param int|ElementInterface|null $value The property value
     * @return static self reference
     */
    public function positionedBefore(ElementInterface|int|null $value): static;

    /**
     * Narrows the query results to only {elements} that are positioned after another {element} in its structure.
     *
     * Possible values include:
     *
     * | Value | Fetches {elements}…
     * | - | -
     * | `1` | after the {element} with an ID of 1.
     * | a [[{element-class}]] object | after the {element} represented by the object.
     *
     * ---
     *
     * ```twig
     * {# Fetch {elements} after this one #}
     * {% set {elements-var} = {twig-method}
     *   .positionedAfter({myElement})
     *   .all() %}
     * ```
     *
     * ```php
     * // Fetch {elements} after this one
     * ${elements-var} = {php-method}
     *     ->positionedAfter(${myElement})
     *     ->all();
     * ```
     *
     * @param int|ElementInterface|null $value The property value
     * @return static self reference
     */
    public function positionedAfter(ElementInterface|int|null $value): static;

    // Query preparation/execution
    // -------------------------------------------------------------------------

    /**
     * Prepares the query for lazy eager loading.
     *
     * @param string $handle The eager loading handle the query is for
     * @param ElementInterface $sourceElement One of the source elements the query is fetching elements for
     * @since 5.0.0
     */
    public function prepForEagerLoading(string $handle, ElementInterface $sourceElement): static;

    /**
     * Returns whether the query results were already eager loaded by the query's source element.
     *
     * @param string|null $alias The eager-loading alias to check
     * @return bool
     * @since 5.0.0
     */
    public function wasEagerLoaded(?string $alias = null): bool;

    /**
     * Returns whether the query result count was already eager loaded by the query's source element.
     *
     * @param string|null $alias The eager-loading alias to check
     * @return bool
     * @since 5.0.0
     */
    public function wasCountEagerLoaded(?string $alias = null): bool;

    /**
     * Executes the query and returns all results as an array.
     *
     * @param Connection|null $db The database connection used to generate the SQL statement.
     * If this parameter is not given, the `db` application component will be used.
     * @return ElementInterface[]|array[] The resulting elements.
     */
    public function all($db = null): array;

    /**
     * Executes the query and returns all results as a collection.
     *
     * @param Connection|null $db The database connection used to generate the SQL statement.
     * If this parameter is not given, the `db` application component will be used.
     * @return ElementCollection A collection of the resulting elements.
     * @since 4.0.0
     */
    public function collect(?Connection $db = null): ElementCollection;

    /**
     * Executes the query and returns a single row of result.
     *
     * @param Connection $db The database connection used to execute the query.
     * If this parameter is not given, the `db` application
     * component will be used.
     * @return mixed The resulting element. Null is returned if the query results in nothing.
     */
    public function one($db = null): mixed;

    /**
     * Executes the query and returns a single row of result at a given offset.
     *
     * @param int $n The offset of the row to return. If [[offset]] is set, $offset will be added to it.
     * @param Connection|null $db The database connection used to generate the SQL statement.
     * If this parameter is not given, the `db` application component will be used.
     * @return mixed The element or row of the query result. Null is returned if the query
     * results in nothing.
     */
    public function nth(int $n, ?Connection $db = null): mixed;

    /**
     * Executes the query and returns the IDs of the resulting elements.
     *
     * @param Connection|null $db The database connection used to generate the SQL statement.
     * If this parameter is not given, the `db` application component will be used.
     * @return int[] The resulting element IDs. An empty array is returned if no elements are found.
     */
    public function ids(?Connection $db = null): array;

    /**
     * Converts a found row into an element instance.
     *
     * @param array $row
     * @return ElementInterface
     * @since 3.6.0
     */
    public function createElement(array $row): ElementInterface;

    /**
     * Performs any post-population processing on elements.
     *
     * @param ElementInterface[]|array[] $elements the populated elements
     * @return ElementInterface[]|array[]
     * @since 3.6.0
     */
    public function afterPopulate(array $elements): array;

    /**
     * Returns the field layouts that could be associated with the resulting elements.
     *
     * @return FieldLayout[]
     * @since 5.6.0
     */
    public function getFieldLayouts(): array;
}
