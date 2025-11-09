@forelse ($students as $s)
<tr data-row-id="{{ $s->id }}"
    data-number="{{ $s->id_number }}"
    data-first="{{ $s->first_name }}"
    data-last="{{ $s->last_name }}"
    data-course="{{ $s->course }}"
    data-status="{{ $s->status }}">
  <td>{{ $s->id_number }}</td>
  <td>{{ $s->first_name }}</td>
  <td>{{ $s->last_name }}</td>
  <td>{{ $s->course }}</td>
  <td>{{ $s->created_at?->format('Y-m-d H:i') ?? '—' }}</td>
  <td>
    <div style="display:flex;gap:8px;align-items:center">
      <button type="button" class="chip chip-soft"
              data-action="preview"
              data-id="{{ $s->id }}"
              data-number="{{ $s->id_number }}"
              data-name="{{ $s->first_name }} {{ $s->last_name }}">Preview</button>

      <button type="button" class="chip chip-emerald"
              data-action="approve"
              data-id="{{ $s->id }}"
              data-number="{{ $s->id_number }}"
              data-name="{{ $s->first_name }} {{ $s->last_name }}">Approve</button>

      <button type="button" class="chip chip-outline"
              data-action="decline"
              data-id="{{ $s->id }}"
              data-number="{{ $s->id_number }}"
              data-name="{{ $s->first_name }} {{ $s->last_name }}">Decline</button>
    </div>
  </td>
</tr>
@empty
<tr><td colspan="6" style="padding:14px;text-align:center;color:#64748b">No pending students.</td></tr>
@endforelse
