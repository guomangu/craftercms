<?php
/**
 * @link https://craftcms.com/
 * @copyright Copyright (c) Pixel & Tonic, Inc.
 * @license https://craftcms.github.io/license/
 */

namespace craft\elements\db;

use Craft;
use craft\db\QueryAbortedException;
use craft\db\Table;
use craft\elements\GlobalSet;
use craft\helpers\Db;

/**
 * GlobalSetQuery represents a SELECT SQL statement for global sets in a way that is independent of DBMS.
 *
 * @template TKey of array-key
 * @template TElement of GlobalSet
 * @extends ElementQuery<TKey,TElement>
 *
 * @author Pixel & Tonic, Inc. <support@pixelandtonic.com>
 * @since 3.0.0
 * @doc-path globals.md
 * @supports-site-params
 * @replace {element} global set
 * @replace {elements} global sets
 * @replace {twig-method} craft.globalSets()
 * @replace {myElement} myGlobalSet
 * @replace {element-class} \craft\elements\GlobalSet
 */
class GlobalSetQuery extends ElementQuery
{
    /**
     * @inheritdoc
     */
    protected array $defaultOrderBy = ['globalsets.sortOrder' => SORT_ASC];

    // General parameters
    // -------------------------------------------------------------------------

    /**
     * @var bool Whether to only return global sets that the user has permission to edit.
     * @used-by editable()
     */
    public bool $editable = false;

    /**
     * @var string|string[]|null The handle(s) that the resulting global sets must have.
     * @used-by handle()
     */
    public string|array|null $handle = null;

    /**
     * Sets the [[$editable]] property.
     *
     * @param bool $value The property value (defaults to true)
     * @return static self reference
     * @uses $editable
     */
    public function editable(bool $value = true): static
    {
        $this->editable = $value;
        return $this;
    }

    /**
     * Narrows the query results based on the global sets’ handles.
     *
     * Possible values include:
     *
     * | Value | Fetches global sets…
     * | - | -
     * | `'foo'` | with a handle of `foo`.
     * | `'not foo'` | not with a handle of `foo`.
     * | `['foo', 'bar']` | with a handle of `foo` or `bar`.
     * | `['not', 'foo', 'bar']` | not with a handle of `foo` or `bar`.
     *
     * ---
     *
     * ```twig
     * {# Fetch the global set with a handle of 'foo' #}
     * {% set {element-var} = {twig-method}
     *   .handle('foo')
     *   .one() %}
     * ```
     *
     * ```php
     * // Fetch the global set with a handle of 'foo'
     * ${element-var} = {php-method}
     *     ->handle('foo')
     *     ->one();
     * ```
     *
     * @param mixed $value The property value
     * @return static self reference
     * @uses $handle
     */
    public function handle(mixed $value): static
    {
        $this->handle = $value;
        return $this;
    }

    /**
     * @inheritdoc
     */
    protected function beforePrepare(): bool
    {
        if (!parent::beforePrepare()) {
            return false;
        }

        $this->joinElementTable(Table::GLOBALSETS);

        $this->query->addSelect([
            'globalsets.name',
            'globalsets.handle',
            'globalsets.sortOrder',
            'globalsets.uid',
        ]);

        if ($this->handle) {
            $this->subQuery->andWhere(Db::parseParam('globalsets.handle', $this->handle));
        }

        $this->_applyEditableParam();
        $this->_applyRefParam();

        return true;
    }


    /**
     * Applies the 'ref' param to the query being prepared.
     */
    private function _applyRefParam(): void
    {
        if (!$this->ref) {
            return;
        }

        $this->subQuery->andWhere(Db::parseParam('globalsets.handle', $this->ref));
    }

    /**
     * Applies the 'editable' param to the query being prepared.
     *
     * @throws QueryAbortedException
     */
    private function _applyEditableParam(): void
    {
        if ($this->editable) {
            // Limit the query to only the global sets the user has permission to edit
            $editableSetIds = Craft::$app->getGlobals()->getEditableSetIds();
            $this->subQuery->andWhere(['elements.id' => $editableSetIds]);
        }
    }
}
