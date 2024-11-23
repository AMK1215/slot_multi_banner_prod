@extends('layouts.master')
@section('content')
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-12">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item active">W/L Report</li>
                </ol>
            </div>
        </div>
    </div>
</section>

<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-end mb-3">
                    <a href="{{ route('home') }}" class="btn btn-success " style="width: 100px;"><i
                            class="fas fa-arrow-left text-white  mr-2"></i>Back</a>
                </div>
                <div class="card " style="border-radius: 20px;">
                    <div class="card-header">
                        <h3>Report</h3>
                    </div>
                    <form role="form" class="text-start" action="{{ route('admin.shan.reports.index') }}" method="GET">
                        <div class="row ml-5">
                            <div class="col-lg-3">
                                <div class="mb-3">
                                    <label class="form-label text-dark fw-bold" for="inputEmail1">From Date</label>
                                    <input type="date" class="form-control border border-1 border-secondary px-2"
                                        id="inputEmail1" name="start_date">
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="mb-3">
                                    <label class="form-label text-dark fw-bold" for="inputEmail1">To Date</label>
                                    <input type="date" class="form-control border border-1 border-secondary px-2"
                                        id="inputEmail1" name="end_date">
                                </div>
                            </div>
                            <div class="col-log-3">
                                <button type="submit" class="btn btn-primary" style="margin-top: 32px;">Search</button>
                                <a href="{{ route('admin.shan.reports.index') }}" class="btn btn-warning" style="margin-top: 32px;">Refresh</a>
                            </div>
                        </div>
                    </form>
                    <div class="card-body">
                        <table id="" class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>AgentName</th>
                                    <th>UserName</th>
                                    <th>TotalStake</th>
                                    <th>TotalBet</th>
                                    <th>TotalWin</th>
                                    <th>TotalNetWin</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($report as $row)
                                <tr>
                                    <td>{{ $row->agent_name }}</td>
                                    <td>{{ $row->player_name }}</td>
                                    <td>{{ $row->total_games }}</td>
                                    <td>{{ number_format($row->total_bet_amount, 2) }}</td>
                                    <td>{{ number_format($row->total_win_amount, 2) }}</td>
                                    <td>{{ number_format($row->total_net_win, 2) }}</td>
                                    <td><a
                                            href="{{ route('admin.reports.details', $row->user_id) }}">Detail</a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>

                        </table>
                        {{ $report->links()}}
                    </div>
                    <!-- /.card-body -->
                </div>
                <!-- /.card -->
            </div>
        </div>
    </div>
</section>
@endsection