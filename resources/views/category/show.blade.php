<x-layouts.panel_layout>

<div class="body_section">

    <a href="{{ route('categories.index') }}" title="Category list">&lsaquo; Back to all categories</a>

    <div class="pr-4 flex gap-4 items-end place-content-end relative w-full">

        <a href="{{ route("categories.edit", $category->id) }}" class="btn_new_entity text-center inline-flex items-center" >Modifica</a>

        <form action="{{ route('categories.destroy', $category->id) }}" method="POST">
            @csrf
            {{ method_field('DELETE') }}
            <button data-delete-message="Stai per cancellare la categoria {{ $category->title }}. Sei sicuro?" class="delete-link delete_btn" type="submit">Elimina</button>
        </form>
    </div>

    <div class="w-full mt-12">
        <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">
            Titolo
        </label>
        <div>
            {{ $category->title }}
        </div>
    </div>
</div>

</x-layouts.panel_layout>
