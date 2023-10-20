<x-layouts.panel_layout>
Benvenuto!
<form action="/logout" method="POST">
    @csrf
    <button class="bg-red-600 rounded text-white p-6" type="submit">Logout</button>
</form>
</x-layouts.panel_layout>
