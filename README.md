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
        $work->setFilter() //to filter data
             ->setTitle("Les stalagmites du réseau du trou Noir")
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
             ->addContributor("Benjamin Lans","author","1111-OOOO-2543-3333","first")
             ->addContributor("Richard Maire","editor")
             ->addContributor("Richard Ortega","Collaborator")
             ->addContributor("Guillaume Devès","co-investigator","OOOO-1111-2222-3333","additional")
            //add subtitle
             ->setSubTitle("subtitle three")
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
            $work->addContributor("Authorfullname","Author role","Author orcid ID","Author sequence"); 
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
     $OrcidClient->readSummary()
```
Read : Allows you to read one or more records by taking its parameter a putCode of type int or string or an array of putCode. The putCode must be a numeric value, it is returned by orcid
  ```php 
     
     // case of reading of many works items (it's an array of putCode that you passed in parameter) of read($putCode)
             /**
              * @var array $putCodes
              */
            $Oresponse= $OrcidClient->read($putCodes);
            $fullRecords=$oresponse->getManyRecord(); 
    // case of reading of single item (it's one putCode that you passed in parameter) of read($putCode)
        /**
         * @var int|string $putCode
         */
       $Oresponse= $OrcidClient->read($putCode);
       $fullRecord=$oresponse->getSingleRecord()

```

### Oresponse
It is a response object returned by Oclient methods. It contains the information of the response returned by Orcid . Requests are made with curl
  ```php 
        $OResponse= $OrcidClient->readSummary();
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
   In the case of reading all the work records in an orcid account with the readSummary method Oresponse has a method which returns the list of Orcid records read This method returns null if Oresponse is not the response to a call to the ReadSummary method
   ```php       
      if($OResponse->hasSuccess()){
         /** @var Records $recordWorkList */
            $recordWorkList=$OResponse->getSummary(); 
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
        $group=$records->getSummary(); 
	
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
      //here are the two type of record and theirs colletcion records.
       use Orcid\Work\Work\Read\Full\Record as FullSingleRecord;
       use Orcid\Work\Work\Read\Full\Records as FullRecords;
       use Orcid\Work\Work\Read\Summaru\Record as FullSingleRecord;
       use Orcid\Work\Work\Read\Summary\Records as SummaryRecords;
 ```
 Please note that there are two types of Record and therefore two of Records (Record collection). The full record and the Summary record.
 You can get a FullRecord after reading an orcid item with the $ oclient-> readSingle ($putcode)  or oclient-> read($putcode) 
 method and an instance of FullRecords after reading many items with the function (oclient-> readMany($putCodesArray) or oclient-> read ($putCodesArray) . A full record contains all the data on the record item while a Summary record contains just the essentials ie: type, title and external identifiers.You will get a list of summary record (SummaryRecords) after reading the summary of all jobs in a user's orci account with the client's readSummary $ oclient-> readSummary () method.
### ExternalId
represents an external identifier and contains the four properties $ idType, $ idValue, $ idUrl, $ idRelationship

```php
        use \Orcid\Work\Data\Data\ExternalId; 
        use Orcid\Work\Work\Read\SingleRecord; // can be SummaryRecord or FullRecord
         
       /**
         * @var Record $record
         */
        $externalIds= $record->getExternalIds()();
        foreach ($externalIds as $externalId){

            /** @var ExternalId $externalId */
            
                $idType=$externalId->getIdType();
                $idValue= $externalId->getIdValue(); 
                $idUrl=$externalId->getIdUrl(); 
                $idRelationship=$externalId->getIdRelationship();
        }
```
### check Data Validity before to send 
```php
      use Orcid\Work\Work\Create\Work;
       $work=new Work();
              $title="the title"; 
              $workType="The work type"; 
              $extIdType="idType"; 
              $exIdValue="idValue";
              
              if(\Orcid\Work\Data\Data::isValidTitle($title)){
                  $work->setTitle($title); 
              }
              if(\Orcid\Work\Data\Data::isValidWorkType($workType)){
                  $work->setType($workType);
              }
              if(\Orcid\Work\Data\Data::isValidExternalIdType($extIdType)){
                  $work->addExternalIdent($extIdType,$exIdValue); 
              }
```
 

#### Curent Evolution 

reorganization of the code: the complex data of an item are transformed into an object to facilitate access to properties (sub-data) and the evolution of sub-data. The added Classes are: Contributor, PublicationDate, ExternalId, Title, Source and Citation. For example: Title contains the properties like (the title value, the subtitle, the translated-title and the translated-language-code) and Citation contains (the citation value, the citation type).

Added functionality to have a full record instance after reading a single record and a full record collection instance after reading Many Records. A full record contains all the data on the record while a Summary record contains just the essentials, ie: the type, title and external identifiers.

Addition of validation and filter functions for the data of an orcid item with the static class: Orcid \ Work \ Data \ Data.


Addition of the possibility of requesting that they be given to be filtered by activating $ work-> setFilter () and the possibility of removing the filter with $ work-> removeFilter ()

Addition of the possibility of forcibly setting the value of a property of an object. This method makes it possible to force the request to send a value without it being validated. Indeed, I try to check the validity of the data based on lists of values ​​accepted by meta and the rules of values, before sending. But since allowed lists of values ​​evolve and the rules can change, it is possible that the validation rules are obsolete or that the list of values ​​accepted for a meta (eg: work-type) has changed compared to the version of the library that you have in your project and that the validation of a valid metadata is refused. In this case you can use the $ work → setPropertyByForce ('type', 'newOrcidWorkType')

