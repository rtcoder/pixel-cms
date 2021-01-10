@extends('layout.app')
@section('title', __('pages.contacts'))

@section('content')

    @if($contact ?? false)
        <h1>@lang('common.edit')</h1>
    @else
        <h1>@lang('common.add')</h1>
    @endif

    <form action="" method="post">
        @csrf

        <label>
            Imię:
            <input type="text" name="first_name"
                   value="{{ old('first_name', $contact->first_name ?? '') }}">
        </label>
        <label>
            Nazwisko:
            <input type="text" name="last_name"
                   value="{{ old('last_name', $contact->last_name ?? '') }}"
                   required>
        </label>
        <label>
            Firma:
            <input type="text" name="company"
                   value="{{ old('company', $contact->company ?? '') }}">
        </label>
        <div class="row">
            <div id="emails">
                @php
                    $addresses = old('email_addresses', ($contact ?? false) && $contact->emailAddresses->count() ? $contact->emailAddresses->toArray() : [['value' => '']]);
                @endphp
                @foreach($addresses as $key => $address)

                    <label data-id="e-{{ $key }}">
                        Email:
                        <input type="email" name="email_addresses[{{ $key }}][value]"
                               value="{{ $address['value']??'' }}">
                        <button class="remove" onclick="removeEmail({{ $key }})">
                            <span class="material-icons">remove</span>
                        </button>
                    </label>
                @endforeach
            </div>
            <button class="add" onclick="addEmail()" type="button">
                <span class="material-icons">add</span>
            </button>
        </div>
        <div class="row">
            <div id="phone-numbers">
                @php
                    $phoneNumbers = old('phone_numbers', ($contact ?? false) && $contact->phoneNumbers->count() ? $contact->phoneNumbers->toArray() : [['value' => '']]);
                @endphp
                @foreach($phoneNumbers as $key => $phoneNumber)

                    <label data-id="p-{{ $key }}">
                        Numer telefonu:
                        <input type="tel" name="phone_numbers[{{ $key }}][value]"
                               value="{{ $phoneNumber['value'] }}">
                        <button class="remove" onclick="removePhone({{ $key }})">
                            <span class="material-icons">remove</span>
                        </button>
                    </label>
                @endforeach
            </div>
            <button class="add" onclick="addPhone()" type="button">
                <span class="material-icons">add</span>
            </button>
        </div>
        <button type="submit">@lang('common.save')</button>
    </form>

@endsection
@section('scripts')
    <script>
        function addEmail() {
            const container = document.getElementById('emails');
            const labelsCount = container.querySelectorAll('label').length;

            container.innerHTML += `<label  data-id="e-${labelsCount}">
                    Email:
                    <input type="email" name="email_addresses[${labelsCount}][value]">
                    <button class="remove" onclick="removeEmail(${labelsCount})">
                        <span class="material-icons">remove</span>
                    </button>
                </label>`;
        }

        function removeEmail(id) {
            const container = document.getElementById('emails');
            container.querySelector(`label[data-id="e-${id}"]`).remove();

            container.querySelectorAll('label').forEach((label, key) => {
                label.setAttribute('data-id', `e-${key}`);

                label.querySelector('input').setAttribute('name', `email_addresses[${key}]`)
                label.querySelector('button').setAttribute('onclick', `removeEmail(${key})`)
            });
            if (!container.querySelectorAll('label').length) {
                addEmail();
            }
        }

        function addPhone() {
            const container = document.getElementById('phone-numbers');
            const labelsCount = container.querySelectorAll('label').length;

            container.innerHTML += `<label  data-id="p-${labelsCount}">
                    Numer telefonu:
                    <input type="tel" name="phone_numbers[${labelsCount}][value]">
                    <button class="remove" onclick="removePhone(${labelsCount})">
                        <span class="material-icons">remove</span>
                    </button>
                </label>`;
        }

        function removePhone(id) {
            const container = document.getElementById('phone-numbers');
            container.querySelector(`label[data-id="p-${id}"]`).remove();

            container.querySelectorAll('label').forEach((label, key) => {
                label.setAttribute('data-id', `p-${key}`);

                label.querySelector('input').setAttribute('name', `phone_numbers[${key}]`)
                label.querySelector('button').setAttribute('onclick', `removePhone(${key})`)
            });
            if (!container.querySelectorAll('label').length) {
                addPhone();
            }
        }
    </script>
@endsection
