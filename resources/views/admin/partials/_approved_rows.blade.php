@forelse ($students as $s)
<tr data-row-id="{{ $s->id }}" data-number="{{ $s->id_number }}" data-first="{{ $s->first_name }}" data-last="{{ $s->last_name }}">
  <td><input type="checkbox" class="row-select" data-id="{{ $s->id }}" data-number="{{ $s->id_number }}"></td>
  <td>{{ $s->id_number }}</td>
  <td>{{ $s->first_name }}</td>
  <td>{{ $s->last_name }}</td>
  <td>{{ $s->course }}</td>
  <td>
    <button type="button" class="chip chip-soft"
            data-action="preview"
            data-id="{{ $s->id }}"
            data-number="{{ $s->id_number }}"
            data-name="{{ $s->first_name }} {{ $s->last_name }}">Preview</button>
    <button type="button" class="chip chip-outline"
            data-action="refresh"
            data-id="{{ $s->id }}"
            data-number="{{ $s->id_number }}"
            data-name="{{ $s->first_name }} {{ $s->last_name }}">Refresh Photo</button>
  </td>
</tr>
@empty
<tr><td colspan="6" style="padding:14px;text-align:center;color:#64748b">No approved students.</td></tr>
@endforelse
