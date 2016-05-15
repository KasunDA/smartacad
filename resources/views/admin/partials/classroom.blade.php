<option value="">Select Class Room</option>
@foreach($classrooms as $classroom)
    <option value="{{ $classroom->classroom_id }}">{{ $classroom->classroom }}</option>
@endforeach