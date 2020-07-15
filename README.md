# Example WordPress Composer

[![CircleCI](https://circleci.com/gh/neuger-digital/wordpress-composer.svg?style=svg&circle-token=8f0e743c6bae3977e63be4accfa103e878265064)](https://circleci.com/gh/neuger-digital/example-wordpress-composer)

This repository is a reference implementation and start state for a modern WordPress workflow utilizing [Composer](https://getcomposer.org/), Continuous Integration (CI), Automated Testing, and Pantheon. Even though this is a good starting point, you will need to customize and maintain the CI/testing set up for your projects.

This repository is based off the [Example WordPress Composer](https://github.com/pantheon-systems/example-wordpress-composer) workflow, which utilizes [Composer](https://getcomposer.org/), [CircleCI](https://circleci.com/) and [Pantheon](https://pantheon.io/). Based on that workflow, all of the website files will be placed in the `/web` folder and WordPress will be installed in the `/web/wp` folder.

This repository is meant to be copied one-time by the the [Terminus Build Tools Plugin](https://github.com/pantheon-systems/terminus-build-tools-plugin) but can also be used as a template. It should not be cloned or forked directly.

The Terminus Build Tools plugin will scaffold a new project, including:

* A Git repository
* A free Pantheon sandbox site
* Continuous Integration configuration/credential set up

For more details and instructions on creating a new project, see the [Terminus Build Tools Plugin](https://github.com/pantheon-systems/terminus-build-tools-plugin/).

## Getting started
These are instructions for installing this site on a Mac.

### Before you begin
* You will need to have [Composer](https://getcomposer.org/) installed
* You will need to have [Lando](https://docs.devwithlando.io/installation/system-requirements.html) installed
* You will need to have [Node.js](https://nodejs.org/en/download/) installed

NOTE: Each instance of 'site-name' should be your project's namespace that's consistent throught your project.

### Setting up a New Site
* Make sure [Terminus Build Tools](https://github.com/pantheon-systems/terminus-build-tools-plugin#installation) is installed on your local machine
* Run `terminus build:project:create --team='Neuger Communications Group' --org="neuger-digital" --ci="circleci" --git="github" --visibility="private" pantheon-systems/example-wordpress-composer site-name`
* In that directory run`git clone git@github.com:neuger-digital/site-name.git`
* Follow the instructions for 'setting up an existing site'

### Setting up an Existing Site
* Create a github repository with the name 'site-name'
* Create a Pantheon site with the WordPress upstream with the name 'site-name'
* Create a directory with the 'site-name', go into it
* Run `lando init` to initialize the environment
	* Choose 'github'
	* Select your user
	* Select the repo 'neuger-digital/site-name'
	* Use the 'pantheon' recipe
	* Select the site 'site-name'
* Run `lando start`
* Run `lando composer install --no-ansi --no-interaction --optimize-autoloader --no-progress` to download dependencies
* Run `lando pull --code=none` to download the media files and database from Pantheon
* Commit and push your project and it should start up a new CircleCI project. In that project you'll want to access Project Settings and go to Environemnt Variables and add the following:
	* TERMINUS_SITE:  Name of the Pantheon site to run tests on, e.g. my_site
	* TERMINUS_TOKEN: The Pantheon machine token
	* GITHUB_TOKEN:   The GitHub personal access token
	* GIT_EMAIL:      The email address to use when making commits

	* TEST_SITE_NAME: The name of the test site to provide when installing
	* ADMIN_PASSWORD: The admin password to use when installing
	* ADMIN_EMAIL:    The email address to give the admin when installing

* You'll also have to add in SSH keys to the CircleCI project, which you can do by running your workflow with SSH and while doing that run the following command `terminus build:project:repair site-name`. You may also have to check to see whether you're logged in via Terminus with `terminus auth:whoami` and if it doesn't show your email address then you can log in with `terminus auth:login` and you may want to do this with a Pantheon Access Token, then run the repair again.
* After this is all set up it should run the workflow "deploy_to_pantheon" and the artifact will be pushed to your Pantheon site.

**Warning:** do NOT push/pull code between Lando and Pantheon directly. All code should be pushed to GitHub and deployed to Pantheon through a continuous integration service, such as CircleCI.

Composer, Terminus and wp-cli commands should be run in Lando rather than on the host machine. This is done by prefixing the desired command with `lando`. For example, after a change to `composer.json` run `lando composer update` rather than `composer update`.

## Important files and directories

### `/web`

Pantheon will serve the site from the `/web` subdirectory due to the configuration in `pantheon.yml`. This is necessary for a Composer based workflow. Having your website in this subdirectory also allows for tests, scripts, and other files related to your project to be stored in your repo without polluting your web document root or being web accessible from Pantheon. They may still be accessible from your version control project if it is public. See [the `pantheon.yml`](https://pantheon.io/docs/pantheon-yml/#nested-docroot) documentation for details.

### `/web/wp`

Even within the `/web` directory you may notice that other directories and files are in different places compared to a default WordPress installation. [WordPress allows installing WordPress core in its own directory](https://codex.wordpress.org/Giving_WordPress_Its_Own_Directory), which is necessary when installing WordPress with Composer.

See `/web/wp-config.php` for key settings, such as `WP_SITEURL`, which must be updated so that WordPress core functions properly in the relocated `/web/wp` directory. The overall layout of directories in the repo is inspired by, but doesn't exactly mirror, [Bedrock](https://github.com/roots/bedrock).

### `composer.json`
This project uses Composer to manage third-party PHP dependencies.

The `require` section of `composer.json` should be used for any dependencies your web project needs, even those that might only be used on non-Live environments. All dependencies in `require` will be pushed to Pantheon.

The `require-dev` section should be used for dependencies that are not a part of the web application but are necesarry to build or test the project. Some example are `php_codesniffer` and `phpunit`. Dev dependencies will not be deployed to Pantheon.

If you are just browsing this repository on GitHub, you may not see some of the directories mentioned above, such as `web/wp`. That is because WordPress core and its plugins are installed via Composer and ignored in the `.gitignore` file.

A custom, [Composer version of WordPress for Pantheon](https://github.com/pantheon-systems/wordpress-composer/) is used as the source for WordPress core.

Third party WordPress dependencies, such as plugins and themes, are added to the project via `composer.json`. The `composer.lock` file keeps track of the exact version of dependency. [Composer `installer-paths`](https://getcomposer.org/doc/faqs/how-do-i-install-a-package-to-a-custom-path-for-my-framework.md#how-do-i-install-a-package-to-a-custom-path-for-my-framework-) are used to ensure the WordPress dependencies are downloaded into the appropriate directory.

Non-WordPress dependencies are downloaded to the `/vendor` directory.

### `.ci`
This `.ci` directory is where all of the scripts that run on Continuous Integration are stored. Provider specific configuration files, such as `.circle/config.yml` and `.gitlab-ci.yml`, make use of these scripts.

The scripts are organized into subdirectories of `.ci` according to their function: `build`, `deploy`, or `test`.

#### Build Scripts `.ci/build`
Steps for building an artifact suitable for deployment. Feel free to add other build scripts here, such as installing Node dependencies, depending on your needs.

- `.ci/build/php` installs PHP dependencies with Composer

#### Build Scripts `.ci/deploy`
Scripts for facilitating code deployment to Pantheon.

- `.ci/deploy/pantheon/create-multidev` creates a new [Pantheon multidev environment](https://pantheon.io/docs/multidev/) for branches other than the default Git branch
  - Note that not all users have multidev access. Please consult [the multidev FAQ doc](https://pantheon.io/docs/multidev-faq/) for details.
- `.ci/deploy/pantheon/dev-multidev` deploys the built artifact to either the Pantheon `dev` or a multidev environment, depending on the Git branch

#### Automated Test Scripts `.ci/tests`
Scripts that run automated tests. Feel free to add or remove scripts here depending on your testing needs.

**Static Testing** `.ci/test/static` and `tests/unit`
Static tests analyze code without executing it. It is good at detecting syntax error but not functionality.

- `.ci/test/static/run` Runs [PHP CodeSniffer](https://github.com/squizlabs/PHP_CodeSniffer) with [WordPress coding standards](https://github.com/WordPress/WordPress-Coding-Standards), PHP Unit, and [PHP syntax checking](https://www.php.net/manual/en/function.php-check-syntax.php).
- `tests/unit/bootstrap.php` Bootstraps the Composer autoloader
- `tests/unit/TestAssert.php` An example Unit test. Project specific test files will need to be created in `tests/unit`.
