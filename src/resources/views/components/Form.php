<?php

namespace TecnoCampos\DynamicModelFilter\Resources\Views\Components;

use Illuminate\View\Component;

class Form extends Component
{
    public array $fields;

    public function __construct(array $fields = [])
    {
        $this->fields = $fields;
    }

    public function render()
    {
        $template = config('dynamic-model-filter.template', 'bootstrap');
        return view("dynamic-model-filter::components.{$template}.filter-form", [
            'fields' => $this->fields,
        ]);
    }
}
