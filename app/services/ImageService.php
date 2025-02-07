<?php
require_once("../repositories/ImageRepository.php");
require_once("../models/Exceptions/ImageNotFoundException.php");
require_once("../models/Image.php");
require_once("../models/Page.php");

class ImageService
{
    private $imageRepository;
    // As for now, we only allow these image types:
    private $allowedImageTypes = array("png", "jpg", "jpeg", "webp");

    public function __construct()
    {
        $this->imageRepository = new ImageRepository();
    }

    public function getImageById($id): ?Image
    {
        $image = $this->imageRepository->getImageById($id);
        if ($image == null) {
            throw new ImageNotFoundException();
        }
        return $image;
    }

    public function getAll(): array
    {
        return $this->imageRepository->getAll();
    }

    /**
     * Assigns images that will be used in the banner for a given page.
     */
    public function setImagesForPage($pageId, $imageIds): void
    {
        $cleanedImagesArray = array();
        foreach ($imageIds as $image) {
            array_push($cleanedImagesArray, htmlspecialchars($image));
        }

        // First we must delete old associations.
        $this->removeImagesForPage($pageId);

        $this->imageRepository->setImagesForPage($pageId, $cleanedImagesArray);
    }

    public function removeImagesForPage($pageId): void
    {
        $this->imageRepository->removeImagesForPage($pageId);
    }

    public function addImage($file, $alt): void
    {
        $alt = htmlspecialchars($alt);

        // save to /public/img
        $targetDirectory = "../public/img/";
        $fileName = basename($file["name"]);
        $fileName = str_replace(" ", "_", $fileName);
        // get file extension
        $fileExtension = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));

        // check if file is an image
        if (!in_array($fileExtension, $this->allowedImageTypes)) {
            throw new UploadException("File is not an image. Allowed formats are: " . implode(', ', $this->allowedImageTypes) . ". This image type: " . $fileExtension);
        }

        // rename jpeg to jpg
        $fileNameWithoutExtension = pathinfo($fileName, PATHINFO_FILENAME);
        $fileExtension = str_replace("jpeg", "jpg", $fileExtension);
        // Where the file is supposed to be saved.
        $targetFile = $targetDirectory . $fileExtension . "/" . $fileNameWithoutExtension . "." . $fileExtension;

        // if file already exists, append a number to the end of the file name
        $i = 1;
        while (file_exists($targetFile)) {
            $fileNameWithoutExtension = pathinfo($fileName, PATHINFO_FILENAME) . "(" . $i . ")";
            $targetFile = $targetDirectory . $fileExtension . "/" . $fileNameWithoutExtension . "." . $fileExtension;
            $i++;
        }
        // generate src
        $src = "/img/" . $fileExtension . "/" . $fileNameWithoutExtension . "." . $fileExtension;

        // Check if folder for this image format exists.
        if (!file_exists($targetDirectory . $fileExtension)) {
            mkdir($targetDirectory . $fileExtension);
        }

        move_uploaded_file($file["tmp_name"], $targetFile);
        $this->imageRepository->addImage($src, $alt);
    }

    public function removeImage($id): void
    {
        $image = $this->imageRepository->getImageById($id);
        if ($image == null) {
            throw new ImageNotFoundException();
        }

        $this->imageRepository->removeImage($id);
        // remove file
        unlink("../public" . $image->getSrc());
    }

    /**
     * Only updates the alt text of an image.
     */
    public function updateImage($id, $alt): void
    {
        $id = htmlspecialchars($id);
        $alt = htmlspecialchars($alt);
        $this->imageRepository->updateImage($id, $alt);
    }

    /**
     * Used by JazzArtist page to assign images to an artist.
     */
    public function assignImagesToArtist($artistId, $imageIds)
    {
        $this->removeImagesFromArtist($artistId);
        foreach ($imageIds as $imageId) {
            $this->imageRepository->assignImageToArtist($artistId, $imageId);
        }
    }

    /**
     * Undoes the previous method.
     */
    public function removeImagesFromArtist($artistId)
    {
        $this->imageRepository->removeImagesForArtist($artistId);
    }

    /**
     * Lets you search for images by their alt text.
     */
    public function search($search): array
    {
        $search = htmlspecialchars($search);
        // split $search into array by spaces
        $search = explode(" ", $search);
        return $this->imageRepository->search($search);
    }

    /**
     * Returns all images that are assigned to a given page.
     */
    public function getImagesForPageId($pageId): array
    {
        return $this->imageRepository->getImagesForPageId($pageId);
    }
}
