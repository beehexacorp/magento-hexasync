# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## What this is

`Beehexa_HexaSync` (`beehexacorp/module-hexa-sync`) is a Magento 2.4 module published on Adobe Commerce Marketplace. It does no data syncing itself: its job is to create a Magento integration (OAuth credentials) and hand those credentials to the external HexaSync platform, which then calls Magento's REST API.

This git repository is the module directory only (`app/code/Beehexa/HexaSync` inside a Magento install, remote `beehexacorp/magento-hexasync`, branch `M2.4`). The surrounding Magento root (`../../../..`, Magento 2.4.7-p1) is a separate, unversioned dev sandbox.

## Commands

The host PHP lacks the DOM/SimpleXML extensions, so `bin/magento`, `phpcs` and `phpunit` fail on the host. Run them in the site's PHP 8.2 CLI container via the wrapper script in the Magento root (Magento is mounted at `/app`; the `deploy` service is the CLI image):

```bash
cd /home/hidrole/Workspace/Sites/defaultm2.local
./docker-compose run --rm deploy bin/magento setup:upgrade
./docker-compose run --rm deploy bin/magento setup:di:compile
./docker-compose run --rm deploy bin/magento cache:flush
./docker-compose run --rm deploy vendor/bin/phpcs --standard=Magento2 --extensions=php,phtml app/code/Beehexa/HexaSync
```

- Pass the command and its arguments directly, as above. The wrapper script expands `$@` unquoted, so `bash -c 'cmd1; cmd2'` is word-split and only the first word runs.
- The owner's interactive shell has `alias php='./docker-compose run --rm deploy bash'` (opens a shell in the container); the alias is not available in non-interactive shells.
- The web containers are started separately by the owner (`apache up -d`). The site is `https://defaultm2.local/` behind the shared `nginx-proxy` on 127.0.0.1, with a certificate signed by the private CA at `/home/hidrole/Workspace/.shared-services/nginx-proxy/certs/.root-ca/rootCA.pem` (renewed 2026-11-20, valid to 2036). The CA is trusted in the user's NSS database (`~/.pki/nssdb`), so the Claude desktop built-in browser can open the site.
- Marketplace submission runs the `Magento2` PHPCS standard; it must pass before a release. Baseline on 25.3.1: 0 errors, about 215 warnings (mostly missing docblocks in `Api/Data/*Interface.php`).
- There are no tests in this module (no `Test/` directory).
- The sandbox runs in developer mode. `Beehexa_Base` was copied into `app/code/Beehexa/Base` from another local site for testing; it is not part of this repository.
- After changing `etc/*.xml`, constructor signatures, or anything under `Api/`, run `setup:di:compile` (or clear `generated/`) since factories and interceptors are generated.

## Architecture

### Registration flow (the core of the module)

Register Store runs Magento's standard OAuth 1.0a integration handshake from the config page, so the merchant never opens System → Integrations. The full contract with the HexaSync platform is in `docs/hexasync-oauth-handshake.md`; keep that file in sync with any change here.

1. Admin opens Stores → Configuration → Beehexa Corp → HexaSync Integration (`etc/adminhtml/system.xml`) and clicks **Register Store**; `view/adminhtml/web/js/register-store.js` asks for confirmation, opens a popup, and POSTs to `hexasync/integration/register`.
2. `Controller/Adminhtml/Integration/Register` → `Model/OauthHandshake::start()`: finds or creates the integration named by `hexasync/integration_name`, deletes its token and sets it inactive, sets its endpoint / identity link URL, creates a verifier token, and POSTs to `<service endpoint>/callback/magento`. The POST carries the same fields as core `OauthService::postToConsumer` (consumer key, consumer secret, verifier, store base URL) plus store code and name; it is done here instead of through core so the HTTP status is checked.
3. The popup goes to `<service endpoint>/callback/magento/identity?oauth_consumer_key=…&success_call_back=…`.
4. HexaSync exchanges the verifier through Magento's own `/oauth/token/request` and `/oauth/token/access`; core marks the integration Active. HexaSync redirects the popup to core's `adminhtml/integration/loginSuccessCallback`, which closes it; the JS reloads the page.
5. HexaSync calls `POST /V1/beehexa/hexasync/connector-information`, which stores account/status/store name/version in `core_config_data` under `beehexa/connector/*` (scope `stores`); `Block/System/Config/Form/Field/RegisteredInformation` shows it.

The integration is also created (inactive) at install time by the data patch `Setup/Patch/Data/AddingIntegrationUser`.

To test the handshake locally, run a small server implementing the two URLs (the reference code in the spec is enough) and use an `http://<docker gateway ip>:<port>` Service Endpoint, which is allowed in developer mode only.

### Other pieces

- `GET /V1/store-information[/:storeId]` (`Model/StoreInformation`) exposes Magento's store address/contact info per store view for HexaSync to read.
- `Block/Adminhtml/Integration` replaces the core `Magento\Integration\Block\Adminhtml\Integration` via a `<preference>` in `etc/adminhtml/di.xml` to add a "Generate HexaSync User" button on System → Integrations.

### Sync log (since 25.1.x)

HexaSync pushes sync results to `POST /V1/hexasync/log` (`Model/LogRepository`, table `beehexa_hexasync_log`). Admins read them under the HexaSync menu → Sync Log (the menu parent `Beehexa_Base::top_level` comes from `Beehexa_Base`, so that module must be installed). `Cron/CheckNewLogs` runs every 12 hours and raises an admin notification plus an optional email (`beehexa/notifications/*`) when new log items arrive.

### Things that are easy to get wrong

- **PHP floor is 8.1** (`composer.json`). Do not use typed class constants (`const string X`) or other 8.3-only syntax.
- **Service Endpoint** is the config field `beehexa/hexasync/production_service` (default `https://app.hexasync.com` in `etc/config.xml`). Keep the field id: existing stores have values saved under it.
- **Connector info scope**: `beehexa/connector/*` must be written with scope `stores` (`ScopeInterface::SCOPE_STORES`). Writing `store` produces rows Magento never reads; `Setup/Patch/Data/MigrateConnectorInfoScope` moves rows left by older versions.
- **Wire contract with the HexaSync platform must not change without the platform team**: everything in `docs/hexasync-oauth-handshake.md`, the REST route URLs in `etc/webapi.xml`, and the integration's `all_resources` access.
- `Register` and `Regenerate` admin controllers are POST-only; callers must send `form_key`.
- **Two config namespaces**: `hexasync/integration_name` (default in `etc/config.xml`) versus `beehexa/...` for everything else (`Helper/Data::XML_CONFIG_PREFIX`).
- The "View on HexaSync" link in the log grid uses the configured Service Endpoint (default scope), falling back to `Helper/Data::HEXASYNC_BASE_URL` (`https://app.hexasync.com`). `beta.hexasync.com` is no longer used.
- `HexaSyncIntegrationInterface` does not declare `getIntegration()`, yet `Regenerate` calls it on the interface type; it only exists on the concrete `HexaSyncIntegrationManagement`.
- The config section `beehexa` is shared with `Beehexa_Base` and uses the resource `Magento_Backend::admin`; changing it here also affects Base's group.
- Unused code that looks live: `HexaSyncIntegrationManagement::getByName()` and `::activateIntegration()`, `Model/Data/HexaSyncIntegrationData`, and the `GenerateIntegration` block/template/JS (not referenced from `system.xml`).

### Versioning

`composer.json` `version` is what Marketplace publishes. Release lines live on branches/tags of `origin` (`M2.4` is the default branch; `V25.3.1` is ahead of it). Run `git fetch --tags` and check which line is newest before starting work.
