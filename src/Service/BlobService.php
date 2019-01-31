<?php

namespace App\Service;

use MicrosoftAzure\Storage\Blob\BlobRestProxy;
use MicrosoftAzure\Storage\Common\Exceptions\ServiceException;
use Psr\Log\LoggerInterface;

class BlobService
{
    private $logger;
    private $blobClient;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
        $this->blobClient = BlobRestProxy::createBlobService($_SERVER['AZURE_STORAGE_CONNECTION_STRING']);
    }

    public function allContainers()
    {
        try {
            $container_list = $this->blobClient->listContainers();
            return $container_list->getContainers();

        } catch (ServiceException $exception) {
            $this->logger->error('failed to get all containers: ' . $exception->getCode() . ':' . $exception->getMessage());
            throw $exception;
        }
    }

    public function allBlobs($container = 'images')
    {
        try {
            $result = $this->blobClient->listBlobs($container);
            return $result->getBlobs();

        } catch (ServiceException $exception) {
            $this->logger->error('failed to get all blobs: ' . $exception->getCode() . ':' . $exception->getMessage());
            throw $exception;
        }
    }

    public function upload($file, $container = 'images')
    {
        try {

            $content = file_get_contents($file);
            $this->blobClient->createBlockBlob($container, $file->getClientOriginalName(), $content);

        } catch (ServiceException $exception) {
            $this->logger->error('failed to upload the file: ' . $exception->getCode() . ':' . $exception->getMessage());
            throw $exception;
        }
    }

    public function delete($blobName, $container = 'images')
    {
        try {
            $this->blobClient->deleteBlob($container, $blobName);
        } catch (ServiceException $exception) {
            $this->logger->error('failed to delete the file: ' . $exception->getCode() . ':' . $exception->getMessage());
            throw $exception;
        }
    }
}
