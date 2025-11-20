@php
use Illuminate\Support\Facades\Storage;

$frontPath = public_path('images/templates/faculty-id-front.png');
$backPath = public_path('images/templates/faculty-id-back.png');

$frontTemplate = file_exists($frontPath) ? str_replace('\\', '/', $frontPath) : null;
$backTemplate = file_exists($backPath) ? str_replace('\\', '/', $backPath) : null;

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
    *{font-family: Arial, Helvetica, sans-serif;}
    body{
      margin:0;
      color:#0f172a;
      -webkit-print-color-adjust:exact;
      background:#fff;
    }
    .card-grid{font-family: Arial, Helvetica, sans-serif;}
    :root { --card-w: 5.3cm; --card-h: 8.5cm; }
    table.card-grid { width:100%; table-layout: fixed; border-collapse: separate; border-spacing: 0.6cm 0.35cm; margin:0.15cm auto; }
    table.card-grid td { width: var(--card-w); height: var(--card-h); padding:0; vertical-align: top; }
    .card{font-family: Arial, Helvetica, sans-serif; position:relative;width:var(--card-w);height:var(--card-h);border-radius:0.35cm;overflow:hidden;box-shadow:0 12px 40px rgba(15,23,42,.25);background:#fff;}
    .card img.bg{position:absolute;inset:0;width:100%;height:100%;object-fit:cover;z-index:0;}
    .placeholder{position:absolute;inset:0;display:grid;place-items:center;text-align:center;color:#94a3b8;font-size:8pt;padding:0.4cm;z-index:1;background:#f8fafc;}
    .overlay{position:absolute;left:0;right:0;z-index:2;color:#0b1f2a;}
      /* Front layout */
    .front .photo{top:2.47cm;left:50%;width:3cm;height:3cm;transform:translateX(-50%);border:0.05cm solid #0f172a;overflow:hidden;border-radius:0.12cm;display:flex;align-items:center;justify-content:center;background:#f8fafc;}
    .front .photo img{width:100%;height:100%;object-fit:cover;object-position:center;}
    .front .nickname{
      top:5.75cm;
      text-align:center;
      font-size:30pt;
      font-family: Arial, Helvetica, sans-serif;
      font-weight:900;
      letter-spacing:1px;
      color:#0b3624;
      line-height:1;
      -webkit-text-stroke:1.2px #fff;text-shadow:-1.2px -1.2px 0 #fff,1.2px -1.2px 0 #fff,-1.2px 1.2px 0 #fff,1.2px 1.2px 0 #fff;
    }
    .front .fullname{top:6.9cm;text-align:center;}
    .front .fullname span{display:inline-block;color:#fff;;font-size:8.5pt;font-weight:600;letter-spacing:0.02cm;line-height:1.1;}
    .front .position{top:7.4cm;text-align:center;font-size:8.4pt;font-weight:800;color:#0f172a;letter-spacing:0.01cm;}
    .front .idnumber{bottom:0.23cm;text-align:center;font-size:10pt;font-weight:900;color:#ffffff;letter-spacing:0.01cm;}
    /* Back layout */
    .back .field{position:absolute;left:0.75cm;right:0.45cm;font-size:6pt;font-weight:800;color:#0f172a;padding:0.04cm 0.07cm;box-sizing:border-box;word-break:break-word;}
    .back .value{font-size:7.2pt;font-weight:800;color:#0f172a;line-height:1.05;}
    .back .gender{top:1.9cm; left:1cm; width:2.25cm;}
    .back .blood{top:1.9cm; left:3.35cm; width:2.15cm;}
    .back .em-name{top:3cm;left:0.8cm;}
    .back .em-address{top:3.7cm;left:0.8cm;}
    .back .em-contact{top:4.27cm;left:1.25cm;}
    .back .civil{top:5.1cm;left:1.4cm;}
    .back .gsis{top:5.5cm;left:1.4cm;}
    .back .tin{top:5.95cm;left:1.4cm;}
    .back .bday{top:6.3cm;left:1.4cm;}
    .page-break{page-break-after:always;}
  </style>
</head>
<body>
@foreach ($pages as $pageIndex => $page)
  @php $pageItems = $page->values(); @endphp
  <table class="card-grid">
    @for ($row = 0; $row < $rowsPerSheet; $row++)
      <tr>
        @for ($col = 0; $col < $cols; $col++)
          @php $index = $row * $cols + $col; $faculty = $pageItems->get($index); @endphp
          <td>
            @if ($faculty)
              @php
                $fullName = trim(($faculty->first_name ? $faculty->first_name.' ' : '') . ($faculty->middle_initial ? $faculty->middle_initial.' ' : '') . ($faculty->last_name ?? ''));
                if($fullName === ''){ $fullName = $faculty->name ?: 'Faculty Name'; }
                $nickname = strtoupper($faculty->first_name ?: strtok($fullName, ' ')) ?: 'NAME';
                $position = $faculty->position ?: 'Position / Department';
                $idNumber = $faculty->id_number ?: ($faculty->gsis_number ?: ($faculty->sss_number ?: '000'));
                $photo = $faculty->photo_path;
                if ($photo && Str::startsWith($photo, ['http://','https://'])) {
                    $photoSrc = $photo;
                } elseif ($photo && file_exists(public_path($photo))) {
                    $photoSrc = public_path($photo);
                } elseif ($photo && Storage::disk('public')->exists($photo)) {
                    $photoSrc = public_path('storage/'.ltrim($photo,'/'));
                } else {
                    $photoSrc = public_path('images/photo-placeholder.png');
                }
              @endphp
              <div class="card front">
                @if ($frontTemplate)
                  <img class="bg" src="{{ $frontTemplate }}" alt="Faculty ID front template">
                @else
                  <div class="placeholder">Place front template at<br>public/images/templates/faculty-id-front.png</div>
                @endif
                <div class="overlay photo"><img src="{{ $photoSrc }}" alt="Faculty photo"></div>
                <div class="overlay nickname">{{ $nickname }}</div>
                <div class="overlay fullname"><span>{{ strtoupper($fullName) }}</span></div>
                <div class="overlay position">{{ strtoupper($position) }}</div>
                <div class="overlay idnumber">{{ $idNumber }}</div>
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
          @php $index = $row * $cols + $col; $faculty = $pageItems->get($index); @endphp
          <td>
            @if ($faculty)
              @php
                $blood = $faculty->blood_type ?: 'N/A';
                $gender = $faculty->gender ?: 'N/A';
                $civil = $faculty->civil_status ?: 'N/A';
                $gsis = $faculty->gsis_number ?: 'N/A';
                $sss = $faculty->sss_number ?: 'N/A';
                $bday = optional($faculty->birthday)->format('M d, Y') ?: 'N/A';
                $emerName = $faculty->emergency_contact_name ?: 'N/A';
                $emerContact = $faculty->emergency_contact_number ?: 'N/A';
                $emerAddress = $faculty->emergency_contact_address ?: '';
                $tin = $faculty->tin_number ?: ($faculty->sss_number ?: 'N/A');
              @endphp
              <div class="card back">
                @if ($backTemplate)
                  <img class="bg" src="{{ $backTemplate }}" alt="Faculty ID back template">
                @else
                  <div class="placeholder">Place back template at<br>public/images/templates/faculty-id-back.png</div>
                @endif
                <div class="field overlay gender"><div class="value">{{ $gender }}</div></div>
                <div class="field overlay blood"><div class="value">{{ strtoupper($blood) }}</div></div>
                <div class="field overlay em-name"><div class="value">{{ $emerName }}</div></div>
                <div class="field overlay em-address"><div class="value">{{ $emerAddress }}</div></div>
                <div class="field overlay em-contact"><div class="value">{{ $emerContact }}</div></div>
                <div class="field overlay civil"><div class="value">{{ $civil }}</div></div>
                <div class="field overlay gsis"><div class="value">{{ $gsis }}</div></div>
                <div class="field overlay tin"><div class="value">{{ $tin }}</div></div>
                <div class="field overlay bday"><div class="value">{{ $bday }}</div></div>
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
