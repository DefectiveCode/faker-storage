# Upgrade Guide

## Upgrading to the next major version

### PHP 8.5

PHP 8.5 or newer is required.

### Generators must expose a `$config` property

The `DefectiveCode\Faker\Generators\Generator` interface now declares `public Config $config { get; }`. Custom generators must have a
public `$config` property typed as `Config` or a subclass of it. Generators using a promoted `public Config $config` constructor
property need no change.

### `Worker::run()` returns `void`

Custom implementations of `DefectiveCode\Faker\Workers\Worker` must declare the return type:

```php
public function run(Faker $faker): void
```

### Narrower `getDefaultConfig()` return types

Classes extending these generators and overriding `getDefaultConfig()` must return the same type or a subclass of it:

| Generator     | Return type            |
| ------------- | ---------------------- |
| `Binary`      | `BinaryConfig`         |
| `Email`       | `EmailConfig`          |
| `RandomImage` | `ImageWithAlphaConfig` |

### Missing disks fail immediately

Calling `generate()` without `toDisk()` now throws an `InvalidArgumentException` before any worker starts.
