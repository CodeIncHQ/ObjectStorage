# Code Inc.'s object storage library

This library provides an abstraction layer for various cloud and local object storage plateforms including:
* OpenStack Swift
* BackBlaze B2
* SFTP
* Local file system

## Usage

### Initializing containers

```php
use CodeInc\ObjectStorage;

// SFTP container
$sftpDirectory = ObjectStorage\Sftp\SftpDirectory::factoryPubKey(
    "/remote/path/to/files",
    "hostname.local",
    "remote-user",
    "path/to/public-key.pub",
    "path/to/private-key",
    "optional-key-passphrase"
);

// Local file system container
$localDirectory = new ObjectStorage\Local\LocalDirectory(
    "/path/to/files"
);

// Swift container
$swiftContainer = ObjectStorage\Swift\SwiftContainer::factory(
    "container-name",
    "container-swift-region",
    "https://open-stack-auth-url.com",
    "open-stack-user",
    "open-stack-password",
    "open-stack-tenant-id",
    "open-stack-tenant-name"
);

// B2 container 
$b2Bucket = ObjectStorage\BackBlazeB2\B2Bucket::factory(
    "container-or-bucket-name",
    "b2-account-id",
    "b2-application-key"
);
```

### Creating a file
```php
use CodeInc\ObjectStorage;

// from an existing file
$object = new ObjectStorage\Utils\InlineObject("test.jpg");
$object->setFileContent("/path/to/test.jpg");

// from a string
$object = new ObjectStorage\Utils\InlineObject("test.txt");
$object->setStringContent("C'est un test au format texte !");
```

### Uploading an object
```php
// uploading an object
$container->uploadObject($object, 'optional-new-object-name.txt');

// transfering an object from a container to another
$destinationContainer->uploadObject(
    $sourceContainer->getObject('test.jpg')
);
```

### Listing object
```php
foreach ($container as $file) {
    var_dump($file->getName());
}
```
`$file` is an object implementing the interface `StoreObjectInterface`.


### Getting an object

```php
header('Content-Type: image/jpeg');
echo $container->getObject('test.jpg')->getContent();
```

`getObject()` returns an object implementing the interface `StoreObjectInterface`.

### Deleting an object

```php
// from a container
$container->deleteObject("test.jpg");

// from an object
$object = $container->getObject('test.jpg');
if ($object instanceof StoreObjectDeleteInterface) {
    $object->delete();
}
```

For objects implementing the `StoreObjectDeleteInterface` you can call the `delete()` method directory on the object.
