# OrcidPHPClient
php client to send and read workflows on orcid

This library was started to support the ORCID OAuth2 authentication workflow. It also supports basic profile access, but is a work in progress. More features are to come as needed by the developer or requested/contributed by other interested parties.

## Usage

### OAuth2

#### 3-Legged Oauth Authorization

To go through the 3-legged oauth process, you must start by redirecting the user to the ORCID authorization page.

```php
// Set up the config for the ORCID API instance
$oauth = new Oauth;
$oauth->setClientId($clientId)
      ->setScope('/authenticate')
      ->setState($state)
      ->showLogin()
      ->setRedirectUri($redirectUri);

// Create and follow the authorization URL
header("Location: " . $oauth->getAuthorizationUrl());
```

Most of the options described in the ORCID documentation (http://members.orcid.org/api/customize-oauth-login-screen) concerning customizing the user authorization experience are encapsulated in the OAuth class.

Once the user authorizes your app, they will be redirected back to your redirect URI. From there, you can exchange the authorization code for an access token.

```php
if (!isset($_GET['code']))
{
	// User didn't authorize our app
	throw new Exception('Authorization failed');
}

$oauth = new Oauth;
$oauth->setClientId($clientId)
      ->setClientSecret($clientSecret)
      ->setRedirectUri($redirectUri);

// Authenticate the user
$oauth->authenticate($_GET['code']);

// Check for successful authentication
if ($oauth->isAuthenticated())
{
	$orcid = new Profile($oauth);

	// Get ORCID iD
	$id = $orcid->id();
}
```

This example uses the ORCID public API. A members API is also available, but the OAuth process is essentially the same.

#### OClient 

An Orcid client makes it possible to communicate with the orcid count whose authentication elements are contained in the Oauth object which is passed to the Oclient constructor 
 
<pre>
// Check for successful authentication

if ($oauth->isAuthenticated())
{
    //1- creation of an orcid client
	$OrcidClient=new OClient($oauth);
}
   // 2-creation of an Orcid work
        $work=new Work();
        $work->setTitle("Les stalagmites du réseau du trou Noir")
             ->setType("Work-paper")
             ->setJournalTitle("naturephysic")
             ->setCitation("The citation........")
            ->setShortDescription("") 
            //add Author with Author FullName 
            ->addAuthor("Benjamin Lans")
            ->addAuthor("Richard Maire")
            ->addAuthor("Richard Ortega")
            ->addAuthor("Guillaume Devès")
            //add subtitle
            ->addSubTitle("subtitle one ")
            ->addSubTitle("subtitle two")
            ->addSubTitle("subtitle three ")
            // add External Ident 
            ->addExternalIdent("doi","10.1038/nphys1170","https://www.nature.com/articles/nphys1170","self")
            ->addExternalIdent("uri","00199711","","https://hal.archives-ouvertes.fr/hal-00199711");
     //3- send the Work
     $OrcidClient->postOne($work); 
</pre>
    
The minimum configuration for sending an Orcid Work is to define the title, the type of document and add at least an external identifier.
The add methods allow you to add several values ​​for the same parameter, by adding a box containing the value to the parameter table.

The different methods of Oclient are:
PostOne: allows you to send an Orcid work taken as a parameter
PostMultiple:
allows to send several jobs, it takes as parameter a list of Work type work array or Works a class which are arrayIterator

Update: to modify it is imperative to add the putCode and at least the title, the type, and an externalId

Delete: allows you to delete a job. It takes as parameter the putCode of work on orcid

ReadSummary: Allows you to read all the works present Orcid registration of the account holder represented by $ oauth. It takes no parameters

ReadSingle: Allows you to read a recording by taking its putCode parameter

ReadMultiple allows you to read several jobs for which the putCode table is taken as a parameter

Ouvrir dans Google Traduction	
Commentaires
Résultat Web avec des liens annexes



### Profile

As alluded to in the samples above, once successfully authenticated via OAuth, you can make subsequent requests to the other public/member APIs. For example:

```php
$orcid = new Profile($oauth);

// Get ORCID profile details
$id    = $orcid->id();
$email = $orcid->email();
$name  = $orcid->fullName();
```

The profile class currently only supports a limited number of helper methods for directly accessing elements from the profile data. This will be expanded upon as needed. The raw JSON data from the profile output is available by calling the `raw()` method.

Note that some fields (like email) may return null if the user has not made that field available.

### Environment and API types

ORCID supports two general API endpoints.  The first is their public API, and a second is for registered ORCID members (membership in this scenario does not simply mean that you have an ORCID account).  The public API is used by default and currently supports all functionality provided by the library.  You can, however, switch to the member API by calling:

```php
$oauth = new Oauth;
$oauth->useMembersApi();
```

If you explicitly want to use the public API, you can do so by calling:

```php
$oauth = new Oauth;
$oauth->usePublicApi();
```

ORCID also supports a sandbox environment designed for testing.  To use this environment, rather than the production environment (which is default), you can call the following command:

```php
$oauth = new Oauth;
$oauth->useSandboxEnvironment();
```

The counterpart to this function, though not explicitly necessary, is:

```php
$oauth = new Oauth;
$oauth->useProductionEnvironment();
```

