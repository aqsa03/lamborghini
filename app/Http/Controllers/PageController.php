<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePageRequest;
use App\Models\CarModel;
use App\Models\Category;
use App\Models\Episode;
use App\Models\Page;
use App\Models\PageSection;
use App\Models\Program;
use App\Models\Season;
use App\Models\ModelVideo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PageController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $pages = Page::all();
        return view('page.index', [
            'pages' => $pages
        ]);
    }
    public function create()
    {
        return view('page.create', [
            'formType' => 'create',
            'page' => null
        ]);
    }
    public function store(StorePageRequest $request)
    {
        $validatedFields = $request->validated();
        Log::info('Creating Page with data', $validatedFields);
        Page::create($validatedFields);
        Log::info('Page created successfully');

        return redirect()->route('pages.index')->with('success', 'page created successfully.');
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Page  $page
     * @return \Illuminate\Http\Response
     */
    public function edit(Page $page)
    {

        $sections = PageSection::where('page_id', '=', $page->id)->orderBy('order_number')->get();
        $editor_data = [
            'page_id' => $page->id,
            'sections' => session()->getOldInput('sections') ? session()->getOldInput('sections') : []
        ];
        if (!session()->getOldInput('sections')) {
            foreach ($sections as $s) {
                if ($s->type == 'main') {
                    $obj = $s->list[0]->collection == 'categories' ? Category::find($s->list[0]->collection_id) : ($s->list[0]->collection == 'models' ?  CarModel::find($s->list[0]->collection_id) : ($s->list[0]->collection == 'videos' ?  ModelVideo::find($s->list[0]->collection_id) : null));
                    array_push($editor_data['sections'], [
                        'label' => $s->title,
                        'type' => $s->type,
                        //'collection' => substr($s->list[0]->collection, 0, strlen($s->list[0]->collection) - 1),
                        'collection' => $s->list[0]->collection,
                        'ref' => $s->list[0]->collection_id,
                        'search_string' => $obj?->title
                    ]);
                }
                if ($s->type == 'rule') {
                    array_push($editor_data['sections'], [
                        'label' => $s->title,
                        'type' => $s->type,
                        //'collection' => substr($s->rule->collection, 0, strlen($s->rule->collection) - 1),
                        'collection' => $s->rule->collection,
                        'limit' => $s->rule->limit,
                        'order_by' => $s->rule->order->field,
                        'asc_desc' => $s->rule->order->asc_desc,
                        'rules' => array_map(function ($where) {
                            return [
                                "field_1" => $where->field,
                                "operator" => $where->operator,
                                "field_value" => $where->value
                            ];
                        }, $s->rule->where)
                    ]);
                }
                if ($s->type == 'custom') {
                    array_push($editor_data['sections'], [
                        'label' => $s->title,
                        'type' => $s->type,
                        'list' => array_map(function ($item) {
                            $obj = $item->collection == 'categories' ? Category::find($item->collection_id) : ($item->collection == 'models' ?  CarModel::find($item->collection_id) : ($item->collection == 'videos' ?  ModelVideo::find($item->collection_id) : null));
                            return [
                                //'collection' => substr($item->collection, 0, strlen($item->collection) - 1),
                                'collection' => $item->collection,
                                'ref' => $item->collection_id,
                                'search_string' => $obj?->title,
                                'image_poster' => [[
                                    'url' => $obj->imagePoster->url ?? ""
                                ]]
                            ];
                        }, $s->list)
                    ]);
                }
            }
        }
        return view('page.form', $editor_data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Page  $page
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Page $page)
    {
        for ($i = 0; $i < 20; $i++) {
            if (isset($request['jsonResponse' . $i])) {
                $section = json_decode($request['jsonResponse' . $i], true);
                $section = isset($section['type']) ? $section : $section[0];
                if ($section['type'] == 'main') {
                    foreach ($section['list'] as $k => $item) {
                        $obj = $item['collection'] == 'categories' ? Category::find($item['ref']) : ($item['collection'] == 'models' ?  CarModel::find($item['ref']) : ($item['collection'] == 'videos' ?  ModelVideo::find($item['ref']) : null));
                        if ($obj === null or !$obj->id) {
                            return back()->withInput(['sections' => PageController::getSectionsFromPostRequest($request)])->withErrors('Oggetto per sezione custom non valido. Numero sezione: ' . ($i + 1) . ' - Numero item: ' . $k);
                        }
                    }
                }
                if ($section['type'] == 'custom') {
                    foreach ($section['list'] as $k => $item) {
                        $obj = $item['collection'] == 'categories' ? Category::find($item['ref']) : ($item['collection'] == 'models' ?  CarModel::find($item['ref']) : ($item['collection'] == 'videos' ?  ModelVideo::find($item['ref']) : null));
                        if ($obj === null or !$obj->id) {
                            return back()->withInput(['sections' => PageController::getSectionsFromPostRequest($request)])->withErrors('Oggetto per sezione custom non valido. Numero sezione: ' . ($i + 1) . ' - Numero item: ' . $k);
                        }
                    }
                }
                if ($section['type'] == 'rule') {
                    if ($section['limit'] < 0) {
                        return back()->withInput(['sections' => PageController::getSectionsFromPostRequest($request)])->withErrors('Limit non valido per sezione ' . $section['type'] . '. Numero sezione: ' . $i + 1);
                    }
                    if (isset($section['rules'])) {
                        foreach ($section['rules'] as $where) {
                            if ($where['field_1'] == 'category_id') {
                                $obj = Category::find($where['field_value']);
                                if ($obj === null or !$obj->id) {
                                    return back()->withInput(['sections' => PageController::getSectionsFromPostRequest($request)])->withErrors('Condizione rule non valida per sezione ' . $section['type'] . '. Numero sezione: ' . ($i + 1) . '. Categoria con id ' . $where['field_value'] . ' non trovata');
                                }
                                if ($section['collection'] != 'programs') {
                                    return back()->withInput(['sections' => PageController::getSectionsFromPostRequest($request)])->withErrors('Condizione rule non valida per sezione ' . $section['type'] . '. Numero sezione: ' . ($i + 1) . '. Condizione con "Category ID" specificabile solo per collection "Program"');
                                }
                            }
                            if ($where['field_1'] == 'model_id') {
                                $obj = CarModel::find($where['field_value']);
                                if ($obj === null or !$obj->id) {
                                    return back()->withInput(['sections' => PageController::getSectionsFromPostRequest($request)])->withErrors('Condizione rule non valida per sezione ' . $section['type'] . '. Numero sezione: ' . ($i + 1) . '. Programma con id ' . $where['field_value'] . ' non trovato');
                                }
                                if ($section['collection'] != 'seasons') {
                                    return back()->withInput(['sections' => PageController::getSectionsFromPostRequest($request)])->withErrors('Condizione rule non valida per sezione ' . $section['type'] . '. Numero sezione: ' . ($i + 1) . '. Condizione con "Program ID" specificabile solo per collection "Season"');
                                }
                            }
                            if ($where['field_1'] == 'video_id') {
                                $obj = ModelVideo::find($where['field_value']);
                                if ($obj === null or !$obj->id) {
                                    return back()->withInput(['sections' => PageController::getSectionsFromPostRequest($request)])->withErrors('Condizione rule non valida per sezione ' . $section['type'] . '. Numero sezione: ' . ($i + 1) . '. Stagione con id ' . $where['field_value'] . ' non trovata');
                                }
                                if ($section['collection'] != 'episodes') {
                                    return back()->withInput(['sections' => PageController::getSectionsFromPostRequest($request)])->withErrors('Condizione rule non valida per sezione ' . $section['type'] . '. Numero sezione: ' . ($i + 1) . '. Condizione con "Season ID" specificabile solo per collection "Episode"');
                                }
                            }
                            if ($where['field_1'] == 'podcast') {
                                if (!in_array($where['field_value'], ['true', 'false', true, false])) {
                                    return back()->withInput(['sections' => PageController::getSectionsFromPostRequest($request)])->withErrors('Condizione rule non valida per sezione ' . $section['type'] . '. Numero sezione: ' . ($i + 1) . '. Valore per podcast non valido. Valori possibili "true" o "false"');
                                }
                                if ($section['collection'] != 'programs') {
                                    return back()->withInput(['sections' => PageController::getSectionsFromPostRequest($request)])->withErrors('Condizione rule non valida per sezione ' . $section['type'] . '. Numero sezione: ' . ($i + 1) . '. Condizione con "podcast" specificabile solo per collection "Program"');
                                }
                            }
                        }
                    }
                }
            }
        }

        //TODO bug fix: i null diventano stringhe "null" !!!!
        $last_index = 1;
        for ($i = 0; $i < 20; $i++) {
            if (isset($request['jsonResponse' . $i])) {
                $section = json_decode($request['jsonResponse' . $i], true);
                $section = isset($section['type']) ? $section : $section[0];
                if ($section['type'] == 'main') {
                    $s = PageSection::updateOrCreate(
                        ['page_id' => $page->id, 'order_number' => $i + 1],
                        [
                            'type' => $section['type'],
                            'list' => [['collection' => $section['collection'], 'collection_id' => intval($section['ref'])]],
                            'list' => array_map(function ($item) {
                                return [
                                    'collection' => $item['collection'],
                                    'collection_id' => intval($item['ref'])
                                ];
                            }, $section['list']),
                            'title' => $section['label'],
                            'search_string' => $section['search_string'],
                            'rule' => null,

                        ]
                    );
                }
                if ($section['type'] == 'custom') {
                    $s = PageSection::updateOrCreate(
                        ['page_id' => $page->id, 'order_number' => $i + 1],
                        [
                            'type' => $section['type'],
                            'list' => array_map(function ($item) {
                                return [
                                    'collection' => $item['collection'],
                                    'collection_id' => intval($item['ref'])
                                ];
                            }, $section['list']),
                            'title' => $section['label'],
                            'rule' => null
                        ]
                    );
                }
                if ($section['type'] == 'rule') {
                    $s = PageSection::updateOrCreate(
                        ['page_id' => $page->id, 'order_number' => $i + 1],
                        [
                            'type' => $section['type'],
                            'list' => null,
                            'title' => $section['label'],
                            'rule' => [
                                'collection' => $section['collection'],
                                'limit' => intval($section['limit']),
                                'order' => [
                                    'field' => $section['order_by'],
                                    'asc_desc' => $section['asc_desc']
                                ],
                                'where' => array_map(function ($item) {
                                    $value = $item['field_value'];
                                    if ($value === 'true') {
                                        $value = true;
                                    }
                                    if ($value === 'false') {
                                        $value = false;
                                    }
                                    if (is_numeric($value)) {
                                        $value = intval($value);
                                    }
                                    return [
                                        'field' => $item['field_1'],
                                        'operator' => $item['operator'],
                                        'value' => $value,
                                    ];
                                }, $section['rules'] ?? [])
                            ]
                        ]
                    );
                }
            } else {
                $last_index = $i + 1;
                break;
            }
        }
        foreach (PageSection::where('page_id', '=', $page->id)->where('order_number', '>=', $last_index)->get() as $del) {
            $del->delete();
        }
        return redirect()->route('pages.index')->with('success', 'Page updated successfully.');
    }
    public function destroy(Request $request)
    {
        try {
            $page = $request->input('page');
            $section = PageSection::where("title", '=', $page)->first();
            Log::info("inside delete method",  ['id' => $section->id]);
            $section->delete();
            return response()->json(['success' => true, 'message' => 'Section deleted successfully']);
        } catch (\Exception $e) {
            $error_message = $e->getMessage();
            return redirect()->route('pages.index')
                ->with('error', $error_message);
        }
    }

    public static function getSectionsFromPostRequest($post)
    {
        $sections = [];
        for ($i = 0; $i < 20; $i++) {
            if (isset($post['jsonResponse' . $i])) {
                $section = json_decode($post['jsonResponse' . $i], true);
                $section = isset($section['type']) ? $section : $section[0];
                array_push($sections, $section);
            }
        }
        return $sections;
    }
}
