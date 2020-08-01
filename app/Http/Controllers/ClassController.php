<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\College;
use App\Level;
use Illuminate\Support\Facades\Storage;
use Str;
use App\ClassManager;

class ClassController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        
    }

    /**
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        
        $params = $request->all();
        $classes = $user->classes()->with('college:id,name','levels:class_manager_id,title')->filter($params)->paginate(5);
        
        foreach($classes as $key => $class){
            $classes[$key]->levels = collect($class->levels)->implode('title',', ');
            $classes[$key]->college = $class->college->name;
        }
        
        $data = [
            'classes' => $classes
        ];
        
        return view('classes',$data);
    }
    
    public function create(){
        
        $colleges = College::all();
        
        $data = [
            'colleges' => $colleges
        ];
        
        return view('classes-add',$data);
    }
    
    public function store(Request $request){
        
        $user = auth()->user();
        
        $filesize = (8 * 1024);
        
        $rules = [
            'college_id' => 'required',
            'title' => 'required',
            'contact_no' => 'required',
            'email' => 'required|email',
            'price' => 'required',
            'levels' => 'required|array',
            'description' => 'required',
            'syllabus' => "required|mimes:pdf,doc,docx|max:{$filesize}",
        ];

        $validationErrorMessages = [];

        $validator = Validator::make($request->all(), $rules, $validationErrorMessages);

        if ($validator->fails()) {
            return redirect(route('classes.create'))->with('errors', $validator->messages())->withInput();
        }
        
        $doc_name = $request->syllabus;
        $original_name = $doc_name->getClientOriginalName();
        $original_name_without_ext = pathinfo($original_name, PATHINFO_FILENAME);
        $ext = $doc_name->getClientOriginalExtension();   
        $filename = Str::random(10).'.'.$ext;
        $doc_upload_path = "documents/";
        
        Storage::disk('local')->putFileAs(
            $doc_upload_path,
            $doc_name,
            $filename
        );
        
        $data = $request->except(['_token','levels','syllabus']);
        $data['syllabus'] = $filename;
        
        $class = $user->classes()->create($data);
        
        $field_levels = $request->get('levels');
        $field_levels = array_filter($field_levels);
        if(!empty($field_levels)){
            $levels = array();
            foreach($field_levels as $level){
                $levels[] = new Level(['title' => $level]);
            }
            
            $class->levels()->saveMany($levels);
        }
        
        return redirect(route('home'))->with('success', "Class added successfully.");
    }
    
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $user = auth()->user();
        $class = $user->classes()->with('college','levels')->where('id',$id)->first();
        
        $class->levels = collect($class->levels)->toArray();
        $class->college = collect($class->college)->toArray();
        
        if(empty($class))
        {
            return redirect(route('classes'))->with('error', "Class not found.");
        }

        $colleges = College::all();
        
        $data = [
            'id' => $id,
            'class' => $class,
            'colleges' => $colleges
        ];

        return view('classes-edit')->with($data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $user = auth()->user();
        $class = $user->classes()->where('id',$id)->first();
        
        $filesize = (8 * 1024);
        
        $rules = [
            'college_id' => 'required',
            'title' => 'required',
            'contact_no' => 'required',
            'email' => 'required|email',
            'price' => 'required',
            'levels' => 'required|array',
            'description' => 'required',
            'syllabus' => "mimes:pdf,doc,docx|max:{$filesize}",
        ];

        $validationErrorMessages = [];

        $validator = Validator::make($request->all(), $rules, $validationErrorMessages);

        if ($validator->fails()) {
            return redirect(route('classes.edit'))->with('errors', $validator->messages())->withInput();
        }
        
        $data = $request->except(['_token','levels','syllabus']);
        
        if($request->has('syllabus')){
            $doc_name = $request->syllabus;
            $original_name = $doc_name->getClientOriginalName();
            $original_name_without_ext = pathinfo($original_name, PATHINFO_FILENAME);
            $ext = $doc_name->getClientOriginalExtension();   
            $filename = Str::random(10).'.'.$ext;
            $doc_upload_path = "documents/";

            Storage::disk('local')->putFileAs(
                $doc_upload_path,
                $doc_name,
                $filename
            );
            
            $existing_file_path = $doc_upload_path.$class->syllabus;
            Storage::disk('local')->delete($existing_file_path);

            $data['syllabus'] = $filename;
        }
        
        $class->update($data);
        
        $field_levels = $request->get('levels');
        $field_levels = array_filter($field_levels);
        if(!empty($field_levels)){
            $class->levels()->delete();
            
            $levels = array();
            foreach($field_levels as $level){
                $levels[] = new Level(['title' => $level]);
            }
            
            $class->levels()->saveMany($levels);
        }
        
        return redirect(route('home'))->with('success', "Class updated successfully.");
    }
    
    /**
    * Remove the specified resource from storage.
    *
    * @param  int  $id
    * @return \Illuminate\Http\Response
    */
    public function destroy($id)
    {
        if(ClassManager::deleteById($id)){
            return redirect(route('home'))->with("success","Class deleted successfully.");
        }
        else{
            return redirect(route('home'))->with("error","Failed to delete classes.");
        }
    }
    
    public function downloadDocument(Request $request, $id){
        
        $class = ClassManager::where('id',$id)->first();
        
        if(empty($class))
        {
            return redirect(route('classes'))->with('error', "Class not found.");
        }
        
        $doc_path = "documents/".DIRECTORY_SEPARATOR.$class->syllabus;
        
//        if(!$this->downloadFile($doc_path)){
//            return redirect(route('home'))->with('error', "File not found.");
//        }
        
        return $this->downloadFile($doc_path);
    }
    
    public function downloadFile($source_file_path, $custom_filename = NULL){ 
        if(!empty($source_file_path) && !empty(Storage::disk('local')->exists($source_file_path))){
            
            $fileinfo = pathinfo($source_file_path);
            $mtype = Storage::disk('local')->mimeType($source_file_path);
            $fsize = Storage::disk('local')->size($source_file_path);
            $name = $fileinfo['filename'];
            $extension = $fileinfo['extension'];
            
            if(!empty($custom_filename)){
                $name = pathinfo($custom_filename,PATHINFO_FILENAME);
            }
            
            $filename = str_slug($name).".{$extension}";

            $headers = array('Content-Type' => $mtype);
            
            return Storage::disk('local')->download($source_file_path, $filename, $headers);
        }
        
        return redirect(route('home'))->with('error', "File not found.");
    }
}
