/**
 * Lint-Staged Configuration.
 *
 * @see https://github.com/okonet/lint-staged#configuration
 */

export default {
  '*.php': 'dev composer lint -- --config .php-cs-fixer.dist.php',
  '*.{js,mjs,cjs,ts,md,json,yaml,yml}': 'prettier --write'
}
