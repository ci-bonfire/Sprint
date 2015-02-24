# HTTP Digest Authentication

HTTP Digest authentication was created to fill the security holes that HTTP Basic provides. It uses the same username/email and password combination that users are familiar with, but never sends the password over the wire. While this method is significantly more secure than HTTP Basic authentication, the API should probably exist on HTTPS protocol.

If you don't understand HTTP Digest authentication, you should research and get a firm grasp on the concepts first. The following links provide good reading:

* [Sitepoint: Understanding HTTP Digest Access Authentication](http://www.sitepoint.com/understanding-http-digest-access-authentication/)
* [RFC 2617 - the official spec](http://www.faqs.org/rfcs/rfc2617.html)

## Enabling Digest Authentication
Enabling digest authentication is simple:

1. Edit `config/api.php` and set `api.auth_type` to `digest`. 
2. Also set the value of `api.realm` to something specific to your site. Often the site name is good.
3. Ensure that your controllers extend from `APIController`.
4. Create and run the migrations: `php sprint forge api` and answer the questions. This creates a new `api_key` column in the user's table that stores the pre-calced digest auth code for the site's realm. This will also set the current `auth_type` in the `config/api.php` file for you, if the file is writeable.
5. Update the `User_model` to have `createDigestKey` prior to `hashPassword` in the `before_insert` and `before_update` arrays. This method will automatically create the digest key ($A1) and save it whenever enough information exists to change it. Since the User_model is an application-level file we don't want to modify it and mess with any other observers you've already put in place.

```
protected $before_insert = ['hashPassword'];
protected $before_update = ['hashPassword'];
```

## How does Digest work?
Every time you send a request to the API it will look for a `Authorization: Digest` header with the information needed to authenticate the user. 

If the header is missing, or the authentication is incorrect, the system will return a 401 Unauthorized header along with the WWW-Authenticate header telling the app that it should be using Digest authentication, and the `realm`, `nonce`, and `opaque` values. You will need to use these values to calculate your own values and return a header with the Authorization information: 

	Authorization: Digest username="", realm="", nonce="", cnonce="", np="1", opaque="", qop="auth" uri="", response=""

Of course, your header will have the above values filled in. This guide does not cover the exact method to calculate that. Please see the links provided above for all that you need to know. The exact method to do this in your specific use case is beyond the scope of this documentation and isn't something that I'll be able to support in the forums.

Once you have authenticated this way, you can still use the [AuthTrait](security/auth_trait) to restrict users even further by group or permission.

## Things to Be Aware Of

* If you change the realm of your site, all `api_keys` will be invalid and you'll have to prompt the user's for their password again to recreate for the new realm.
* This is implemented in a stateless manner, so this process must be completed for every API request. The nonce is regenerated for each request, so it can not be stored on the client side and re-used for future requests. This helps protect against replay attacks.
