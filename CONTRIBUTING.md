<img src="https://raw.githubusercontent.com/meeio-app/backend/refs/heads/master/assets/banner.png">

<div align="center">
  <p align="center">
    Create, manage and practice your flashcard with ease!
    <br />
    <a href="#">Explore the docs</a>
    ·
    <a href="https://github.com/meeio-app/backend/issues/new/choose">Submit a request</a>
    <br />
  </p>
</div>

## Overview

The Meeio API is a Symfony REST API designed to facilitate the creation, organization, and review of flashcards. Built with a developer-friendly approach, this API serves as the backend for a Nuxt.js application, providing users with the tools to efficiently manage their learning resources.

## Features

- **Create and Organize Flashcards**: Users can create flashcards categorized by topics and units.
- **Review Sessions**: Facilitates organized review sessions for effective learning.
- **RESTful API**: Built using Symfony 6, adhering to best practices in API development.
- **Docker Support**: Easy setup and development using Docker and Devcontainer.

## Getting Started

To get started with the Meeio API, follow the instructions below.

### Prerequisites

Make sure you have the following installed:

- [Docker](https://www.docker.com/get-started)
- [Docker Compose](https://docs.docker.com/compose/install/)
- [Visual Studio Code](https://code.visualstudio.com/)

### Installation

1. **Clone the repository**:

   ```bash
   git clone https://github.com/meeio-app/backend.git
   cd backend
   ```

> [!IMPORTANT]
> Once cloned, the recommended way to work is by using the **devcontainer** prebuilt file. Is contains everything you want to work properly. For that, we recommend using the **Visual Studio Code** editor.
Otherwise, you can still do the manual way.

2. **Build the Docker containers**:

   ```bash
   cd .devcontainer
   docker-compose up -d
   ```

3. **Prepare the project**:

   ```bash
   docker-compose exec task a:d:c
   ```

4. **Run the application**:

   The API should now be accessible at `http://localhost`.

## Contributing Guidelines

We welcome contributions to the Meeio API! To maintain a high-quality codebase and collaborative environment, please follow these guidelines:

### Code of Conduct

This project adheres to a [Code of Conduct](CODE_OF_CONDUCT.md). We expect all contributors to treat each other with respect and to foster a welcoming environment.

### Getting Started

1. **Fork the repository**: Click on the "Fork" button in the top right corner of the repository page.

2. **Clone your fork**:

   ```bash
   git clone https://github.com/your-username/backend.git
   ```

### Conventional Commits

All commit messages should adhere to the [Conventional Commits](https://www.conventionalcommits.org/en/v1.0.0/) specification. Use the following format:

```
type(scope): subject

[optional body]

[optional footer]
```

**Types of Commits**:
- **feat**: A new feature
- **fix**: A bug fix
- **docs**: Documentation only changes
- **style**: Formatting changes that do not affect code
- **refactor**: Code changes that do not add a feature or fix a bug
- **perf**: Performance improvements
- **test**: Adding or correcting tests
- **chore**: Changes to the build process or auxiliary tools
- **core**: Changes that does't affect the code itself but rather the Github project like a readme file

### Testing

> [!NOTE]
> To run all tests at once, you can simply tun the taskfile command
> ```bash
> task a:t
> ```

Before submitting your changes, ensure that all tests pass:

1. **Run unit tests**:
   ```bash
   composer run test
   ```
   
2. **Add tests** for new features or bug fixes where applicable.

### Code Style

We use [PHP Coding Standards Fixer]([https://github.com/squizlabs/PHP_CodeSniffer](https://cs.symfony.com/)) to enforce code style. Please check your code for style issues before submitting:

```bash
composer run cs:check
# or
composer run cs:fix
```

### Static Analysis

We utilize [PHPStan](https://phpstan.org/) for static analysis. Make sure to run static analysis on your code:

```bash
composer run stan
```

### Issue Tracking

Use GitHub Issues for tracking bugs and feature requests. Please use the provided issue templates when creating a new issue.

### Discussions

We encourage open discussions about features, bugs, and improvements. Please be respectful when responding to others, whether in issues or discussions. Constructive feedback helps create a positive and productive environment.

### Submitting Changes

1. **Create a new branch**:
   
   ```bash
   git checkout -b feature/your-feature-name
   ```
  
2. **Make your changes** and commit them following the [Conventional Commits](#conventional-commits) guidelines.
   
3. **Push your changes** to your fork:

   ```bash
   git push origin feature/your-feature-name
   ```
   
5. **Open a pull request**: Go to the original repository, click on the "Pull requests" tab, and then click "New pull request."

## License

Distributed under the AGPL-3.0 License. See [License](https://github.com/meeio-app/backend/blob/master/LICENSE) for more information.
