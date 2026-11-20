# Magento ⇄ HexaSync store registration (OAuth 1.0a)

Spec for the two HexaSync endpoints behind the **Register Store** button of the `Beehexa_HexaSync` Magento
module (version after 25.3.1).

This is Magento's standard integration handshake
([Adobe docs: OAuth-based authentication](https://developer.adobe.com/commerce/webapi/get-started/authentication/gs-authentication-oauth/)).
The module starts it from Stores → Configuration → Beehexa Corp, so the merchant never opens
System → Integrations. Request fields are the same ones core Magento sends, plus two extras
(`store_code`, `store_name`).

It replaces the old flow (`GET /callback/magento?k=<RSA-encrypted credentials>`), which is removed from the module.

## Summary

HexaSync implements two endpoints under the **Service Endpoint** the merchant configured
(default `https://app.hexasync.com`):

| # | Endpoint | Caller | Purpose |
|---|---|---|---|
| A | `POST {service}/callback/magento` | Magento server | Receive consumer key, consumer secret and verifier |
| B | `GET {service}/callback/magento/identity` | Merchant's browser (popup) | Sign the merchant in, exchange tokens, finish |

```
Admin browser            Magento                               HexaSync
     |  Register Store     |                                      |
     |-------------------->|  reset integration, create verifier  |
     |                     |-- A. POST /callback/magento -------->|  save pending registration
     |                     |<------------- 200 -------------------|
     |<-- identity URL ----|                                      |
     |-- B. GET /callback/magento/identity (popup) -------------->|  sign in, choose account
     |                     |<-- B1. POST /oauth/token/request ----|
     |                     |---------- request token ------------>|
     |                     |<-- B2. POST /oauth/token/access -----|
     |                     |---------- access token ------------->|  Magento sets integration Active
     |<------------ B3. 302 → success_call_back ------------------|
     |  popup closes, config page reloads                         |
     |                     |<-- POST connector-information -------|  existing API, unchanged
```

---

## A. `POST {service}/callback/magento`

Called server to server by Magento right after the merchant confirms. No authentication header: the request is
identified by the consumer key, and it is only useful to a party that can also complete step B.

### Request

```http
POST /callback/magento HTTP/1.1
Host: app.hexasync.com
Content-Type: application/x-www-form-urlencoded

oauth_consumer_key=mirry75j5w28ozx147b2uwkp843dg577&oauth_consumer_secret=4n1x0q8v6t2c7z9k3m5b1d8f6h2j4l0p&store_base_url=https%3A%2F%2Fshop.example.com%2F&oauth_verifier=8s2k1v9c3x7m5n4b6z0q2w8e4r6t1y3u&store_code=default&store_name=Default+Store+View
```

| Field | Example | Notes |
|---|---|---|
| `oauth_consumer_key` | `mirry75j5w28ozx147b2uwkp843dg577` | 32 chars. Identifies the registration; the browser sends the same value to B |
| `oauth_consumer_secret` | `4n1x0q8v…` | 32 chars. Store encrypted at rest; needed to sign every later request |
| `oauth_verifier` | `8s2k1v9c…` | 32 chars, one-time. Needed in B2 only |
| `store_base_url` | `https://shop.example.com/` | Base URL of the store view. Always ends with `/` |
| `store_code` | `default` | Store view code (module extra, not sent by core Magento) |
| `store_name` | `Default Store View` | Store view name (module extra) |

### What to do

1. Validate that `oauth_consumer_key`, `oauth_consumer_secret`, `oauth_verifier` and `store_base_url` are present.
2. Save a **pending registration** keyed by `oauth_consumer_key`, with a created-at timestamp.
   If one already exists for that key, **replace it**: the merchant clicked Register again and the old verifier
   is dead.
3. Reply `200`.

### Response

```http
HTTP/1.1 200 OK
Content-Type: application/json

{"ok": true}
```

Magento only looks at the status code. Any `2xx` continues the flow; anything else aborts it and the merchant sees
`HexaSync did not accept the registration request (HTTP <status>)`. Redirects are **not** followed, so answer on
this exact URL. The body is ignored.

| Situation | Status |
|---|---|
| Saved | `200` |
| Missing / malformed field | `400` |
| Temporary failure on HexaSync | `503` |

Magento waits up to 5 seconds by default (`oauth/consumer/post_timeout`), so do not do slow work here.

---

## B. `GET {service}/callback/magento/identity`

Opened in a popup (700×620) in the merchant's browser, immediately after A succeeded.

### Request

```http
GET /callback/magento/identity?oauth_consumer_key=mirry75j5w28ozx147b2uwkp843dg577&success_call_back=https%3A%2F%2Fshop.example.com%2Fadmin_x1%2Fadmin%2Fintegration%2FloginSuccessCallback%2Fkey%2F0f3c…%2F HTTP/1.1
Host: app.hexasync.com
```

| Parameter | Notes |
|---|---|
| `oauth_consumer_key` | Must match a pending registration from A |
| `success_call_back` | Magento admin URL to redirect the popup to when done. Opaque: use it exactly as received |

### What to do

1. Look up the pending registration by `oauth_consumer_key`.
   Unknown or older than 5 minutes → show an error page asking the merchant to click Register Store again.
2. Check that `success_call_back` starts with the scheme and host of the saved `store_base_url`; refuse
   otherwise (prevents using this endpoint as an open redirect).
3. Make sure the merchant is signed in to HexaSync (show the login form if not) and let them choose the
   account / workspace the store belongs to. Keep the two query parameters across the login redirect.
4. Run **B1** and **B2** below, server side.
5. Persist the connection on the chosen account: `store_base_url`, `store_code`, `store_name`, consumer key,
   consumer secret, access token, access token secret. Delete the pending registration and the verifier.
6. **B3**: redirect the popup to `success_call_back`.

Steps 4–6 must complete **within 300 seconds of request A** (Magento's `oauth/consumer/expiration_period`).
After that Magento answers B1 with `Consumer key has expired`.

### B1. Request token

```http
POST /oauth/token/request HTTP/1.1
Host: shop.example.com
Authorization: OAuth oauth_consumer_key="mirry75j5w28ozx147b2uwkp843dg577", oauth_nonce="5f1c9a0e7b3d4c2a8e6f0b1d3c5a7e9f", oauth_signature_method="HMAC-SHA256", oauth_timestamp="1795167000", oauth_version="1.0", oauth_signature="kq3J…%3D"
Content-Length: 0
```

- URL: `{store_base_url}oauth/token/request`
- Signing key: `{consumer_secret}&` (empty token secret)

```http
HTTP/1.1 200 OK

oauth_token=4cqw0r7vo0s5goyyqnjb72sqj3vxwr0h&oauth_token_secret=rig3x3j5a9z5j6d4ubjwyf9f1l21itrr
```

### B2. Access token

```http
POST /oauth/token/access HTTP/1.1
Host: shop.example.com
Authorization: OAuth oauth_consumer_key="mirry75j…", oauth_nonce="…", oauth_signature_method="HMAC-SHA256", oauth_timestamp="1795167001", oauth_token="4cqw0r7vo0s5goyyqnjb72sqj3vxwr0h", oauth_verifier="8s2k1v9c3x7m5n4b6z0q2w8e4r6t1y3u", oauth_version="1.0", oauth_signature="…"
Content-Length: 0
```

- URL: `{store_base_url}oauth/token/access`
- `oauth_token` = request token from B1; `oauth_verifier` = value from request A
- Signing key: `{consumer_secret}&{request_token_secret}`

```http
HTTP/1.1 200 OK

oauth_token=0vt8hhk1r2nuipfhaogjr55y2sajgq3r&oauth_token_secret=2g8xx5mvtfvj4q5ioj71jvhxhtowlp5k
```

These are the long-lived **access token** and **access token secret**. Magento marks the integration **Active**
at this point; no action is needed from the merchant in Magento.

If B1 or B2 does not return `200`, show the response body to the merchant in a readable form and ask them to
click Register Store again.

### B3. Finish

```http
HTTP/1.1 302 Found
Location: https://shop.example.com/admin_x1/admin/integration/loginSuccessCallback/key/0f3c…/
```

Magento's page closes the popup; the configuration page then reloads by itself. If the merchant cancels on the
HexaSync page, close the window (`window.close()`); the integration simply stays inactive.

---

## Signing requests (OAuth 1.0a, HMAC-SHA256)

Use `HMAC-SHA256`. Any OAuth 1.0a library works; set the signature method explicitly because many default to
`HMAC-SHA1`.

1. Collect all `oauth_*` parameters except `oauth_signature`, plus the URL query parameters.
   A JSON request body is **not** part of the signature.
2. Percent-encode each name and value (RFC 3986: everything except `A-Z a-z 0-9 - . _ ~`), sort by name,
   join as `name=value` with `&`.
3. Base string = `METHOD & enc(URL without query) & enc(parameter string)`.
4. Key = `enc(consumer_secret) & enc(token_secret)`; token secret is empty for B1.
5. `oauth_signature = base64(HMAC-SHA256(key, base string))`.

Example base string for B1:

```
POST&https%3A%2F%2Fshop.example.com%2Foauth%2Ftoken%2Frequest&oauth_consumer_key%3Dmirry75j5w28ozx147b2uwkp843dg577%26oauth_nonce%3D5f1c9a0e7b3d4c2a8e6f0b1d3c5a7e9f%26oauth_signature_method%3DHMAC-SHA256%26oauth_timestamp%3D1795167000%26oauth_version%3D1.0
```

## Reference implementation

Python, standard library only. The signing and token-exchange logic is what the module was tested against end to
end on Magento 2.4.7 (request A received, B1 and B2 returned `200`, the integration became Active, and signed
calls to `store-information` and `connector-information` returned `200`). The expiry and return-address checks,
storage, login and error pages are for HexaSync to implement and were not part of that test.

```python
import base64, hashlib, hmac, json, secrets, time, urllib.parse, urllib.request

PENDING = {}  # oauth_consumer_key -> fields from request A (use real storage, encrypted)

def enc(value):
    return urllib.parse.quote(str(value), safe='~')

def signed_request(method, url, consumer_key, consumer_secret,
                   token='', token_secret='', extra_oauth=None, json_body=None):
    oauth = {
        'oauth_consumer_key': consumer_key,
        'oauth_nonce': secrets.token_hex(16),
        'oauth_signature_method': 'HMAC-SHA256',
        'oauth_timestamp': str(int(time.time())),
        'oauth_version': '1.0',
    }
    if token:
        oauth['oauth_token'] = token
    oauth.update(extra_oauth or {})

    parsed = urllib.parse.urlsplit(url)
    to_sign = dict(oauth, **dict(urllib.parse.parse_qsl(parsed.query)))
    param_string = '&'.join(f'{enc(k)}={enc(v)}' for k, v in sorted(to_sign.items()))
    base_url = f'{parsed.scheme}://{parsed.netloc}{parsed.path}'
    base_string = '&'.join([method, enc(base_url), enc(param_string)])
    key = f'{enc(consumer_secret)}&{enc(token_secret)}'.encode()
    oauth['oauth_signature'] = base64.b64encode(
        hmac.new(key, base_string.encode(), hashlib.sha256).digest()).decode()

    headers = {'Authorization': 'OAuth ' + ', '.join(f'{k}="{enc(v)}"' for k, v in oauth.items())}
    data = b'' if method == 'POST' else None
    if json_body is not None:
        data = json.dumps(json_body).encode()
        headers['Content-Type'] = 'application/json'
    request = urllib.request.Request(url, data=data, headers=headers, method=method)
    with urllib.request.urlopen(request, timeout=30) as response:
        return response.read().decode()

# A. POST /callback/magento
def callback_magento(form):
    required = ('oauth_consumer_key', 'oauth_consumer_secret', 'oauth_verifier', 'store_base_url')
    if any(not form.get(name) for name in required):
        return 400, {'ok': False, 'error': 'missing field'}
    PENDING[form['oauth_consumer_key']] = dict(form, received_at=time.time())
    return 200, {'ok': True}

# B. GET /callback/magento/identity  (after the merchant is signed in to HexaSync)
def callback_magento_identity(query):
    pending = PENDING.get(query.get('oauth_consumer_key'))
    if not pending or time.time() - pending['received_at'] > 300:
        return 400, 'This registration has expired. Click Register Store in Magento again.'
    if not query['success_call_back'].startswith(origin_of(pending['store_base_url'])):
        return 400, 'Invalid return address.'

    base = pending['store_base_url']
    key, secret = pending['oauth_consumer_key'], pending['oauth_consumer_secret']

    # B1
    request_token = dict(urllib.parse.parse_qsl(
        signed_request('POST', base + 'oauth/token/request', key, secret)))
    # B2
    access_token = dict(urllib.parse.parse_qsl(
        signed_request('POST', base + 'oauth/token/access', key, secret,
                       request_token['oauth_token'], request_token['oauth_token_secret'],
                       extra_oauth={'oauth_verifier': pending['oauth_verifier']})))

    save_connection(pending, access_token['oauth_token'], access_token['oauth_token_secret'])
    del PENDING[key]

    # Tell Magento who registered, so the merchant sees it in the configuration page
    signed_request('POST', base + 'rest/V1/beehexa/hexasync/connector-information', key, secret,
                   access_token['oauth_token'], access_token['oauth_token_secret'],
                   json_body={'connector': {
                       'account': 'merchant@example.com', 'status': '1',
                       'store_name': pending.get('store_name'),
                       'store_code': pending.get('store_code'), 'version': '25.4.0'}})

    return 302, query['success_call_back']  # B3

def origin_of(url):
    parts = urllib.parse.urlsplit(url)
    return f'{parts.scheme}://{parts.netloc}/'
```

## After registration

- REST base: `{store_base_url}rest/`. Sign every call with all four values (consumer key and secret, access
  token and secret), HMAC-SHA256, as above. Example: `GET {store_base_url}rest/V1/store-information`.
- `POST /V1/beehexa/hexasync/connector-information` (existing API, payload shown in the sample) is what makes
  the account, store name and status appear in Magento. Call it once after B2.
- The integration has access to all API resources, as before.

## Test checklist for HexaSync

- [ ] A returns `200` for a valid POST and `400` when a field is missing
- [ ] A called twice for the same consumer key keeps only the newest verifier
- [ ] B with an unknown or expired consumer key shows an error and does not redirect
- [ ] B rejects a `success_call_back` on a different host than `store_base_url`
- [ ] B survives the login redirect (query parameters preserved)
- [ ] After B, the integration is Active in Magento and a signed REST call returns `200`
- [ ] Clicking Register Store on an already connected store replaces the stored tokens (the old ones stop working
      as soon as the merchant confirms in Magento)
- [ ] A merchant who takes more than 5 minutes sees a clear "try again" message

## Notes on behaviour in Magento

- Register Store always deletes the integration's current token and sets it inactive before request A
  (Magento allows one token per consumer). A connected store stops syncing until the new handshake completes;
  the merchant is warned before confirming. This also applies when request A then fails.
- `http://` Service Endpoints are accepted only when Magento runs in developer mode; production requires https,
  because request A carries the consumer secret.
- The two paths are constants in `Model/OauthHandshake.php` (`CALLBACK_PATH`, `IDENTITY_PATH`).
