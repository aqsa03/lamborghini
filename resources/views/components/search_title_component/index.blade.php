@props(['entityType', 'selectedEntityId', 'selectedEntityTitle'])

<div x-data="searchByTitleData_{{ $entityType }}('{{ $entityType }}', '{{ $selectedEntityId ?? "" }}', '{{ $selectedEntityTitle ?? "" }}')">
    <input type="hidden" name="{{ Str::singular($entityType) }}_id" x-model="selectedEntityId" />
    <template x-if="error">
        <div class="p-4 mb-4 text-sm text-red-700 bg-red-100 dark:bg-red-200 dark:text-red-800" role="alert" x-text="error"></div>
    </template>
    <template x-if="error === false">
        <div>
            <template x-if="selectedEntityTitle.length > 0">
                <div>
                    {{ trans("search.Selected entity") }}: <span x-text="selectedEntityTitle"></span>
                </div>
            </template>
            <input class="form_input" placeholder="{{ trans("search.Search by title") }}" type="text" x-model="title" @input.debounce.250ms="search">
            <div x-cloak x-show="isLoading">
                <x-loading-spinner></x-loading-spinner>
            </div>
            <div x-cloak x-show="results.length > 0" class="search_results p-4 border overflow-auto" style="max-height: 150px">
            <template x-for="result in results">
                <div class="py-2 border-b">
                    <a :href="`#${result.id}`" :data-id="result.id" :data-title="result.title" x-text="result.title" x-on:click.prevent="selectEntity"></a>
                </div>
            </template>
            </div>
        </div>
    </template>
</div>
<script>
const searchByTitleData_{{ $entityType }} = (entityType, selectedEntityId, selectedEntityTitle) => {
    if (!['programs', 'seasons', 'episodes', 'lives', 'news'].includes(entityType)) return { error: "Search widget: invalid entity type" };
    return {
        error: false,
        entityType: "",
        title: "",
        results: [],
        selectedEntityId: null,
        selectedEntityTitle: "",
        isLoading: false,
        init() {
            this.entityType = entityType;
            this.selectedEntityId = selectedEntityId;
            this.selectedEntityTitle = selectedEntityTitle;
        },
        search() {
            this.isLoading = true;
            fetch(`${BASE_URL}/admin/${this.entityType}/search_by_title?title=${this.title}`)
                .then(res => res.json())
                .then(results => {
                    this.results = results;
                    this.isLoading = false;
                })
                .catch(err => {
                    this.isLoading = false;
                    this.error = "An error occured while loading data. Reload the page. If the error persists please contact the techical support."
                });
        },
        selectEntity(ev) {
            this.selectedEntityId = ev.target.dataset.id;
            this.selectedEntityTitle = ev.target.dataset.title;
            this.results = [];
        }
    }
}
</script>
