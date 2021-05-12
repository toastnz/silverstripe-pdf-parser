<?php

namespace AndrewAndante\SilverStripePDFParser\Extractor;

use Psr\Log\LoggerInterface;
use SilverStripe\Assets\File;
use SilverStripe\Core\Injector\Injector;
use SilverStripe\TextExtraction\Extractor\FileTextExtractor;
use Smalot\PdfParser\Parser;
use Throwable;

class PDFParserTextExtractor extends FileTextExtractor
{

    /**
     * @inheritDoc
     */
    public function isAvailable(): bool
    {
        return class_exists(Parser::class);
    }

    /**
     * @inheritDoc
     */
    public function supportsExtension($extension): bool
    {
        return strtolower($extension) === 'pdf';
    }

    /**
     * @inheritDoc
     */
    public function supportsMime($mime): bool
    {
        return in_array(
            strtolower($mime),
            [
                'application/pdf',
                'application/x-pdf',
                'application/x-bzpdf',
                'application/x-gzpdf'
            ]
        );
    }

    /**
     * @inheritDoc
     */
    public function getContent($file): string
    {
        $pdfParser = new Parser();
        try {
            $path = $file instanceof File ? self::getPathFromFile($file) : $file;
            return $pdfParser->parseFile($path)->getText();
        } catch (Throwable $e) {
            Injector::inst()->get(LoggerInterface::class)->info(
                sprintf(
                    '[PDFParserTextExtractor] Error extracting text from "%s" (message: %s)',
                    $path ?? 'unknown file',
                    $e->getMessage()
                )
            );
        }

        return '';
    }
}
