<?php

namespace App\Support;

use Illuminate\Http\Response;
use Illuminate\Support\Collection;

/**
 * Builds an Excel 2003 XML workbook without requiring an extra package.
 *
 * Excel reads the UTF-8 declaration and the Tahoma/right-aligned styles, so
 * Persian text keeps its shaping and direction instead of being split up.
 */
class ExcelExport
{
    /**
     * @param  list<string>  $headers
     * @param  Collection<int, list<mixed>>  $rows
     */
    public static function response(array $headers, Collection $rows, string $filename): Response
    {
        $xml = self::prologue();
        $xml .= '<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet"'
            .' xmlns:o="urn:schemas-microsoft-com:office:office"'
            .' xmlns:x="urn:schemas-microsoft-com:office:excel"'
            .' xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet">';
        $xml .= self::styles();
        $xml .= '<Worksheet ss:Name="'.self::escape($filename).'"><Table>';

        foreach ($headers as $header) {
            $xml .= '<Column ss:Width="110"/>';
        }

        $xml .= '<Row>';

        foreach ($headers as $header) {
            $xml .= self::cell($header, 'Header');
        }

        $xml .= '</Row>';

        foreach ($rows as $row) {
            $xml .= '<Row>';

            foreach ($row as $value) {
                $xml .= self::cell($value, is_int($value) || is_float($value) ? 'Number' : 'Text');
            }

            $xml .= '</Row>';
        }

        $xml .= '</Table></Worksheet></Workbook>';

        return response($xml, 200, [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
            'Cache-Control' => 'no-store, no-cache, must-revalidate',
        ]);
    }

    private static function prologue(): string
    {
        return '<?xml version="1.0" encoding="UTF-8"?>'."\n"
            .'<?mso-application progid="Excel.Sheet"?>'."\n";
    }

    private static function styles(): string
    {
        return '<Styles>'
            .'<Style ss:ID="Default" ss:Name="Normal">'
            .'<Alignment ss:Vertical="Center"/>'
            .'<Font ss:FontName="Tahoma" ss:Size="10"/>'
            .'</Style>'
            .'<Style ss:ID="Text">'
            .'<Alignment ss:Horizontal="Right" ss:Vertical="Center" ss:ReadingOrder="RightToLeft"/>'
            .'<Font ss:FontName="Tahoma" ss:Size="10"/>'
            .'</Style>'
            .'<Style ss:ID="Number">'
            .'<Alignment ss:Horizontal="Right" ss:Vertical="Center"/>'
            .'<Font ss:FontName="Tahoma" ss:Size="10"/>'
            .'</Style>'
            .'<Style ss:ID="Header">'
            .'<Alignment ss:Horizontal="Right" ss:Vertical="Center" ss:ReadingOrder="RightToLeft"/>'
            .'<Font ss:FontName="Tahoma" ss:Size="10" ss:Bold="1"/>'
            .'<Interior ss:Color="#E2E8F0" ss:Pattern="Solid"/>'
            .'</Style>'
            .'</Styles>';
    }

    private static function cell(mixed $value, string $style): string
    {
        $type = $style === 'Number' ? 'Number' : 'String';
        $content = $value === null ? '' : (string) $value;

        return '<Cell ss:StyleID="'.$style.'"><Data ss:Type="'.$type.'">'
            .self::escape($content)
            .'</Data></Cell>';
    }

    private static function escape(string $value): string
    {
        return htmlspecialchars($value, ENT_XML1 | ENT_QUOTES, 'UTF-8');
    }
}
