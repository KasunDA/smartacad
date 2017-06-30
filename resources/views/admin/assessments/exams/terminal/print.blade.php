<!DOCTYPE html>
<html lang="en">
<head>
    <head>
        <title>Student Terminal Result Sheet</title>
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

                <div style="color:#666; font-size: 36px; font-weight: bolder; font-family: 'verdana', 'lucida grande', 'sans-serif'">
                    {{ strtoupper($mySchool->full_name) }}
                </div>
                {!! ($mySchool->address) ? '<div style="font-size: 14px; font-weight: bold;">'.$mySchool->address.'</div>' : '' !!}
                {!! ($mySchool->motto) ? '<h5>'.$mySchool->motto.'</h5>' : '' !!}
                {!! ($mySchool->website) ? '<h6>'.$mySchool->website.'</h6>' : '' !!}
            </div>
        </div>

        <div align="center">
            <h5><strong>{{ $term->academic_term }} ACADEMIC TERM REPORT</strong></h5>
        </div>
        <div style="position:relative; width:100%">
            <div  style="position:relative">
                <div id="apDi1">
                    <table class="table table-bordered" width="800">
                        <caption style="font-weight: bolder">Student's Information</caption>
                        @if(isset($position->student_sum_total))
                            <tr>
                                <th width="120" style="background-color: #F2F0F0 !important;">Full Name: </th>
                                <td width="280">{{ $student->fullNames() }}</td>
                                <th width="100" style="background-color: #F2F0F0 !important;">Gender: </th>
                                <td width="100">{{ $student->gender }}</td>
                                {{--<th width="100" style="background-color: #F2F0F0 !important;">Position: </th>--}}
                                {{--<td width="100">{{ Assessment::formatPosition($position->class_position) }}</td>--}}
                                {{--<th width="100" style="background-color: #F2F0F0 !important;">Total: </th>--}}
                                {{--<td width="100">{{ $position->student_sum_total }}</td>--}}
                            </tr>
                            <tr>
                                <th width="120" style="background-color: #F2F0F0 !important;">Student No.: </th>
                                <td width="280">{{ $student->student_no }}</td>
                                <th width="100" style="background-color: #F2F0F0 !important;">Age: </th>
                                <td width="100">{!! ($student->dob) ? $student->dob->age . ' Year(s)' : '-' !!}</td>
                                {{--<th width="100" style="background-color: #F2F0F0 !important;">Out of: </th>--}}
                                {{--<td width="100">{{ $position->class_size }}</td>--}}
                            </tr>
                            <tr>
                                <th width="120" style="background-color: #F2F0F0 !important;">Classroom: </th>
                                <td width="280">{{ $classroom->classroom }}</td>
                                <th width="100" style="background-color: #F2F0F0 !important;">Score(%): </th>
                                <td width="100">{!! ($position->exam_perfect_score > 0)
                                    ? number_format((($position->student_sum_total / $position->exam_perfect_score) * 100), 2) . '%' : '-' !!}
                                </td>
                            </tr>
                        @else
                            <tr>
                                <th>No Record Found</th>
                            </tr>
                        @endif
                    </table>
                </div>
                <div id="apDiv1">
                    <table class="table table-bordered" width="750" style="margin-top: 100px;">
                        <caption style="font-weight: bolder">Continuous Assessment (C.A) and Exam Details by Subjects</caption>
                        <thead>
                            <tr style="font-weight:bold; background-color:#CCCCCC;">
                                <th width="260" colspan="2"></th>
                                <th width="330" colspan="2" style="text-align: center">Assessment Scores</th>
                                <th width="170" style="text-align: center">Total</th>
                                <th width="200" colspan="2" style="text-align: center">Grade / Remark</th>
                            </tr>
                            <tr style="font-weight:bold; background-color:#CCCCCC;">
                                <th width="8">#</th>
                                <th width="252">Subject Name</th>
                                <th width="90">C. A ({{$classroom->classLevel()->first()->classGroup()->first()->ca_weight_point}})</th>
                                <th width="100">Exam ({{$classroom->classLevel()->first()->classGroup()->first()->exam_weight_point}})</th>
                                <th width="110">
                                    Total ({{$classroom->classLevel()->first()->classGroup()->first()->ca_weight_point +
                                        $classroom->classLevel()->first()->classGroup()->first()->exam_weight_point}})
                                </th>
                                <th width="60">Grade</th>
                                <th width="160">Remark</th>
                            </tr>
                        </thead>
                        <tbody style="font-size: 11px;">
                            <?php $h = 1; $subIds = []; ?>
                            @if($groups->count() > 0)
                                @foreach($groups as $group)
                                    <?php $cas = $examss = $total = $count = 0;?>
                                    @foreach($group->getImmediateDescendants() as $subject)
                                        @foreach($exams as $exam)
                                            @if($subject->subject_id == $exam->subject_id)
                                                <?php
                                                $cas += $exam->ca;
                                                $examss += $exam->exam;
                                                $total += ($exam->ca + $exam->exam);
                                                $count++;
                                                $subIds[] = $exam->subject_id;
                                                ?>
                                            @endif
                                        @endforeach
                                    @endforeach
                                    <?php
                                    $c = ($count > 0) ? ($cas / $count) : 0;
                                    $e = ($count > 0) ? ($examss / $count) : 0;
                                    $to = ($count > 0) ? ($c + $e) : 0;
                                    $grade = $classroom->classLevel()->first()->classGroup()->first()
                                            ->grades()->where('lower_bound', '<=', ($to))->where('upper_bound', '>=', ($to))->first();
                                    ?>
                                    <tr style="background-color: #F2F0F0 !important; font-weight:bold">
                                        <td class="text-center">{{$h++}} </td>
                                        <td>{{ $group->name }}</td>
                                        <td>{{ number_format($c, 1) }}</td>
                                        <td>{{ number_format($e, 1) }}</td>
                                        <td>{{ number_format($to, 1) }}</td>
                                        <td>{!! ($grade) ? $grade->grade_abbr : '<span class="label label-danger">nil</span>' !!}</td>
                                        <td>{!! ($grade) ? ucwords($grade->grade) : '<span class="label label-danger">nil</span>' !!}</td>
                                    </tr>
                                @endforeach
                            @endif
                            @if(count($exams) > 0)
                                @foreach($exams as $exam)
                                    @if(!in_array($exam->subject_id, $subIds))
                                        <?php
                                        $total = $exam->ca + $exam->exam;
                                        $grade = $classroom->classLevel()->first()->classGroup()->first()
                                                ->grades()->where('lower_bound', '<=', ($total))->where('upper_bound', '>=', ($total))->first();
                                        ?>
                                        <tr style="background-color: #F2F0F0 !important; font-weight:bold">
                                            <td class="text-center">{{$h++}}</td>
                                            <td>{{ $exam->subjectClassroom->subject->subject }}</td>
                                            <td>{!! ($exam->ca) ? number_format($exam->ca, 1) : '<span class="label label-danger">nil</span>' !!}</td>
                                            <td>{!! ($exam->exam) ? number_format($exam->exam, 1) : '<span class="label label-danger">nil</span>' !!}</td>
                                            <td>{!! ($total) ? number_format($total, 1) : '<span class="label label-danger">nil</span>' !!}</td>
                                            <td>{!! ($grade) ? $grade->grade_abbr : '<span class="label label-danger">nil</span>' !!}</td>
                                            <td>{!! ($grade) ? ucwords($grade->grade) : '<span class="label label-danger">nil</span>' !!}</td>
                                        </tr>
                                    @endif
                                @endforeach
                            @else
                                <tr><th colspan="7">No Record Found</th></tr>
                            @endif
                        </tbody>
                        <tfoot>
                            <tr style="font-weight:bold; background-color:#CCCCCC;">
                                <th width="8">#</th>
                                <th width="252">Subject Name</th>
                                <th width="90">C. A ({{$classroom->classLevel()->first()->classGroup()->first()->ca_weight_point}})</th>
                                <th width="100">Exam ({{$classroom->classLevel()->first()->classGroup()->first()->exam_weight_point}})</th>
                                <th width="110">
                                    Total ({{$classroom->classLevel()->first()->classGroup()->first()->ca_weight_point +
                                        $classroom->classLevel()->first()->classGroup()->first()->exam_weight_point}})
                                </th>
                                <th width="60">Grade</th>
                                <th width="160">Remark</th>
                            </tr>
                        </tfoot>
                    </table>
                    <?php $remark = $student->remarks()->where('academic_term_id', $term->academic_term_id);?>
                    <?php $gender1 = ($student->gender == 'Male') ? 'Boy' : 'Girl' ?>
                    <?php $gender2 = ($student->gender == 'Male') ? 'His' : 'She' ?>
                    <table class="table table-bordered">
                        <tr >
                            <td width="370">
                                <div >
                                    <div>
                                        <b>Class Teacher's Remark: </b>
                                        {!! ($remark->count() > 0 and (isset($remark->first()->class_teacher) or $remark->first()->class_teacher != ''))
                                            ? $remark->first()->class_teacher
                                            : $student->simpleName().' is  a responsible child, determined and friendly '.$gender1.'. '.$gender2.' should keep it up.'
                                        !!}
                                    </div><br/>
                                        {!!
                                            ($classroom->classMasters()->where('academic_year_id', $term->academic_year_id)->count() > 0)
                                            ? (($classroom->classMasters()->where('academic_year_id', $term->academic_year_id)->first()->user()->count() > 0)
                                                ? '<div><strong>Name: </strong>'.$classroom->classMasters()->where('academic_year_id', $term->academic_year_id)->first()->user()->first()->simpleNameNSalutation().'</div>'
                                                : '' ) : ''
                                        !!}

                                </div>
                            </td>
                            <td>
                                <div>
                                    <div>
                                        <b>Principal's Remark:</b>
                                        {!! ($remark->count() > 0 and (isset($remark->first()->principal) or $remark->first()->principal != ''))
                                            ? $remark->first()->principal
                                            : $student->simpleName().' is an easy-going '.$gender1.' and '.$gender2.' has improved a great deal '.$gender2.' should keep it up.'
                                        !!}
                                    </div><br />
                                    {{--<div><b>Name: </b></div>--}}
                                </div>
                            </td>
                        </tr>
                    </table>
                    <h6 align="center" style="text-align: center">
                        Powered by
                        <a href="{{ env('DEVELOPER_SITE_ADDRESS') }}" title="{{ env('DEVELOPER_SITE_NAME') }}" target="_blank">
                            {!! (env('DEVELOPER_SITE_NAME')) ? env('DEVELOPER_SITE_NAME') : 'Smart School' !!} ™
                        </a>
                    </h6>
                </div>
                <div id="apDiv2">
                    <ul class="list-unstyled profile-nav">
                        <li>
                            <img style="max-width: 150px; max-height: 180px;" src="{{ $student->getAvatarPath() }}" class="img-responsive pic-bordered" alt="{{ $student->fullNames() }}"/>
                        </li>
                    </ul>
                    @if($term->nextAcademicTerm())
                        <table class="table table-bordered" style="margin-top: 20px;">
                            <tr>
                                <th style="background-color: #F2F0F0 !important;"  width="150">Next Term Begins: </th>
                                <td>{{ $term->nextAcademicTerm()->term_begins->format('D, jS M, Y') }}</td>
                            </tr>
                            <tr>
                                <th style="background-color: #F2F0F0 !important;"  width="150">Next Term Ends: </th>
                                <td>{{ $term->nextAcademicTerm()->term_ends->format('D, jS M, Y') }}</td>
                            </tr>
                        </table>
                    @endif
                    <?php $domain = $student->domainAssessment()->where('academic_term_id', $term->academic_term_id);?>
                    @if($domain->count() > 0)
                        <table class="table table-bordered" style="margin-top: 34px;">
                            <thead>
                                <tr style="font-weight:bold; background-color:#CCCCCC; !important;">
                                    <th width="50%">Affective Domains</th>
                                    <th width="10%">5</th>
                                    <th width="10%">4</th>
                                    <th width="10%">3</th>
                                    <th width="10%">2</th>
                                    <th width="10%">1</th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach($domain->first()->domainDetails()->get() as $detail)
                                <tr>
                                    <td width="50%" style="background-color: #F2F0F0 !important; font-size: 11px;">{{ $detail->domain()->first()->domain }}:</td>
                                    {!! ($detail->option == 5) ? '<td width="10%" style="background-color:#F2F0F0 !important;"><input type="checkbox" checked disabled/> </td>' : '<td></td>'  !!}
                                    {!! ($detail->option == 4) ? '<td width="10%" style="background-color:#F2F0F0 !important;"><input type="checkbox" checked disabled/> </td>' : '<td></td>' !!}
                                    {!! ($detail->option == 3) ? '<td width="10%" style="background-color:#F2F0F0 !important;"><input type="checkbox" checked disabled/> </td>' : '<td></td>' !!}
                                    {!! ($detail->option == 2) ? '<td width="10%" style="background-color:#F2F0F0 !important;"><input type="checkbox" checked disabled/> </td>' : '<td></td>' !!}
                                    {!! ($detail->option == 1) ? '<td width="10%" style="background-color:#F2F0F0 !important;"><input type="checkbox" checked disabled/> </td>' : '<td></td>' !!}
                                </tr>
                            @endforeach
                            <tr>
                                <td colspan="6"><br />
                                    <div style="font-weight:bold; !important;">
                                        <strong>Keys to Ratings</strong>
                                    </div>
                                    <div style="font-size: 9px;">
                                        <ol>
                                            <li>Has No Regard For Observable Traits</li>
                                            <li>Shows Minimal Regard For Observable Traits</li>
                                            <li>Acceptable Level of Observable Traits</li>
                                            <li>Maintains Hgh Level of Observable Traits</li>
                                            <li>Maintains an Excellent of Observable Traits</li>
                                        </ol>
                                    </div>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    @endif
                    @if($classroom->classLevel()->first()->classGroup()->first()->grades()->count() > 0)
                        <table class="table table-bordered">
                            <tr>
                                <th>GRADE</th>
                                <th>SCORES</th>
                                <th>REMARKS</th>
                            </tr>
                            @foreach($classroom->classLevel()->first()->classGroup()->first()->grades()->get() as $grade)
                                <tr>
                                    <th>{{ $grade->grade_abbr }}</th>
                                    <th>{{ $grade->upper_bound . ' - ' . $grade->lower_bound }}</th>
                                    <th>{{ $grade->grade }}</th>
                                </tr>
                            @endforeach
                        </table>
                    @endif
                </div>
                <br clear="all" />
            </div>
        </div>
    </div>
</body>
</html>