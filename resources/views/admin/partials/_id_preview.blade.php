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

$frontTemplatePath = public_path('images/templates/id-card-front.png');
$backTemplatePath  = public_path('images/templates/id-card-back.png');
$frontTemplate = file_exists($frontTemplatePath) ? asset('images/templates/id-card-front.png') : null;
$backTemplate  = file_exists($backTemplatePath)  ? asset('images/templates/id-card-back.png') : null;
$photoFallback = asset('images/photo-placeholder.png');

$courseLine = course_full($student->course ?? '');
$firstLine = trim($student->first_name . ' ' . ($student->middle_initial ? $student->middle_initial . '.' : ''));
$gender = strtoupper($student->gender ?? '');
$gender = in_array($gender, ['M', 'F'], true) ? $gender : '';
$barcode = !empty($student->id_number) ? barcode_svg($student->id_number) : '';
@endphp

<style>
  .preview-sheet {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
    justify-content: center;
    padding: 12px 4px 18px;
    background: #f8fafc;
  }
  .preview-card-wrap {
    position: relative;
    width: calc(5.3cm * 1.35);
    height: calc(8.5cm * 1.35);
  }
  .preview-card {
    position: relative;
    width: 5.3cm;
    height: 8.5cm;
    border: 0.35mm solid #0b3a29;
    border-radius: 0.18cm;
    overflow: hidden;
    background: #fff;
    transform: scale(1.35);
    transform-origin: top left;
    box-shadow: 0 18px 42px rgba(15,23,42,.15);
  }
  .preview-card img.card-bg {
    position: absolute;
    inset: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
    z-index: 0;
  }
  .preview-card.front .overlay,
  .preview-card.back .overlay {
    position: absolute;
    z-index: 2;
    color: #0b1f2a;
    font-weight: 500;
    letter-spacing: 0.12px;
    white-space: pre-line;
  }
  .preview-card.front .student-no {
    top: 1.65cm;
    left: 0.3cm;
    font-size: 9pt;
    letter-spacing: 0.3px;
  }
  .preview-card.front .photo-frame {
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
  .preview-card.front .photo-frame img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
    border-radius: 0.02cm;
  }
  .preview-card.front .name-block {
    position: absolute;
    left: 0.72cm;
    right: 0.72cm;
    top: 5.02cm;
    text-align: center;
    color: #062323;
  }
  .preview-card.front .name-block .last {
    font-size: 15pt;
    font-weight: 800;
    letter-spacing: 0.28px;
    text-transform: uppercase;
    line-height: 1.05;
  }
  .preview-card.front .name-block .first {
    margin-top: 0.07cm;
    font-size: 13pt;
    font-weight: 800;
    letter-spacing: 0.26px;
    text-transform: uppercase;
  }
  .preview-card.front .barcode {
    position: absolute;
    left: 0.58cm;
    right: 0.58cm;
    bottom: 0.52cm;
    z-index: 2;
  }
  .preview-card.front .barcode svg {
    display: block;
    width: 100%;
    height: 1cm;
  }
  .preview-card.front .course-line {
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
  .preview-card.back .address {
    top: 1.6cm;
    left: 1.9cm;
    right: 0.76cm;
    font-size: 6.6pt;
    line-height: 1.2;
  }
  .preview-card.back .gender {
    top: 2.75cm;
    left: 1.5cm;
    width: 0.88cm;
    font-size: 8.2pt;
    text-align: center;
  }
  .preview-card.back .blood {
    top: 2.75cm;
    left: 3.6cm;
    width: 1.12cm;
    font-size: 8pt;
    text-align: center;
  }
  .preview-card.back .emergency-name {
    top: 3.95cm;
    left: 1.2cm;
    right: 0.78cm;
    font-size: 6.6pt;
    line-height: 1.18;
  }
  .preview-card.back .emergency-address {
    top: 4.5cm;
    left: 1.5cm;
    right: 0.78cm;
    font-size: 6.3pt;
    line-height: 1.18;
  }
  .preview-card.back .emergency-contact {
    top: 5.5cm;
    left: 2.1cm;
    right: 0.78cm;
    font-size: 6.4pt;
  }
</style>

<div class="preview-sheet">
  <div class="preview-card-wrap">
    <div class="preview-card front">
    @if ($frontTemplate)
      <img class="card-bg" src="{{ $frontTemplate }}" alt="ID front template">
    @endif
    <div class="overlay student-no">{{ $student->id_number }}</div>
    <div class="photo-frame">
      <img src="{{ $photoUrl ?: $photoFallback }}" alt="Student photo"
           onerror="this.src='{{ $photoFallback }}'">
    </div>
    <div class="name-block">
      <div class="last">{{ strtoupper($student->last_name ?? '') }}</div>
      <div class="first">{{ strtoupper($firstLine) }}</div>
    </div>
    @if ($courseLine !== '')
      <div class="course-line">{{ $courseLine }}</div>
    @endif
    @if ($barcode)
      <div class="barcode">{!! $barcode !!}</div>
    @endif
    </div>
  </div>

  <div class="preview-card-wrap">
    <div class="preview-card back">
    @if ($backTemplate)
      <img class="card-bg" src="{{ $backTemplate }}" alt="ID back template">
    @endif
    <div class="overlay address">{{ $student->address ?? '' }}</div>
    @if ($gender !== '')
      <div class="overlay gender">{{ $gender }}</div>
    @endif
    @if (!empty($student->blood_type))
      <div class="overlay blood">{{ strtoupper($student->blood_type) }}</div>
    @endif
    <div class="overlay emergency-name">{{ $student->guardian_name ?? '' }}</div>
    <div class="overlay emergency-address">{{ $student->parent_address ?? '' }}</div>
    <div class="overlay emergency-contact">{{ $student->guardian_contact ?? '' }}</div>
    </div>
  </div>
</div>
