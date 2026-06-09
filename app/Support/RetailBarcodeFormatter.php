<?php

namespace App\Support;

use App\Enums\BarcodeType;

class RetailBarcodeFormatter
{
    public static function normalizeType(?string $value): ?string
    {
        $normalized = strtoupper(trim((string) $value));

        return match ($normalized) {
            'UPCA', 'UPC-A' => BarcodeType::UPC_A->value,
            'EAN13', 'EAN-13' => BarcodeType::EAN_13->value,
            'ITF14', 'ITF-14' => BarcodeType::ITF_14->value,
            'CODE128', 'CODE-128' => BarcodeType::CODE_128->value,
            default => $normalized !== '' ? $normalized : null,
        };
    }

    public static function normalizeDigits(?string $value): ?string
    {
        $digits = preg_replace('/\D+/', '', (string) $value) ?? '';

        return $digits !== '' ? $digits : null;
    }

    public static function resolveCanonicalBarcode(
        ?string $barcodeType,
        ?string $barcodeNumber,
        ?string $upc,
        ?string $ean,
        ?string $gtin,
    ): array {
        $type = self::normalizeType($barcodeType);
        $upcDigits = self::normalizeDigits($upc);
        $eanDigits = self::normalizeDigits($ean);
        $barcodeDigits = self::normalizeDigits($barcodeNumber);
        $gtinDigits = self::normalizeDigits($gtin);

        return match ($type) {
            BarcodeType::UPC_A->value => self::buildUpcA($upcDigits ?? $barcodeDigits ?? $gtinDigits),
            BarcodeType::EAN_13->value => self::buildEan13($eanDigits ?? $barcodeDigits ?? $gtinDigits),
            default => [
                'barcode_type' => $type,
                'barcode_number' => $barcodeDigits ?? $gtinDigits,
                'gtin' => $gtinDigits ?? $barcodeDigits,
                'upc' => $upcDigits,
                'ean' => $eanDigits,
                'error' => null,
            ],
        };
    }

    public static function isPreviewSupported(?string $barcodeType): bool
    {
        return in_array(self::normalizeType($barcodeType), [
            BarcodeType::UPC_A->value,
            BarcodeType::EAN_13->value,
        ], true);
    }

    private static function buildUpcA(?string $digits): array
    {
        if ($digits === null) {
            return self::errorPayload(BarcodeType::UPC_A->value, 'UPC-A requires 11 or 12 digits.');
        }

        if (strlen($digits) === 11) {
            $digits .= self::computeUpcCheckDigit($digits);
        }

        if (strlen($digits) !== 12 || ! self::isValidUpcA($digits)) {
            return self::errorPayload(BarcodeType::UPC_A->value, 'UPC-A must contain a valid 12-digit code.');
        }

        return [
            'barcode_type' => BarcodeType::UPC_A->value,
            'barcode_number' => $digits,
            'gtin' => $digits,
            'upc' => $digits,
            'ean' => null,
            'error' => null,
        ];
    }

    private static function buildEan13(?string $digits): array
    {
        if ($digits === null) {
            return self::errorPayload(BarcodeType::EAN_13->value, 'EAN-13 requires 12 or 13 digits.');
        }

        if (strlen($digits) === 12) {
            $digits .= self::computeEan13CheckDigit($digits);
        }

        if (strlen($digits) !== 13 || ! self::isValidEan13($digits)) {
            return self::errorPayload(BarcodeType::EAN_13->value, 'EAN-13 must contain a valid 13-digit code.');
        }

        return [
            'barcode_type' => BarcodeType::EAN_13->value,
            'barcode_number' => $digits,
            'gtin' => $digits,
            'upc' => null,
            'ean' => $digits,
            'error' => null,
        ];
    }

    public static function isValidUpcA(string $digits): bool
    {
        return self::computeUpcCheckDigit(substr($digits, 0, 11)) === substr($digits, -1);
    }

    public static function isValidEan13(string $digits): bool
    {
        return self::computeEan13CheckDigit(substr($digits, 0, 12)) === substr($digits, -1);
    }

    public static function computeUpcCheckDigit(string $baseDigits): string
    {
        $sumOdd = 0;
        $sumEven = 0;

        foreach (str_split($baseDigits) as $index => $digit) {
            if ($index % 2 === 0) {
                $sumOdd += (int) $digit;
            } else {
                $sumEven += (int) $digit;
            }
        }

        $total = ($sumOdd * 3) + $sumEven;

        return (string) ((10 - ($total % 10)) % 10);
    }

    public static function computeEan13CheckDigit(string $baseDigits): string
    {
        $sum = 0;

        foreach (str_split($baseDigits) as $index => $digit) {
            $sum += (int) $digit * ($index % 2 === 0 ? 1 : 3);
        }

        return (string) ((10 - ($sum % 10)) % 10);
    }

    private static function errorPayload(?string $type, string $message): array
    {
        return [
            'barcode_type' => $type,
            'barcode_number' => null,
            'gtin' => null,
            'upc' => null,
            'ean' => null,
            'error' => $message,
        ];
    }
}
