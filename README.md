# Sulu Validation Bundle

[![StyleCI](https://styleci.io/repos/67592167/shield)](https://styleci.io/repos/67592167)
[![Build Status](https://travis-ci.org/sulu/SuluValidationBundle.svg?branch=master)](https://travis-ci.org/sulu/SuluValidationBundle)

This bundle validates requests for pre-configured routes.
 
## How it works
 
On every request an event listener checks if a schema in 
`sulu_validation.schemas` is defined for the current route id.

It then uses [json schema validation](http://json-schema.org/) for
validating if the request data matches the configured schema.

## Installation

You can install this bundle with `composer` using the `sulu/validation-bundle` package.

```bash
composer require sulu/validation-bundle
```

## Implementation

### 1. Add SuluValidation to your project:

In `composer.json`:

```
"sulu/validation-bundle": "~0.1"
```

In your Kernel:

```
new Sulu\Bundle\ValidationBundle\SuluValidationBundle()
```

### 2. Prepend your config:

Prepend your budles config as follows 

```
class YourBundleExtension extends Extension implements PrependExtensionInterface
{

    ...

    public function prepend(ContainerBuilder $container)
    {
        if ($container->hasExtension('sulu_validation')) {
            $container->prependExtensionConfig(
                'sulu_validation',
                [
                    'schemas' => [
                        'example_route_id' => '@YourBundle/Validation/ExampleActionSchema.json',
                    ],
                ]
            );
        }
    }
}
```

### 3. Define your schema:

For the example above create a file `Validation\ExampleActionSchema.json`.

Let's assume you would like to require a property `locale` in your api,
that must contain of 2 letters:

```json
{
    "title": "Example schema",
    "type": "object",
    "properties": {
        "locale": {
            "type": "string",
            "minLength": 2,
            "maxLength": 2
        }
    },
    "required": [
        "locale"
    ]
}
```

## Overwrite schema

Of course you can overwrite an existing schema. Just exchange
configuration parameter in your applications configuration file:

```
sulu_validation:
    schemas:
        example_route_id: '@AnotherBundle/Validation/anotherActionSchema.json'
```
