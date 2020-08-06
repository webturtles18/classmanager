@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        @if(!$is_recipient)
        <div class="col-md-5">
            <div class="card">
                <div class="card-header">
                    <h3>Add Recipient</h3>
                </div>
                <div class="card-body">
                    @include('includes.status')

                    <form action="{{ route('recipients') }}" method="post">
                        @csrf
                        <div class="form-row">
                            <div class="form-group col-md-12">
                                <label>Currency</label>
                                <input type="text" name="currency" class="form-control" value="INR" readonly=""/>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-12">
                                <div class="row">
                                    <div class="col-md-12">
                                        <label class="control-label">Select recipient type<span class="text-danger">*</span></label>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="radio col-md-6">
                                        <div class="form-control">
                                            <label><input type="radio" name="recipient_type" value="1" checked /> Personal</label>
                                        </div>
                                    </div>
                                    <div class="radio col-md-6">
                                        <div class="form-control">
                                            <label><input type="radio" name="recipient_type" value="2"/> Business</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-12">
                                <label>Account Holder Name<span class="text-danger">*</span></label>
                                <input type="text" name="ac_holder_name" class="form-control" value="{{ old('ac_holder_name') }}"/>
                            </div>
                        </div>
                        <h4 class="page-header m-y-2">Recipient bank details</h4>
                        <div class="form-row">
                            <div class="form-group col-md-12">
                                <label>IFSC Code</label>
                                <input type="text" name="ifsc_code" class="form-control" value="YESB0236041" readonly=""/>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-12">
                                <label>Account number</label>
                                <input type="text" name="account_number" class="form-control" value="678911234567891" readonly=""/>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Add Recipient</button>
                    </form>
                </div>
            </div>
        </div>
        @else
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3>Recipient Details</h3>
                </div>
                <div class="card-body">
                    @include('includes.status')
                    <div class="row mb-3 text-muted">
                        <div class="col-md-6">
                            <strong>Profile ID:</strong> <span>{{ $recipient->profile }}</span>
                        </div>
                        <div class="col-md-6">
                            <strong>Recipient ID:</strong> <span>{{ $recipient->recipient_id }}</span>
                        </div>
                    </div>
                    <div class="row mb-3 text-muted">
                        <div class="col-md-12">
                            <strong>Account Holder Name:</strong> <span>{{ $recipient->account_holder_name }}</span>
                        </div>
                    </div>
                    <div class="row mb-3 text-muted">
                        <div class="col-md-6">
                            <strong>Currency:</strong> <span>{{ $recipient->currency }}</span>
                        </div>
                        <div class="col-md-6">
                            <strong>Country Code:</strong> <span>{{ $recipient->country }}</span>
                        </div>
                    </div>
                    <div class="row mb-3 text-muted">
                        <div class="col-md-6">
                            <strong>Type:</strong> <span>{{ strtoupper($recipient->type) }}</span>
                        </div>
                        <div class="col-md-6">
                            <strong>Recipient Type:</strong> <span>{{ $recipient->details['legalType'] }}</span>
                        </div>
                    </div>
                    <div class="row mb-3 text-muted">
                        <div class="col-md-6">
                            <strong>Account Number:</strong> <span>{{ $recipient->details['accountNumber'] }}</span>
                        </div>
                        <div class="col-md-6">
                            <strong>IFSC Code:</strong> <span>{{ $recipient->details['ifscCode'] }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

@section('scripts')
<script type="text/javascript">
    
    
</script>
@endsection