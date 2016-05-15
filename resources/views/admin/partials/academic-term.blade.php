<option value="">Select Academic Term</option>
@foreach($academic_terms as $academic_term)
    <option value="{{ $academic_term->academic_term_id }}">{{ $academic_term->academic_term }}</option>
@endforeach