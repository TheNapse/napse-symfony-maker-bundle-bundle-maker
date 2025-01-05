# Napse BundleMaker

![License](https://img.shields.io/badge/license-MIT-blue.svg)
![Symfony](https://img.shields.io/badge/symfony-6.x-blue.svg)
![PHP](https://img.shields.io/badge/php-8.1%2B-blue.svg)

## Table of Contents

- [Introduction](#introduction)
- [Features](#features)
- [Installation](#installation)
- [Usage](#usage)
    - [Generating a New Bundle](#generating-a-new-bundle)
- [Generated Structure](#generated-structure)
- [Composer Integration](#composer-integration)
- [Contributing](#contributing)
- [License](#license)
- [Contact](#contact)

## Introduction

**Napse BundleMaker** is a custom Symfony Maker command designed to streamline the creation of new Symfony bundles with
a predefined and consistent structure. By automating the setup process, BundleMaker ensures that all necessary
directories, configuration files, and boilerplate code are generated, allowing developers to focus on implementing
business logic rather than setup tasks.

## Features

- **Automated Directory Structure**: Creates essential directories such as `src`, `config`, `Resources`, `tests`,
  `public`, `translations`, `templates`, and `migrations`.
- **Git Integration**: Generates `.gitignore` and `.gitkeep` files to manage version control effectively.
- **Composer Configuration**: Automatically generates a `composer.json` file with proper PSR-4 autoloading and package
  naming conventions.
- **Namespace Handling**: Supports bundle names with namespaces (e.g., `Napse\DemoBundle`), ensuring correct namespace
  declarations in generated classes.
- **Customizable Paths**: Allows specifying the path where the bundle should be created, with a default option provided.

## Installation

To integrate **Napse BundleMaker** into your Symfony project, follow these steps:

### 1. Clone or Download the BundleMaker

Ensure that the `MakeCustomBundleCommand` and its associated helper classes are placed within your Symfony project's
directory structure, typically under `src/BundleMaker/Maker/`.

### 2. Register the Command

Symfony Maker commands are usually registered automatically if they follow the correct namespace and directory
structure. However, ensure that your `BundleMaker` bundle is registered in `config/bundles.php`:

```php
// config/bundles.php

return [
    // ... other bundles
    Napse\BundleMaker\BundleMaker::class => ['all' => true],
];
```

### 3. Install Dependencies

If your BundleMaker relies on additional packages, ensure they are installed via Composer. Based on the provided code,
ensure that `symfony/maker-bundle` is included as a development dependency:

```bash
composer require symfony/maker-bundle --dev
```

## Usage

Once installed, you can use the `make:bundle` command to generate new Symfony bundles effortlessly.

### Generating a New Bundle

Run the following command in your terminal:

```bash
php bin/console make:bundle
```

**Prompts:**

1. **Bundle Name**: Enter the desired bundle name with namespace (e.g., `Napse\DemoBundle`).
2. **Path**: Specify the directory where the bundle should be created. Press `Enter` to accept the default path
   `../Bundles`.

**Example:**

```bash
$ php bin/console make:bundle
Please enter the name of the bundle (with namespace, e.g., Napse\DemoBundle): Napse\DemoBundle
Please enter the path where the bundle should be created (Default: ../Bundles): 
The bundle "Napse\DemoBundle" was successfully created at "../Bundles/napse-demo-bundle".
```

## Generated Structure

After running the command, the following directory structure will be created under the specified path (
`../Bundles/napse-demo-bundle/` in the example):

```
napse-demo-bundle/
├── src/
│   ├── DemoBundle.php
│   └── .gitkeep
├── config/
│   └── .gitkeep
├── Resources/
│   └── .gitkeep
├── tests/
│   └── .gitkeep
├── public/
│   └── .gitkeep
├── translations/
│   └── .gitkeep
├── templates/
│   └── .gitkeep
├── migrations/
│   └── .gitkeep
├── .gitignore
└── composer.json
```

### File Descriptions

- **src/**: Contains the PHP code for the bundle, including the main bundle class.
- **config/**: Stores configuration files specific to the bundle.
- **Resources/**: Holds resources like templates, assets, and other non-PHP files.
- **tests/**: Directory for PHPUnit tests related to the bundle.
- **public/**: Publicly accessible assets such as images, CSS, and JavaScript files.
- **translations/**: Contains translation files for internationalization.
- **templates/**: Holds Twig templates used by the bundle.
- **migrations/**: Directory for database migration files.
- **.gitignore**: Specifies files and directories to be ignored by Git.
- **.gitkeep**: Placeholder files to ensure empty directories are tracked by Git.
- **composer.json**: Defines package metadata and autoloading rules for Composer.

## Composer Integration

To integrate the newly created bundle into your main Symfony project, follow these steps:

### 1. Update Autoloading

Add the new bundle's namespace to your project's `composer.json` under the `autoload` section:

```json
{
  "autoload": {
    "psr-4": {
      "App\\": "src/",
      "Napse\\DemoBundle\\": "../Bundles/napse-demo-bundle/src/"
    }
  },
  "extra": {
    "symfony": {
      "allow-contrib": false,
      "require": "6.4.*"
    }
  }
}
```

### 2. Regenerate the Autoloader

Run the following command to update Composer's autoloader:

```bash
composer dump-autoload
```

### 3. Register the Bundle

Add the new bundle to your `config/bundles.php` to enable it within your Symfony application:

```php
// config/bundles.php

return [
    // ... other bundles
    Napse\DemoBundle\DemoBundle::class => ['all' => true],
];
```

## License

This project is licensed under the [MIT License](LICENSE).

## Contact

For any questions or feedback, please open an issue or contact the maintainer
at [github@schmitt.codes](mailto:github@schmitt.codes).
