<x-layouts.panel_layout>

<div class="body_section">

<x-form_errors :errors="$errors"></x-form_errors>
<form class="default_form" action="{{ $formType == 'create' ? route('users.store') : route('users.update', $user->id) }}" method="POST">
    @csrf
    {{ $formType == 'edit' ? method_field('PUT') : '' }}

    <div class="w-full px-3 mt-12">
        <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="name">
            {{ trans('general.Username')  }}
        </label>
        <input value="{{ old('name', $user?->name) }}" class="form_input" type="text" name="name" placeholder="{{ trans('general.Username')  }}" required />
    </div>

    <div class="w-full px-3 mt-12">
        <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="email">
            {{ trans('general.E-mail')  }}
        </label>
        <input value="{{ old('email', $user?->email) }}" class="form_input" type="email" name="email" placeholder="{{ trans('general.E-mail')  }}" required />
    </div>

    @if($formType == 'create')
    <div class="w-full px-3 mt-12">
        <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="password">
            {{ trans('general.Password')  }}
        </label>
        <input class="form_input" type="password" name="password" placeholder="{{ trans('general.Password')  }}" required />
    </div>

    <div class="w-full px-3 mt-12">
        <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="password_confirmation">
            {{ trans('general.Password confirmation')  }}
        </label>
        <input class="form_input" type="password" name="password_confirmation" placeholder="{{ trans('general.Password confirmation')  }}" required />
    </div>
    @endif

    <div class="w-full px-3 mt-12">
        <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="active">
            {{ trans('users.Is writer')  }}
        </label>
        <input disabled {{ $formType == 'create' ? 'checked' : (old('is_writer', $user?->is_writer) == 1 ? 'checked' : '') }} type="checkbox" name="is_writer" value="1" />
    </div>
    <div class="w-full px-3 mt-12">
        <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="active">
            {{ trans('users.Is editor')  }}
        </label>
        <input {{ old('is_editor', $user?->is_editor) == 1 ? 'checked' : '' }} type="checkbox" name="is_editor" value="1"/>
    </div>
    <div class="w-full px-3 mt-12">
        <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="active">
            {{ trans('users.Is admin')  }}
        </label>
        <input {{ old('is_admin', $user?->is_admin) == 1 ? 'checked' : '' }} type="checkbox" name="is_admin" value="1"/>
    </div>

    @if (Auth::user()->is_root())
    <div class="w-full px-3 mt-12">
        <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="active">
            {{ trans('users.Is root')  }}
        </label>
        <input {{ old('is_root', $user?->is_root) == 1 ? 'checked' : '' }} type="checkbox" name="is_root" value="1"/>
    </div>
    @endif

    <div class="w-full px-3 mt-12">
        <button class="btn_save" type="submit">{{ trans('general.save')  }}</button>
    </div>



</form>

</div>

</x-layouts.panel_layout>
