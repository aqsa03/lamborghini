<x-layouts.panel_layout>

<div class="body_section" x-data="notificationFormData()">


    <x-form_errors :errors="$errors"></x-form_errors>
    <form class="default_form" action="{{ $formType == 'create' ? route('notifications.store') : route('notifications.update', $notification->id) }}" method="POST">
        @csrf
        {{ $formType == 'edit' ? method_field('PUT') : '' }}

        <input type="hidden" name="status" value="READY" />
        <div class="w-full md:w-1/2 px-3 mt-12">
            <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="topic">
                {{ trans('notifications.topic') }}
            </label>
            <div class="inline-block relative w-64">
                <select name="topic" class="form_select">
                    @foreach ($topics as $topic)
                    <option {{ strtolower(old("topic", $notification?->topic)) == strtolower($topic) ? 'selected' : '' }} value="{{ $topic }}">{{ $topic }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <x-selector.all :entity="$notification"></x-selector.all>

        <div class="w-full px-3 mt-12">
            <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="title">
                {{ trans("notifications.Notification title") }}
            </label>
            <input value="{{ old('title', $notification?->title) }}" class="form_input" type="text" name="title" placeholder="{{ trans("notifications.Notification title") }}" required />
        </div>

        <div class="w-full px-3 mt-12">
            <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="message">
                {{ trans("notifications.Notification message") }}
            </label>
            <textarea name="message" class="form_select" rows="5">{{ old('message', $notification?->message) }}</textarea>
        </div>

        <div class="w-full px-3 mt-12">
            <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="scheduled_at">
                {{ trans("general.scheduled at") }}
            </label>
            <input class="form_input" type="datetime-local" name="scheduled_at" placeholder="{{ trans("general.scheduled at") }}" required value="{{ old("scheduled_at", $notification?->scheduled_at ?? now()) }}" />
        </div>


        <br><br>

        <div class="w-full px-3 mt-12">
            <button class="btn_save" type="submit">{{ trans("general.save") }}</button>
        </div>
    </form>

</div>

<script>
    const notificationFormData = () => {
        return {
            selectedType: null,
            init() {
                this.selectedType = document.querySelector("select[name='type']").value;
            }
        }
    }
</script>

</x-layouts.panel_layout>
