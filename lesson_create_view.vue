<template>
    <section class="app-user-edit">
        <div class="card">
            <div class="row">
                <div class="col-lg-8">
                    <div class="card-header">
                        <h4 class="card-title">Create lessons</h4>
                        <span>
                    Sweet pie candy jelly. Sesame snaps biscuit sugar plum. Sweet roll topping fruitcake. Caramels
                                liquorice biscuit ice cream fruitcake cotton candy tart. Donut caramels gingerbread jelly-o
                                gingerbread pudding. Gummi bears pastry marshmallow candy canes pie. Pie apple pie carrot cake.
                </span>
                    </div>
                    <div class="card-header">
                        <h5>About lesson</h5>
                        <span>
                    Sweet pie candy jelly. Sesame snaps biscuit sugar plum. Sweet roll topping fruitcake. Caramels
                                liquorice biscuit ice cream fruitcake cotton candy tart. Donut caramels gingerbread jelly-o
                                gingerbread pudding. Gummi bears pastry marshmallow candy canes pie. Pie apple pie carrot cake.
                </span>
                    </div>
                    <div class="card-body">
                        <form class="form-validate" novalidate="novalidate">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <input type="text" class="form-control"
                                                       :class="{'is-invalid': errors['name']}"
                                                       placeholder="name" v-model="lesson.name">
                                                <span v-if="errors['name']" class="invalid-feedback" role="alert"
                                                      v-for="error in errors['name']">
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
                                <textarea class="form-control" :class="{'is-invalid': errors['description']}"
                                          v-model="lesson.description" placeholder="description" rows="10"></textarea>
                                        <span v-if="errors['description']" class="invalid-feedback" role="alert"
                                              v-for="error in errors['description']">
                                                    <strong>
                                                        {{ error }}
                                                    </strong>
                                                </span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>schedule for opening</label>
                                        <input type="datetime-local" class="form-control"
                                               :class="{'is-invalid': errors['date']}"
                                               v-model="lesson.date">
                                        <span v-if="errors['date']" class="invalid-feedback" role="alert"
                                              v-for="error in errors['date']">
                                                                <strong>
                                                                    {{ error }}
                                                                </strong>
                                                            </span>
                                    </div>
                                </div>
                            </div>
                            <div class="row pt-3" v-for="(section, indexSection) in lesson.sections"
                                 :key="indexSection">
                                <div class="col-md-12">
                            <span class="delete-button" v-if="indexSection>0"
                                  @click="deleteSection(indexSection)"><i class="bi bi-x-lg cursor-pointer"></i></span>
                                    <h5>{{ indexSection + 1 }} section</h5>
                                </div>
                                <div class="col-md-12">
                                    <span>
                                        Sweet pie candy jelly. Sesame snaps biscuit sugar plum. Sweet roll topping fruitcake. Caramels
                                liquorice biscuit ice cream fruitcake cotton candy tart. Donut caramels gingerbread jelly-o
                                gingerbread pudding. Gummi bears pastry marshmallow candy canes pie. Pie apple pie carrot cake
                                    </span>
                                </div>
                                <div class="col-md-6 mt-1">
                                    <div class="form-group">
                                        <input type="text" class="form-control" :class="{'is-invalid': errors['sections.' + indexSection + '.name']}"
                                               placeholder="name" v-model="section.name">
                                        <span v-if="errors['sections.' + indexSection + '.name']" class="invalid-feedback"
                                              role="alert"
                                              v-for="error in errors['sections.' + indexSection + '.name']">
                                                                <strong>
                                                                    {{ error }}
                                                                </strong>
                                                            </span>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                <textarea class="form-control" :class="{'is-invalid': errors['sections.' + indexSection + '.description']}"
                                          v-model="section.description" placeholder="description" rows="10"></textarea>
                                        <span v-if="errors['sections.' + indexSection + '.description']"
                                              class="invalid-feedback" role="alert"
                                              v-for="error in errors['sections.' + indexSection + '.description']">
                                                    <strong>
                                                        {{ error }}
                                                    </strong>
                                                </span>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <h5>Content</h5>
                                    <div class="row" v-for="(content, indexContent) in section.contents"
                                         :key="indexContent">
                                        <div class="col-lg-12">
                                            <h5>
                                                {{ content.content.title }}
                                                <span
                                                    @click="deleteContent(indexSection, indexContent)">
                                                    <i class="bi bi-x-lg cursor-pointer"></i>
                                                </span>
                                            </h5>
                                        </div>
                                        <div class="col-lg-3" v-if="content.content.type === 0">
                                            <div class="form-group">
                                                <img :src="content.content.url"
                                                     :class="{'is-invalid': errors['sections.'+indexSection+'.contents.'+indexContent+'.content.id']}"
                                                     @click="triggerContentAddedFile(indexSection, indexContent)"
                                                     class="w-100 cursor-pointer"
                                                     alt="placeholder">
                                                <span v-if="errors['sections.'+indexSection+'.contents.'+indexContent+'.content.id']"
                                                      class="invalid-feedback"
                                                      role="alert"
                                                      v-for="error in errors['sections.'+indexSection+'.contents.'+indexContent+'.content.id']">
                                                        <strong>
                                                            {{ error }}
                                                        </strong>
                                                    </span>
                                            </div>
                                        </div>
                                        <div class="col-lg-6" v-else-if="content.content.type === 1">
                                            <div class="form-group">
                                                <input type="url" class="form-control"
                                                       :class="{'is-invalid': errors['sections.'+indexSection+'.contents.'+indexContent+'.content.url']}"
                                                       v-model="content.content.url" placeholder="url">
                                                <span v-if="errors['sections.'+indexSection+'.contents.'+indexContent+'.content.url']"
                                                      class="invalid-feedback"
                                                      role="alert"
                                                      v-for="error in errors['sections.'+indexSection+'.contents.'+indexContent+'.content.url']">
                                                    <strong>
                                                        {{ error }}
                                                    </strong>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="col-lg-6" v-else-if="content.content.type === 2">
                                            <div class="form-group">
                                                <input type="url" class="form-control"
                                                       :class="{'is-invalid': errors['sections.'+indexSection+'.contents.'+indexContent+'.content.url']}"
                                                       v-model="content.content.url" placeholder="url">
                                                <span v-if="errors['sections.'+indexSection+'.contents.'+indexContent+'.content.url']"
                                                      class="invalid-feedback"
                                                      role="alert"
                                                      v-for="error in errors['sections.'+indexSection+'.contents.'+indexContent+'.content.url']">
                                                    <strong>
                                                        {{ error }}
                                                    </strong>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="col-lg-12" v-else-if="content.content.type === 3">
                                            <div class="form-group">
                                                <textarea class="form-control"
                                                          :class="{'is-invalid': errors['sections.'+indexSection+'.contents.'+indexContent+'.content.text']}"
                                                          v-model="content.content.text" placeholder="text" rows="10"></textarea>
                                                <span v-if="errors['sections.'+indexSection+'.contents.'+indexContent+'.content.text']"
                                                      class="invalid-feedback"
                                                      role="alert"
                                                      v-for="error in errors['sections.'+indexSection+'.contents.'+indexContent+'.content.text']">
                                                    <strong>
                                                        {{ error }}
                                                    </strong>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="col-lg-6" v-else-if="content.content.type === 4">
                                            <div class="form-group">
                                                <input type="url" class="form-control"
                                                       :class="{'is-invalid': errors['sections.'+indexSection+'.contents.'+indexContent+'.content.url']}"
                                                       v-model="content.content.url" placeholder="url">
                                                <span v-if="errors['sections.'+indexSection+'.contents.'+indexContent+'.content.url']"
                                                      class="invalid-feedback"
                                                      role="alert"
                                                      v-for="error in errors['sections.'+indexSection+'.contents.'+indexContent+'.content.url']">
                                                    <strong>
                                                        {{ error }}
                                                    </strong>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="col-lg-12" v-else-if="content.content.type === 5">
                                            <div class="form-group">
                                                <textarea class="form-control"
                                                          :class="{'is-invalid': errors['sections.'+indexSection+'.contents.'+indexContent+'.content.text']}"
                                                          v-model="content.content.text" placeholder="text" rows="10"></textarea>
                                                <span v-if="errors['sections.'+indexSection+'.contents.'+indexContent+'.content.text']"
                                                      class="invalid-feedback"
                                                      role="alert"
                                                      v-for="error in errors['sections.'+indexSection+'.contents.'+indexContent+'.content.text']">
                                                    <strong>
                                                        {{ error }}
                                                    </strong>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="col-lg-12" v-else>
                                            <div class="row">
                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <input type="text" class="form-control"
                                                               :class="{'is-invalid': errors['description']}"
                                                               v-model="content.content.name" placeholder="name">
                                                        <span v-if="errors['section.contents.'+indexContent+'.id']"
                                                              class="invalid-feedback"
                                                              role="alert"
                                                              v-for="error in errors['section.contents.'+indexContent+'.id']">
                                                            <strong>
                                                                {{ error }}
                                                            </strong>
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="col-lg-3">
                                                    <button type="button" @click="addQuestion(indexSection, indexContent)"
                                                            class="btn btn-primary mb-1 mb-sm-0 mr-0 mr-sm-1 waves-effect waves-float waves-light">
                                                        add question
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="row" v-for="(question, indexQuestion) in content.content.questions" :key="indexQuestion">
                                                <div class="col-lg-6 pt-1">
                                                    <div class="form-group">
                                                    <span class="delete-button"
                                                          v-if="indexQuestion>0"
                                                          @click="deleteQuestion(indexSection, indexContent, indexQuestion)">
                                                        <i class="bi bi-x-lg cursor-pointer"></i>
                                                    </span>
                                                        <input type="text" class="form-control"
                                                               :class="{'is-invalid': errors['section.contents.'+indexContent+'.content.questions.' + indexQuestion + '.question']}"
                                                               v-model="question.question" placeholder="question">
                                                        <span v-if="errors['section.contents.'+indexContent+'.content.questions.' + indexQuestion + '.question']"
                                                              class="invalid-feedback"
                                                              role="alert"
                                                              v-for="error in errors['section.contents.'+indexContent+'.content.questions.' + indexQuestion + '.question']">
                                                        <strong>
                                                            {{ error }}
                                                        </strong>
                                                    </span>
                                                    </div>
                                                    <div class="" v-for="(answer, indexAnswer) in question.answers" :key="indexAnswer">
                                                        <div class="form-group">
                                                    <span class="delete-button"
                                                          v-if="indexAnswer>0"
                                                          @click="deleteAnswer(indexSection, indexContent, indexQuestion, indexAnswer)">
                                                        <i class="bi bi-x-lg cursor-pointer"></i>
                                                    </span>
                                                            <input type="text" class="form-control"
                                                                   :class="{'is-invalid': errors['section.contents.'+indexContent+'.content.questions.' + indexQuestion + '.answers.' + indexAnswer + '.answer']}"
                                                                   v-model="answer.answer" placeholder="answer">
                                                            <span v-if="errors['section.contents.'+indexContent+'.content.questions.' + indexQuestion + '.answers.' + indexAnswer + '.answer']"
                                                                  class="invalid-feedback"
                                                                  role="alert"
                                                                  v-for="error in errors['section.contents.'+indexContent+'.content.questions.' + indexQuestion + '.answers.' + indexAnswer + '.answer']">
                                                            <strong>
                                                                {{ error }}
                                                            </strong>
                                                        </span>
                                                            <div class="pt-1">

                                                            </div>
                                                            <div class="custom-control custom-checkbox">
                                                                <input v-model="answer.correct" type="checkbox" class="custom-control-input" :id="'is-correct-' + indexSection + '-' + indexContent + '_' + indexQuestion + '_' +indexAnswer" checked="">
                                                                <label class="custom-control-label" :for="'is-correct-' + indexSection + '-' + indexContent + '_' + indexQuestion + '_' +indexAnswer">Is correct</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <button type="button" @click="addAnswer(indexSection, indexContent, indexQuestion)"
                                                            class="btn btn-primary mb-1 mb-sm-0 mr-0 mr-sm-1 waves-effect waves-float waves-light">
                                                        add answer
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group pt-1">
                                        <button type="button" @click="addContent(indexSection)"
                                                class="btn btn-primary mb-1 mb-sm-0 mr-0 mr-sm-1 waves-effect waves-float waves-light">
                                            add content
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <input :class="{'is-invalid': errors['preview']}" ref='contentFile' name="file"
                                   @change="changeContentFile($event)" type="file" class="form-control d-none">
                            <div class="form-group">
                                <button type="button" @click="addSection"
                                        class="btn btn-primary mb-1 mb-sm-0 mr-0 mr-sm-1 waves-effect waves-float waves-light">
                                    add section
                                </button>
                            </div>


                            <div class="row">
                                <div class="col-12 d-flex flex-sm-row flex-column mt-2">
                                    <button @click="saveLesson" type="button"
                                            class="btn btn-primary mb-1 mb-sm-0 mr-0 mr-sm-1 waves-effect waves-float waves-light">
                                        save
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="col-lg-2 lesson-list text-right">
                    <ul>
                        <li v-for="(lesson, indexLesson) in lessons" :key="indexLesson">
                            <a :href="'/creator/courses/'+courseId+'/lessons/'+lesson.id+'/edit'">
                                {{ordinalSuffixOf(indexLesson + 1)}} lesson
                            </a>
                        </li>
                    </ul>
                    <a :href="'/creator/courses/'+courseId+'/lessons/create'" class="btn btn-primary waves-effect waves-float waves-light">
                        add lesson
                    </a>
                </div>
            </div>
        </div>
    </section>
</template>

<style lang="scss">
.delete-button {
    position: absolute;
    right: -5px;
    z-index: 1000;
}
.lesson-list{
    margin-top: 144px;
    font-size: 1.3rem;
    ul{
        list-style-type: none;
    }
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
import Content from './content'
import Swal from "sweetalert2";

export default {
    props: ['courseId'],
    components: {Content},
    data() {
        return {
            lesson: {
                name: null,
                description: null,
                date: null,
                sections: [
                    {
                        name: null,
                        description: null,
                        contents: []
                    }
                ],
            },
            errors: {},
            sectionIndex: null,
            contentIndex: null,
            lessons: {}
        }
    },
    methods: {
        triggerContentAddedFile(indexSection, indexContent) {
            this.$refs.contentFile.click()
            this.sectionIndex = indexSection
            this.contentIndex = indexContent
        },
        changeContentFile(event) {
            const formData = new FormData();
            let file = event.target.files[0]
            formData.append('file', file);
            const headers = {'Content-Type': 'multipart/form-data'};
            axios.post('/creator/media', formData, {headers}).then((res) => {
                if (res.status == 200) {
                    this.lesson.sections[this.sectionIndex].contents[this.contentIndex].content.id = res.data.payload.id
                    this.lesson.sections[this.sectionIndex].contents[this.contentIndex].content.url = res.data.payload.url
                }
            });

        },
        addContent(indexSection) {
            let content = new Vue({
                ...Content,
                parent: this,
            }).$mount()
            let swalOpen = Swal.fire({
                heightAuto: false,
                html: content.$el,
                focusConfirm: false,
                showCancelButton: false,
                showConfirmButton: false,
                showCloseButton: true,
                customClass: {
                    container: 'course-lesson-section-content',
                },
                didDestroy: () => content.$destroy()
            })
            let vm = this;
            content.$on('newContent', (data) => {
                console.log(vm.lesson.sections[indexSection].contents)
                vm.lesson.sections[indexSection].contents.push(data)
                swalOpen.close()
            })
        },
        deleteContent(indexSection, indexContent) {
            this.lesson.sections[indexSection].contents.splice(indexContent, 1)
        },
        addSection() {
            this.lesson.sections.push({
                name: null,
                description: null,
                contents: []
            })
        },
        deleteSection(key) {
            this.lesson.sections.splice(key, 1);
        },
        addQuestion(indexSection, indexContent){
            this.lesson.sections[indexSection].contents[indexContent].content.questions.push({
                question: null,
                answers: [
                    {
                        answer: null,
                        correct: false
                    }
                ]
            })
        },
        addAnswer(indexSection, indexContent, indexQuestion){
            this.lesson.sections[indexSection].contents[indexContent].content.questions[indexQuestion].answers.push({
                title: null,
                correct: false
            })
        },
        deleteAnswer(indexSection, indexContent, indexQuestion, indexAnswer){
            this.lesson.sections[indexSection].contents[indexContent].content.questions[indexQuestion].answers.splice(indexAnswer, 1)
        },
        deleteQuestion(indexSection, indexContent, indexQuestion){
            this.lesson.sections[indexSection].contents[indexContent].content.questions.splice(indexQuestion, 1)
        },
        saveLesson(){
            window.showPreloader()
            axios.post('/creator/courses/'+this.courseId+'/lessons', this.lesson).then((response) => {
                this.errors = {}
                window.successResponse(response.data)
            }).catch((error) => {
                window.errorResponse(error.response)
                if (error.response.data.toast){
                    window.toastSwal(error.response.data.toast.type, error.response.data.toast.text)
                }
                this.errors = error.response.data.errors ?? {}
            })
        },
        getLessons(){
            axios.get('/creator/courses/'+this.courseId+'/lessons/all').then((response) => {
                this.lessons = response.data.data
                window.hidePreloader()
            }).catch((error) => {
                window.errorResponse(error.response)
            })
        },
        ordinalSuffixOf(i) {
            var j = i % 10,
                k = i % 100;
            if (j == 1 && k != 11) {
                return i + "st";
            }
            if (j == 2 && k != 12) {
                return i + "nd";
            }
            if (j == 3 && k != 13) {
                return i + "rd";
            }
            return i + "th";
        }
    },
    mounted() {
        this.getLessons()
    }
}
</script>
