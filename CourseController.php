<?php

namespace App\Http\Controllers\Creator;

use App\Enums\CourseReviewType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Creator\Course\CourseRequest;
use App\Models\Course\Course;
use App\Models\Course\CourseAble;
use App\Models\Course\CourseCategory;
use App\Models\Course\CourseInclude;
use App\Models\Course\CourseLearn;
use App\Models\Course\CourseLesson;
use App\Models\Course\CourseLessonFile;
use App\Models\Course\CourseLessonTask;
use App\Models\Course\CourseProgram;
use App\Services\Helpers\AjaxResponseNotification;
use Illuminate\Http\Request;
use Plank\Mediable\Media;
use Plank\Mediable\MediaUploader;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\Creator\CourseResource;


class CourseController extends Controller
{
    public function __construct()
    {
        //todo service, repositories
    }

    public function index()
    {
        //todo repository
        return view('creator.course.index');
    }

    public function getCourses()
    {
        return CourseResource::collection(Course::where('creator_id', auth('creator')->id())->with([
            'media',
            'courseIncludes',
            'courseAbles',
            'coursePrograms',
            'courseLearns',
        ])->get());
    }

    public function show(Course $course)
    {
        return view('creator.course.show', [
            'course' => $course->load([
                'courseIncludes',
                'courseAbles',
                'coursePrograms',
                'courseReviews',
                'courseLessons',
                'category'
            ]),
            'reviewTypes' => CourseReviewType::getKeys()]);
    }

    public function getCourse(Course $course)
    {
        //todo policy
        return new CourseResource($course->load([
            'media',
            'courseIncludes',
            'courseAbles',
            'coursePrograms',
            'courseLearns',
        ]));
    }

    public function searchCourses(string $search){
        return CourseResource::collection(Course::where([['creator_id', '=',auth('creator')->id()], ['name', 'LIKE', '%' . $search . '%']])
            ->orWhere([['creator_id', '=',auth('creator')->id()], ['description', 'LIKE', '%' . $search . '%']])
            ->with([
                'media',
                'courseIncludes',
                'courseAbles',
                'coursePrograms',
                'courseLearns',
        ])->get());
    }
    public function create()
    {
        $categories = CourseCategory::all();
        return view('creator.course.create', ['categories'=>$categories]);
    }

    public function store(CourseRequest $req){
        //todo dto, service, repository, transaction, response
        $previews = [];
        foreach ($req->previews as $preview) {
            $previews[] = $preview['id'];
        }
        $course = new Course();
        $course->name = $req->input('name');
        $course->date = $req->input('date');
        $course->duration = $req->input('duration');
        $course->price = $req->input('price');
        $course->description = $req->input('description');
        $course->category_id = $req->input('category');
        $course->creator_id = auth('creator')->id();
        $course->save();
        $course->attachMedia($previews, 'preview');
        $courseId = $course->id;
        foreach ($req->includes as $include){
            $courseItem = new CourseInclude();
            $courseItem->title = $include['title'];
            $courseItem->description = $include['description'];
            $courseItem->course_id = $courseId;
            $courseItem->save();
            $courseItem->attachMedia($include['file']['id'], 'image');
        }
        foreach ($req->ables as $able){
            $courseAble = new CourseAble();
            $courseAble->title = $able['title'];
            $courseAble->course_id = $courseId;
            $courseAble->save();
        }
        foreach ($req->programs as $program){
            $courseProgram = new CourseProgram();
            $courseProgram->section = $program['section'];
            $courseProgram->description = $program['description'];
            $courseProgram->course_id = $courseId;
            $courseProgram->save();
        }
        foreach ($req->learns as $learn){
            $courseLearn = new CourseLearn();
            $courseLearn->description = $learn['description'];
            $courseLearn->course_id = $courseId;
            $courseLearn->save();
        }
        return AjaxResponseNotification::success('Course successfully created', [], route('creator.courses.show', ['course' => $courseId]));
    }

    public function edit(Course $course)
    {
        $categories = CourseCategory::all();
        return view('creator.course.edit', ['courseId' => $course->id, 'categories'=> $categories]);
    }
    public function update(CourseRequest $req, Course $course){
        //todo dto, service, repository, transaction, response
        $previews = [];
        foreach ($req->previews as $preview) {
            $previews[] = $preview['id'];
        }
        $course->name = $req->input('name');
        $course->date = $req->input('date');
        $course->duration = $req->input('duration');
        $course->price = $req->input('price');
        $course->description = $req->input('description');
        $course->creator_id = auth('creator')->id();
        $course->category_id = $req->input('category');
        $course->save();
        $course->syncMedia($previews, 'preview');
        $courseId = $course->id;
        $includesNew = [];
        $includesOld = $course->courseIncludes->pluck('id')->toArray();
        foreach ($req->includes as $include){
            if (isset($include['id'])){
                $courseItem = CourseInclude::find($include['id']) ?? new CourseInclude();
                $includesNew[] = $include['id'];
            }else{
                $courseItem = new CourseInclude();
            }
            $courseItem->title = $include['title'];
            $courseItem->description = $include['description'];
            $courseItem->course_id = $courseId;
            $courseItem->save();
            $courseItem->syncMedia($include['file']['id'], 'image');
        }
        $includesDelete = array_diff($includesOld, $includesNew);
        CourseInclude::destroy($includesDelete);
        $ablesNew = [];
        $ablesOld = $course->courseAbles->pluck('id')->toArray();
        foreach ($req->ables as $able){
            if (isset($able['id'])){
                $courseAble = CourseAble::find($able['id']) ?? new CourseAble();
                $ablesNew[] = $able['id'];
            }else{
                $courseAble = new CourseAble();
            }
            $courseAble->title = $able['title'];
            $courseAble->course_id = $courseId;
            $courseAble->save();
        }
        $ablesDelete = array_diff($ablesOld, $ablesNew);
        CourseAble::destroy($ablesDelete);
        $programsNew = [];
        $programsOld = $course->coursePrograms->pluck('id')->toArray();
        foreach ($req->programs as $program){
            if (isset($program['id'])){
                $courseProgram = CourseProgram::find($program['id']) ?? new CourseProgram();
                $programsNew[] = $program['id'];
            }else{
                $courseProgram = new CourseProgram();
            }
            $courseProgram->section = $program['section'];
            $courseProgram->description = $program['description'];
            $courseProgram->course_id = $courseId;
            $courseProgram->save();
        }
        $programsDelete = array_diff($programsOld, $programsNew);
        CourseProgram::destroy($programsDelete);
        $learnsNew = [];
        $learnsOld = $course->courseLearns->pluck('id')->toArray();
        foreach ($req->learns as $learn){
            if (isset($learn['id'])){
                $courseLearn = CourseLearn::find($learn['id']) ?? new CourseLearn();
                $learnsNew[] = $learn['id'];
            }else{
                $courseLearn = new CourseLearn();
            }
            $courseLearn->description = $learn['description'];
            $courseLearn->course_id = $courseId;
            $courseLearn->save();
        }
        $learnsDelete = array_diff($learnsOld, $learnsNew);
        CourseLearn::destroy($learnsDelete);
        return AjaxResponseNotification::success('Course successfully updated', [], route('creator.courses.show', ['course' => $courseId]));
    }
    public function destroy(Course $course)
    {
        if($course->delete()){
            return AjaxResponseNotification::success('Course successfully deleted');
        }
        return AjaxResponseNotification::error('Course not deleted');
    }
}
