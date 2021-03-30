<?php

namespace SkoreLabs\NovaPanels;

use Laravel\Nova\Contracts\Resolvable;

class SelectorPanel extends StackedFieldsPanel
{
    /**
     * @var string
     */
    public $activeOption = '';

    /**
     * @var array
     */
    public $options = [];

    /**
     * You can set the active Option
     * @param  $name
     * @return mixed
     */
    public function activeOption($name)
    {
        $this->activeOption = $name;

        return $this;
    }

    /**
     * Add an Option to the Select Field
     * @param  $name
     * @param  $field
     * @param  $relationType
     * @return mixed
     */
    public function addOption($name, $field, $relationType = '')
    {
        if (call_user_func([$field->resourceClass, 'authorizedToViewAny'], request())) {
            $this->options[] = [
                'name'           => $name,
                'field'          => $field,
                'targetRelation' => $field->attribute,
                'panel'          => $field->component == 'belongs-to-field' ? $name : null,
            ];

            if ($field->component == 'belongs-to-field') {
                $field->panel       = $name;
                $field->belongsToId = '';
                $field->value       = '';
            }
        }

        return $this;
    }

    /**
     * Prepare the given fields.
     *
     * @param  \Closure|array $fields
     * @return array
     */
    protected function prepareFields($fields)
    {
        $fieldsCol = collect(is_callable($fields) ? $fields() : $fields);

        $fieldsCol->each(function ($value, $key) {
            $this->addOption($key, $value);
        });

        return $this->data ?? [];
    }

    /**
     * @return mixed
     */
    public function inTabs()
    {
        return $this->withMeta(['inTabs' => true]);
    }

    /**
     * Get additional meta information to merge with the field payload.
     *
     * @return array
     */
    public function meta()
    {
        return array_merge([
            'activeOption' => $this->activeOption,
            'options'      => $this->options,
            'listable'     => (isset($this->meta['inTabs']) && $this->meta['inTabs']) ? false : true,
        ], $this->meta);
    }

    /**
     * Resolve the fields in the Options
     * @return mixed
     */
    public function resolve($resource, $attribute = null)
    {
        return collect($this->options)->each(function ($option, $key) use ($resource) {
            if ($option['field'] instanceof Resolvable) {
                $option['field']->resolve($resource);
            };
        });
    }

    /**
     * Show the Select Options on the right side
     * @param $value
     */
    public function withSelect()
    {
        return $this->withMeta(['withSelect' => true]);
    }
}
