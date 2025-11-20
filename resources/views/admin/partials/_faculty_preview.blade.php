@php
use Illuminate\Support\Facades\Storage;

$frontPath = public_path('images/templates/faculty-id-front.png');
$backPath = public_path('images/templates/faculty-id-back.png');

$frontTemplate = file_exists($frontPath) ? asset('images/templates/faculty-id-front.png') : null;
$backTemplate = file_exists($backPath) ? asset('images/templates/faculty-id-back.png') : null;

$fullName = trim(($faculty->first_name ? $faculty->first_name.' ' : '') . ($faculty->middle_initial ? $faculty->middle_initial.' ' : '') . ($faculty->last_name ?? ''));
if($fullName === ''){ $fullName = $faculty->name ?: 'Faculty Name'; }
$nickname = strtoupper($faculty->first_name ?: strtok($fullName, ' ')) ?: 'NAME';
$position = $faculty->position ?: 'Position / Department';
$idNumber = $faculty->id_number ?: ($faculty->gsis_number ?: ($faculty->sss_number ?: '000'));
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

$photoSrc = $photoUrl ?? asset('images/photo-placeholder.png');
@endphp
<style>
  *{font-family: Arial, Helvetica, sans-serif;}
  .preview-wrap{display:flex;gap:16px;justify-content:center;align-items:flex-start;}
  .card{position:relative;width:5.3cm;height:8.5cm;border-radius:14px;overflow:hidden;box-shadow:0 12px 40px rgba(15,23,42,.25);background:#fff;border:1px solid #e2e8f0;}
  .card *{font-family: Arial, Helvetica, sans-serif !important;}
  .card img.bg{position:absolute;inset:0;width:100%;height:100%;object-fit:cover;z-index:0;}
  .placeholder{position:absolute;inset:0;display:grid;place-items:center;text-align:center;color:#94a3b8;font-size:10pt;padding:0.4cm;z-index:1;background:#f8fafc;}
  .overlay{position:absolute;left:0;right:0;z-index:2;color:#0b3624;}
  /* Front layout */
  .front .photo{top:2.47cm;left:50%;width:3.3cm;height:3.3cm;transform:translateX(-50%);border:0.05cm solid #0f172a;overflow:hidden;border-radius:0.12cm;display:flex;align-items:center;justify-content:center;background:#f8fafc;}
  .front .photo img{width:100%;height:100%;object-fit:cover;object-position:center;}
  .front .nickname{top:5.6cm;text-align:center;font-size:30pt;font-weight:900;letter-spacing:1px;color:#0b3624;line-height:1;-webkit-text-stroke:0.5px #fff;text-shadow:-0.5px -0.5px 0 #fff,0.5px -0.5px 0 #fff,-0.5px 0.5px 0 #fff,0.5px 0.5px 0 #fff;}
  .front .fullname{top:6.8cm;text-align:center;}
  .front .fullname span{display:inline-block;color:#fff;font-size:8.5pt;font-weight:600;letter-spacing:0.02cm;line-height:1.1;}
  .front .position{top:7.3cm;text-align:center;font-size:8.4pt;font-weight:800;color:#0f172a;letter-spacing:0.01cm;}
  .front .idnumber{bottom:0.15cm;text-align:center;font-size:10pt;font-weight:900;color:#ffffff;letter-spacing:0.01cm;}
  /* Back layout */
  .back .field{position:absolute;left:0.75cm;right:0.45cm;font-size:6pt;font-weight:800;color:#0f172a;padding:0.04cm 0.07cm;box-sizing:border-box;word-break:break-word;}
  .back .value{font-size:7.2pt;font-weight:800;color:#0f172a;line-height:1.05;}
  .back .gender{top:1.9cm; left:1cm; width:2.25cm;}
  .back .blood{top:1.9cm; left:3.35cm; width:2.15cm;}
  .back .em-name{top:3cm;left:0.5cm;}
  .back .em-address{top:3.7cm;left:0.5cm;}
  .back .em-contact{top:4.27cm;left:0.55cm;}
  .back .civil{top:5.1cm;left:1.4cm;}
  .back .gsis{top:5.5cm;left:1.4cm;}
  .back .tin{top:5.95cm;left:1.4cm;}
  .back .bday{top:6.3cm;left:1.4cm;}
</style>
<div class="preview-wrap">
  <div class="card front">
    @if ($frontTemplate)
      <img class="bg" src="{{ $frontTemplate }}" alt="Faculty ID front template">
    @else
      <div class="placeholder">Place faculty front template at<br>public/images/templates/faculty-id-front.png</div>
    @endif
    <div class="overlay photo"><img src="{{ $photoSrc }}" alt="Faculty photo"></div>
    <div class="overlay nickname">{{ $nickname }}</div>
    <div class="overlay fullname"><span>{{ strtoupper($fullName) }}</span></div>
    <div class="overlay position">{{ strtoupper($position) }}</div>
    <div class="overlay idnumber">{{ $idNumber }}</div>
  </div>
  <div class="card back">
    @if ($backTemplate)
      <img class="bg" src="{{ $backTemplate }}" alt="Faculty ID back template">
    @else
      <div class="placeholder">Place faculty back template at<br>public/images/templates/faculty-id-back.png</div>
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
</div>
