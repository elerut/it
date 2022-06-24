<?php

namespace App\Http\Controllers\Creator;

use App\Enums\CourseLessonSectionContentType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Creator\CourseLesson\StoreRequest;
use App\Http\Requests\Creator\CourseLesson\UpdateRequest;
use App\Http\Resources\Creator\CourseLessonResource;
use App\Models\Course\Course;
use App\Models\Course\CourseLesson;
use App\Services\App\Write\CourseLessonWriteService;
use App\Services\Helpers\AjaxResponseNotification;

class LessonController extends Controller
{
    protected CourseLessonWriteService $lessonWriteService;
    public function __construct(CourseLessonWriteService $lessonWriteService)
    {
        $this->lessonWriteService = $lessonWriteService;
    }

    public function create(Course $course)
    {
        return view('creator.lesson.create', ['courseId' => $course->id]);
    }

    public function getLessons(Course $course)
    {
        return CourseLessonResource::collection(CourseLesson::where('course_id', $course->id)->with([
            'courseLessonSections',
            'courseLessonSections.courseLessonSectionContents',
        ])->get());
    }
    public function store(StoreRequest $storeRequest)
    {
        if ($this->lessonWriteService->create($storeRequest->getDto())){
            return AjaxResponseNotification::success('Lesson successfully created', [], route('creator.courses.show', ['course' => $storeRequest->getDto()->courseId]));
        }
        return AjaxResponseNotification::error('Lesson not created');
    }
    public function getLesson(Course $course, CourseLesson $courseLesson)
    {
        //todo policy
        return new CourseLessonResource($courseLesson->load([
            'courseLessonSections',
            'courseLessonSections.courseLessonSectionContents',
        ]));
    }
    public function edit(Course $course, CourseLesson $courseLesson)
    {
        return view('creator.lesson.edit', ['courseId' => $course->id, 'courseLessonId' => $courseLesson->id]);
    }

    public function update(UpdateRequest $updateRequest, Course $course, CourseLesson $courseLesson)
    {
        if ($this->lessonWriteService->update($updateRequest->getDto(), $courseLesson)){
            return AjaxResponseNotification::success('Lesson successfully updated', [], route('creator.courses.show', ['course' => $course]));
        }
        return AjaxResponseNotification::error('Lesson not updated');
    }

    public function destroy(Course $course, CourseLesson $courseLesson)
    {
        if($courseLesson->delete()){
            return AjaxResponseNotification::success('Lesson successfully deleted');
        }
        return AjaxResponseNotification::error('Lesson not deleted');
    }

    public function show(Course $course, CourseLesson $courseLesson)
    {
        $courseLesson->load('courseLessonSections');
        $courseLesson->courseLessonSections->load('courseLessonSectionContents');
        return view('creator.lesson.show', ['course'=>$course,'lesson'=>$courseLesson, 'contentType'=>CourseLessonSectionContentType::getKeys()]);
    }
}
