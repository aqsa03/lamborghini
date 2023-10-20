@props(['errors'])
@if ($errors->any())
<x-alerts.error>
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
</x-alerts.error>
@endif
