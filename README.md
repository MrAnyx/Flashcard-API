<img src="https://raw.githubusercontent.com/MrAnyx/Flashcard-API/refs/heads/master/assets/banner.png">

<div align="center">
  <p align="center">
    Create, manage and practice your flashcard with ease!
    <br />
    <a href="#">Explore the docs</a>
    ·
    <a href="https://github.com/MrAnyx/Flashcard-API/issues/new/choose">Submit a request</a>
    <br />
  </p>
</div>

<div align="center">
  <a href="https://github.com/MrAnyx/Flashcard-API/tags"><img alt="GitHub Tag" src="https://img.shields.io/github/v/tag/MrAnyx/Flashcard-API?style=flat&colorB=38BDF8"></a>
  <a href="https://github.com/MrAnyx/Flashcard-API/stargazers"><img alt="GitHub Repo stars" src="https://img.shields.io/github/stars/MrAnyx/Flashcard-API?style=flat&colorB=38BDF8"></a>
  <a href="https://github.com/MrAnyx/Flashcard-API/graphs/contributors"><img alt="GitHub contributors" src="https://img.shields.io/github/contributors/MrAnyx/Flashcard-API?style=flat&colorB=38BDF8"></a>
  <a href="https://github.com/MrAnyx/Flashcard-API/issues"><img alt="GitHub Issues or Pull Requests" src="https://img.shields.io/github/issues-raw/MrAnyx/Flashcard-API?style=flat&colorB=38BDF8"></a>
  <a href="https://github.com/MrAnyx/Flashcard-API/blob/master/LICENSE"><img alt="GitHub License" src="https://img.shields.io/github/license/MrAnyx/Flashcard-API?style=flat&colorB=38BDF8"></a>
</div>

<br />

> [!NOTE]
> This project is still in active development. It may change in the future.

## About The Project

<picture>
  <source media="(prefers-color-scheme: dark)" srcset="https://raw.githubusercontent.com/MrAnyx/Flashcard-API/refs/heads/master/assets/dark.png">
  <source media="(prefers-color-scheme: light)" srcset="https://raw.githubusercontent.com/MrAnyx/Flashcard-API/refs/heads/master/assets/light.png">
  <img src="https://raw.githubusercontent.com/MrAnyx/Flashcard-API/refs/heads/master/assets/dark.png">
</picture>

This project is the [Symfony backend REST API](https://github.com/MrAnyx/Flashcard-API) for a [flashcard](https://en.wikipedia.org/wiki/Flashcard) platform.

I started this project because I couldn’t find a flashcard app that combined both a modern, visually appealing interface and effective functionality.

My goal was to create a good-looking flashcard app that provides a streamlined user experience.

Additionally, I wanted to deepen my understanding of the algorithms that drive flashcard-based learning, particularly those focused on optimizing review timing and content retention.

While I have always worked with [Symfony](https://symfony.com/) for both frontend and backend, I wanted to expand into using a dedicated frontend framework like [Nuxt](https://nuxt.com/) to take advantage of [Vue.js](https://vuejs.org/), which I really enjoy working with.

This project merges these goals by offering an intuitive platform that makes studying both efficient and enjoyable.

## Built With

This project is entirely built using [Symfony](https://symfony.com/) and its ecosystem like [Doctrine](https://www.doctrine-project.org/). It also uses [Docker](https://www.docker.com/) and [Devcontainer](https://containers.dev/) when developing.

## Contributing

Contributions are what make the open source community such an amazing place to learn, inspire, and create. Any contributions you make are **greatly appreciated**.

If you have a suggestion that would make this better, please fork the repo and create a pull request. You can also simply open an issue with the tag "enhancement".
Don't forget to give the project a star! Thanks again!

1. **Fork the repository**

   Start by forking the project repository on GitHub. This creates a copy of the project in your account where you can freely make changes.

2. **Clone the repository**

   ```bash
   git clone https://github.com/<your-username>/Flashcard-API.git
   ```

3. **Create a new branch**

   Use a descriptive branch name that reflects the purpose of your changes. For example:

   ```bash
   git checkout -b feature/new-feature
   ```

4. **Make changes**

   Implement your changes. Make sure to follow coding standards and write clear, maintainable code. Add tests where appropriate, especially if your changes affect core functionality.

5. **Test your changes**

   Ensure that your changes don’t break existing functionality by running the test suite:

   ```bash
   # Unit tests
   ./vendor/bin/pest

   # Static tests
   vendor/bin/phpstan analyse -c phpstan.neon --memory-limit 500M  --ansi --error-format=table

   # Code style
   ./vendor/bin/php-cs-fixer fix --dry-run
   ```

6. **Sync your changes**

   Write clear and concise commit messages that describe what you’ve done. It must follow the [Conventional Commit](https://www.conventionalcommits.org/) standard. Then, push your changes and open a pull request.

## Code of Conduct

Please respect other contributors and maintainers by following our Code of Conduct. Contributions should align with the values of inclusivity, transparency, and respect for everyone involved in the project.

## Need Help?

If you’re new to open-source contribution or have questions, feel free to open an [issue](https://github.com/MrAnyx/Flashcard-API/issues/new/choose) or open a [discussion thread](https://github.com/MrAnyx/Flashcard-API/discussions). We’re happy to guide you through your first contribution!

## Top contributors:

<a href="https://github.com/MrAnyx/Flashcard-API/graphs/contributors">
  <img src="https://contrib.rocks/image?repo=MrAnyx/Flashcard-API" alt="contrib.rocks image" />
</a>

## License

Distributed under the AGPL-3.0 License. See [License](https://github.com/MrAnyx/Flashcard-API/blob/master/LICENSE) for more information.

## Acknowledgments

This project wouldn’t be possible without the resources from the following individuals and communities:

- [Grafikart](https://grafikart.fr/) for all the great tutorials about Symfony
- [Symfony](https://symfony.com/) for all the great tools and ecosystem they created
- [Doctrine ORM](https://www.doctrine-project.org/index.html) for their easy to use ORM for PHP and Symfony
- [Symfony Cast](https://symfonycasts.com/) for all the Symfony tips
- [Open Spaced Repetition]() for their amazing [Free Spaced Repetition Scheduler (FSRS)](https://github.com/open-spaced-repetition/fsrs4anki/wiki) algorithm
- [Best-README-Template](https://github.com/othneildrew/Best-README-Template) for this beautiful README template

## Useful links

- https://www.baeldung.com/rest-api-error-handling-best-practices
- https://github.com/open-spaced-repetition/fsrs4anki/wiki/Compare-Anki's-built-in-scheduler-and-FSRS
- https://github.com/open-spaced-repetition/fsrs4anki/wiki/The-Algorithm#fsrs-v4
- https://huggingface.co/spaces/open-spaced-repetition/fsrs4anki_previewer
- https://symfony.com/doc/2.x/security/access_denied_handler.html
- https://symfony.com/doc/current/security.html#security-securing-controller-attributes
