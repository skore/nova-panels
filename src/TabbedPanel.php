<?php

namespace SkoreLabs\NovaPanels;

use Illuminate\Http\Resources\MergeValue;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Laravel\Nova\Contracts\ListableField;
use Laravel\Nova\Panel;
use SkoreLabs\NovaPanels\Contracts\PanelTab;

class TabbedPanel extends StackedFieldsPanel
{
    /**
     * @var mixed
     */
    public $defaultSearch = false;

    /**
     * @var bool
     */
    public $showTitle = false;

    /**
     * @var array
     */
    private $tabs = [];

    /**
     * Add fields to the Tab.
     *
     * @param \SkoreLabs\NovaPanels\Contracts\PanelTab $tab
     *
     * @return $this
     */
    public function addFields(PanelTab $tab): self
    {
        $this->tabs[] = $tab;

        foreach ($tab->getFields() as $field) {
            if ($field instanceof Panel) {
                $this->addFields(
                    new Tab($field->name, $field->data)
                );
                continue;
            }

            if ($field instanceof MergeValue) {
                if (!isset($field->panel)) {
                    $field->panel = $this->name;
                }

                $this->addFields(
                    new Tab($tab->getTitle(), $field->data)
                );
                continue;
            }

            $field->panel = $this->name;

            $meta = [
                'tab'     => $tab->getName(),
                'tabSlug' => $tab->getSlug(),
                'tabInfo' => Arr::except($tab->toArray(), ['fields', 'slug']),
            ];

            if ($field instanceof ListableField) {
                $meta += [
                    'listable'    => false,
                    'listableTab' => true,
                ];
            }

            $field->withMeta($meta);

            $this->data[] = $field;
        }

        return $this;
    }

    /**
     * Show default Search if you need more space.
     *
     * @param bool $value
     *
     * @return $this
     */
    public function defaultSearch(bool $value = true): self
    {
        $this->defaultSearch = $value;

        return $this;
    }

    /**
     * Whether the show the title.
     *
     * @param bool $show
     *
     * @return $this
     */
    public function showTitle($show = true): self
    {
        $this->showTitle = $show;

        return $this;
    }

    /**
     * Prepare the panel for JSON serialization.
     *
     * @return array
     */
    public function jsonSerialize()
    {
        return array_merge(parent::jsonSerialize(), [
            'defaultSearch' => $this->defaultSearch,
            'showTitle'     => $this->showTitle,
        ]);
    }

    /**
     * Prepare the given fields.
     *
     * @param \Closure|array $fields
     *
     * @return array
     */
    protected function prepareFields($fields)
    {
        $this->convertFieldsToTabs($fields)
            ->filter(static function (Tab $tab): bool {
                return $tab->shouldShow();
            })
            ->each(function (Tab $tab): void {
                $this->addFields($tab);
            });

        return $this->data ?? [];
    }

    /**
     * Convert fields to tabs.
     *
     * @param \Closure|array $fields
     *
     * @return \Illuminate\Support\Collection
     */
    private function convertFieldsToTabs($fields): Collection
    {
        $fieldsCollection = collect(
            is_callable($fields) ? $fields() : $fields
        );

        return $fieldsCollection->map(function ($fields, $key) {
            return $this->convertToTab($fields, $key);
        })->values();
    }

    /**
     * @param mixed      $fields
     * @param string|int $key
     *
     * @return \SkoreLabs\NovaPanels\Contracts\PanelTab
     */
    private function convertToTab($fields, $key): PanelTab
    {
        if ($fields instanceof PanelTab) {
            return $fields;
        }

        if ($fields instanceof Panel) {
            return new Tab($fields->name, $fields->data);
        }

        if (!is_array($fields) && $fields instanceof MergeValue) {
            return new Tab(head($fields->data)->name, $fields->data);
        }

        /**
         * If a field is not nested into an array or a Tab object
         * it acts as a tab in itself.
         *
         * @link https://github.com/eminiarts/nova-tabs/issues/141
         */
        if (!is_array($fields)) {
            return new Tab($fields->name, [$fields]);
        }

        return new Tab($key, $fields);
    }

    public function limitTabs($number)
    {
        $this->withMeta(['tabsLimit' => $number]);

        return $this;
    }
}
