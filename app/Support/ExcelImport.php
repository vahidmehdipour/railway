<?php

namespace App\Support;

use App\Models\Evaluation;
use App\Models\EvaluationPeriod;
use App\Models\Personnel;
use App\Models\Role;
use App\Models\Station;
use Carbon\Carbon;
use Hekmatinasser\Verta\Verta;
use Illuminate\Http\UploadedFile;
use RuntimeException;
use Throwable;
use ZipArchive;

/**
 * Reads personnel spreadsheets (CSV, XLSX and Excel 2003 XML) and prepares a
 * preview that can be committed after the super admin confirms it.
 *
 * No extra Composer package is required: XLSX is read through ZipArchive and
 * DOM, which are available on the cPanel PHP installation as well.
 */
class ExcelImport
{
    private const MAX_ROWS = 500;

    /** @var array<string, list<string>> */
    private const HEADER_ALIASES = [
        'first_name' => ['نام', 'اسم', 'first_name', 'firstname', 'first'],
        'last_name' => ['نام خانوادگی', 'فامیلی', 'last_name', 'lastname', 'last', 'surname'],
        'role' => ['نقش', 'سمت', 'شغل', 'role', 'role_name', 'name_fa'],
        'entry_year' => ['سال ورود', 'سال استخدام', 'سال ورود به خدمت', 'entry_year', 'ورود'],
        'decree_year' => ['سال صدور حکم', 'سال حکم', 'decree_year'],
        'national_code' => ['کد ملی', 'کدملی', 'national_code', 'nationalcode', 'کد پرسنلی'],
        'is_active' => ['وضعیت', 'active', 'is_active'],
        'evaluation_date' => ['تاریخ ارزیابی', 'تاریخ', 'evaluation_date', 'date'],
        'period' => ['دوره ارزیابی', 'دوره', 'period', 'period_title'],
        'station' => ['ایستگاه', 'station', 'station_name'],
        'theory_score' => ['نمره تئوری', 'نمره تئوریک', 'تئوری', 'theory_score', 'theory'],
        'practical_score' => ['نمره عملی', 'عملی', 'practical_score', 'practical'],
        'ethics_score' => ['نمره اخلاق', 'اخلاق', 'ethics_score', 'ethics'],
        'accident_count' => ['تعداد سانحه', 'سانحه', 'تعداد سانحهها', 'accident_count', 'accidents'],
        'near_miss_count' => ['تعداد شبه سانحه', 'شبه سانحه', 'تعداد شبه سانحهها', 'near_miss_count', 'near_miss'],
        'warning_count' => ['تعداد اخطار', 'اخطار', 'تعداد اخطارها', 'warning_count', 'warnings'],
        'description' => ['توضیحات', 'توضیح', 'description', 'notes'],
    ];

    private const SCORE_KEYS = [
        'evaluation_date',
        'theory_score',
        'practical_score',
        'ethics_score',
        'accident_count',
        'near_miss_count',
        'warning_count',
    ];

    /**
     * Parse the uploaded file into a preview payload.
     *
     * @return array{filename: string, mode: string, headers: list<string>, rows: list<array<string, mixed>>, summary: array<string, int>}
     */
    public function parse(UploadedFile $file, string $requestedMode): array
    {
        $table = $this->readTable($file);
        $headerIndex = $this->findHeaderRow($table);
        $headers = $this->mapHeaders($table[$headerIndex]);

        if (! in_array('first_name', $headers, true) || ! in_array('last_name', $headers, true)) {
            throw new RuntimeException('ستونهای «نام» و «نام خانوادگی» در فایل پیدا نشد. از فایل الگو استفاده کنید.');
        }

        $hasScoreColumns = array_intersect($headers, self::SCORE_KEYS) !== [];
        $mode = $requestedMode === 'auto' ? ($hasScoreColumns ? 'scores' : 'personnel') : $requestedMode;

        if ($mode === 'scores' && ! $hasScoreColumns) {
            throw new RuntimeException('حالت «پرسنل و امتیاز» انتخاب شده اما ستونهای امتیاز در فایل وجود ندارد.');
        }

        $dataRows = array_slice($table, $headerIndex + 1);
        if ($dataRows === []) {
            throw new RuntimeException('فایل حاوی هیچ ردیف پرسنلی نیست.');
        }

        $roles = $this->roleMap();
        $personnels = $this->personnelMap();
        $periods = $this->periodMap();
        $stations = $this->stationMap();
        $activePeriod = EvaluationPeriod::query()->active()->orderByDesc('start_date')->first();

        $rows = [];

        foreach ($dataRows as $offset => $cells) {
            if ($this->isEmptyRow($cells)) {
                continue;
            }

            if (count($rows) >= self::MAX_ROWS) {
                throw new RuntimeException('حداکثر '.self::MAX_ROWS.' ردیف در هر بار وارد کردن مجاز است.');
            }

            $values = [];
            foreach ($headers as $index => $key) {
                if ($key !== null && ! array_key_exists($key, $values)) {
                    $values[$key] = trim((string) ($cells[$index] ?? ''));
                }
            }

            $rows[] = $this->mapRow(
                $values,
                $mode,
                $headerIndex + $offset + 2,
                $roles,
                $personnels,
                $periods,
                $stations,
                $activePeriod,
            );
        }

        if ($rows === []) {
            throw new RuntimeException('فایل حاوی هیچ ردیف پرسنلی نیست.');
        }

        return [
            'filename' => $file->getClientOriginalName(),
            'mode' => $mode,
            'headers' => array_values(array_filter($headers)),
            'rows' => $rows,
            'summary' => [
                'total' => count($rows),
                'new' => count(array_filter($rows, fn (array $row) => $row['status'] === 'new')),
                'existing' => count(array_filter($rows, fn (array $row) => $row['status'] === 'existing')),
                'scores' => count(array_filter($rows, fn (array $row) => $row['scores'] !== null)),
                'errors' => count(array_filter($rows, fn (array $row) => $row['errors'] !== [])),
            ],
        ];
    }

    /**
     * Commit a preview payload. Invalid rows are never written.
     *
     * @param  array<string, mixed>  $payload
     * @return array{personnels_created: int, evaluations_created: int, existing_used: int}
     */
    public function commit(array $payload, int $userId): array
    {
        $counts = [
            'personnels_created' => 0,
            'evaluations_created' => 0,
            'existing_used' => 0,
        ];

        foreach ($payload['rows'] as $row) {
            if ($row['errors'] !== []) {
                continue;
            }

            if ($row['status'] === 'existing') {
                $personnel = Personnel::query()->findOrFail($row['personnel_id']);
                $counts['existing_used']++;
            } else {
                $personnel = Personnel::query()->create($row['person']);
                $counts['personnels_created']++;
            }

            if ($row['scores'] !== null) {
                Evaluation::query()->create([
                    ...$row['scores'],
                    'personnel_id' => $personnel->id,
                    'user_id' => $userId,
                ]);

                $counts['evaluations_created']++;
            }
        }

        return $counts;
    }

    /**
     * Build the CSV template with a UTF-8 BOM so Excel detects Persian text.
     */
    public function template(string $mode): string
    {
        $headers = [
            'نام',
            'نام خانوادگی',
            'نقش',
            'سال ورود',
            'سال صدور حکم',
            'کد ملی',
            'وضعیت',
        ];

        if ($mode === 'scores') {
            $headers = [
                ...$headers,
                'تاریخ ارزیابی',
                'دوره ارزیابی',
                'ایستگاه',
                'نمره تئوری',
                'نمره عملی',
                'نمره اخلاق',
                'تعداد سانحه',
                'تعداد شبه سانحه',
                'تعداد اخطار',
                'توضیحات',
            ];
        }

        return "\xEF\xBB\xBF".implode(',', $headers)."\r\n";
    }

    /**
     * Read a spreadsheet into a raw table of strings.
     *
     * @return list<list<string>>
     */
    private function readTable(UploadedFile $file): array
    {
        $extension = strtolower($file->getClientOriginalExtension());
        $path = $file->getRealPath();
        $contents = file_get_contents($path);

        if ($contents === false) {
            throw new RuntimeException('امکان خواندن فایل وجود ندارد.');
        }

        $table = match ($extension) {
            'xlsx' => $this->readXlsx($path),
            'xls', 'xml' => str_starts_with(ltrim($contents), '<?xml')
                ? $this->readXmlSpreadsheet($contents)
                : throw new RuntimeException('فایل xls قدیمی قابل خواندن نیست. لطفاً آن را به xlsx یا CSV تبدیل کنید.'),
            default => $this->readCsv($contents),
        };

        $table = $this->trimTable($table);

        if ($table === []) {
            throw new RuntimeException('فامل خالی است یا محتوای قابل خواندی ندارد.');
        }

        return $table;
    }

    /**
     * @return list<list<string>>
     */
    private function readCsv(string $contents): array
    {
        if (str_starts_with($contents, "\xFF\xFE")) {
            $contents = mb_convert_encoding(substr($contents, 2), 'UTF-8', 'UTF-16LE');
        } elseif (str_starts_with($contents, "\xFE\xFF")) {
            $contents = mb_convert_encoding(substr($contents, 2), 'UTF-8', 'UTF-16BE');
        } elseif (str_starts_with($contents, "\xEF\xBB\xBF")) {
            $contents = substr($contents, 3);
        }

        $lines = preg_split('/\r\n|\n|\r/u', $contents) ?: [];
        $firstLine = $lines[0] ?? '';
        $delimiter = $this->detectDelimiter($firstLine);
        $table = [];

        foreach ($lines as $line) {
            if (trim($line) === '') {
                continue;
            }

            $row = str_getcsv($line, $delimiter, '"', '\\');
            $table[] = array_map(fn ($value) => is_string($value) ? trim($value) : '', $row);
        }

        return $table;
    }

    private function detectDelimiter(string $line): string
    {
        $counts = [];

        foreach ([',', ';', "\t"] as $candidate) {
            $counts[$candidate] = substr_count($line, $candidate);
        }

        arsort($counts);

        return array_key_first($counts) ?? ',';
    }

    /**
     * @return list<list<string>>
     */
    private function readXlsx(string $path): array
    {
        if (! class_exists(ZipArchive::class)) {
            throw new RuntimeException('برای خواندن فایل xlsx نیاز به افزونه zip است.');
        }

        $zip = new ZipArchive;

        if ($zip->open($path) !== true) {
            throw new RuntimeException('فایل xlsx معتبر نیست.');
        }

        $sharedStrings = [];
        $sharedXml = $zip->getFromName('xl/sharedStrings.xml');

        if ($sharedXml !== false) {
            $document = new \DOMDocument;
            $document->loadXML($sharedXml);

            foreach ($document->getElementsByTagNameNS('*', 'si') as $item) {
                $sharedStrings[] = $this->textOf($item);
            }
        }

        $sheetPath = $this->firstSheetPath($zip);
        $sheetXml = $zip->getFromName($sheetPath);
        $zip->close();

        if ($sheetXml === false) {
            throw new RuntimeException('برگه اصلی فایل xlsx پیدا نشد.');
        }

        $document = new \DOMDocument;

        if (! $document->loadXML($sheetXml)) {
            throw new RuntimeException('محتوای فایل xlsx قابل خواندن نیست.');
        }

        $table = [];

        foreach ($document->getElementsByTagNameNS('*', 'row') as $row) {
            $cells = [];

            foreach ($row->getElementsByTagNameNS('*', 'c') as $cell) {
                $index = $this->columnIndex($cell->getAttribute('r'));
                $type = $cell->getAttribute('t');

                if ($type === 'inlineStr') {
                    $value = '';

                    foreach ($cell->getElementsByTagNameNS('*', 't') as $text) {
                        $value .= $text->textContent;
                    }
                } else {
                    $raw = $this->firstText($cell, 'v');
                    $value = $type === 's' ? ($sharedStrings[(int) $raw] ?? '') : $raw;
                }

                $cells[$index] = trim($value);
            }

            if ($cells !== []) {
                ksort($cells);
                $width = max(array_keys($cells)) + 1;
                $table[] = array_pad(array_values($cells), $width, '');
            }
        }

        return $table;
    }

    private function firstSheetPath(ZipArchive $zip): string
    {
        $workbookXml = $zip->getFromName('xl/workbook.xml');

        if ($workbookXml !== false) {
            $document = new \DOMDocument;
            $document->loadXML($workbookXml);
            $sheet = $document->getElementsByTagNameNS('*', 'sheet')->item(0);
            $relationId = $sheet?->getAttributeNS('http://schemas.openxmlformats.org/officeDocument/2006/relationships', 'id');

            if ($relationId) {
                $relsXml = $zip->getFromName('xl/_rels/workbook.xml.rels');

                if ($relsXml !== false) {
                    $rels = new \DOMDocument;
                    $rels->loadXML($relsXml);

                    foreach ($rels->getElementsByTagNameNS('*', 'Relationship') as $relation) {
                        if ($relation->getAttribute('Id') !== $relationId) {
                            continue;
                        }

                        $target = ltrim($relation->getAttribute('Target'), '/');

                        if (! str_starts_with($target, 'xl/')) {
                            $target = 'xl/'.$target;
                        }

                        return $target;
                    }
                }
            }
        }

        return 'xl/worksheets/sheet1.xml';
    }

    private function columnIndex(string $reference): int
    {
        if (ctype_digit(trim($reference))) {
            return max(0, (int) $reference - 1);
        }

        $letters = strtoupper(preg_replace('/[^A-Z]/i', '', $reference) ?: 'A');
        $index = 0;

        foreach (str_split($letters) as $letter) {
            $index = $index * 26 + (ord($letter) - ord('A') + 1);
        }

        return max(0, $index - 1);
    }

    /**
     * @return list<list<string>>
     */
    private function readXmlSpreadsheet(string $contents): array
    {
        $namespace = 'urn:schemas-microsoft-com:office:spreadsheet';
        $document = new \DOMDocument;

        if (! $document->loadXML($contents)) {
            throw new RuntimeException('فایل اکسل XML معتبر نیست.');
        }

        $table = [];

        foreach ($document->getElementsByTagNameNS($namespace, 'Row') as $row) {
            $cells = [];

            foreach ($row->getElementsByTagNameNS($namespace, 'Cell') as $cell) {
                $index = $this->columnIndex($cell->getAttributeNS($namespace, 'Index') ?: 'A');

                while (array_key_exists($index, $cells)) {
                    $index++;
                }

                $cells[$index] = trim($this->firstText($cell, 'Data', $namespace));
            }

            if ($cells !== []) {
                ksort($cells);
                $width = max(array_keys($cells)) + 1;
                $table[] = array_pad(array_values($cells), $width, '');
            }
        }

        return $table;
    }

    /**
     * @param  list<list<string>>  $table
     */
    private function findHeaderRow(array $table): int
    {
        foreach (array_slice($table, 0, min(10, count($table)), true) as $index => $row) {
            $headers = $this->mapHeaders($row);

            if (in_array('first_name', $headers, true) && in_array('last_name', $headers, true)) {
                return $index;
            }
        }

        return 0;
    }

    /**
     * @param  list<string>  $row
     * @return list<string|null>
     */
    private function mapHeaders(array $row): array
    {
        $aliases = [];

        foreach (self::HEADER_ALIASES as $key => $names) {
            foreach ($names as $name) {
                $aliases[$this->normalizeHeader($name)] = $key;
            }
        }

        return array_map(
            fn (string $header): ?string => $aliases[$this->normalizeHeader($header)] ?? null,
            $row,
        );
    }

    private function normalizeHeader(string $value): string
    {
        $value = en_num(mb_strtolower(trim($value)));

        return preg_replace('/[\sـ_؛،,.:\/\\\\()\-]/u', '', $value) ?? $value;
    }

    /**
     * @param  list<list<string>>  $table
     * @return list<list<string>>
     */
    private function trimTable(array $table): array
    {
        $trimmed = [];

        foreach ($table as $row) {
            while ($row !== [] && trim((string) end($row)) === '') {
                array_pop($row);
            }

            if ($row !== []) {
                $trimmed[] = array_map(fn ($value) => (string) $value, $row);
            }
        }

        return $trimmed;
    }

    /**
     * @param  list<string>  $cells
     */
    private function isEmptyRow(array $cells): bool
    {
        foreach ($cells as $cell) {
            if (trim($cell) !== '') {
                return false;
            }
        }

        return true;
    }

    /**
     * @param  array<string, string>  $values
     * @param  array<string, string>  $roles
     * @param  array<string, int>  $personnels
     * @param  array<string, int>  $periods
     * @param  array<string, int>  $stations
     * @return array<string, mixed>
     */
    private function mapRow(
        array $values,
        string $mode,
        int $rowNumber,
        array $roles,
        array $personnels,
        array $periods,
        array $stations,
        ?EvaluationPeriod $activePeriod,
    ): array {
        $errors = [];
        $first = $values['first_name'] ?? '';
        $last = $values['last_name'] ?? '';
        $roleName = $values['role'] ?? '';
        $nationalCode = en_num($values['national_code'] ?? '');
        $entryYear = en_num($values['entry_year'] ?? '');
        $decreeYear = en_num($values['decree_year'] ?? '');

        if ($first === '' || $last === '') {
            $errors[] = 'نام و نام خانوادگی الزامی است.';
        }

        $roleId = $roles[$this->normalizeLookup($roleName)] ?? null;

        if ($roleId === null) {
            $errors[] = $roleName === ''
                ? 'نقش پرسنل مشخص نشده است.'
                : 'نقش «'.$roleName.'» در تنظیمات تعریف نشده است.';
        }

        if (! preg_match('/^\d{4}$/', $entryYear) || (int) $entryYear < 1300 || (int) $entryYear > 2100) {
            $errors[] = 'سال ورود معتبر نیست.';
        }

        if ($decreeYear !== '' && (! preg_match('/^\d{4}$/', $decreeYear) || (int) $decreeYear < 1300 || (int) $decreeYear > 2100)) {
            $errors[] = 'سال صدور حکم معتبر نیست.';
        }

        if ($nationalCode !== '' && ! preg_match('/^\d{10}$/', $nationalCode)) {
            $errors[] = 'کد ملی باید ۱۰ رقم باشد.';
        }

        $existingId = null;
        $label = trim($first.' '.$last);

        if ($nationalCode !== '') {
            $existingId = $personnels[$nationalCode] ?? null;
        } elseif ($first !== '' && $last !== '') {
            $existingId = $personnels[$this->normalizeLookup($label)] ?? null;
        }

        $person = [
            'role_id' => $roleId,
            'first_name' => $first,
            'last_name' => $last,
            'entry_year' => $entryYear === '' ? null : (int) $entryYear,
            'decree_year' => $decreeYear === '' ? null : (int) $decreeYear,
            'national_code' => $nationalCode === '' ? null : $nationalCode,
            'is_active' => $this->toBoolean($values['is_active'] ?? ''),
        ];

        $scoreDisplay = [
            'date' => $values['evaluation_date'] ?? '',
            'period' => $values['period'] ?? ($activePeriod?->title ?? ''),
            'station' => $values['station'] ?? '',
        ];
        $scores = $mode === 'scores'
            ? $this->mapScores($values, $rowNumber, $periods, $stations, $activePeriod, $errors)
            : null;

        return [
            'row' => $rowNumber,
            'person' => $person,
            'scores' => $scores,
            'status' => $existingId !== null ? 'existing' : 'new',
            'personnel_id' => $existingId,
            'label' => $label,
            'display' => [
                'role' => $roleName,
                'national_code' => $nationalCode,
                ...($scores !== null ? $scoreDisplay : []),
            ],
            'errors' => $errors,
        ];
    }

    /**
     * @param  array<string, string>  $values
     * @param  array<string, int>  $periods
     * @param  array<string, int>  $stations
     * @param  list<string>  $errors
     * @return array<string, mixed>|null
     */
    private function mapScores(
        array $values,
        int $rowNumber,
        array $periods,
        array $stations,
        ?EvaluationPeriod $activePeriod,
        array &$errors,
    ): ?array {
        $date = $this->parseDate($values['evaluation_date'] ?? '');

        if ($date === null) {
            $errors[] = 'تاریخ ارزیابی معتبر نیست (مثال: ۱۴۰۴/۰۱/۰۱ یا 2025-03-21).';
        }

        $periodName = $values['period'] ?? '';
        $periodId = $periodName !== ''
            ? ($periods[$this->normalizeLookup($periodName)] ?? null)
            : $activePeriod?->id;

        if ($periodId === null) {
            $errors[] = $periodName === ''
                ? 'دوره ارزیابی فعالی وجود ندارد؛ نام دوره را در فایل وارد کنید.'
                : 'دوره ارزیابی «'.$periodName.'» پیدا نشد.';
        }

        $stationName = $values['station'] ?? '';
        $stationId = null;

        if ($stationName !== '') {
            $stationId = $stations[$this->normalizeLookup($stationName)] ?? null;

            if ($stationId === null) {
                $errors[] = 'ایستگاه «'.$stationName.'» در تنظیمات تعریف نشده است.';
            }
        }

        $scores = [];

        foreach (['theory_score', 'practical_score', 'ethics_score'] as $key) {
            $value = en_num($values[$key] ?? '');

            if ($value === '') {
                $scores[$key] = null;

                continue;
            }

            if (! is_numeric($value) || (float) $value < 0) {
                $errors[] = 'یکی از نمرات معتبر نیست (ردیف '.$rowNumber.').';
                $scores[$key] = null;

                continue;
            }

            $scores[$key] = round((float) $value, 2);
        }

        foreach (['accident_count', 'near_miss_count', 'warning_count'] as $key) {
            $value = en_num($values[$key] ?? '');

            if ($value === '') {
                $scores[$key] = 0;

                continue;
            }

            if (! preg_match('/^\d+$/', $value)) {
                $errors[] = 'تعداد سانحهها، شبه سانحهها یا اخطارها معتبر نیست (ردیف '.$rowNumber.').';
                $scores[$key] = 0;

                continue;
            }

            $scores[$key] = (int) $value;
        }

        return [
            'evaluation_date' => $date,
            'period_id' => $periodId,
            'station_id' => $stationId,
            'description' => $values['description'] ?? null,
            'theory_score' => $scores['theory_score'],
            'practical_score' => $scores['practical_score'],
            'ethics_score' => $scores['ethics_score'],
            'accident_count' => $scores['accident_count'],
            'near_miss_count' => $scores['near_miss_count'],
            'warning_count' => $scores['warning_count'],
        ];
    }

    private function parseDate(string $value): ?string
    {
        $value = trim(en_num($value));

        if ($value === '') {
            return null;
        }

        if (preg_match('/^\d{5}(\.\d+)?$/', $value)) {
            return Carbon::create(1899, 12, 30)->addDays((int) $value)->format('Y-m-d');
        }

        if (! preg_match('/^(\d{4})[-.\/](\d{1,2})[-.\/](\d{1,2})$/u', $value, $matches)) {
            return null;
        }

        $year = (int) $matches[1];
        $month = (int) $matches[2];
        $day = (int) $matches[3];

        if ($year >= 1900) {
            try {
                $date = Carbon::createFromFormat('!Y-n-j', "{$year}-{$month}-{$day}");

                return $date->format('Y-n-j') === "{$year}-{$month}-{$day}"
                    ? $date->format('Y-m-d')
                    : null;
            } catch (Throwable) {
                return null;
            }
        }

        if (! Verta::isValidDate($year, $month, $day)) {
            return null;
        }

        [$gregorianYear, $gregorianMonth, $gregorianDay] = Verta::jalaliToGregorian($year, $month, $day);

        return sprintf('%04d-%02d-%02d', $gregorianYear, $gregorianMonth, $gregorianDay);
    }

    private function toBoolean(string $value): bool
    {
        return ! in_array($this->normalizeLookup($value), ['غیرفعال', '0', 'false', 'خیر', 'inactive', 'no'], true);
    }

    private function normalizeLookup(string $value): string
    {
        $value = en_num(mb_strtolower(trim($value)));

        return preg_replace('/[\sـ_؛،,.:\/\\\\()\-]/u', '', $value) ?? $value;
    }

    /**
     * @return array<string, int>
     */
    private function roleMap(): array
    {
        $map = [];

        foreach (Role::query()->get(['id', 'name_fa', 'name_en']) as $role) {
            $map[$this->normalizeLookup($role->name_fa)] = $role->id;
            $map[$this->normalizeLookup($role->name_en)] = $role->id;
        }

        return $map;
    }

    /**
     * @return array<string, int>
     */
    private function personnelMap(): array
    {
        $map = [];

        foreach (Personnel::query()->get(['id', 'first_name', 'last_name', 'national_code']) as $personnel) {
            if ($personnel->national_code !== null) {
                $map[en_num($personnel->national_code)] = $personnel->id;
            }

            $map[$this->normalizeLookup($personnel->first_name.' '.$personnel->last_name)] = $personnel->id;
        }

        return $map;
    }

    /**
     * @return array<string, int>
     */
    private function periodMap(): array
    {
        $map = [];

        foreach (EvaluationPeriod::query()->get(['id', 'title']) as $period) {
            $map[$this->normalizeLookup($period->title)] = $period->id;
        }

        return $map;
    }

    /**
     * @return array<string, int>
     */
    private function stationMap(): array
    {
        $map = [];

        foreach (Station::query()->get(['id', 'name']) as $station) {
            $map[$this->normalizeLookup($station->name)] = $station->id;
        }

        return $map;
    }

    private function textOf(\DOMNode $node): string
    {
        return trim(preg_replace('/\s+/u', ' ', $node->textContent) ?? '');
    }

    private function firstText(\DOMNode $node, string $tag, ?string $namespace = null): string
    {
        $element = $namespace === null
            ? $node->getElementsByTagNameNS('*', $tag)->item(0)
            : $node->getElementsByTagNameNS($namespace, $tag)->item(0);

        return $element?->textContent ?? '';
    }
}
