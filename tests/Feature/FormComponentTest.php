<?php

namespace TecnoCampos\DynamicModelFilter\Tests\Feature;

use Orchestra\Testbench\TestCase;
use TecnoCampos\DynamicModelFilter\RequestFiltersServiceProvider;
use Illuminate\Support\Facades\Blade;

class FormComponentTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        (new RequestFiltersServiceProvider($this->app))->boot();
    }

    protected function getPackageProviders($app)
    {
        return [RequestFiltersServiceProvider::class];
    }

    /** @test */
    public function it_renders_form_component_for_both_tailwind()
    {
        config()->set('dynamic-model-filter.template', 'tailwind');
        $html = \Illuminate\Support\Facades\Blade::render('<x-DMF::form :fields="[]" />');
        $this->assertStringContainsString('<form method="GET" id="dynamic-filter-form">', $html);
        $this->assertStringContainsString('class="flex flex-col gap-4"', $html);
    }

    /** @test */
    public function it_renders_form_component_for_bootstrap()
    {
        config()->set('dynamic-model-filter.template', 'bootstrap');
        $html = \Illuminate\Support\Facades\Blade::render('<x-DMF::form :fields="[]" />');
        $this->assertStringContainsString('<form method="GET" id="dynamic-filter-form">', $html);
        $this->assertStringContainsString('class="row g-3"', $html);
    }

    /** @test */
    public function it_renders_text_select_and_date_fields()
    {
        config()->set('dynamic-model-filter.template', 'bootstrap');

        $html = \Illuminate\Support\Facades\Blade::render('<x-DMF::form :fields="[
            \'name\' => [\'type\' => \'text\', \'label\' => \'Name\'],
            \'status\' => [\'type\' => \'select\', \'label\' => \'Status\', \'data\' => [\'active\' => \'Active\', \'inactive\' => \'Inactive\']],
            \'created_at\' => [\'type\' => \'date\', \'label\' => \'Date\', \'class\' => \'calendar\']
        ]" />');

        $this->assertStringContainsString('<input type="text"', $html);
        $this->assertStringContainsString('name="name"', $html);

        $this->assertStringContainsString('<select', $html);
        $this->assertStringContainsString('name="status"', $html);
        $this->assertStringContainsString('Active</option>', $html);
        $this->assertStringContainsString('Inactive</option>', $html);

        $this->assertStringContainsString('<input type="date"', $html);
        $this->assertStringContainsString('calendar" name', $html);
    }
}
