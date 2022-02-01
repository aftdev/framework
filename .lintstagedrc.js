/**
 * Lint-Staged Configuration.
 *
 * @see https://github.com/okonet/lint-staged#configuration
 */

export default {
  '*.php': 'dev composer lint -- --config .php-cs-fixer.dist.php',
  '*.{md,json,yaml,yml}': 'prettier --write'
}
