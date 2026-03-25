<?php
declare(strict_types=1);

namespace App\QrCode;

use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\PngWriter;

final class QrCodeService
{
    public function __construct(private string $outputDirAbsolute) {}

    public function generatePng(string $url, string $hash): string
    {
        if (!is_dir($this->outputDirAbsolute)) {
            mkdir($this->outputDirAbsolute, 0775, true);
        }

        $filename = $hash . '.png';
        $path = rtrim($this->outputDirAbsolute, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $filename;

        $writer = new PngWriter();
        $qrCode = new QrCode(
            data: $url,
            encoding: new Encoding('UTF-8'),
            errorCorrectionLevel: ErrorCorrectionLevel::Low,
            size: 320,
            margin: 10,
            roundBlockSizeMode: RoundBlockSizeMode::Margin,
            foregroundColor: new Color(0, 0, 0),
            backgroundColor: new Color(255, 255, 255),
        );

        $result = $writer->write($qrCode);

        $result->saveToFile($path);

        return $path;
    }
}
