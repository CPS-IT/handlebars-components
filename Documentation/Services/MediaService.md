# [Media service](../../Classes/Service/MediaService.php)

The [Media service](../../Classes/Service/MediaService.php) can be used to retrieve
media for a given record. This can be one of the following:

* Relation (identified by field name)
* File references
* Files
* File collections
* Folders

Under the hood, the service uses the `FilesProcessor` from TYPO3 core to collect
files. The collected files are then converted using an implementation of the provided
[`ResourceConverterInterface`](../../Classes/Resource/Converter/ResourceConverterInterface.php),
which defaults to [`MediaResourceConverter`](../../Classes/Resource/Converter/MediaResourceConverter.php).

As a result, the collected files are provided as instances of the
[`MediaInterface`](../../Classes/Domain/Model/Media/MediaInterface.php). By default,
two implementations exist for this model:

* [`OnlineMedia`](../../Classes/Domain/Model/Media/OnlineMedia.php) for all files that
  present online media, such as YouTube or Vimeo videos.
* [`Media`](../../Classes/Domain/Model/Media/Media.php) for all other files that are
  representable by an implementation of the `FileInterface` from TYPO3 core.

As part of the `MediaService`, the extension also provides an
[`ImageProcessor`](../../Classes/Resource/Processing/ImageProcessor.php) that is able
to process images based on a given
[`ImageProcessingInstruction`](../../Classes/Resource/Processing/ImageProcessingInstruction.php).

## Example

### Fetch files by relation

```php
$mediaService->getFromRelation(
    'assets',
    'tt_content',
    $record
);
```

### Fetch files or folders by given identifiers

```php
$mediaService->getFromFileReferences($fileReferenceIds);
$mediaService->getFromFiles($fileIds);
$mediaService->getFromFileCollections($fileCollectionIds);
$mediaService->getFromFolders($folderIdentifiers);
```

### Process images

```php
use \Fr\Typo3HandlebarsComponents\Resource\Processing\ImageProcessingInstruction;

$processedImages = [];
$media = $mediaService->getFromRelation(
    'assets',
    'tt_content',
    $record
);

foreach ($media as $currentMedia) {
    $processingInstruction = new ImageProcessingInstruction($currentMedia, '320c', 180);
    $processedImages[] = $imageProcessor->process($currentMedia, $processingInstruction);
}
```