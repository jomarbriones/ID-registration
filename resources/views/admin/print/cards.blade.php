@php
use Illuminate\Support\Str;

function course_full($course) {
    if (!$course) return '';
    return Str::startsWith($course, 'BS ')
        ? 'Bachelor of Science in ' . Str::after($course, 'BS ')
        : $course;
}

function barcode_svg($value) {
    try {
        $generator = new \Picqer\Barcode\BarcodeGeneratorSVG();
        return $generator->getBarcode($value, $generator::TYPE_CODE128, 1.6, 55);
    } catch (\Throwable $e) {
        return '';
    }
}

$frontTemplate = public_path('images/templates/id-card-front.png');
$frontTemplate = file_exists($frontTemplate) ? str_replace('\\', '/', $frontTemplate) : null;

$backTemplate = public_path('images/templates/id-card-back.png');
$backTemplate = file_exists($backTemplate) ? str_replace('\\', '/', $backTemplate) : null;

$photoPlaceholder = public_path('images/photo-placeholder.png');
$photoPlaceholder = file_exists($photoPlaceholder) ? str_replace('\\', '/', $photoPlaceholder) : null;

$cols = 4;
$rowsPerSheet = 2;
$cardsPerSheet = $cols * $rowsPerSheet;
$pages = $items->chunk($cardsPerSheet)->values();
@endphp
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <style>
    @page { size: A4 landscape; margin: 0.6cm; }
    body {
      font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif;
      font-size: 8pt;
      color: #0f172a;
      margin: 0;
      -webkit-print-color-adjust: exact;
    }

    :root {
      --card-w: 5.3cm;
      --card-h: 8.5cm;
    }

    table.card-grid {
      width: 100%;
      table-layout: fixed;
      border-collapse: separate;
      border-spacing: 0.6cm 0.35cm;
      margin: 0.15cm auto;
    }
    table.card-grid td {
      width: var(--card-w);
      height: var(--card-h);
      padding: 0;
      vertical-align: top;
    }

    .card {
      position: relative;
      width: var(--card-w);
      height: var(--card-h);
      box-sizing: border-box;
    }
    .card img.card-bg {
      position: absolute;
      inset: 0;
      width: 100%;
      height: 100%;
      object-fit: cover;
      z-index: 0;
    }

    .card .overlay {
      position: absolute;
      z-index: 2;
      color: #0b1f2a;
      font-weight: 500;
      letter-spacing: 0.12px;
      white-space: pre-line;
    }

    .card.front .student-no {
      top: 1.65cm;
      left: 0.3cm;
      font-size: 9pt;
      letter-spacing: 0.3px;
    }

    .card.front .photo-frame {
      position: absolute;
      top: 2.3cm;
      left: 1.4cm;
      width: 2.54cm;
      height: 2.54cm;
      padding: 0.024cm;
      background: transparent;
      overflow: hidden;
      z-index: 2;
      box-sizing: border-box;
    }
    .card.front .photo-frame img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      display: block;
      border-radius: 0.02cm;
    }

    .card.front .name-block {
      position: absolute;
      left: 0.72cm;
      right: 0.72cm;
      top: 5.02cm;
      text-align: center;
      color: #062323;
    }
    .card.front .name-block .last {
      font-size: 15pt;
      font-weight: 800;
      letter-spacing: 0.28px;
      text-transform: uppercase;
      line-height: 1.05;
    }
    .card.front .name-block .first {
      margin-top: 0.07cm;
      font-size: 13pt;
      font-weight: 800;
      letter-spacing: 0.26px;
      text-transform: uppercase;
    }

    .card.front .barcode {
      position: absolute;
      left: 0.58cm;
      right: 0.58cm;
      bottom: 0.52cm;
      z-index: 2;
    }
    .card.front .barcode svg {
      display: block;
      width: 100%;
      height: 1cm;
    }
    .card.front .course-line {
      position: absolute;
      left: 0.76cm;
      right: 0.76cm;
      top: 6.5cm;
      font-size: 10.2pt;
      font-weight: 500;
      line-height: 1.22;
      text-align: center;
      color: #021c11;
      z-index: 2;
    }

    .card.back .address {
      top: 1.6cm;
      left: 1.9cm;
      right: 0.76cm;
      font-size: 6.6pt;
      line-height: 1.2;
      word-break: break-word;
    }
    .card.back .gender {
      top: 2.75cm;
      left: 1.5cm;
      width: 0.88cm;
      font-size: 8.2pt;
      text-align: center;
    }
    .card.back .blood {
      top: 2.75cm;
      left: 3.6cm;
      width: 1.12cm;
      font-size: 8pt;
      text-align: center;
    }
    .card.back .emergency-name {
      top: 3.95cm;
      left: 1.2cm;
      right: 0.78cm;
      font-size: 6.6pt;
      line-height: 1.18;
      word-break: break-word;
    }
    .card.back .emergency-address {
      top: 4.5cm;
      left: 1.5cm;
      right: 0.78cm;
      font-size: 6.3pt;
      line-height: 1.18;
      word-break: break-word;
    }
    .card.back .emergency-contact {
      top: 5.5cm;
      left: 2.1cm;
      right: 0.78cm;
      font-size: 6.4pt;
      word-break: break-word;
    }

    .page-break { page-break-after: always; }
  </style>
</head>
<body>
@foreach ($pages as $pageIndex => $page)
  @php
      $pageItems = $page->values();
  @endphp
  <table class="card-grid">
    @for ($row = 0; $row < $rowsPerSheet; $row++)
      <tr>
        @for ($col = 0; $col < $cols; $col++)
          @php
              $index = $row * $cols + $col;
              $record = $pageItems->get($index);
          @endphp
          <td>
            @if ($record)
              @php
                  $s = $record['model'];
                  $firstLine = trim($s->first_name . ' ' . ($s->middle_initial ? $s->middle_initial . '.' : ''));
                  $barcode = barcode_svg($s->id_number);
              @endphp
              <div class="card front">
                @if ($frontTemplate)
                  <img class="card-bg" src="{{ $frontTemplate }}" alt="ID card front template">
                @endif
                <div class="overlay student-no">{{ $s->id_number }}</div>
                <div class="photo-frame">
                  @if ($record['photo_abs'])
                    <img src="{{ $record['photo_abs'] }}" alt="Student photo">
                  @elseif ($photoPlaceholder)
                    <img src="{{ $photoPlaceholder }}" alt="Placeholder photo">
                  @endif
                </div>
              @php $courseLine = course_full($s->course ?? ''); @endphp
                <div class="name-block">
                  <div class="last">{{ strtoupper($s->last_name ?? '') }}</div>
                  <div class="first">{{ strtoupper($firstLine) }}</div>
                </div>
                @if ($courseLine !== '')
                  <div class="course-line">{{ $courseLine }}</div>
                @endif
                @if ($barcode)
                  <div class="barcode">{!! $barcode !!}</div>
                @endif
              </div>
            @endif
          </td>
        @endfor
      </tr>
    @endfor
  </table>

  <div class="page-break"></div>

  <table class="card-grid">
    @for ($row = 0; $row < $rowsPerSheet; $row++)
      <tr>
        @for ($col = 0; $col < $cols; $col++)
          @php
              $index = $row * $cols + $col;
              $record = $pageItems->get($index);
          @endphp
          <td>
            @if ($record)
              @php
                  $s = $record['model'];
                  $gender = strtoupper($s->gender ?? '');
                  $gender = in_array($gender, ['M', 'F'], true) ? $gender : '';
              @endphp
              <div class="card back">
                @if ($backTemplate)
                  <img class="card-bg" src="{{ $backTemplate }}" alt="ID card back template">
                @endif
                <div class="overlay address">{{ $s->address ?? '' }}</div>
                @if ($gender !== '')
                  <div class="overlay gender">{{ $gender }}</div>
                @endif
                @if (!empty($s->blood_type))
                  <div class="overlay blood">{{ strtoupper($s->blood_type) }}</div>
                @endif
                <div class="overlay emergency-name">{{ $s->guardian_name ?? '' }}</div>
                <div class="overlay emergency-address">{{ $s->parent_address ?? '' }}</div>
                <div class="overlay emergency-contact">{{ $s->guardian_contact ?? '' }}</div>
              </div>
            @endif
          </td>
        @endfor
      </tr>
    @endfor
  </table>

  @if ($pageIndex < $pages->count() - 1)
    <div class="page-break"></div>
  @endif
@endforeach
</body>
</html>
