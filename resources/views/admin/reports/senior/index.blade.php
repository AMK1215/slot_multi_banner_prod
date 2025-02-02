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
                    <a href="{{ route('home') }}" class="btn btn-primary " style="width: 100px;"><i
                            class="fas fa-arrow-left text-white  mr-2"></i>Back</a>
                </div>
                <div class="card " style="border-radius: 20px;">
                    <div class="card-header">
                        @if (Auth::user()->hasRole('Senior'))
                        <h3>Owner Report</h3>
                        @elseif(Auth::user()->role('Owner'))
                        <h3>Agent Report</h3>
                        @else
                        <h4>Sub Agent Report</h4>
                        @endif
                    </div>
                    <div class="card-body">
                        <table id="mytable" class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                @if (Auth::user()->hasRole('Senior'))
                                    <th>Owner</th>
                                @elseif(Auth::user()->role('Owner'))
                                    <th>Agent</th>
                                @else
                                    <th>SubAgent</th>
                                @endif
                                    <th>Total Bets</th>
                                    <th>Total Wins</th>
                                    <th>Total Net</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data as $row)
                                <tr>
                                    <td>{{ $row['admin_name'] }}</td>
                                    <td>{{ $row['total_bets'] }}</td>
                                    <td>{{ $row['total_wins'] }}</td>
                                    <td>{{ $row['total_net'] }}</td>
                                </tr>
                                @endforeach
                            </tbody>

                        </table>
                    </div>
                    <!-- /.card-body -->
                </div>
                <!-- /.card -->
            </div>
        </div>
    </div>
</section>
@endsection