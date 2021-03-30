<?php

namespace SkoreLabs\NovaPanels;

use Illuminate\Support\Str;
use Laravel\Nova\Panel;

abstract class StackedFieldsPanel extends Panel
{
    /**
     * Prepare the panel for JSON serialization.
     *
     * @return array
     */
    public function jsonSerialize()
    {
        return array_merge(parent::jsonSerialize(), [
            'component' => 'detail-'.Str::kebab(class_basename(get_class($this))),
        ]);
    }
}
