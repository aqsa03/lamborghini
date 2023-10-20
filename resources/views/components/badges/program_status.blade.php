@props(['status'])

@if ($status === App\Enums\ProgramStatus::DRAFT->value)
<div class="badge__program_draft">
@elseif ($status === App\Enums\ProgramStatus::PUBLISHED->value)
<div class="badge__program_published">
@else
<div class="badge__program_defaultstatus">
@endif
    {{ trans("general.".strtolower($slot)) }}
</div>
