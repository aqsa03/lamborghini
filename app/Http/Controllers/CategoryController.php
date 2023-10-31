<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Models\Image;
use App\Http\Requests\StoreCategoryRequest;
use Illuminate\Support\Facades\Log;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $categories = Category::latest()
        ->leftJoin('categories as parent', 'categories.parent_id', '=', 'parent.id')
        ->select('categories.*', 'parent.title as parent_id')
        ->paginate(20);
    
    return view('category.index', [
        'total' => $categories->total(),
        'categories' => $categories
    ])->with('i', (request()->input('page', 1) - 1) * 20);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $categories = Category::latest()->whereNull('parent_id')->get();
        return view('category.form', [
            'formType' => 'create',
            'category' => null,
            'categories'=>$categories,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreCategoryRequest $request)
    {
        $validatedFields=$request->validated();
        if($image = Image::createAndStoreFromRequest($request, 'image', 'category')){
            $validatedFields['image_id'] = $image->id;
        }
        $validatedFields['parent_id']=$request->parent_id==='null'?null:$request->parent_id;
        Log::info('Creating Category with data',$validatedFields);
        Category::create($validatedFields);
        Log::info('Category created successfully');

        return redirect()->route('categories.index')->with('success','Category created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function show(Category $category)
    {
        return view('category.show', [
            'category' => $category
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function edit(Category $category)
    {
        $categories = Category::latest()->whereNull('parent_id')->get();
        return view('category.form', [
            'category' => $category,
            'formType' => 'edit',
            'categories'=>$categories,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Category $category)
    {
        $request->validate([
            'title' => 'required|max:255|unique:categories,title,'.$category->id,
        ]);
        $validatedFields=[];
        $validatedFields['title']=$request->title;
        $validatedFields['description']=$request->description;
        $validatedFields['parent_id']=$request->parent_id==='null'?null:$request->parent_id;

        if($image = Image::createAndStoreFromRequest($request, 'image', 'category')){
            $validatedFields['image_id'] = $image->id;
        }
        Log::info('Category data to update',$validatedFields);
        $category->update($validatedFields);
        Log::info('Category updated successfully');

        return redirect()->route('categories.index')->with('success','Category updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function destroy(Category $category)
    {

        try{
            $category->delete();
            return redirect()->route('categories.index')
                        ->with('success','Category deleted successfully');
        } catch(\Exception $e) {
            $error_message = $e->getMessage();
            if(substr($error_message, 0, 15) == 'SQLSTATE[23000]'){
                preg_match('/`'.config('database.connections.'.config('database.default').'.database').'`.`(.*)`, CONSTRAINT/', $error_message, $matches);
                $error_message = isset($matches[1]) ? 'Unable to delete category with associated '.$matches[1] : 'Unable to delete category with associated entities' ;
            }
            return redirect()->route('categories.index')
                        ->with('error', $error_message);
        }
    }
}
