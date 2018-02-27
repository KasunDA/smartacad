<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Broad Sheet for {{ $classRoom->classroom }} in {{ $academicYear->academic_year }}</title>
    <link rel="shortcut icon" href="{{ public_path($mySchool->getLogoPath()) }}" />
    {{--<style type="text/css">--}}
        {{--.invoice-box{--}}
            {{--max-width:800px;--}}
            {{--margin:auto;--}}
            {{--padding:30px;--}}
            {{--border:1px solid #eee;--}}
            {{--box-shadow:0 0 10px rgba(0, 0, 0, .15);--}}
            {{--font-size:15px;--}}
            {{--line-height:20px;--}}
            {{--font-family:'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;--}}
            {{--color:#555;--}}
        {{--}--}}

        {{--.invoice-box table{--}}
            {{--width:100%;--}}
            {{--line-height:inherit;--}}
            {{--text-align:left;--}}
        {{--}--}}

        {{--.invoice-box table td{--}}
            {{--padding:5px;--}}
            {{--vertical-align:top;--}}
        {{--}--}}

        {{--.invoice-box table tr td:nth-child(2){--}}
            {{--text-align:left;--}}
        {{--}--}}

        {{--.invoice-box table tr.top table td{--}}
            {{--padding-bottom:0px;--}}
        {{--}--}}

        {{--.invoice-box table tr.top table td.title{--}}
            {{--font-size:35px;--}}
            {{--line-height:40px;--}}
            {{--color:#333;--}}
        {{--}--}}

        {{--.invoice-box table tr.information table td{--}}
            {{--padding-bottom:0px;--}}
        {{--}--}}

        {{--.invoice-box table tr.heading td{--}}
            {{--background:#eee;--}}
            {{--font-size:14px;--}}
            {{--border-bottom:1px solid #ddd;--}}
            {{--font-weight:bold;--}}
        {{--}--}}

        {{--.invoice-box table tr.details td{--}}
            {{--padding-bottom:20px;--}}
        {{--}--}}

        {{--.invoice-box table tr.item td{--}}
            {{--border-bottom:1px solid #eee;--}}
            {{--font-size:12px;--}}
        {{--}--}}

        {{--.invoice-box table tr.item td:last-child{--}}
            {{--text-align: right;--}}
            {{--font-size:13px;--}}
        {{--}--}}

        {{--.invoice-box table tr.item.last td{--}}
            {{--border-bottom:none;--}}
        {{--}--}}

        {{--.invoice-box table tr.total td{--}}
            {{--border-top:2px solid #eee;--}}
            {{--font-weight:bold;--}}
            {{--font-size:14px;--}}
        {{--}--}}

        {{--.invoice-box table tr.total td:last-child{--}}
            {{--text-align: right;--}}
        {{--}--}}

        {{--@media print {--}}
            {{--body * {--}}
                {{--visibility: hidden;--}}
            {{--}--}}
            {{--.invoice-box * {--}}
                {{--visibility: visible;--}}
            {{--}--}}
        {{--}--}}

        {{--@media only screen and (max-width: 600px) {--}}
            {{--.invoice-box table tr.top table td{--}}
                {{--width:100%;--}}
                {{--display:block;--}}
                {{--text-align:center;--}}
            {{--}--}}

            {{--.invoice-box table tr.information table td{--}}
                {{--width:100%;--}}
                {{--display:block;--}}
                {{--text-align:center;--}}
            {{--}--}}
        {{--}--}}
        {{--.invoice-right{--}}
            {{--text-align:right !important;--}}
        {{--}--}}

        {{--.page-break {--}}
            {{--page-break-after: always;--}}
        {{--}--}}
    {{--</style>--}}
</head><body>
<div class="invoice-box">
    <table cellpadding="0" cellspacing="0" border="1">
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
                            <strong>Academic Year: {{ $academicYear->academic_year }}</strong><br>
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
                    <th colspan="4" style="text-align: center">{{ str_replace('_', ' ',$subject) }}</th>
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
            <tr class="total"><th colspan="5" style="text-align: center">No Record Found</th></tr>
        @endforelse
        <!-- / Table Details-->
    </table>
</div>
</body></html>
