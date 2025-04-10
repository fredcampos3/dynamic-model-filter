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

## 🖼 Blade Component (Form) – Optional UI Integration

You can use the <x-DMF::form /> component to render filter forms automatically based on a simple fields array.

### Basic usage

```blade
<x-DMF::form :fields="[
    'start_date' => ['type' => 'date', 'label' => __('Start Date')],
    'end_date' => ['type' => 'date', 'label' => __('End Date')],
    'status' => [
        'type' => 'select',
        'label' => __('Status'),
        'data' => ['active' => 'Active', 'inactive' => 'Inactive'],
        'attributes' => '',
        'class' => 'form-select'
    ],
]" />
```

- All submitted fields will persist values using request().
- The component will auto-render the correct field types (text, select, date).

---

### UI Templates

You can define which template is used (Bootstrap or Tailwind) via config:

1. Set in .env:

```bash
DMF_TEMPLATE=bootstrap
```

2. Or override in config file config/dynamic-model-filter.php:

```php
return [
  'template' => env('DMF_TEMPLATE', 'tailwind'), // or 'bootstrap'
];
```

The component will automatically load one of the following views:

- resources/views/components/bootstrap/filter-form.blade.php
- resources/views/components/tailwind/filter-form.blade.php

---

### Dynamic behavior

- size: sets the grid column width (1–12) for Bootstrap and Tailwind layouts
- data: associative array for select
- attributes: any custom HTML attributes (multiple, readonly, etc.)
- class: additional CSS classes

---

## 📅 Date Support (pt_BR / en)

The filter handles date input in both BR (`d/m/Y`) and EN (`m/d/Y`) formats automatically using `Carbon` and app locale.

---

## ✅ Requirements

- PHP 8.0+
- Laravel 8+

---

## 🤝 Contributing

Contributions are welcome! If you find a bug or have an idea for an improvement, feel free to open an issue or submit a pull request.

### How to Contribute

1. **Fork** this repository
2. **Clone** your fork:

```bash
git clone https://github.com/fredcampos3/dynamic-model-filter.git
```

3. **Create a new branch** for your changes:

```bash
git checkout -b feature/your-feature-name
```

4. **Make your changes** and commit:

```bash
git commit -m "Add feature: your description"
```

5. **Push** to your fork:

```bash
git push origin feature/your-feature-name
```

6. **Open a Pull Request** to the `main` branch of this repository

Please ensure your code follows Laravel conventions and includes relevant tests if applicable.

---

## 📖 License

MIT © TecnoCampos