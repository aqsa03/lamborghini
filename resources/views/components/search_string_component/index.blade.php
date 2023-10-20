@props(['entityType', 'selectedEntityId', 'selectedEntityString', 'disabledInputHidden'])

<div x-data="searchByTitleData_{{ $entityType }}('{{ $entityType }}', '{{ $selectedEntityId ?? "" }}', '{{ $selectedEntityString ?? "" }}')">
    <input type="hidden" name="{{ $entityType == 'lives' ? 'live_id' : Str::singular($entityType).'_id' }}" x-model="selectedEntityId" x-bind:disabled="{{ $disabledInputHidden }}" />
    <template x-if="error">
        <div class="p-4 mb-4 text-sm text-red-700 bg-red-100 dark:bg-red-200 dark:text-red-800" role="alert" x-text="error"></div>
    </template>
    <template x-if="error === false">
        <div>
            <template x-if="selectedEntityString.length > 0">
                <div>
                    {{ trans("search.Selected entity") }}: <span x-text="selectedEntityString"></span>
                </div>
            </template>
            <input class="form_input" placeholder="{{ trans("search.Search by string") }}" type="text" x-model="string" @input.debounce.250ms="search">
            <div x-cloak x-show="isLoading">
                <x-loading-spinner></x-loading-spinner>
            </div>
            <div x-cloak x-show="results.length > 0" class="search_results p-4 border overflow-auto" style="max-height: 150px">
            <template x-for="result in results">
                <div class="py-2 border-b">
                    <a :href="`#${result.id}`" :data-id="result.id" :data-string="result.search_string" x-text="result.search_string" x-on:click.prevent="selectEntity"></a>
                </div>
            </template>
            </div>
        </div>
    </template>
</div>
<script>
const searchByTitleData_{{ $entityType }} = (entityType, selectedEntityId, selectedEntityString) => {
    if (!['programs', 'seasons', 'episodes', 'lives', 'news'].includes(entityType)) return { error: "Search widget: invalid entity type" };
    return {
        error: false,
        entityType: "",
        string: "",
        results: [],
        selectedEntityId: null,
        selectedEntityString: "",
        isLoading: false,
        init() {
            this.entityType = entityType;
            this.selectedEntityId = selectedEntityId;
            this.selectedEntityString = selectedEntityString;
        },
        search() {
            this.isLoading = true;
            fetch(`${BASE_URL}/admin/${this.entityType}/search_by_string?string=${this.string}`)
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
            this.selectedEntityString = ev.target.dataset.string;
            this.results = [];
        }
    }
}
</script>
