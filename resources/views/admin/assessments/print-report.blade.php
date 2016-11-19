<!DOCTYPE html>
<html lang="en">
<head>
    <head>
        <title>Student Terminal Assessment Sheet</title>
        <link href="{{ asset('assets/global/plugins/bootstrap/css/bootstrap.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ asset('assets/custom/css/print.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ $mySchool->getLogoPath() }}" rel="shortcut icon">
        <style type="text/css">
            .table th,
            .table td {
                padding: 6px;
                line-height: 13px;
                text-align: left;
                vertical-align: top;
                border-top: 1px solid #dddddd;
            }
            #apDiv1 {
                position:absolute;
                left:0px;
                top:55px;
                width:750px;
                z-index:1;
                text-align: center;
            }
            #apDiv2 {
                position:absolute;
                left:779px;
                top:10px;
                width:310px;
                z-index:2;
            }
            #apDi1 {
                position:absolute;
                left:0px;
                top:13px;
                width:650px;
                z-index:3;
            }
            .style1 {font-size: x-small;}
        </style>
    </head>
<body style="padding-top: 15px; background-color: white" bgcolor="white">
    <div class="container-fluid">
        <div class="you">
            <div align="center" style="width:100%;">
                <div align="center"><img style="width: 80px; height: 80px;;" src="{{ $mySchool->getLogoPath() }}" alt="School Logo"/></div>

                <div style="color:#666; font-size: 30px; font-weight: bolder; font-family: 'verdana', 'lucida grande', 'sans-serif'">
                    {{ strtoupper($mySchool->full_name) }}
                </div>
                {!! ($mySchool->address) ? '<div style="font-size: 12px; font-weight: bold;">'.$mySchool->address.'</div>' : '' !!}
                {!! ($mySchool->motto) ? '<h6>'.$mySchool->motto.'</h6>' : '' !!}
                {!! ($mySchool->website) ? '<small>'.$mySchool->website.'</small>' : '' !!}
            </div>
        </div>

        <div align="center">
            <h5><strong>{{ $term->academic_term }} Academic Term Assessment Report</strong></h5>
        </div>
        <div style="position:relative; width:100%">
            <div  style="position:relative">
                <div id="apDi1">
                    <table class="table table-bordered" width="860">
                        <caption style="font-weight: bolder">Student's Information</caption>
                        <tr>
                            <th width="120" style="background-color: #F2F0F0 !important;">Full Name: </th>
                            <td width="360">{{ $student->fullNames() }}</td>
                            <th width="100" style="background-color: #F2F0F0 !important;">Assessments: </th>
                            <td width="80">{{ $setup_details->count() }} </td>
                            <th width="100" style="background-color: #F2F0F0 !important;">Gender: </th>
                            <td width="100">{{ $student->gender }}</td>
                        </tr>
                        <tr>
                            <th width="120" style="background-color: #F2F0F0 !important;">Classroom: </th>
                            <td width="360">{{ $classroom->classroom }}</td>
                            <th width="100" style="background-color: #F2F0F0 !important;">Student ID: </th>
                            <td width="80">{{ $student->student_no }}</td>
                            <th width="100" style="background-color: #F2F0F0 !important;">Age: </th>
                            <td width="100">{!! ($student->dob) ? $student->dob->age . ' Year(s)' : '' !!}</td>
                        </tr>
                    </table>
                </div>
                <div id="apDiv1">
                    <table class="table table-bordered" width="750" style="margin-top: 80px;">
                        <caption style="font-weight: bolder">Continuous Assessment (C.A) and Exam Details by Subjects</caption>
                        <thead>
                        <tr>
                            <th colspan="2"></th>
                            <th class="text-center" colspan="{{ $setup_details->count() }}">Student Scores [Points]</th>
                            <th class="text-center">Total</th>
                        </tr>
                        <tr>
                            <th>#</th>
                            <th>Subject Name</th>
                            <?php $su = 0;?>
                            @foreach($setup_details->get() as $setup)
                                <th>{{ Assessment::formatPosition($setup->number) }} C. A. [{{ $setup->weight_point }}]</th>
                                <?php $su += $setup->weight_point; ?>
                            @endforeach
                            <th>Total ({{ $su }})</th>
                        </tr>
                        </thead>
                        <tbody style="font-size: 11px;">
                            @if($subjectClasses->count() > 0)
                                <?php $j = 1; ?>
                                @foreach($subjectClasses as $sub)
                                    <?php $check = $total = 0; ?>
                                    <tr class="odd gradeX">
                                        <td class="center">{{$j++}}</td>
                                        <td>{{ $sub->subjectClassroom->subject->subject }}</td>
                                        @foreach($assessments as $assessment)
                                            @if($assessment->subject_classroom_id === $sub->subject_classroom_id and $check < $setup_details->count())
                                                @for($i=1; $i <= $setup_details->count(); $i++)
                                                    @if($i === $assessment->number)
                                                        <td>{{$assessment->score}}</td>
                                                        <?php $total += $assessment->score; ?>
                                                    @endif
                                                @endfor
                                                <?php $check++; ?>
                                            @endif
                                        @endforeach
                                        <td>{!! ($total) ? number_format($total, 0) : '<span class="label label-danger">nil</span>' !!}</td>
                                    </tr>
                                @endforeach
                            @else
                                <tr><th colspan="{{ $setup_details->count() + 3 }}">No Record Found</th></tr>
                            @endif
                        </tbody>
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Subject Name</th>
                            <?php $su = 0;?>
                            @foreach($setup_details->get() as $setup)
                                <th>{{ Assessment::formatPosition($setup->number) }} C. A. [{{ $setup->weight_point }}]</th>
                                <?php $su += $setup->weight_point; ?>
                            @endforeach
                            <th>Total ({{ $su }})</th>
                        </tr>
                        </thead>
                    </table>
                    <h6 align="center" style="text-align: center">
                        Powered by
                        <a href="{{ env('DEVELOPER_SITE_ADDRESS') }}" title="{{ env('DEVELOPER_SITE_NAME') }}" target="_blank">
                            {!! (env('DEVELOPER_SITE_NAME')) ? env('DEVELOPER_SITE_NAME') : 'Smart School' !!} ™
                        </a>
                    </h6>
                </div>
                <br clear="all" />
            </div>
        </div>
    </div>
</body>
</html>