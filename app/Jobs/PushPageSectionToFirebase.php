<?php

namespace App\Jobs;

use App\Enums\PageSectionType;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\PageSection;

class PushPageSectionToFirebase implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 3;

    /**
     * Delete the job if its models no longer exist.
     *
     * @var bool
     */
    public $deleteWhenMissingModels = true;

    /**
     * The pageSection instance.
     *
     * @var \App\Models\PageSection
     */
    public $pageSection;

    /**
     * Create a new job instance.
     *
     * @param  App\Models\PageSection  $pageSection
     * @return void
     */
    public function __construct(PageSection $pageSection)
    {
        $this->pageSection = $pageSection;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $firestore = app('firebase.firestore');
        $db = $firestore->database();

        if (!$this->pageSection->page_id) {
            throw new \Exception('unable to push section without page id to firebase');
        }

        $data = [
            'title' => $this->pageSection->title,
            'order_number' => $this->pageSection->order_number,
            'type' => $this->pageSection->type,
            // 'page' => $this->pageSection->page_id ? $db->collection('pages')->document($this->pageSection->page->id) : null,
            // 'page_id' => $this->pageSection->page_id ? $this->pageSection->page->id : null,
        ];
        if ($this->pageSection->type == PageSectionType::MAIN->value) {
            $data['list'] = array_map(function ($entity) use ($db) {
                $entity->collection_ref = $db->collection($entity->collection)->document($entity->collection_id);
                return $entity;
            }, $this->pageSection->list);
            // $data['collection'] = $this->pageSection->list[0]->collection;
            // $data['collection_id'] = $this->pageSection->list[0]->collection_id;
            // $data['collection_ref'] = $db->collection($this->pageSection->list[0]->collection)->document($this->pageSection->list[0]->collection_id);
        }
        if ($this->pageSection->type == PageSectionType::CUSTOM->value) {
            $data['list'] = array_map(function ($entity) use ($db) {
                $entity->collection_ref = $db->collection($entity->collection)->document($entity->collection_id);
                return $entity;
            }, $this->pageSection->list);
        }
        if (
            $this->pageSection->type == PageSectionType::RULE->value
        ) {
            $data['rule'] = $this->pageSection->rule;
        }

        $db->collection('pages')->document($this->pageSection->page_id)->collection('sections')->document($this->pageSection->id)->set($data);
    }
}
