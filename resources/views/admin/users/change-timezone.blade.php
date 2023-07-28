@extends('layouts.admin')
@section('content')

    <div class="card">
        <div class="card-header">
            Changed TimeZone
        </div>

        <div class="card-body">
            <form method="POST" action="{{ route("admin.store-timezone") }}" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <label>Select TimeZone</label>
                    <select class="form-control {{ $errors->has('timezone') ? 'is-invalid' : '' }}" name="timezone" id="timezone">
                        <option value disabled {{ old('timezone', null) === null ? 'selected' : '' }}>Select TimeZone</option>
                        @foreach(App\user::TIMEZONE_SELECT as $key => $label)
                            <option value="{{ $key }}" {{ old('timezone', $user->timezone) === (string) $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    @if($errors->has('timezone'))
                        <div class="invalid-feedback">
                            {{ $errors->first('timezone') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.ship.fields.type_helper') }}</span>
                </div>
                <div class="form-group">
                    <button class="btn btn-danger" type="submit">
                        Update TimeZone
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
