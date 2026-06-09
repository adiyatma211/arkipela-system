<?php

namespace App\Services;

use App\Enums\BarcodeType;
use App\Models\ProductPackaging;
use App\Models\ProductSku;
use App\Support\RetailBarcodeFormatter;
use Illuminate\Support\Facades\File;

class BarcodeAssetService
{
    public function syncProductSku(ProductSku $productSku): void
    {
        $this->syncBarcodeAsset($productSku, 'generated/barcodes/skus');
    }

    public function syncProductPackaging(ProductPackaging $productPackaging): void
    {
        $this->syncBarcodeAsset($productPackaging, 'generated/barcodes/packagings');
    }

    public function deleteBarcodeAsset(ProductSku|ProductPackaging $record): void
    {
        $path = $record->barcode_image_path;

        if ($path && File::exists(public_path($path))) {
            File::delete(public_path($path));
        }
    }

    private function syncBarcodeAsset(ProductSku|ProductPackaging $record, string $directory): void
    {
        $resolved = RetailBarcodeFormatter::resolveCanonicalBarcode(
            barcodeType: $record->barcode_type,
            barcodeNumber: $record->barcode_number,
            upc: $record->upc,
            ean: $record->ean,
            gtin: $record->gtin,
        );

        $payload = [
            'barcode_type' => $resolved['barcode_type'],
            'barcode_number' => $resolved['barcode_number'],
            'gtin' => $resolved['gtin'],
            'upc' => $resolved['upc'],
            'ean' => $resolved['ean'],
        ];

        if ($resolved['error'] || ! $resolved['barcode_number'] || ! RetailBarcodeFormatter::isPreviewSupported($resolved['barcode_type'])) {
            $this->deleteBarcodeAsset($record);
            $payload['barcode_image_path'] = null;
            $record->forceFill($payload)->saveQuietly();

            return;
        }

        $svg = $this->generateSvg($resolved['barcode_type'], $resolved['barcode_number']);
        $slug = strtolower(str_replace(['-', ' '], '', $resolved['barcode_type']));
        $path = "{$directory}/{$record->id}-{$slug}.svg";

        File::ensureDirectoryExists(public_path($directory));
        File::put(public_path($path), $svg);

        $payload['barcode_image_path'] = $path;
        $record->forceFill($payload)->saveQuietly();
    }

    private function generateSvg(string $barcodeType, string $barcodeNumber): string
    {
        return match ($barcodeType) {
            BarcodeType::UPC_A->value => $this->generateUpcASvg($barcodeNumber),
            BarcodeType::EAN_13->value => $this->generateEan13Svg($barcodeNumber),
            default => '',
        };
    }

    private function generateUpcASvg(string $digits): string
    {
        $lCodes = $this->leftDigitPatterns();
        $rCodes = $this->rightDigitPatterns();
        $left = substr($digits, 0, 6);
        $right = substr($digits, 6, 6);

        $binary = '101';
        foreach (str_split($left) as $digit) {
            $binary .= $lCodes[$digit];
        }
        $binary .= '01010';
        foreach (str_split($right) as $digit) {
            $binary .= $rCodes[$digit];
        }
        $binary .= '101';

        return $this->renderSvg($binary, $digits);
    }

    private function generateEan13Svg(string $digits): string
    {
        $lCodes = $this->leftDigitPatterns();
        $gCodes = $this->ean13GPatterns();
        $rCodes = $this->rightDigitPatterns();
        $parities = $this->ean13ParityPatterns()[$digits[0]];
        $leftDigits = substr($digits, 1, 6);
        $rightDigits = substr($digits, 7, 6);

        $binary = '101';
        foreach (str_split($leftDigits) as $index => $digit) {
            $binary .= $parities[$index] === 'L' ? $lCodes[$digit] : $gCodes[$digit];
        }
        $binary .= '01010';
        foreach (str_split($rightDigits) as $digit) {
            $binary .= $rCodes[$digit];
        }
        $binary .= '101';

        return $this->renderSvg($binary, $digits);
    }

    private function renderSvg(string $binary, string $label): string
    {
        $moduleWidth = 2;
        $quietZone = 12;
        $barHeight = 86;
        $guardHeight = 98;
        $textHeight = 20;
        $width = (strlen($binary) + ($quietZone * 2)) * $moduleWidth;
        $height = $guardHeight + $textHeight + 10;
        $x = $quietZone * $moduleWidth;
        $bars = [];

        foreach (str_split($binary) as $index => $bit) {
            if ($bit !== '1') {
                continue;
            }

            $isGuard = ($index >= 0 && $index <= 2)
                || ($index >= 45 && $index <= 49)
                || ($index >= 92 && $index <= 94);
            $heightForBar = $isGuard ? $guardHeight : $barHeight;

            $bars[] = sprintf(
                '<rect x="%d" y="0" width="%d" height="%d" fill="#111111" />',
                $x + ($index * $moduleWidth),
                $moduleWidth,
                $heightForBar,
            );
        }

        return sprintf(<<<'SVG'
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 %d %d" role="img" aria-label="Barcode %s">
  <rect width="100%%" height="100%%" fill="#ffffff"/>
  <g>
    %s
  </g>
  <text x="50%%" y="%d" text-anchor="middle" font-family="monospace" font-size="18" letter-spacing="2">%s</text>
</svg>
SVG,
            $width,
            $height,
            $label,
            implode("\n    ", $bars),
            $guardHeight + $textHeight,
            $label,
        );
    }

    private function leftDigitPatterns(): array
    {
        return [
            '0' => '0001101',
            '1' => '0011001',
            '2' => '0010011',
            '3' => '0111101',
            '4' => '0100011',
            '5' => '0110001',
            '6' => '0101111',
            '7' => '0111011',
            '8' => '0110111',
            '9' => '0001011',
        ];
    }

    private function rightDigitPatterns(): array
    {
        return [
            '0' => '1110010',
            '1' => '1100110',
            '2' => '1101100',
            '3' => '1000010',
            '4' => '1011100',
            '5' => '1001110',
            '6' => '1010000',
            '7' => '1000100',
            '8' => '1001000',
            '9' => '1110100',
        ];
    }

    private function ean13GPatterns(): array
    {
        return [
            '0' => '0100111',
            '1' => '0110011',
            '2' => '0011011',
            '3' => '0100001',
            '4' => '0011101',
            '5' => '0111001',
            '6' => '0000101',
            '7' => '0010001',
            '8' => '0001001',
            '9' => '0010111',
        ];
    }

    private function ean13ParityPatterns(): array
    {
        return [
            '0' => ['L', 'L', 'L', 'L', 'L', 'L'],
            '1' => ['L', 'L', 'G', 'L', 'G', 'G'],
            '2' => ['L', 'L', 'G', 'G', 'L', 'G'],
            '3' => ['L', 'L', 'G', 'G', 'G', 'L'],
            '4' => ['L', 'G', 'L', 'L', 'G', 'G'],
            '5' => ['L', 'G', 'G', 'L', 'L', 'G'],
            '6' => ['L', 'G', 'G', 'G', 'L', 'L'],
            '7' => ['L', 'G', 'L', 'G', 'L', 'G'],
            '8' => ['L', 'G', 'L', 'G', 'G', 'L'],
            '9' => ['L', 'G', 'G', 'L', 'G', 'L'],
        ];
    }
}
