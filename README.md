# Dynamic Model Filter for Laravel

Apply dynamic filters to Eloquent models based on request data — including support for `where`, `whereDate`, `between`, `like`, and nested `relation` filters. Comes with a flexible Blade form component for easy integration.

---

## 🚀 Installation

### 1. Require the package (for local testing)

```bash
composer require tecnocampos/dynamic-model-filter
```

---

## ⚙️ Setup

The package auto-registers via `ServiceProvider` using Laravel's package discovery.

If you're loading views from the package, they will be available under the namespace:  
`dynamicfilters::`

---

## ✅ Usage

### 1. Add the Trait to your Model

```php
use TecnoCampos\DynamicModelFilter\Traits\FilterRequestScope;

class User extends Model
{
  use FilterRequestScope;

  public static array $filterRequest = [
    'status' => 'text',
    'created_at' => 'date',
    'search' => 'multi|type:like|fields:name,email',
  ];
}
```

---

### 2. Apply Filters in Controller

```php
$users = User::applyFilters()->paginate();
```

---

## 🧠 Filter Syntax Reference

Each entry in `public static array $filterRequest` uses the format:

```
field_name => 'type|option1|option2|...'
```

### Supported Types

| Type       | Description                                    |
|------------|------------------------------------------------|
| `text`     | Basic `where`                                  |
| `like`     | `where` with `%value%`                         |
| `date`     | `whereDate`                                    |
| `between`  | Two filters used for range (`first`, `last`)   |
| `relation` | Uses `whereHas` with nested filters            |
| `multi`    | Search across multiple fields                  |

### Extra Options

| Option            | Usage                                      |
|-------------------|--------------------------------------------|
| `first` / `last`  | Used with `between` filters                |
| `field:column`    | When input name differs from DB field      |
| `type:text/like`  | Type used inside relation or multi-search  |
| `relation:name`   | Relation path (`user.address`)             |
| `fields:field1,field2` | Used with `multi` type                |
| `source:request`  | Fetch value from `request()` (default)     |

---

## 🧩 Example: Advanced

```php
public static array $filterRequest = [
  'start_date' => 'between|first|field:created_at',
  'end_date' => 'between|last|field:created_at',
  'status' => 'text',
  'search' => 'multi|type:text|fields:name,email',
  'subscription' => 'relation|type:text|field:id|relation:subscriptions',
];
```

---

## 🖼 Blade Component (Form) (Optional)

Use the built-in Blade component to render filters dynamically:

### Include:

```blade
@include('dynamicfilters::components.filter-form', [
  'filters' => [
    'inputs' => [
      'name' => 'type:text|id:name|placeholder:Name or Email...|label:Search',
      'status' => "type:select|id:status|options:{\"0\":\"Active\",\"1\":\"Disabled\"}|placeholder:All|label:Status",
    ],
    'action' => route('users.index'), // Optional
    'method' => 'GET',
  ]
])
```

### Auto Features

- Keeps values after submit.
- Generates inputs based on type (`text`, `select`, `date`).
- Supports `options:` for selects via JSON.

---

## 🔧 Customizing

You may override the default layout or logic by publishing the views:

```bash
php artisan vendor:publish --tag=views
```

> Or modify the form component at `src/resources/views/components/filter-form.blade.php`

---

## 📅 Date Support (pt_BR / en)

The filter handles date input in both BR (`d/m/Y`) and EN (`m/d/Y`) formats automatically using `Carbon` and app locale.

---

## ✅ Requirements

- PHP 8.0+
- Laravel 9+

---

## 📖 License

MIT © TecnoCampos
```