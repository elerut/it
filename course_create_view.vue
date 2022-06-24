<template>
    <section class="app-user-edit">
        <div class="card">
            <div class="row">
                <div class="col-lg-8">
                    <div class="card-header">
                        <h4 class="card-title">Create course</h4>
                        <span>
                    Sweet pie candy jelly. Sesame snaps biscuit sugar plum. Sweet roll topping fruitcake. Caramels
                                liquorice biscuit ice cream fruitcake cotton candy tart. Donut caramels gingerbread jelly-o
                                gingerbread pudding. Gummi bears pastry marshmallow candy canes pie. Pie apple pie carrot cake.
                </span>
                    </div>
                    <div class="card-body">
                        <form class="form-validate" novalidate="novalidate">
                            <div class="row">
                                <div class="col-md-12">
                                    <h5>About course</h5>
                                </div>
                                <div class="col-md-6">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <input type="text" class="form-control" :class="{'is-invalid': errors['name']}"
                                                       placeholder="name" v-model="course.name">
                                                <span v-if="errors['name']" class="invalid-feedback" role="alert"
                                                      v-for="error in errors['name']">
                                                                <strong>
                                                                    {{ error }}
                                                                </strong>
                                                            </span>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <input type="date" class="form-control"
                                                       :class="{'is-invalid': errors['date']}"
                                                       v-model="course.date">
                                                <span v-if="errors['date']" class="invalid-feedback" role="alert"
                                                      v-for="error in errors['date']">
                                                                <strong>
                                                                    {{ error }}
                                                                </strong>
                                                            </span>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <input type="number" class="form-control" placeholder="duration"
                                                       :class="{'is-invalid': errors['duration']}" v-model="course.duration">
                                                <span v-if="errors['duration']" class="invalid-feedback" role="alert"
                                                      v-for="error in errors['duration']">
                                                                <strong>
                                                                    {{ error }}
                                                                </strong>
                                                            </span>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <input type="number" class="form-control" :class="{'is-invalid': errors['price']}"
                                                       v-model="course.price" placeholder="price">
                                                <span v-if="errors['price']" class="invalid-feedback" role="alert"
                                                      v-for="error in errors['price']">
                                                                <strong>
                                                                    {{ error }}
                                                                </strong>
                                                            </span>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <select class="form-control" id="basicSelect" v-model="course.category" :class="{'is-invalid': errors['category']}">
                                                    <option :value="null" disabled>Select Category</option>
                                                    <option v-for="category in categories" v-bind:value="category.id">
                                                        {{ category.title }}
                                                    </option>
                                                </select>
                                                <span v-if="errors['category']" class="invalid-feedback" role="alert"
                                                      v-for="error in errors['category']">
                                                                <strong>
                                                                    {{ error }}
                                                                </strong>
                                                            </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <vue-editor placeholder="Description" :editorToolbar="customToolbar" :class="{'is-invalid': errors['description']}"
                                                    v-model="course.description">

                                        </vue-editor>
                                        <span v-if="errors['description']" class="invalid-feedback" role="alert"
                                              v-for="error in errors['description']">
                                                    <strong>
                                                        {{ error }}
                                                    </strong>
                                                </span>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <h5>media</h5>
                                    <div class="row">
                                        <div class="col-md-3" v-for="(preview, indexPreview) in course.previews"
                                             :key="indexPreview">
                                            <div class="form-group">
                                        <span class="delete-button" v-if="indexPreview>0"
                                              @click="deletePreview(indexPreview)"><i
                                            class="bi bi-x-lg cursor-pointer"></i></span>
                                                <img :src="preview.url"
                                                     v-if="preview.type == 'image'"
                                                     :class="{'is-invalid': errors['previews.'+indexPreview+'.id']}"
                                                     @click="triggerPreviewAddedFile(indexPreview)" class="w-100 cursor-pointer"
                                                     alt="placeholder">
                                                <video :src="preview.url"
                                                       v-if="preview.type == 'video'"
                                                       @click="triggerPreviewAddedFile(indexPreview)" class="w-100 cursor-pointer">
                                                </video>
                                                <span v-if="errors['previews.'+indexPreview+'.id']" class="invalid-feedback"
                                                      role="alert" v-for="error in errors['previews.'+indexPreview+'.id']">
                                                        <strong>
                                                            {{ error }}
                                                        </strong>
                                                    </span>
                                            </div>
                                        </div>
                                        <div class="col-md-3 preview-btn">
                                            <div class="form-group">
                                                <button @click="addPreview" type="button"
                                                        class="btn btn-primary mb-1 mb-sm-0 mr-0 mr-sm-1 waves-effect waves-float waves-light">
                                                    add media
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <input :class="{'is-invalid': errors['preview']}" ref='coursePreviewFile' name="file"
                                           @change="changePreviewFile($event)" type="file" class="form-control d-none">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <h5>What will be included in the course?</h5>
                                </div>
                                <div class="col-lg-12" v-for="(include, indexInclude) in course.includes" :key="indexInclude">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="row">
                                        <span class="delete-button" v-if="indexInclude>0"
                                              @click="deleteInclude(indexInclude)"><i
                                            class="bi bi-x-lg cursor-pointer"></i></span>
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <input type="text"
                                                               :class="{'is-invalid': errors['includes' + '.' + indexInclude + '.' + 'title']}"
                                                               class="form-control" placeholder="title" value="" name="username"
                                                               v-model="include.title">
                                                        <span v-if="errors['includes' + '.' + indexInclude + '.' + 'title']"
                                                              class="invalid-feedback" role="alert"
                                                              v-for="error in errors['includes' + '.' + indexInclude + '.' + 'title']">
                                                            <strong>
                                                                {{ error }}
                                                            </strong>
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                <textarea name="description"
                                                          :class="{'is-invalid': errors['includes' + '.' + indexInclude + '.' + 'description']}"
                                                          class="form-control" rows="1" placeholder="description"
                                                          v-model="include.description"></textarea>
                                                        <span
                                                            v-if="errors['includes' + '.' + indexInclude + '.' + 'description']"
                                                            class="invalid-feedback" role="alert"
                                                            v-for="error in errors['includes' + '.' + indexInclude + '.' + 'description']">
                                                            <strong>
                                                                {{ error }}
                                                            </strong>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <h5>media</h5>
                                                    <div class="form-group">
                                                        <img :src="include.file.url"
                                                             :class="{'is-invalid': errors['includes' + '.' + indexInclude + '.' + 'file']}"
                                                             @click="triggerIncludeAddedFile(indexInclude)"
                                                             class="w-100 cursor-pointer" alt="placeholder">
                                                        <span v-if="errors['includes' + '.' + indexInclude + '.' + 'file']"
                                                              class="invalid-feedback" role="alert"
                                                              v-for="error in errors['includes' + '.' + indexInclude + '.' + 'file']">
                                                            <strong>
                                                                {{ error }}
                                                            </strong>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <input :class="{'is-invalid': errors['preview']}" ref='courseIncludeFile' name="file"
                                               @change="changeIncludeFile($event)" type="file" class="form-control d-none">
                                        <button @click="addInclude" type="button"
                                                class="btn btn-primary mb-1 mb-sm-0 mr-0 mr-sm-1 waves-effect waves-float waves-light">
                                            add section
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <h5>You will learn</h5>
                                    <div class="row">
                                        <div class="col-md-12" v-for="(learn, learnIndex) in course.learns" :key="learnIndex">
                                            <div class="form-group">
                                        <span class="delete-button" v-if="learnIndex>0"
                                              @click="deleteLearn(learnIndex)"><i class="bi bi-x-lg cursor-pointer"></i></span>
                                                <input
                                                    :class="{'is-invalid': errors['learns' + '.' + learnIndex + '.' + 'description']}"
                                                    v-model="learn.description" type="text" class="form-control"
                                                    placeholder="description">
                                                <span v-if="errors['learns' + '.' + learnIndex + '.' + 'description']"
                                                      class="invalid-feedback" role="alert"
                                                      v-for="error in errors['learns' + '.' + learnIndex + '.' + 'description']">
                                                            <strong>
                                                                {{ error }}
                                                            </strong>
                                                        </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <button type="button" @click="addLearn"
                                                class="btn btn-primary mb-1 mb-sm-0 mr-0 mr-sm-1 waves-effect waves-float waves-light">
                                            add section
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <h5>You will be able</h5>
                                    <div class="row">
                                        <div class="col-md-12" v-for="(able, ableIndex) in course.ables" :key="ableIndex">
                                    <span class="delete-button" v-if="ableIndex>0" @click="deleteAble(ableIndex)"><i
                                        class="bi bi-x-lg cursor-pointer"></i></span>
                                            <div class="form-group">
                                                <input
                                                    :class="{'is-invalid': errors['ables' + '.' + ableIndex + '.' + 'title']}"
                                                    type="text" v-model="able.title" class="form-control" placeholder="title">
                                                <span v-if="errors['includes' + '.' + ableIndex + '.' + 'title']"
                                                      class="invalid-feedback" role="alert"
                                                      v-for="error in errors['includes' + '.' + ableIndex + '.' + 'title']">
                                                            <strong>
                                                                {{ error }}
                                                            </strong>
                                                        </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <button type="button" @click="addAble"
                                                class="btn btn-primary mb-1 mb-sm-0 mr-0 mr-sm-1 waves-effect waves-float waves-light">
                                            add section
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <h5>Course program</h5>
                                    <div class="row" v-for="(program, programIndex) in course.programs" :key="programIndex">
                                <span class="delete-button" v-if="programIndex>0"
                                      @click="deleteProgram(programIndex)"><i
                                    class="bi bi-x-lg cursor-pointer"></i></span>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <input
                                                    :class="{'is-invalid': errors['programs' + '.' + programIndex + '.' + 'section']}"
                                                    v-model="program.section" type="text" class="form-control"
                                                    placeholder="1 section" value="" name="username" id="username">
                                                <span v-if="errors['programs' + '.' + programIndex + '.' + 'section']"
                                                      class="invalid-feedback" role="alert"
                                                      v-for="error in errors['programs' + '.' + programIndex + '.' + 'section']">
                                                            <strong>
                                                                {{ error }}
                                                            </strong>
                                                        </span>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <vue-editor placeholder="Description" :editorToolbar="customToolbar" :class="{'is-invalid': errors['programs' + '.' + programIndex + '.' + 'description']}"
                                                            v-model="program.description">

                                                </vue-editor>
                                                <span v-if="errors['includes' + '.' + programIndex + '.' + 'description']"
                                                      class="invalid-feedback" role="alert"
                                                      v-for="error in errors['includes' + '.' + programIndex + '.' + 'description']">
                                                            <strong>
                                                                {{ error }}
                                                            </strong>
                                                        </span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <button type="button" @click="addProgram"
                                                class="btn btn-primary mb-1 mb-sm-0 mr-0 mr-sm-1 waves-effect waves-float waves-light">
                                            add section
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-12 d-flex flex-sm-row flex-column mt-2">
                                    <button type="button" @click="saveCourse"
                                            class="btn btn-primary mb-1 mb-sm-0 mr-0 mr-sm-1 waves-effect waves-float waves-light">
                                        save and create lessons
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>

<style>
.delete-button {
    position: absolute;
    right: -5px;
    z-index: 1000;
}

.preview-btn {
    display: flex;
    align-items: center;
}

img.is-invalid {
    border: 1px solid #ea5455;
    border-radius: 0.357rem;
}
</style>

<script>
import {VueEditor} from "vue2-editor";
export default {
    props: {
        categories: {required: true}
    },
    data() {
        return {
            customToolbar:[[{ 'size': ['small', false, 'large', 'huge'] }],[{ 'align': [] }], ['bold', 'italic', 'underline', 'strike']],
            course: {
                name: null,
                description: null,
                price: null,
                duration: null,
                date: null,
                category: null,
                previews: [
                    {url: '/assets/creator/img/photo-file.png', type: "image"}
                ],
                learns: [
                    {description: null}
                ],
                includes: [
                    {
                        title: null,
                        description: null,
                        file: {
                            url: '/assets/creator/img/photo-file.png'
                        },
                    }
                ],
                ables: [
                    {
                        title: null
                    }
                ],
                programs: [
                    {
                        section: null,
                        description: null
                    }
                ],
            },
            errors: {},
            previewIndex: null,
            includeIndex: null,
        }
    },
    methods: {
        triggerPreviewAddedFile(index) {
            this.$refs.coursePreviewFile.click()
            this.previewIndex = index
        },
        triggerIncludeAddedFile(index) {
            this.$refs.courseIncludeFile.click()
            this.includeIndex = index
        },
        addPreview() {
            this.course.previews.push({url: '/assets/creator/img/photo-file.png', type:'image'})
        },
        addLearn() {
            this.course.learns.push({description: null})
        },
        deleteLearn(key) {
            this.course.learns.splice(key, 1);
        },
        addInclude() {
            this.course.includes.push({
                title: null, description: null,
                file: {url: '/assets/creator/img/photo-file.png'}
            })
        },
        deletePreview(key) {
            this.course.previews.splice(key, 1);
        },
        deleteInclude(key) {
            this.course.includes.splice(key, 1);
        },
        changeIncludeFile(event) {
            const formData = new FormData();
            let file = event.target.files[0]
            formData.append('file', file);
            const headers = {'Content-Type': 'multipart/form-data'};
            axios.post('/creator/media', formData, {headers}).then((res) => {
                if (res.status == 200) {
                    this.course.includes[this.includeIndex].file.id = res.data.payload.id
                    var fr = new FileReader();
                    let vm = this
                    fr.onload = function () {
                        vm.course.includes[vm.includeIndex].file.url = fr.result
                    }
                    fr.readAsDataURL(file);
                }
            });

        },
        changePreviewFile() {
            const formData = new FormData();
            let file = event.target.files[0]
            formData.append('file', file);
            const headers = {'Content-Type': 'multipart/form-data'};
            axios.post('/creator/media', formData, {headers}).then((res) => {
                if (res.status == 200) {
                    this.course.previews[this.previewIndex].id = res.data.payload.id
                    this.course.previews[this.previewIndex].type = res.data.payload.type
                    var fr = new FileReader();
                    let vm = this
                    fr.onload = function () {
                        vm.course.previews[vm.previewIndex].url = fr.result
                    }
                    fr.readAsDataURL(file);
                }
            });
        },
        addAble() {
            this.course.ables.push({title: null})
        },
        deleteAble(key) {
            this.course.ables.splice(key, 1);
        },
        addProgram() {
            this.course.programs.push({title: null})
        },
        deleteProgram(key) {
            this.course.programs.splice(key, 1);
        },
        addFile(lesson) {
            lesson.files.push({type: 'file'})
        },
        deleteFile(lesson, key) {
            lesson.files.splice(key, 1);
        },
        addTask(lessonIndex) {
            this.course.lessons[lessonIndex].tasks.push({title: null, file: null})
        },
        deleteTask(lessonIndex, key) {
            this.course.lessons[lessonIndex].tasks.splice(key, 1);
        },
        changeTaskFile(event, lessonIndex, index) {
            this.course.lessons[lessonIndex].tasks[index].file = event.target.files[0]
        },
        saveCourse() {
            window.showPreloader()
            axios.post('/creator/courses', this.course).then((response) => {
                this.errors = {}
                window.successResponse(response.data)
            }).catch((error) => {
                window.errorResponse(error.response)
                if (error.response.data.toast){
                    window.toastSwal(error.response.data.toast.type, error.response.data.toast.text)
                }
                this.errors = error.response.data.errors ?? {}
            })
        }

    },
}
</script>
