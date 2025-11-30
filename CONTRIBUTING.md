# Contributing to Laravel JSON Schema

We welcome contributions to the Laravel JSON Schema package! Please follow these guidelines to help us maintain a high-quality codebase.

## How to Contribute

### Reporting Bugs

If you find a bug, please open an issue on the GitHub repository. Provide a clear and concise description of the bug, including steps to reproduce it, expected behavior, and actual behavior.

### Suggesting Features

If you have a feature request or an idea for an improvement, please open an issue to discuss it. Clearly describe the proposed feature, its benefits, and potential use cases.

### Submitting Pull Requests

1.  **Fork the repository** and clone it to your local machine.
2.  **Create a new branch** for your feature or bug fix: `git checkout -b feature/your-feature-name` or `git checkout -b bugfix/your-bug-fix`.
3.  **Make your changes** and ensure they adhere to the existing coding style.
4.  **Write tests** for your changes. All new features and bug fixes should be covered by tests.
5.  **Run the tests** to ensure everything passes: `composer test`.
6.  **Commit your changes** with a clear and concise commit message.
7.  **Push your branch** to your forked repository.
8.  **Open a Pull Request** against the `main` branch of the original repository.

## Coding Standards

-   Follow the [PSR-2](https://www.php-fig.org/psr/psr-2/) coding style guide.
-   Use meaningful variable and function names.
-   Add DocBlocks to all public methods and properties.
-   Keep your code clean, readable, and well-commented where necessary (explain *why*, not *what*).

## Running Tests

To run the test suite, navigate to the package root directory and execute:

```bash
composer test
```
