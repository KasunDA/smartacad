<option value="">Nothing Selected</option>
@foreach($lgas as $lga)
    <option value="{{ $lga->lga_id }}">{{ $lga->lga }}</option>
@endforeach