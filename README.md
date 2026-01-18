<p align="center">
    <picture>
      <source media="(prefers-color-scheme: dark)" srcset="https://defectivecode.com/logos/logo-animated-dark.png">
      <img width="450" alt="Defective Code Logo" src="https://defectivecode.com/logos/logo-animated-light.png">
    </picture>
</p>

[English](https://www.defectivecode.com/packages/faker-storage/en) |
[العربية](https://www.defectivecode.com/packages/faker-storage/ar) |
[বাংলা](https://www.defectivecode.com/packages/faker-storage/bn) |
[Bosanski](https://www.defectivecode.com/packages/faker-storage/bs) |
[Deutsch](https://www.defectivecode.com/packages/faker-storage/de) |
[Español](https://www.defectivecode.com/packages/faker-storage/es) |
[Français](https://www.defectivecode.com/packages/faker-storage/fr) |
[हिन्दी](https://www.defectivecode.com/packages/faker-storage/hi) |
[Italiano](https://www.defectivecode.com/packages/faker-storage/it) |
[日本語](https://www.defectivecode.com/packages/faker-storage/ja) |
[한국어](https://www.defectivecode.com/packages/faker-storage/ko) |
[मराठी](https://www.defectivecode.com/packages/faker-storage/mr) |
[Português](https://www.defectivecode.com/packages/faker-storage/pt) |
[Русский](https://www.defectivecode.com/packages/faker-storage/ru) |
[Kiswahili](https://www.defectivecode.com/packages/faker-storage/sw) |
[தமிழ்](https://www.defectivecode.com/packages/faker-storage/ta) |
[తెలుగు](https://www.defectivecode.com/packages/faker-storage/te) |
[Türkçe](https://www.defectivecode.com/packages/faker-storage/tr) |
[اردو](https://www.defectivecode.com/packages/faker-storage/ur) |
[Tiếng Việt](https://www.defectivecode.com/packages/faker-storage/vi) |
[中文](https://www.defectivecode.com/packages/faker-storage/zh)

# Introduction

**Faker Storage** is a high-performance PHP package designed to generate and store large volumes of fake data files
efficiently. Built with concurrency in mind, it leverages Swoole or PCNTL to generate thousands of files in parallel,
making it ideal for load testing, development environments, and storage system benchmarking.

The package provides a fluent API for generating various file types including images (PNG, JPG, GIF, BMP, WEBP, AVIF),
text files, CSV files, binary data, and RFC822-compliant emails. Each generator produces deterministic output when
seeded, ensuring reproducible test data across environments.

## Key Features

- **High-Performance Concurrency**: Automatic selection between Swoole (coroutines) and PCNTL (process forking) for
  parallel file generation
- **Multiple File Generators**: Built-in support for images, text, CSV, binary, and email files
- **Deterministic Output**: Seed-based generation ensures reproducible results
- **Flexible Storage**: Works with any League Flysystem adapter (local, S3, etc.)
- **Customizable File Naming**: Closure-based name generation with full control
- **Image Support**: Multiple formats with configurable dimensions and compression
- **Email Generation**: RFC822-compliant emails with attachments and MIME support

## Example

```php
use DefectiveCode\Faker\Faker;
use League\Flysystem\Filesystem;
use DefectiveCode\Faker\Generators\Png;
use League\Flysystem\Local\LocalFilesystemAdapter;

// Generate 1000 PNG images with 10 concurrent workers
Faker::make(Png::class)
    ->width(800, 1920)
    ->height(600, 1080)
    ->toDisk(new Filesystem(new LocalFilesystemAdapter('/path/to/storage')))
    ->basePath('images')
    ->count(1000)
    ->concurrency(10)
    ->seed(42)
    ->generate();

// Generate CSV files
use DefectiveCode\Faker\Generators\Csv;

Faker::make(Csv::class)
    ->columns(5, 10)
    ->rows(100, 500)
    ->delimiter(',')
    ->toDisk(new Filesystem(new LocalFilesystemAdapter('/path/to/storage')))
    ->count(50)
    ->generate();

// Generate emails with attachments
use DefectiveCode\Faker\Generators\Email;

Faker::make(Email::class)
    ->paragraphs(3, 5)
    ->sentences(2, 4)
    ->withAttachment(Png::class, 1, 3)
    ->toDisk(new Filesystem(new LocalFilesystemAdapter('/path/to/storage')))
    ->count(100)
    ->generate();
```

# Documentation

You may read the [documentation on our website](https://www.defectivecode.com/packages/faker-storage).

# Support Guidelines

Thanks for choosing our open source package! Please take a moment to check out these support guidelines. They'll help
you get the most out of our project.

## Community Driven Support

Our open-source project is fueled by our awesome community. If you have questions or need assistance, StackOverflow and
other online resources are your best bets.

## Bugs, and Feature Prioritization

The reality of managing an open-source project means we can't address every reported bug or feature request immediately.
We prioritize issues in the following order:

### 1. Bugs Affecting Our Paid Products

Bugs that impact our paid products will always be our top priority. In some cases, we may only address bugs that affect
us directly.

### 2. Community Pull Requests

If you've identified a bug and have a solution, please submit a pull request. After issues affecting our products, we
give the next highest priority to these community-driven fixes. Once reviewed and approved, we'll merge your solution
and credit your contribution.

### 3. Financial Support

For issues outside the mentioned categories, you can opt to fund their resolution. Each open issue is linked to an order
form where you can contribute financially. We prioritize these issues based on the funding amount provided.

### Community Contributions

Open source thrives when its community is active. Even if you're not fixing bugs, consider contributing through code
improvements, documentation updates, tutorials, or by assisting others in community channels. We highly encourage
everyone, as a community, to help support open-source work.

_To reiterate, DefectiveCode will prioritize bugs based on how they impact our paid products, community pull requests,
and the financial support received for issues._

# License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
