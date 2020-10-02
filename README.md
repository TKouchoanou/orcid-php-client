# Orcid-PHP-Client
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


### Work
Work is a class that allows you to create a publication on your orcid account. The data of a document to send to orcid must be added to a Work instance by the setters. Work provides methods to create xml in the format accepted by Orcid
```php
   // creation of an Orcid work
        $work=new Work();
        $work->setTitle("Les stalagmites du réseau du trou Noir")
	     ->setTranslatedTitle('The stalagmites of the Black hole network')
             ->setTranslatedTitleLanguageCode('en')
             ->setType("Work-paper")
	     ->setWorkUrl("the work url")
             ->setJournalTitle("naturephysic")
             ->setCitation("The work citation....")//if you don't set citationType formatted-unspecified will be set
	     ->setCitationType('the citation type')
             ->setShortDescription("the work description...") // the descript must be  less than 500 characters
	     ->setPublicationDate('1998','09','20')// the first parameter year is required if you want to set date
	     ->setLanguageCode('fr')
	     ->setCountry('us')
            //add Authors with Author FullName and role, by default the role 'author' will be chosen your can also add the orcidID and the sequence of author
             ->addAuthor("Benjamin Lans","author","1111-OOOO-2543-3333","first")
             ->addAuthor("Richard Maire","editor")
             ->addAuthor("Richard Ortega","Collaborator")
             ->addAuthor("Guillaume Devès","co-investigator","OOOO-1111-2222-3333","additional")
            //add subtitle
             ->addSubTitle("subtitle three")
            // add External Ident the type , the value, the url, the relationship by default url willbe empity and relationship will be self . idtype and idValue   are required
             ->addExternalIdent("doi","10.1038/nphys1170","https://www.nature.com/articles/nphys1170","self")
             ->addExternalIdent("uri","00199711");
  
```
The minimum configuration for sending an Orcid Work is to define the title, the type of document and add at least an external identifier.
```php
 // minimum configuration to create an Orcid work
        $work=new Work();
        $work->setTitle('title')
	     ->setType('workType')
             ->addExternalIdent('idType','idValue');  
```
In the case of a work modification, Put-code is required .
```php
    $putCode =14563; 
    $work->setPutCode($putCode); 
```
### Works
Works is a class that inherits from arrayIterator. It is a list of orcid works to which we can add instances of type Work with the append method 
```php
        $worksList=new Works();
        $worksList->append($firstwork);
        $worksList->append($secondwork);
        $worksList->append($thirdwork);
```
and on which we can iterate with foreach for example
```php
    foreach ($worksList as $work){
            /**
             * @var Work $work
             */
            $work->addAuthor("Authorfullname","Author role","Author orcid ID","Author sequence"); 
        }
```

### OClient 

An Orcid client makes it possible to communicate with the orcid count whose authentication elements are contained in the Oauth object which is passed to the Oclient constructor 
 
```php
// Check for successful authentication

if ($oauth->isAuthenticated())
{
    // creation of an orcid client
	$OrcidClient=new OClient($oauth);
}
```

The different methods of Oclient are:

Send: allows you to send one or more publications. It takes as parameter an array of instance of the work class, an instance of works to send several publications or an instance of the work class to send a single publication

  ```php 
     // send one or several work(s)
           /*** @var Work|Works|Work[] $works  */
     $OrcidClient->send($works); 
  ```
Update: This method allows you to modify a Work already sent to Orcid. You can only modify a work already present in an orcid account with a putCode to recover.To modify don't forget to set putCode
  ```php 
     // update a Work
     $OrcidClient->update($work); 
```
Delete: allows you to delete a job. It takes as parameter the putCode of work on orcid
```php 
     // delete a Work
     $putcode=14563; 
     $OrcidClient->delete($putcode); 
```
ReadSummary: Allows you to read all the works present Orcid registration of the account holder represented by $ oauth. 
  ```php 
     // read Summary
     $OrcidClient->ReadSummary()
```
Read : Allows you to read one or more records by taking its parameter a putCode of type int or string or an array of putCode. The putCode must be a numeric value, it is returned by orcid
  ```php 
     // read work(s)
      /**
         * @var int|string|array $putCode
         */
        $OrcidClient->read($putCode);
```

### Oresponse
It is a response object returned by Oclient methods. It contains the information of the response returned by Orcid . Requests are made with curl
  ```php 
    $OResponse= $OrcidClient->ReadSummary();
        $code=$OResponse->getCode();
        $header=$OResponse->getHeaders();
        $body=$OResponse->getBody();
```
in case of error Orcid returns data which can be retrieved by these methods which will return null or an empty string if there has been no error
  ```php 
        if($OResponse->hasError()){
           
            $errorCode=$OResponse->getErrorCode();
            $userMessage=$OResponse->getUserMessage();
            $developperMessage=$OResponse->getDevelopperMessage();
        }
	
   ```
   In the case of reading all the work records in an orcid account with the ReadSummary method Oresponse has a method which returns the list of Orcid records read This method returns null if Oresponse is not the response to a call to the ReadSummary method
   ```php       
      if($OResponse->hasSuccess()){
         /** @var Records $recordWorkList */
            $recordWorkList=$OResponse->getWorkRecordList(); 
        }
 ```
 This method returns an instance of Records which is a list of Record instances
### Records and Record
It is an instance whose set of properties represents an orcid work from the user's orcid account. It has some properties in common with the Work instance (the class used to create a work to send to Orcid) and specific properties coming from orcid
   ```php    
   
        /**
         * @var Records $records
         */
	 
	 //returns date of last modification of oricd registrations
        $groupelastModifiedDate=$records->getLastModifiedDate(); 
	
	// returns a complex associative array coming directly from Orcid and containing the information on the work read
        $group=$records->getOrcidWorks(); 
	
        foreach ($records as $record){
            /**
             * @var Record $record
             */
            $putCode= $record->getPutCode();
            $workSource=$record->getSource();
            $workPath=$record->getPath();
            $lastModifiedDate=$record->getLastModifiedDate();//returns date of last modification of this record work
            $title=$record->getTitle(); 
	    
	    //returns an external identifier array of type ExternalId
            /** @var ExternalId [] $externalIds */
            $externalIds= $record->getExternals();
        }
 ```
 
### ExternalId
represents an external identifier and contains the four properties $ idType, $ idValue, $ idUrl, $ idRelationship

```php
       /**
         * @var Record $record
         */
        $externalIds= $record->getExternals();
        foreach ($externalIds as $externalId){

            /** @var ExternalId $externalId */
            
                $idType=$externalId->getIdType();
                $idValue= $externalId->getIdValue(); 
                $idUrl=$externalId->getIdUrl(); 
                $idRelationship=$externalId->getIdRelationship();
        }
```
