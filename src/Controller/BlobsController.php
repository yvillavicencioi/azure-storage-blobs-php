<?php

namespace App\Controller;

use App\Service\BlobService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BlobsController extends AbstractController
{
    /**
     * @Route("/", name="blobs")
     */
    public function index(BlobService $storage)
    {
        return $this->render('blobs/index.html.twig', [
            'controller_name' => 'BlobsController',
            'blobs' => $storage->allBlobs(),
            'containers' => $storage->allContainers(),
        ]);
    }

    /**
     * @Route("/upload/image", methods={"POST"})
     */
    public function create(Request $request, BlobService $storage)
    {

        $file = $request->files->get('newFile');

        if (empty($file)) {
            return new Response("No file specified", Response::HTTP_UNPROCESSABLE_ENTITY, ['content-type' => 'text/plain']);
        }

        $storage->upload($file);

        return $this->redirectToRoute('blobs');
    }

    /**
     * @Route("/delete/{blobName}")
     */
    public function deleteBlob($blobName, BlobService $storage)
    {
        $storage->delete($blobName);

        return $this->redirectToRoute('blobs');
    }
}
