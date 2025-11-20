@foreach ($faculties as $f)
  <tr data-row-id="{{ $f->id }}">
    <td><input type="checkbox" class="row-select" data-id="{{ $f->id }}" name="faculty_row_select_{{ $f->id }}" id="faculty-row-{{ $f->id }}"></td>
    <td>{{ $f->id_number ?? '—' }}</td>
    <td>{{ $f->last_name ?? '—' }}</td>
    <td>{{ $f->first_name ?? '—' }}</td>
    <td>{{ $f->middle_initial ?? '—' }}</td>
    <td>{{ $f->position ?? '—' }}</td>
    <td>
      <div style="display:flex;gap:6px;align-items:center">
        <button class="chip chip-soft" data-action="preview" data-id="{{ $f->id }}">Preview</button>
        <div class="dropdown" style="position:relative">
          <button class="chip chip-outline" data-menu="faculty-{{ $f->id }}" aria-haspopup="true">⋮</button>
          <div class="menu" data-menu-target="faculty-{{ $f->id }}" style="display:none;position:absolute;right:0;top:100%;background:#fff;border:1px solid #e2e8f0;border-radius:8px;box-shadow:0 10px 30px rgba(15,23,42,.1);padding:6px;min-width:120px;z-index:5">
            <button class="chip chip-soft" data-action="edit" data-id="{{ $f->id }}" style="width:100%;justify-content:flex-start">Edit</button>
            <button class="chip chip-outline" data-action="delete" data-id="{{ $f->id }}" style="width:100%;justify-content:flex-start">Delete</button>
          </div>
        </div>
      </div>
    </td>
  </tr>
@endforeach
@if ($faculties->isEmpty())
  <tr><td colspan="7" style="padding:14px;text-align:center;color:#64748b">No faculty records yet.</td></tr>
@endif
