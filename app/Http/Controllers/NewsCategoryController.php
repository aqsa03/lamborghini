<?php

namespace App\Http\Controllers;

use App\Models\NewsCategory;
use Illuminate\Http\Request;

class NewsCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $newsCategories = NewsCategory::latest();
        return view('newsCategory.index',[
                'total' => $newsCategories->count(),
                'newsCategories' => $newsCategories->paginate(20)
            ])
            ->with('i', (request()->input('page', 1) - 1) * 20);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('newsCategory.form', [
            'formType' => 'create',
            'newsCategory' => null
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|unique:news_categories|max:255',
        ]);

        NewsCategory::create([
            'title' => $request->title
        ]);

        return redirect()->route('newsCategories.index')->with('success','NewsCategory created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\NewsCategory  $newsCategory
     * @return \Illuminate\Http\Response
     */
    public function show(NewsCategory $newsCategory)
    {
        return view('newsCategory.show', [
            'newsCategory' => $newsCategory
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\NewsCategory  $newsCategory
     * @return \Illuminate\Http\Response
     */
    public function edit(NewsCategory $newsCategory)
    {
        return view('newsCategory.form', [
            'newsCategory' => $newsCategory,
            'formType' => 'edit'
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\NewsCategory  $newsCategory
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, NewsCategory $newsCategory)
    {
        $request->validate([
            'title' => 'required|max:255|unique:news_categories,title,'.$newsCategory->id,
        ]);

        $newsCategory->update([
            'title' => $request->title
        ]);

        return redirect()->route('newsCategories.index')->with('success','NewsCategory updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\NewsCategory  $newsCategory
     * @return \Illuminate\Http\Response
     */
    public function destroy(NewsCategory $newsCategory)
    {

        try{
            $newsCategory->delete();
            return redirect()->route('newsCategories.index')
                        ->with('success','NewsCategory deleted successfully');
        } catch(\Exception $e) {
            $error_message = $e->getMessage();
            if(substr($error_message, 0, 15) == 'SQLSTATE[23000]'){
                preg_match('/`'.config('database.connections.'.config('database.default').'.database').'`.`(.*)`, CONSTRAINT/', $error_message, $matches);
                $error_message = isset($matches[1]) ? 'Unable to delete newsCategory with associated '.$matches[1] : 'Unable to delete newsCategory with associated entities' ;
            }
            return redirect()->route('newsCategories.index')
                        ->with('error', $error_message);
        }
    }
}
