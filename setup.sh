#!/usr/bin/env bash
# If you do not have composer installed. Check out the following documentation
# @see https://getcomposer.org/doc/00-intro.md#installation-linux-unix-osx
# @see https://getcomposer.org/doc/00-intro.md#installation-windows

# Check if we have a "composer.json" file
if [ -e composer.json ]; then
  # Update Composer to install PHP_CodeSniffer
  echo "## Updating Composer";
  composer update

  # Add WordPress Coding Standards Rules
  echo "## Adding coding standards";
  composer create-project wp-coding-standards/wpcs --no-dev

  # Make PHP CodeSniffer aware of the WordPress Coding Standards rules.
  vendor/bin/phpcs --config-set installed_paths wpcs

  # Make the pre-commit executable.
  chmod +x .githooks/pre-commit

  # Set git to use .githooks by setting up symlinks for each hook.
  find .git/hooks -type l -exec rm {} \;
  find .githooks -type f -exec ln -sf ../../{} .git/hooks/ \;
  
else
  echo "There is no composer.json file available"
fi
