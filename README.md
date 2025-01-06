# Add a welcome bar to the top of your website updatable via API

[![Latest Version on Packagist](https://img.shields.io/packagist/v/daikazu/welcome-bar.svg?style=flat-square)](https://packagist.org/packages/daikazu/welcome-bar)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/daikazu/welcome-bar/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/daikazu/welcome-bar/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/daikazu/welcome-bar/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/daikazu/welcome-bar/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/daikazu/welcome-bar.svg?style=flat-square)](https://packagist.org/packages/daikazu/welcome-bar)

## Installation

You can install the package via composer:

```bash
composer require daikazu/welcome-bar
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="welcome-bar-config"
```

This is the contents of the published config file:

```php
return [
    /*
      |--------------------------------------------------------------------------
      | Storage Path
      |--------------------------------------------------------------------------
      */
    'storage_path' => storage_path('app/welcome-bar.json'),

    /*
    |--------------------------------------------------------------------------
    | Route Middlewares or Security
    |--------------------------------------------------------------------------
    |
    | For example, you could specify a route middleware group here that your
    | "update" route uses for authentication or token checking.
    |
    */
    'middleware' => [
        'update' => ['api'], // Or anything your app uses, e.g. 'auth:api', or a custom 'welcome-bar-auth'
        'fetch'  => ['web'], // Or 'api'
    ],
];
```

Optionally, you can publish the views using

```bash
php artisan vendor:publish --tag="welcome-bar-views"
```

## Usage


Add this to your layout file (e.g., `resources/views/layouts/app.blade.php`):

```html
 @include('welcome-bar::welcome-bar')
 ```

Schedule the cleanup command to run in your console routes file

```php
Schedule::command('welcome-bar:prune')->daily();

````

## Data Structure

This package uses a JSON array of “message objects” to display one or more stacked bars at the top of your webpage. The
order of the array determines the vertical stacking order: the first element in the array appears at the very top, the
second beneath it, and so on.

```json
[
    {
        "message": "Random Message 1",
        "cta": {
            "label": "More info",
            "url": "http://google.com",
            "target": "_blank"
        },
        "schedule": {
            "start": "2025-01-01T00:00:00",
            "end": "2025-01-05T23:59:59"
        },
        "behavior": {
            "closable": true,
            "autoHide": false,
            "autoHideDelay": 5000
        },
        "theme": {
            "variant": "prominent",
            "background": "#0099ff",
            "text": "#ffffff",
            "button": {
                "background": "#ffffff",
                "text": "#82ff38",
                "contrastStrategy": "auto"
            }
        }
    },
    {
        "message": "Random Message 2",
        "cta": {
            "label": "Another link",
            "url": "https://example.com",
            "target": "_self"
        },
        "schedule": {
            "start": "2025-01-02T00:00:00",
            "end": "2025-02-01T23:59:59"
        },
        "behavior": {
            "closable": false,
            "autoHide": true,
            "autoHideDelay": 10000
        },
        "theme": {
            "variant": "subtle",
            "background": "#0066cc",
            "text": "#ffffff",
            "button": {
                "background": "#ffffff",
                "text": "#000000",
                "contrastStrategy": "manual"
            }
        }
    }
]
```

**Field Explanations**

**message**

• **Type:** string

• **Description:** The main text shown on the bar. This is **required**.

**cta**

• **Type:** object (optional fields)

• **label** (string): The text on the button.

• **url** (string): The link destination.

• **target** (string): _blank or _self. Defaults to _self if omitted.

If cta.label and cta.url are both provided, a button will appear.

**schedule**

• **Type:** object

• **start** (string, ISO datetime): When this message first becomes visible.

• **end** (string, ISO datetime): When this message stops being visible.

Messages are only displayed **while** the current date/time falls between start and end. If you omit these fields, the
message is always considered valid.

**behavior**

• **Type:** object

• **closable** (boolean): Whether to show a close (×) button.

• **autoHide** (boolean): Whether the bar should automatically hide itself after some time.

• **autoHideDelay** (number): The duration in milliseconds (e.g., 5000 = 5 seconds) before the bar hides if autoHide is
true.

**theme**

• **Type:** object

• **variant** (string): Arbitrary descriptor for the style theme (e.g., "prominent", "subtle").

• **background** (string): Hex code for the bar’s background color.

• **text** (string): Hex code for the bar’s text color.

• **button** (object): Further styles for the CTA button.

• **background** (string): Button background color.

• **text** (string): Button text color.

• **contrastStrategy** (string, "auto" or "manual"):

• If "auto", the bar can compute the best contrasting text color based on background.

• If "manual", the text color is taken as-is from text.

**How to Update the Data**

You can **update** this JSON array via:

• **A POST request** to your application’s update route (e.g. POST /welcome-bar/update), or

• **Directly saving** a JSON file in the configured temporary storage location (depending on your setup).

**How Data is Rendered**

Each entry in the array is displayed as a stacked bar at the top of the page, in the order provided. The package checks:

1. **Is** now() **between** schedule.start **and** schedule.end**?**

2. **Is the data valid?** (e.g., do we have a message?)

If valid, the bar is rendered with the specified colors, CTA button, and behavior rules.

**Tip:** If you want to hide all existing bars, simply remove (or empty) this JSON array.

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Mike Wall](https://github.com/daikazu)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
