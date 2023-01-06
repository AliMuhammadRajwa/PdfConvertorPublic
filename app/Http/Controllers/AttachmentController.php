<?php

namespace App\Http\Controllers;

use Imagick;
use App\Models\Attachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use BaconQrCode\Renderer\Image\Png;
use BaconQrCode\Writer;
use Picqer\Barcode\BarcodeGeneratorPNG;

class AttachmentController extends Controller
{
    public function uploadPdf(Request $request)
    {
        $model = new Attachment();
        if ( $request->hasFile('pdf_file_from_request') ) {
            Storage::deleteDirectory('3pagerpdf/pdf');
            $pdf      = $request->file('pdf_file_from_request');
            $fileName = time() . '.' . $pdf->getClientOriginalName();
            Storage::putFileAs('3pagerpdf/pdf', $pdf, $fileName);
            $model->pdf_file_name_in_datebase = $fileName;
            $model->save();
            flash()->addSuccess('Pdf Added');
            return back();
        }
    }

    public function convertpdftoimages(  )
    {
        // Get the PDF file from the storage folder
        $pdfPath = storage_path('app/3pagerpdf/pdf/1672892738.3pagerpdf.pdf');

        // Create an Imagick object
        $imagick = new Imagick();

        // Set the resolution and output format
        $imagick->setResolution(300, 300);
        $imagick->setFormat('jpeg');

        // Read the PDF file into the Imagick object
        $imagick->readImage($pdfPath);

        // Convert each page of the PDF to an image
        foreach ($imagick as $page) {
            $page->writeImage(storage_path("app/temp_images/page{$page->getImageIndex()}.jpg"));
        }

        flash()->addSuccess('Convertion Successfull');
        return back();
    }


    public function add_bar_code_to_images(  )
    {
        $imageFilenames = glob('storage/app/temp_images/*.{jpg,jpeg,png,gif}', GLOB_BRACE);
        foreach ($imageFilenames as $imageFilename) {
            $barcode = new Imagick();

            $barcode->setBackgroundColor(new ImagickPixel('transparent'));
            $barcode->setPointSize(12);
            $barcode->setFont('storage/app/fonts/arial.ttf');
            $barcode->setTextAntialias(true);
            $barcode->setGravity(Imagick::GRAVITY_CENTER);
            $barcode->annotateImage(NULL, 0, 0, 0, 'Your barcode text goes here');

            $barcode->trimImage(0);

            $barcode->setImageFormat('png');

            $originalImage = new Imagick($imageFilename);

            $originalImage->compositeImage($barcode, Imagick::COMPOSITE_OVER, 0, 0);

            $barcode->clear();
            $barcode->destroy();

            $originalImage->setImageFormat('jpg');
            $originalImage->writeImage($imageFilename);

            $originalImage->clear();
            $originalImage->destroy();


        }
        flash()->addSuccess('Bar Code Added Succesfuully');
        return back();



    }

    public function add_bar_code_to_images2(  )
    {

        $files = File::files('app/temp_images');

            foreach ($files as $file) {
                // Generate a barcode image for the file
                $barcodeGenerator = new BarcodeGeneratorPNG();
                $barcodeImage     = $barcodeGenerator->getBarcode($file->getPathname(), $barcodeGenerator::TYPE_CODE_128);

                // Load the original image
                $originalImage = imagecreatefromjpeg($file->getPathname());

                // Merge the barcode image with the original image
                imagecopy($originalImage, $barcodeImage, 0, 0, 0, 0, 300, 300);

                // Save the modified image
                imagejpeg($originalImage, $file->getPathname());
            }

        flash()->addSuccess('Bar Code Added Succesfuully');
        return back();



    }



}
