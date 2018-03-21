<!doctype html>
<html><head>
    <meta charset="utf-8">
    <title>Broad Sheet for {{ $classRoom->classroom }} in {{ $academicYear->academic_year }}</title>
    <link href="{{ asset('assets/global/plugins/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/custom/css/invoice.css') }}" rel="stylesheet" type="text/css" media="all" />

    <link rel="shortcut icon" href="{{ $mySchool->getLogoPath() }}" />
</head><body>

<div class="invoice-box">
    <table cellpadding="0" cellspacing="0" style="border: thin">
        <!-- School Details-->
        <tr class="top">
            <td colspan="16">
                <table>
                    <tr>
                        <td class="title">
                            <img src="{{$mySchool->getLogoPath()}}" style="max-width:100px; max-height:100px;">
                        </td>

                        <td class="invoice-right">
                            <div style="color:#666; font-size: 26px;">
                                {{ strtoupper($mySchool->full_name) }}
                            </div>
                            {!! ($mySchool->address) ? '<div style="font-size: 12px; font-weight: bold;">'.$mySchool->address.'</div>' : '' !!}
                            {!! ($mySchool->email) ? '<small>email: '.$mySchool->email.'</small>' : '' !!}
                            {!! ($mySchool->website) ? '<small>website: '.$mySchool->website.'</small>' : '' !!}<br>
                            {!! ($mySchool->phone_no) ? '<small>phone no.: ' . $mySchool->phone_no . ', ' . $mySchool->phone_no2 ?? '' . '</small>' : '' !!}
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <!-- / School Details-->

        <!-- Class Details-->
        <tr class="information">
            <td colspan="16">
                <table>
                    <tr>
                        <td>
                            Class Level: <strong>{{ $classRoom->classLevel->classlevel }}</strong><br>
                            Class Room: <strong>{{ $classRoom->classroom }}<strong><br>
                        </td>

                        <td class="invoice-right">
                            <strong>Academic Year: {{ $academicYear->academic_year }}</strong>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <!-- / Class Details-->
        <!-- Table Header-->
        <tr class="heading">
            <th></th>
            @foreach($subjects as $subject)
                @if($loop->iteration == 4)
                    <th>Subjects</th>
                @endif
                @if($loop->iteration > 4)
                    <th colspan="4" class="text-center">{{ str_replace('_', ' ',$subject) }}</th>
                @endif
            @endforeach
        </tr>
        <tr>
            <th>#</th>
            <th>Name</th>
            @for($i = 0; $i < count($subjects) - 4; $i++)
                <th>1st</th>
                <th>2nd</th>
                <th>3rd</th>
                <th>&Sigma;</th>
            @endfor
        </tr>
        <!-- / Table Header-->
        <!-- Table Details-->
        @forelse($examsStudents as $student)
            <?php $first = $second = $third = null; ?>
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{!! $student[0]->Name !!}</td>
                @foreach($student as $exam)
                    <?php if($exam->term_type_id == 1) $first = $exam; ?>
                    <?php if($exam->term_type_id == 2) $second = $exam; ?>
                    <?php if($exam->term_type_id == 3) $third = $exam; ?>
                @endforeach
                @foreach($subjects as $subject)
                    @if($loop->iteration > 4)
                        <td>{!! !empty($first) && $first->{$subject} ? number_format($first->{$subject}, 1) : LabelHelper::danger() !!}</td>
                        <td>{!! !empty($second) && $second->{$subject} ? number_format($second->{$subject}, 1) : LabelHelper::danger() !!}</td>
                        <td>{!! !empty($third) && $third->{$subject} ? number_format($third->{$subject}, 1) : LabelHelper::danger() !!}</td>
                        <td>{{ number_format(($first->{$subject} ?? 0) + ($second->{$subject} ?? 0) + ($third->{$subject} ?? 0), 1) }}</td>
                    @endif
                @endforeach
            </tr>
        @empty
            <tr class="total"><th colspan="5" class="text-center">No Record Found</th></tr>
        @endforelse
        <!-- / Table Details-->
    </table>
</div>

<script src="{{ asset('assets/global/plugins/jquery.min.js')}}" type="text/javascript"></script>
<script src="{{ asset('assets/global/plugins/bootstrap/js/bootstrap.min.js')}}" type="text/javascript"></script>
<script src="{{ asset('assets/global/scripts/jquery.PrintArea.js')}}" type="text/javascript"></script>

<script>
    $(document).ready(function () {
        $('.print-button').click(function () {
            $('.invoice-box').printArea()
        })
        setTabActive('[href="/exams/broad-sheet"]');
    })
</script>

</body></html>
