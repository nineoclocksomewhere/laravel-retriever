# Testing and validation

## Local checks

Run these commands from the package root:

```bash
composer install
composer validate --strict
find . -path './vendor' -prune -o -path './.git' -prune -o -name '*.php' -print0 | xargs -0 -n1 php -l
composer test
git diff --check
```

## CI matrix

The `1.x` workflow runs on PHP 7.4 and PHP 8.0–8.5. Each job lints PHP syntax,
validates Composer files, installs compatible PHPUnit and Testbench
dependencies, and runs `composer test`. The workflow reads package registry
credentials from the `COMPOSER_AUTH` secret.

## Running the matrix with Act

Build or obtain a Docker image for each PHP/Testbench pair with Composer and a
`docker` user. Run this from the package root; the loop stops at the first
failure:

```bash
for matrix in \
  '7.4:^6.0' \
  '8.0:^7.0' \
  '8.1:^8.0' \
  '8.2:^9.0' \
  '8.3:^10.0' \
  '8.3:^11.0' \
  '8.4:^11.0' \
  '8.5:^11.0'
do
  php_version="${matrix%%:*}"
  testbench="${matrix#*:}"
  printf '\n===== PHP %s / Testbench %s =====\n' "$php_version" "$testbench"
  act pull_request \
    -W .github/workflows/php.yml \
    -j build \
    --matrix "php-version:$php_version" \
    --matrix "testbench:$testbench" \
    -P "ubuntu-latest=nocs/php:$php_version" \
    --pull=false \
    --container-options "--user docker -v $HOME/.config/nocs/.composer:/tmp/composer-auth:ro" \
    --env COMPOSER_HOME=/tmp/composer-auth \
    --env COMPOSER_CACHE_DIR=/tmp/composer-cache \
    --env ACT_PREINSTALLED_PHP=true || {
      act_status=$?
      printf 'Act failed for PHP %s / Testbench %s (status %s); stopping the loop.\n' "$php_version" "$testbench" "$act_status" >&2
      break
    }
done
```

The selected image supplies PHP and Composer instead of the setup action. The
read-only Composer home mount supplies local registry credentials, and
Composer writes its cache to `/tmp/composer-cache`.
