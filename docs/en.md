# Introduction

**Faker Storage** is a high-performance PHP package designed to generate and store large volumes of fake data files
efficiently. Built with concurrency in mind, it leverages Swoole or PCNTL to generate thousands of files in parallel,
making it ideal for load testing, development environments, and storage system benchmarking.

The package provides a fluent API for generating various file types including images (PNG, JPG, GIF, BMP, WEBP, AVIF),
text files, CSV files, binary data, and RFC822-compliant emails. Each generator produces deterministic output when
seeded, ensuring reproducible test data across environments.

## Key Features

-   **High-Performance Concurrency**: Automatic selection between Swoole (coroutines) and PCNTL (process forking) for
    parallel file generation
-   **Multiple File Generators**: Built-in support for images, text, CSV, binary, and email files
-   **Deterministic Output**: Seed-based generation ensures reproducible results
-   **Flexible Storage**: Works with any League Flysystem adapter (local, S3, etc.)
-   **Customizable File Naming**: Closure-based name generation with full control
-   **Image Support**: Multiple formats with configurable dimensions and compression
-   **Email Generation**: RFC822-compliant emails with attachments and MIME support

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

# Installation

Install the package via Composer:

```bash
composer require defectivecode/faker-storage
```

## Requirements

-   PHP >= 8.4
-   ext-gd (for image generation)
-   ext-swoole (optional, for better performance)
-   ext-pcntl (fallback for concurrency)

## Optional Dependencies

For optimal performance, install the Swoole extension:

```bash
pecl install swoole
```

The package will automatically use Swoole if available, falling back to PCNTL otherwise.

# Usage

## Basic Workflow

All generators follow the same pattern:

1. Create a Faker instance with a generator
2. Configure the generator (optional)
3. Set storage destination
4. Configure concurrency and count
5. Generate files

```php
use DefectiveCode\Faker\Faker;
use League\Flysystem\Filesystem;
use DefectiveCode\Faker\Generators\Text;
use League\Flysystem\Local\LocalFilesystemAdapter;

Faker::make(Text::class)
    ->paragraphs(5, 10)          // Generator-specific configuration
    ->toDisk(new Filesystem(new LocalFilesystemAdapter('/storage')))
    ->basePath('documents')      // Files will be in /storage/documents/
    ->count(100)                 // Generate 100 files
    ->concurrency(4)             // Use 4 workers
    ->seed(123)                  // For deterministic output
    ->generate();
```

## Storage Configuration

### Using Flysystem

Faker Storage uses League Flysystem for storage abstraction:

```php
use League\Flysystem\Filesystem;
use League\Flysystem\Local\LocalFilesystemAdapter;

// Local storage
$filesystem = new Filesystem(new LocalFilesystemAdapter('/path/to/storage'));

Faker::make(Png::class)
    ->toDisk($filesystem)
    ->generate();
```

### AWS S3 Storage

```php
use Aws\S3\S3Client;
use League\Flysystem\Filesystem;
use League\Flysystem\AwsS3V3\AwsS3V3Adapter;

$client = new S3Client([
    'credentials' => [
        'key'    => 'your-key',
        'secret' => 'your-secret',
    ],
    'region' => 'us-east-1',
    'version' => 'latest',
]);

$adapter = new AwsS3V3Adapter($client, 'your-bucket-name');
$filesystem = new Filesystem($adapter);

Faker::make(Png::class)
    ->toDisk($filesystem)
    ->basePath('uploads/images')
    ->generate();
```

### Disk Options

Pass additional options to the filesystem adapter:

```php
Faker::make(Png::class)
    ->toDisk($filesystem)
    ->diskOptions([
        'visibility' => 'public',
        'ACL' => 'public-read',
        'CacheControl' => 'max-age=31536000',
    ])
    ->generate();
```

## Concurrency Configuration

Control parallel execution with the `concurrency()` method:

```php
// Use 10 worker threads/processes
Faker::make(Png::class)
    ->concurrency(10)
    ->generate();

// Swoole-specific: Set both threads and coroutines per thread
Faker::make(Png::class)
    ->concurrency(threads: 4, coroutines: 8)  // 4 workers, 8 coroutines each
    ->generate();
```

## File Naming

### Default Naming

By default, files are named using UUID v4:

```php
// Generates: e7f0a8d3-5c2b-4f9e-8a1d-3b4c5d6e7f8a.png
Faker::make(Png::class)->generate();
```

### Built-in Name Generators

```php
use DefectiveCode\Faker\NameGenerator;

// UUID-based (default)
NameGenerator::setDefault('uuid'); // Generates: e7f0a8d3-5c2b-4f9e-8a1d-3b4c5d6e7f8a.png

// Sequential numbering
NameGenerator::setDefault('sequence'); // Generates: 1.png, 2.png, 3.png, ...
```

### Custom Naming

Provide a closure to customize file names:

```php
use DefectiveCode\Faker\NameGenerator;

// Custom closure
Faker::make(Png::class)
    ->nameGenerator(function (int $seed, int $completedFiles, $generator) {
        return "custom-{$completedFiles}-{$seed}.png";
    })
    ->generate();

// Date-based naming
Faker::make(Png::class)
    ->nameGenerator(function (int $seed, int $completedFiles, $generator) {
        return date('Y/m/d') . "/image-{$completedFiles}.png";
    })
    ->generate();
```

## Seeding for Reproducibility

Set a seed to generate the same files across runs:

```php
Faker::make(Png::class)
    ->seed(42)
    ->count(10)
    ->generate();
```

Each file gets a unique deterministic seed derived from the base seed and file index.

# Generators

## Image Generators

All image generators support dimension and quality configuration.

### PNG

```php
use DefectiveCode\Faker\Generators\Png;

Faker::make(Png::class)
    ->width(800, 1920)          // Random width between 800-1920px
    ->height(600, 1080)         // Random height between 600-1080px
    ->withAlpha(true)           // Enable alpha/transparency channel
    ->grid(5)                   // Optional: Generate 5x5 symmetric pattern
    ->toDisk($filesystem)
    ->generate();
```

### JPG

```php
use DefectiveCode\Faker\Generators\Jpg;

Faker::make(Jpg::class)
    ->width(800, 1920)          // Random width between 800-1920px
    ->height(600, 1080)         // Random height between 600-1080px
    ->grid(5)                   // Optional: Generate 5x5 symmetric pattern
    ->toDisk($filesystem)
    ->generate();
```

### GIF

```php
use DefectiveCode\Faker\Generators\Gif;

Faker::make(Gif::class)
    ->width(800, 1920)          // Random width between 800-1920px
    ->height(600, 1080)         // Random height between 600-1080px
    ->withAlpha(true)           // Enable alpha/transparency channel
    ->grid(5)                   // Optional: Generate 5x5 symmetric pattern
    ->toDisk($filesystem)
    ->generate();
```

### BMP

```php
use DefectiveCode\Faker\Generators\Bmp;

Faker::make(Bmp::class)
    ->width(800, 1920)          // Random width between 800-1920px
    ->height(600, 1080)         // Random height between 600-1080px
    ->grid(5)                   // Optional: Generate 5x5 symmetric pattern
    ->toDisk($filesystem)
    ->generate();
```

### WEBP

```php
use DefectiveCode\Faker\Generators\Webp;

Faker::make(Webp::class)
    ->width(800, 1920)          // Random width between 800-1920px
    ->height(600, 1080)         // Random height between 600-1080px
    ->withAlpha(true)           // Enable alpha/transparency channel
    ->grid(5)                   // Optional: Generate 5x5 symmetric pattern
    ->toDisk($filesystem)
    ->generate();
```

### AVIF

```php
use DefectiveCode\Faker\Generators\Avif;

Faker::make(Avif::class)
    ->width(800, 1920)          // Random width between 800-1920px
    ->height(600, 1080)         // Random height between 600-1080px
    ->withAlpha(true)           // Enable alpha/transparency channel
    ->grid(5)                   // Optional: Generate 5x5 symmetric pattern
    ->toDisk($filesystem)
    ->generate();
```

### Random Image

Generates a random image format:

```php
use DefectiveCode\Faker\Generators\RandomImage;

Faker::make(RandomImage::class)
    ->width(800, 1920)
    ->height(600, 1080)
    ->withAlpha(false)          // Random from: AVIF, BMP, GIF, JPEG, PNG, WEBP
    ->toDisk($filesystem)
    ->generate();

Faker::make(RandomImage::class)
    ->width(800, 1920)
    ->height(600, 1080)
    ->withAlpha(true)           // Random from: AVIF, GIF, PNG, WEBP
    ->toDisk($filesystem)
    ->generate();
```

## Text Generator

Generate plain text files with paragraphs:

```php
use DefectiveCode\Faker\Generators\Text;

Faker::make(Text::class)
    ->paragraphs(5, 10)         // 5-10 paragraphs per file
    ->sentences(3, 6)           // 3-6 sentences per paragraph
    ->toDisk($filesystem)
    ->generate();
```

**Output Example:**

```
Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.

Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.
```

## CSV Generator

Generate CSV files with random data:

```php
use DefectiveCode\Faker\Generators\Csv;

Faker::make(Csv::class)
    ->columns(5, 10)            // 5-10 columns
    ->rows(100, 500)            // 100-500 rows
    ->delimiter(',')            // Column delimiter
    ->enclosure('"')            // Field enclosure
    ->escape('\\')              // Escape character
    ->eol("\n")                 // Line ending
    ->toDisk($filesystem)
    ->generate();
```

**Output Example:**

```csv
"John Doe","john@example.com","555-1234","New York","Engineer"
"Jane Smith","jane@example.com","555-5678","Los Angeles","Designer"
"Bob Johnson","bob@example.com","555-9012","Chicago","Manager"
```

## Binary Generator

Generate random binary data:

```php
use DefectiveCode\Faker\Generators\Binary;

Faker::make(Binary::class)
    ->length(1024, 1048576)     // 1KB - 1MB
    ->toDisk($filesystem)
    ->generate();
```

## Email Generator

Generate RFC822-compliant email files:

```php
use DefectiveCode\Faker\Generators\Email;

Faker::make(Email::class)
    ->paragraphs(3, 5)          // Paragraphs in email body
    ->sentences(2, 4)           // Sentences per paragraph
    ->withAttachment(Png::class, 1, 3)  // Add 1-3 PNG attachments
    ->toDisk($filesystem)
    ->generate();
```

### Email Headers

Generated emails include:

-   `To`: Random name and email
-   `From`: Random name and email
-   `Subject`: Random sentence
-   `Date`: Current timestamp
-   `Message-ID`: Deterministic ID based on seed

### Email with Attachments

Attach files using generator class names or instances:

```php
use DefectiveCode\Faker\Generators\Email;
use DefectiveCode\Faker\Generators\Png;
use DefectiveCode\Faker\Generators\Pdf;

Faker::make(Email::class)
    ->withAttachment(Png::class, 1, 3)  // 1-3 PNG attachments
    ->generate();

// Attach using configured generator instance
$pngGenerator = new Png(Png::getDefaultConfig());
$pngGenerator->width(400, 800)->height(300, 600);

Faker::make(Email::class)
    ->withAttachment($pngGenerator, 2, 5)
    ->generate();
```

**Output Example:**

```
To: John Doe <john.doe@example.com>
From: Jane Smith <jane.smith@example.com>
Subject: Important meeting tomorrow
Date: Fri, 03 Jan 2026 10:30:00 +0000
Message-ID: <3e92e5c2b0d632b3a36fbbb17484b7fe@example.com>
Content-Type: multipart/mixed; boundary="----=_Part_123"

------=_Part_123
Content-Type: text/plain; charset=utf-8
Content-Transfer-Encoding: quoted-printable

Lorem ipsum dolor sit amet, consectetur adipiscing elit...

------=_Part_123
Content-Type: image/png; name="attachment.png"
Content-Disposition: attachment; filename="attachment.png"
Content-Transfer-Encoding: base64

iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==
------=_Part_123--
```

# Advanced Usage

## Custom Generators

Create your own generators by implementing the `Generator` interface:

```php
use DefectiveCode\Faker\Configs\Config;
use DefectiveCode\Faker\Concerns\SetsSeed;
use DefectiveCode\Faker\Generators\Generator;
use DefectiveCode\Faker\Concerns\PreparesFaker;

class MyCustomGenerator implements Generator
{
    use PreparesFaker;
    use SetsSeed;

    public function __construct(public Config $config) {}

    public static function getDefaultConfig(): Config
    {
        return new MyCustomConfig([
            'contentType' => 'application/x-custom',
            'nameGenerator' => NameGenerator::default('extension'),
        ]);
    }

    public function generate(): mixed
    {
        // Your generation logic here
        $data = $this->faker->randomElement(['foo', 'bar', 'baz']);

        $stream = fopen('php://temp', 'w+');
        fwrite($stream, $data);

        return $stream;
    }
}

// Use your custom generator
Faker::make(MyCustomGenerator::class)
    ->toDisk($filesystem)
    ->generate();
```

## Configuration Classes

Each generator uses a configuration class extending `Config`:

```php
use DefectiveCode\Faker\Configs\Config;

class MyCustomConfig extends Config
{
    public int $minValue = 1;
    public int $maxValue = 100;
}
```

## Performance Tips

1. **Use Swoole**: Install the Swoole extension for the best performance
2. **Tune Concurrency**: Match thread count to CPU cores for optimal throughput
3. **Batch Operations**: Generate large batches rather than multiple small runs
4. **Storage Location**: Use fast storage (SSD, local disk) for temporary files before uploading
5. **Network I/O**: When using S3, increase concurrency to maximize bandwidth usage

# Configuration

## Global Configuration Methods

These methods are available on all `Faker` instances:

### `make(string $generator): Faker`

Create a new Faker instance with the specified generator:

```php
Faker::make(Png::class)
```

### `toDisk(Filesystem $filesystem): Faker`

Set the storage destination (required):

```php
Faker::make(Png::class)
    ->toDisk(new Filesystem(new LocalFilesystemAdapter('/storage')))
```

### `basePath(string $basePath): Faker`

Set the base path within the filesystem:

```php
Faker::make(Png::class)
    ->basePath('images/uploads')  // Files stored in /storage/images/uploads/
```

### `count(int $count): Faker`

Set the number of files to generate:

```php
Faker::make(Png::class)
    ->count(1000)
```

### `concurrency(int $threads, ?int $coroutines = null): Faker`

Configure parallel execution:

```php
// Basic concurrency
Faker::make(Png::class)
    ->concurrency(4)

// Swoole-specific: threads and coroutines
Faker::make(Png::class)
    ->concurrency(threads: 4, coroutines: 8)
```

### `seed(int $seed): Faker`

Set seed for deterministic generation:

```php
Faker::make(Png::class)
    ->seed(42)
```

### `nameGenerator(Closure $generator): Faker`

Customize file naming:

```php
Faker::make(Png::class)
    ->nameGenerator(function (int $seed, int $completedFiles, $generator) {
        return "file-{$completedFiles}.png";
    })
```

### `diskOptions(array $diskOptions): Faker`

Pass options to the filesystem adapter:

```php
Faker::make(Png::class)
    ->diskOptions([
        'visibility' => 'public',
        'ACL' => 'public-read',
    ])
```

### `generate(): void`

Execute file generation:

```php
Faker::make(Png::class)->generate();
```
