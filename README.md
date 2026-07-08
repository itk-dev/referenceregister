# Referenceregister

``` shell
task
```

> [!TIP]
> If development OIDC login does not work, try deleting the cache pools from [Symfony's share
> directory](https://symfony.com/blog/new-in-symfony-7-4-share-directory):
>
> ``` shell
> rm -fr var/share/dev/pools/*
> ```
>
> This is automatically done when running `task site:update`.

## Development

For development – and **only for development** – you can set

``` dotenv
APP_DO_NOT_HASH_ENTRY_ID=true
```

in your local environment (e.g. in `.env.local`) to disable hashing of entry IDs.
