<x-layouts.panel_layout>

<div class="body_section">

    <x-form_errors :errors="$errors"></x-form_errors>
    <form class="default_form" action="{{ route('setting.update', $setting->id) }}" method="POST">
        @csrf
        {{ method_field('PUT') }}

        <div class="w-full px-3 mt-12">
            <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="today_num_posts">
                today_num_posts
            </label>
            <input value="{{ $setting->today_num_posts }}" class="form_input" type="number" name="today_num_posts" required />
        </div>

        <div class="w-full px-3 mt-12">
            <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="yesterday_num_posts">
                yesterday_num_posts
            </label>
            <input value="{{ $setting->yesterday_num_posts }}" class="form_input" type="number" name="yesterday_num_posts" required />
        </div>

        @if (Auth::user()->is_root())
        <div class="w-full px-3 mt-12">
            <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="minimum_supported_version">
                minimum_supported_version
            </label>
            <input value="{{ $setting->minimum_supported_version }}" class="form_input" type="text" name="minimum_supported_version" required />
        </div>

        <div class="w-full px-3 mt-12">
            <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="user_custom_claim_to_view">
            user_custom_claim_to_view
            </label>
            <input value="{{ $setting->user_custom_claim_to_view }}" class="form_input" type="text" name="user_custom_claim_to_view" required />
        </div>

        <div class="w-full px-3 mt-12">
            <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="user_roles_allowed_to_view">
                user_roles_allowed_to_view
            </label>
            <input value="{{ $setting->user_roles_allowed_to_view }}" class="form_input" type="text" name="user_roles_allowed_to_view" />
        </div>

        <div class="w-full px-3 mt-12">
            <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="user_custom_claim_to_vote">
            user_custom_claim_to_vote
            </label>
            <input value="{{ $setting->user_custom_claim_to_vote }}" class="form_input" type="text" name="user_custom_claim_to_vote" required />
        </div>

        <div class="w-full px-3 mt-12">
            <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="user_roles_allowed_to_vote">
                user_roles_allowed_to_vote
            </label>
            <input value="{{ $setting->user_roles_allowed_to_vote }}" class="form_input" type="text" name="user_roles_allowed_to_vote" />
        </div>
        @endif

        <br><br>

        <div class="w-full px-3 mt-12">
            <button class="btn_save" type="submit">{{ trans("general.save") }}</button>
        </div>
    </form>

</div>

</x-layouts.panel_layout>
