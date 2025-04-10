<?php

namespace TecnoCampos\DynamicModelFilter\Tests\Feature;

use Orchestra\Testbench\TestCase;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use TecnoCampos\DynamicModelFilter\Traits\FilterRequestScope;

class FilterTraitTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('status')->nullable();
            $table->date('created_at')->nullable();
        });

        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('plan');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        TestUser::insert([
            ['name' => 'Alice', 'email' => 'alice@email.com', 'status' => 'active', 'created_at' => '2024-01-01'],
            ['name' => 'Bob', 'email' => 'bob@email.com', 'status' => 'inactive', 'created_at' => '2024-01-10'],
        ]);
        TestSubscription::insert([
            ['user_id' => 1, 'plan' => 'premium'],
            ['user_id' => 2, 'plan' => 'basic'],
        ]);
    }

    protected function tearDown(): void
    {
        Schema::dropIfExists('subscriptions');
        Schema::dropIfExists('users');
        parent::tearDown();
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
    }

    /** @test */
    public function it_filters_with_text_type()
    {
        request()->merge(['name' => 'Alice']);
        $results = TestUser::applyFilters()->get();
    
        $this->assertCount(1, $results);
        $this->assertEquals('Alice', $results->first()->name);
    }

    /** @test */
    public function it_filters_with_like_type()
    {
        request()->merge(['email' => 'bob@']);
        $results = TestUser::applyFilters()->get();

        $this->assertCount(1, $results);
        $this->assertEquals('Bob', $results->first()->name);
    }

    /** @test */
    public function it_filters_with_between_first_last()
    {
        request()->merge([
            'created_start' => '2024-01-05',
            'created_end' => '2024-01-20',
        ]);
        $results = TestUser::applyFilters()->get();

        $this->assertCount(1, $results);
        $this->assertEquals('Bob', $results->first()->name);
    }

    /** @test */
    public function it_filters_with_relation()
    {
        request()->merge(['subscription' => 'premium']);
        $results = TestUser::applyFilters()->get();

        $this->assertCount(1, $results);
        $this->assertEquals('Alice', $results->first()->name);
    }

    /** @test */
    public function it_filters_with_multi_field_like()
    {
        request()->merge(['search' => 'bob']);
        $results = TestUser::applyFilters()->get();

        $this->assertCount(1, $results);
        $this->assertEquals('Bob', $results->first()->name);
    }

    /** @test */
    public function it_filters_with_relation_field_array()
    {
        request()->merge(['plans' => ['basic', 'premium']]);
        $results = TestUser::applyFilters()->get();

        $this->assertCount(2, $results);
        $this->assertEqualsCanonicalizing(['Alice', 'Bob'], $results->pluck('name')->all());
    }

    /** @test */
    public function it_filters_with_between_field_request()
    {
        request()->merge([
            'plan_date' => '2024-01-01',
            'date' => 'created_at',
        ]);
        $results = TestUser::applyFilters()->get();

        $this->assertCount(2, $results);
        $this->assertEqualsCanonicalizing(['Alice', 'Bob'], $results->pluck('name')->all());
    }
}

class TestUser extends Model
{
    use FilterRequestScope;

    protected $table = 'users';
    public $timestamps = false;

    protected $guarded = [];

    public static array $filterRequest = [
        'name' => 'text',
        'email' => 'like',
        'created_start' => 'between|first|field:created_at',
        'created_end' => 'between|last|field:created_at',
        'plan_date' => 'between|first|request:date',
        'status' => 'text',
        'plans' => 'relation|array|plan|subscriptions',
        'search' => 'multi|type:like|fields:name,email',
        'subscription' => 'relation|type:text|field:plan|relation:subscriptions',
    ];

    public function subscriptions()
    {
        return $this->hasMany(TestSubscription::class, 'user_id');
    }
}

class TestSubscription extends Model
{
    protected $table = 'subscriptions';
    public $timestamps = false;
    protected $guarded = [];
}
