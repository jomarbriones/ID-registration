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
  .fp-card{position:relative;width:5.3cm;height:8.5cm;border-radius:14px;overflow:hidden;box-shadow:0 12px 40px rgba(15,23,42,.25);background:#fff;border:1px solid #e2e8f0;flex:0 0 auto;}
  .fp-card *{font-family: Arial, Helvetica, sans-serif !important;}
  .fp-card img.fp-bg{position:absolute;inset:0;width:100%;height:100%;object-fit:cover;z-index:0;}
  .fp-placeholder{position:absolute;inset:0;display:grid;place-items:center;text-align:center;color:#94a3b8;font-size:10pt;padding:0.4cm;z-index:1;background:#f8fafc;}
  .fp-overlay{position:absolute;left:0;right:0;z-index:2;color:#0b3624;}
  /* Front layout */
  .fp-front .fp-photo{top:2.47cm;left:50%;width:3.3cm;height:3.3cm;transform:translateX(-50%);border:0.05cm solid #0f172a;overflow:hidden;border-radius:0.12cm;display:flex;align-items:center;justify-content:center;background:#f8fafc;}
  .fp-front .fp-photo img{width:100%;height:100%;object-fit:cover;object-position:center;}
  .fp-front .fp-nickname{top:5.75cm;text-align:center;font-size:30pt;font-weight:900;letter-spacing:1px;color:#0b3624;line-height:1;-webkit-text-stroke:1.2px #fff;text-shadow:-1.2px -1.2px 0 #fff,1.2px -1.2px 0 #fff,-1.2px 1.2px 0 #fff,1.2px 1.2px 0 #fff;}
  .fp-front .fp-fullname{top:6.8cm;text-align:center;}
  .fp-front .fp-fullname span{display:inline-block;color:#fff;font-size:8.5pt;font-weight:600;letter-spacing:0.02cm;line-height:1.1;}
  .fp-front .fp-position{top:7.3cm;text-align:center;font-size:8.4pt;font-weight:800;color:#0f172a;letter-spacing:0.01cm;}
  .fp-front .fp-idnumber{bottom:0.15cm;text-align:center;font-size:10pt;font-weight:900;color:#ffffff;letter-spacing:0.01cm;}
  /* Back layout */
  .fp-back .fp-field{position:absolute;left:0.75cm;right:0.45cm;font-size:6pt;font-weight:800;color:#0f172a;padding:0.04cm 0.07cm;box-sizing:border-box;word-break:break-word;}
  .fp-back .fp-value{font-size:7.2pt;font-weight:800;color:#0f172a;line-height:1.05;}
  .fp-back .fp-gender{top:1.9cm; left:1cm; width:2.25cm;}
  .fp-back .fp-blood{top:1.9cm; left:3.35cm; width:2.15cm;}
  .fp-back .fp-em-name{top:3cm;left:0.5cm;}
  .fp-back .fp-em-address{top:3.7cm;left:0.5cm;}
  .fp-back .fp-em-contact{top:4.27cm;left:0.55cm;}
  .fp-back .fp-civil{top:5.1cm;left:1.4cm;}
  .fp-back .fp-gsis{top:5.5cm;left:1.4cm;}
  .fp-back .fp-tin{top:5.95cm;left:1.4cm;}
  .fp-back .fp-bday{top:6.3cm;left:1.4cm;}
</style>
<div class="preview-wrap">
  <div class="fp-card fp-front">
    @if ($frontTemplate)
      <img class="fp-bg" src="{{ $frontTemplate }}" alt="Faculty ID front template">
    @else
      <div class="fp-placeholder">Place faculty front template at<br><code>public/images/templates/faculty-id-front.png</code></div>
    @endif
    <div class="fp-overlay fp-photo"><img src="{{ $photoSrc }}" alt="Faculty photo"></div>
    <div class="fp-overlay fp-nickname">{{ $nickname }}</div>
    <div class="fp-overlay fp-fullname"><span>{{ strtoupper($fullName) }}</span></div>
    <div class="fp-overlay fp-position">{{ strtoupper($position) }}</div>
    <div class="fp-overlay fp-idnumber">{{ $idNumber }}</div>
  </div>
  <div class="fp-card fp-back">
    @if ($backTemplate)
      <img class="fp-bg" src="{{ $backTemplate }}" alt="Faculty ID back template">
    @else
      <div class="fp-placeholder">Place faculty back template at<br><code>public/images/templates/faculty-id-back.png</code></div>
    @endif
    <div class="fp-field fp-overlay fp-gender"><div class="fp-value">{{ $gender }}</div></div>
    <div class="fp-field fp-overlay fp-blood"><div class="fp-value">{{ strtoupper($blood) }}</div></div>
    <div class="fp-field fp-overlay fp-em-name"><div class="fp-value">{{ $emerName }}</div></div>
    <div class="fp-field fp-overlay fp-em-address"><div class="fp-value">{{ $emerAddress }}</div></div>
    <div class="fp-field fp-overlay fp-em-contact"><div class="fp-value">{{ $emerContact }}</div></div>
    <div class="fp-field fp-overlay fp-civil"><div class="fp-value">{{ $civil }}</div></div>
    <div class="fp-field fp-overlay fp-gsis"><div class="fp-value">{{ $gsis }}</div></div>
    <div class="fp-field fp-overlay fp-tin"><div class="fp-value">{{ $tin }}</div></div>
    <div class="fp-field fp-overlay fp-bday"><div class="fp-value">{{ $bday }}</div></div>
  </div>
</div>
