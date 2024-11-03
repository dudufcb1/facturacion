@extends('layouts.app')

@section('content')
  <form action="{{ route('invoices.store') }}" method="POST">
    @csrf
    <div class="form-group">
      <label for="company_id">Company</label>
      <select name="company_id" id="company_id" class="form-control">
        @foreach ($companies as $company)
          <option value="{{ $company->id }}" {{ $company->id == $defaultCompany->id ? 'selected' : '' }}>
            {{ $company->name }}
          </option>
        @endforeach
      </select>
    </div>
    <!-- Other form fields... -->
    <button type="submit" class="btn btn-primary">Create Invoice</button>
  </form>
@endsection
