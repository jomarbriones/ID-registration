<div style="display:grid;grid-template-columns:220px 1fr;gap:16px;align-items:start">
  <div style="aspect-ratio:3/4;border:1px solid #e2e8f0;border-radius:12px;overflow:hidden;background:#f8fafc;display:flex;align-items:center;justify-content:center">
    @if ($s->picture_path)
      <img src="{{ asset('storage/'.$s->picture_path) }}" alt="photo" style="width:100%;height:100%;object-fit:cover">
    @else
      <span style="color:#94a3b8;font-size:12px">No photo</span>
    @endif
  </div>

  <dl style="display:grid;grid-template-columns:160px 1fr;gap:10px 12px">
    <dt style="color:#64748b">Student Number</dt><dd>{{ $s->id_number }}</dd>
    <dt style="color:#64748b">Name</dt><dd>{{ $s->last_name }}, {{ $s->first_name }} {{ $s->middle_initial }}</dd>
    <dt style="color:#64748b">Course</dt><dd>{{ $s->course }}</dd>
    <dt style="color:#64748b">Blood Type</dt><dd>{{ $s->blood_type ?: '—' }}</dd>
    <dt style="color:#64748b">Address</dt><dd>{{ $s->address ?: '—' }}</dd>
    <dt style="color:#64748b">Guardian</dt><dd>{{ $s->guardian_name ?: '—' }} ({{ $s->guardian_contact ?: '—' }})</dd>
    <dt style="color:#64748b">Submitted</dt><dd>{{ $s->created_at?->format('Y-m-d H:i') ?? '—' }}</dd>
  </dl>
</div>