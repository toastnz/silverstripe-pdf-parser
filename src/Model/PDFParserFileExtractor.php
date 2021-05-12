<?php

use Smalot\PdfParser\Parser;

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
    public function getContent($path): string
    {
        $pdfParser = new Parser();
        try {
            return $pdfParser->parseFile($path)->getText();
        } catch (Exception $e) {
            SS_Log::log(
                sprintf(
                    '[PDFParserFileExtractor] Error extracting text from "%s" (message: %s)',
                    $path,
                    $e->getMessage()
                ),
                SS_Log::NOTICE
            );
        }

        return '';
    }
}
